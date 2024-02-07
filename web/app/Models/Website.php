<?php

namespace App\Models;

use App\Libraries\Nginx;
use App\Libraries\Trait\SiteTrait;
use Illuminate\Database\Eloquent\Collection;
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
            Nginx::restart();
        }

        return $website;
    }

    public function update(array $attributes = [], array $options = [])
    {
        if ($this instanceof Website
        && $this->exists) {
            $pathExists = Website::where('path', $attributes['path'])
                ->where('id', '!=', $this->id)->exists;
            $domainExists = Website::getSite($attributes['domain'])
                ->where('id', '!=', $this->id)->exists;

            if ($pathExists || $domainExists) {
                return $pathExists
                    ? 'Path already used by another site.'
                    : 'Domain already used by another site.';
            }

            if ($this->path != $attributes['path']) {
                Nginx::moveRoot($this, $attributes);
            }
            if ($this->domain != $attributes['domain']) {
                Nginx::moveDomain($this, $attributes);
            }
        } elseif ($this instanceof Collection) {
            // sorry, multiple update website configuration is not good idea
            return false;
        }

        return $this->fill($attributes)->save($options);
    }

    public static function getSite(string $domain): \Illuminate\Database\Eloquent\Builder
    {
        return self::where('domain', $domain);
    }
}
