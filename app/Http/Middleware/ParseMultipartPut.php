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
            $value = rtrim($value, "\r\n");

            $this->assignValue($result, $name, $value);
        }

        return $result;
    }

    /**
     * Assign a form field value into the nested result array using PHP-style
     * bracket notation, also supporting dotted notation like "details[0].productId".
     */
    private function assignValue(array &$result, string $name, $value): void
    {
        if ($name === '') {
            return;
        }

        $parts = $this->parseName($name);

        $ref = &$result;
        $last = count($parts) - 1;

        foreach ($parts as $i => $part) {
            if ($i === $last) {
                if ($part === '') {
                    $ref[] = $value;
                } else {
                    $ref[$part] = $value;
                }
                break;
            }

            if ($part === '') {
                $ref[] = [];
                end($ref);
                $ref = &$ref[key($ref)];
            } else {
                if (!isset($ref[$part]) || !is_array($ref[$part])) {
                    $ref[$part] = [];
                }
                $ref = &$ref[$part];
            }
        }

        unset($ref);
    }

    /**
     * Split a form field name into its nesting segments, supporting both
     * bracket notation (details[0][productId]) and dotted notation
     * (details[0].productId).
     */
    private function parseName(string $name): array
    {
        if (!str_contains($name, '[')) {
            return $name === '' ? [] : explode('.', $name);
        }

        $parts = [];
        $length = strlen($name);
        $bracketPos = strpos($name, '[');
        $parts[] = substr($name, 0, $bracketPos);

        for ($i = $bracketPos; $i < $length;) {
            if ($name[$i] === '[') {
                $end = strpos($name, ']', $i + 1);

                if ($end === false) {
                    $parts[] = substr($name, $i + 1);
                    break;
                }

                $parts[] = substr($name, $i + 1, $end - $i - 1);
                $i = $end + 1;
            } elseif ($name[$i] === '.') {
                $start = $i + 1;
                $end = $start;

                while ($end < $length && $name[$end] !== '[' && $name[$end] !== '.') {
                    $end++;
                }

                $parts[] = substr($name, $start, $end - $start);
                $i = $end;
            } else {
                $i++;
            }
        }

        return $parts;
    }

    /**
     * Mirror Laravel's ConvertEmptyStringsToNull middleware, which runs before
     * this one and therefore does not affect the manually parsed body.
     */
    private function nullifyEmptyStrings(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->nullifyEmptyStrings($value);
            } elseif ($value === '') {
                $data[$key] = null;
            }
        }

        return $data;
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
                $request->request->replace($this->nullifyEmptyStrings($parsed));
            }
        }

        return $next($request);
    }
}