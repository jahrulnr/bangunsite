<?php

namespace App\Http\Middleware;

use Closure;

class CleanResponse
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if ($request->format() == 'html' && $response->getContent() != false) {
            $response = $this->render($response);
        }

        return $response;
    }

    public function render($response)
    {
        $content = $response->getContent();

        $parse = explode("\n", $content);
        $lines = [];
        $result = '';

        foreach ($parse as $line) {
            $trim = trim(preg_replace("/(\t+|\s+)/", ' ', $line));
            $find = str_starts_with($trim, '<link')
                || str_starts_with($trim, '<script src=');
            if (! in_array($trim, $lines)
                && $find) {
                $lines[] = $trim;
            } elseif ($find) {
                continue;
            }

            $result .= $line."\n";
        }

        $response->setContent($result);
        $response->headers->remove('Content-Length');

        return $response;
    }
}
