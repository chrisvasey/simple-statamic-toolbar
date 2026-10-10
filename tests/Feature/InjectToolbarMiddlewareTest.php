<?php

namespace Chrisvasey\SimpleStatamicToolbar\Tests\Feature;

use Chrisvasey\SimpleStatamicToolbar\Http\Middleware\InjectToolbar;
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
}
