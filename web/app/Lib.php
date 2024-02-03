<?php

function setIcon(string $icon): string
{
    return '<i class="'.$icon.'"></i>';
}

function validatePath(string $path): bool
{
    return strpbrk($path, '\\?%*:|"<>') === false;
}

function bytesReadable(int $bytes)
{
    $symbols = ['B', 'K', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y'];
    $exp = floor(log($bytes) / log(1024));

    return sprintf('%.1f'.$symbols[$exp], ($bytes / pow(1024, floor($exp))));
}

function saveReferer($url = null)
{
    session('ref', $_SERVER['HTTP_REFERER'] ?? ($url ?: route('home')));
}

function getReferer()
{
    return session('ref', route('home'));
}

function getDomain(string $url)
{
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        $parse = parse_url($url);
        if (str_starts_with($_SERVER['HTTP_HOST'], $parse['host']) > 0) {
            return $_SERVER['HTTP_HOST'];
        } else {
            return $parse['host'];
        }
    }
}
