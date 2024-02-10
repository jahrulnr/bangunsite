<?php

function setIcon(string $icon): string
{
    return '<i class="'.$icon.'"></i>';
}

function setIconByType($path): string
{
    $basename = basename($path);
    if (! str_contains($basename, '.') && $basename != 'artisan') {
        return setIcon('far fa-file fa-sm');
    }

    $ext = explode('.', $basename);
    $ext = count($ext) > 2 ? $ext[count($ext) - 2].'.'.end($ext) : end($ext);

    if (preg_match('/(php)/i', $ext)) {
        return setIcon('fab fa-php text-info fa-sm');
    }
    if (preg_match('/(htm)/i', $ext)) {
        return setIcon('fab fa-html5 text-danger fa-sm');
    }
    if (preg_match('/(json|lock|xml|phar)/i', $ext)) {
        return setIcon('fas fa-file-code fa-sm');
    }
    if (preg_match('/(js)/i', $ext)) {
        return setIcon('fab fa-js text-warning fa-sm');
    }
    if (preg_match('/(css)/i', $ext)) {
        return setIcon('fab fa-css3 text-info fa-sm');
    }
    if (preg_match('/(sql)/i', $ext)) {
        return setIcon('fas fa-database text-warning fa-sm');
    }
    if (preg_match('/(sh|bash|batch)/i', $ext)) {
        return setIcon('fas fa-terminal text-danger fa-sm');
    }

    if (preg_match('/(git)/i', $ext)) {
        return setIcon('fas fa-code-branch text-danger fa-sm');
    }
    if (preg_match('/(artisan)/i', $ext)) {
        return setIcon('fab fa-laravel text-danger fa-sm');
    }
    if (preg_match('/log/i', $ext)) {
        return setIcon('fas fa-list-alt text-warning fa-sm');
    }
    if (preg_match('/(zip|rar|gz|bz|tar|7z)/i', $ext)) {
        return setIcon('fas fa-archive fa-sm');
    }
    if (preg_match('/(conf|env|example|htaccess)/i', $ext)) {
        return setIcon('fas fa-cog text-info fa-sm');
    }
    if (preg_match('/(bk|backup)/i', $ext)) {
        return setIcon('fas fa-undo-alt fa-sm');
    }
    if (preg_match('/(md)/i', $ext)) {
        return setIcon('fas fa-info fa-sm');
    }

    if (preg_match('/(mp4|3gp|mkv|webp|avi)/i', $ext)) {
        return setIcon('fas fa-file-video fa-sm');
    }
    if (preg_match('/(mp3|aac|mid|wav)/i', $ext)) {
        return setIcon('fas fa-file-audio fa-sm');
    }
    if (preg_match('/(jpg|jpeg|png|ico|gif|bmp)/i', $ext)) {
        return setIcon('fas fa-file-image fa-sm');
    }
    if (preg_match('/(pdf)/i', $ext)) {
        return setIcon('fas fa-file-pdf fa-sm');
    }
    if (preg_match('/(xls)/i', $ext)) {
        return setIcon('fas fa-file-excel fa-sm');
    }
    if (preg_match('/(doc)/i', $ext)) {
        return setIcon('fas fa-file-word fa-sm');
    }
    if (preg_match('/(ppt)/i', $ext)) {
        return setIcon('fas fa-file-powerpoint fa-sm');
    }

    return setIcon('far fa-file fa-sm');
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
