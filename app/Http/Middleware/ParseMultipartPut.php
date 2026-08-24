<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ParseMultipartPut
{
    private function parseMultipartData(string $contentType, string $rawBody): array
    {
        preg_match('/boundary=(.*)$/', $contentType, $matches);
        $boundary = $matches[1] ?? '';

        if (!$boundary) {
            return [];
        }

        $blocks = explode('--' . $boundary, $rawBody);
        $result = [];

        foreach ($blocks as $block) {
            if (strpos($block, 'Content-Disposition: form-data;') === false) {
                continue;
            }

            preg_match('/name="(.+?)"/', $block, $nameMatch);
            $name = $nameMatch[1] ?? '';

            $contentStart = strpos($block, "\r\n\r\n");
            if ($contentStart === false) {
                continue;
            }

            $value = substr($block, $contentStart + 4);
            $value = rtrim($value, "\r\n--");

            if (preg_match('/^(.+)\[\d+\]$/', $name, $arrayMatch)) {
                $key = $arrayMatch[1];
                if (!isset($result[$key]) || !is_array($result[$key])) {
                    $result[$key] = [];
                }
                $result[$key][] = $value;
            } else {
                $result[$name] = $value;
            }
        }

        return $result;
    }

    public function handle(Request $request, Closure $next)
    {
        if (($request->isMethod('put') || $request->isMethod('patch'))
            && str_starts_with($request->headers->get('Content-Type'), 'multipart/form-data')
            && empty($request->request->all())
        ) {
            $rawBody = file_get_contents('php://input');
            if ($rawBody) {
                $parsed = $this->parseMultipartData(
                    $request->headers->get('Content-Type'),
                    $rawBody
                );
                $request->request->replace($parsed);
            }
        }

        return $next($request);
    }
}