<?php

namespace Chrisvasey\SimpleStatamicToolbar\Tests\Feature;

use Chrisvasey\SimpleStatamicToolbar\Http\Middleware\InjectToolbar;
use Chrisvasey\SimpleStatamicToolbar\ServiceProvider;
use Chrisvasey\SimpleStatamicToolbar\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Statamic\Facades\User;

class InjectToolbarMiddlewareTest extends TestCase
{
    private InjectToolbar $middleware;

    protected function setUp(): void
    {
        parent::setUp();

        $this->middleware = new InjectToolbar;
    }

    private function makeRequest(string $path = '/'): Request
    {
        return Request::create($path);
    }

    private function makeHtmlResponse(): Response
    {
        return new Response('<html><body><h1>Hello</h1></body></html>', 200, ['Content-Type' => 'text/html']);
    }

    private function callMiddleware(Request $request, Response $response): Response
    {
        return $this->middleware->handle($request, function () use ($response) {
            return $response;
        });
    }

    public function test_injects_toolbar_for_authenticated_html_responses()
    {
        $user = User::make()->email('test@example.com')->makeSuper();
        $user->save();
        $this->actingAs($user);

        $response = $this->callMiddleware(
            $this->makeRequest(),
            $this->makeHtmlResponse()
        );

        $content = $response->getContent();
        $this->assertStringContainsString('class="sst-toolbar"', $content);
        $this->assertStringContainsString('Control Panel', $content);
        $scriptUrl = asset('vendor/simple-statamic-toolbar/js/toolbar.js')
            .'?v='.md5_file(__DIR__.'/../../resources/js/toolbar.js');
        $this->assertStringContainsString('<script src="'.$scriptUrl.'" defer></script>', $content);
        $this->assertStringContainsString('</body>', $content);
    }

    public function test_does_not_inject_for_guests()
    {
        $response = $this->callMiddleware(
            $this->makeRequest(),
            $this->makeHtmlResponse()
        );

        $content = $response->getContent();
        $this->assertStringNotContainsString('class="sst-toolbar"', $content);
        $this->assertStringNotContainsString('toolbar.js', $content);
    }

    public function test_does_not_inject_for_cp_routes()
    {
        $user = User::make()->email('test@example.com')->makeSuper();
        $user->save();
        $this->actingAs($user);

        $cpRoute = config('statamic.cp.route', 'cp');

        $response = $this->callMiddleware(
            $this->makeRequest("/{$cpRoute}/dashboard"),
            $this->makeHtmlResponse()
        );

        $content = $response->getContent();
        $this->assertStringNotContainsString('class="sst-toolbar"', $content);
    }

    public function test_does_not_inject_for_non_html_responses()
    {
        $user = User::make()->email('test@example.com')->makeSuper();
        $user->save();
        $this->actingAs($user);

        $jsonResponse = new Response('{"ok":true}', 200, ['Content-Type' => 'application/json']);

        $response = $this->callMiddleware(
            $this->makeRequest(),
            $jsonResponse
        );

        $content = $response->getContent();
        $this->assertStringNotContainsString('class="sst-toolbar"', $content);
    }

    public function test_does_not_inject_for_non_200_responses()
    {
        $user = User::make()->email('test@example.com')->makeSuper();
        $user->save();
        $this->actingAs($user);

        $notFoundResponse = new Response('<html><body>Not found</body></html>', 404, ['Content-Type' => 'text/html']);

        $response = $this->callMiddleware(
            $this->makeRequest(),
            $notFoundResponse
        );

        $content = $response->getContent();
        $this->assertStringNotContainsString('class="sst-toolbar"', $content);
    }

    public function test_renders_the_published_antlers_override_with_toolbar_data(): void
    {
        $this->actingAs(User::make()->email('test@example.com')->makeSuper());
        $temporaryPath = sys_get_temp_dir().'/simple-statamic-toolbar-'.bin2hex(random_bytes(8));
        $viewPath = $temporaryPath.'/views/vendor/simple-statamic-toolbar/components/_toolbar.antlers.html';

        try {
            $this->app['files']->ensureDirectoryExists(dirname($viewPath));
            $this->app['files']->put($viewPath, '<aside data-toolbar="published-override">{{ toolbar_script_url }}</aside>');
            config(['view.paths' => [$temporaryPath.'/views']]);
            $this->app['view']->replaceNamespace('simple-statamic-toolbar', []);
            (new ServiceProvider($this->app))->bootAddon();

            $this->assertSame($viewPath, $this->app['view']->getFinder()->find('simple-statamic-toolbar::components._toolbar'));

            $response = $this->callMiddleware($this->makeRequest(), $this->makeHtmlResponse());
            $scriptUrl = asset('vendor/simple-statamic-toolbar/js/toolbar.js')
                .'?v='.md5_file(__DIR__.'/../../resources/js/toolbar.js');

            $this->assertStringContainsString('<aside data-toolbar="published-override">'.$scriptUrl.'</aside>', $response->getContent());
            $this->assertStringNotContainsString('class="sst-toolbar"', $response->getContent());
        } finally {
            $this->app['files']->deleteDirectory($temporaryPath);
        }
    }

    public function test_injects_toolbar_once_before_uppercase_and_mixed_case_closing_body_tags(): void
    {
        $this->actingAs(User::make()->email('test@example.com')->makeSuper());

        foreach (['</BODY>', '</BoDy>'] as $closingTag) {
            $response = $this->callMiddleware(
                $this->makeRequest(),
                new Response('<html><body>Hello'.$closingTag.'</html>', 200, ['Content-Type' => 'text/html'])
            );

            $content = $response->getContent();
            $this->assertSame(1, substr_count($content, 'class="sst-toolbar"'));
            $this->assertLessThan(stripos($content, '</body>'), strpos($content, 'class="sst-toolbar"'));
        }
    }

    public function test_control_panel_link_preserves_the_request_subdirectory(): void
    {
        $this->actingAs(User::make()->email('test@example.com')->makeSuper());
        $request = Request::create('https://example.com/subdirectory/about', 'GET', [], [], [], [
            'SCRIPT_NAME' => '/subdirectory/index.php',
            'SCRIPT_FILENAME' => '/var/www/html/subdirectory/index.php',
            'PHP_SELF' => '/subdirectory/index.php',
        ]);
        $this->app->instance('request', $request);
        $this->app['url']->setRequest($request);

        $this->assertSame('/subdirectory', $request->getBaseUrl());

        $response = $this->callMiddleware($request, $this->makeHtmlResponse());

        $this->assertStringContainsString('href="https://example.com/subdirectory/cp"', $response->getContent());
    }
}
