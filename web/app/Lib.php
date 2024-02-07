<?php

function setIcon(string $icon): string
{
    return '<i class="'.$icon.'"></i>';
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
