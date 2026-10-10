<?php

namespace Chrisvasey\SimpleStatamicToolbar\Http\Middleware;

use Chrisvasey\SimpleStatamicToolbar\Tags\ToolbarTheme;
use Closure;
use Illuminate\Http\Request;
use Statamic\Facades\Antlers;
use Statamic\Facades\Data;
use Symfony\Component\HttpFoundation\Response;

class InjectToolbar
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $this->shouldInject($request, $response)) {
            return $response;
        }

        $content = $response->getContent();

        if (stripos($content, '</body>') === false) {
            return $response;
        }

        $toolbar = $this->renderToolbar($request);

        $response->setContent(
            str_replace('</body>', $toolbar.'</body>', $content)
        );

        return $response;
    }

    protected function shouldInject(Request $request, Response $response): bool
    {
        if (! auth()->check()) {
            return false;
        }

        if ($response->getStatusCode() !== 200) {
            return false;
        }

        $contentType = $response->headers->get('Content-Type', '');
        if (! str_contains($contentType, 'text/html')) {
            return false;
        }

        $cpRoute = config('statamic.cp.route', 'cp');
        if ($request->is($cpRoute, $cpRoute.'/*')) {
            return false;
        }

        return true;
    }

    protected function renderToolbar(Request $request): string
    {
        $theme = ToolbarTheme::index();

        $entry = Data::findByRequestUrl($request->url());

        $data = array_merge($theme, [
            'edit_url' => $entry?->editUrl(),
            'cp_url' => '/'.config('statamic.cp.route', 'cp'),
            'toolbar_script_url' => asset('vendor/simple-statamic-toolbar/js/toolbar.js')
                .'?v='.md5_file(__DIR__.'/../../../resources/js/toolbar.js'),
        ]);

        $template = file_get_contents(__DIR__.'/../../../resources/views/components/_toolbar.antlers.html');

        return (string) Antlers::parse($template, $data);
    }
}
