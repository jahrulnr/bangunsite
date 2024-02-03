<?php

namespace App\Models;

use App\Libraries\Trait\SiteTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class Website extends Model
{
    use SiteTrait;

    protected $fillable = [
        'name',
        'domain',
        'path',
        'ssl',
        'config',
        'active',
    ];

    public static function create(array $attributes = [])
    {
        $website = static::query()->create($attributes);

        $createConfig = ! $website ?: self::createConfig($website->domain, $attributes);
        if (! $createConfig) {
            $website->delete();
            Session::flash('error', 'Fail to create website config');

            return false;
        }
        if ($website->active) {
            self::enableSite($website->domain);
        }

        return $website;
    }

    public static function getSite(string $domain): \Illuminate\Database\Eloquent\Builder
    {
        return self::where('domain', $domain);
    }
}
