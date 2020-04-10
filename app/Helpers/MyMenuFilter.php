<?php
namespace App\Helpers;

use JeroenNoten\LaravelAdminLte\Menu\Builder;
use JeroenNoten\LaravelAdminLte\Menu\Filters\FilterInterface;
use \Auth;

class MyMenuFilter implements FilterInterface
{
    public function transform($item, Builder $builder)
    {
        if (isset($item['permission']) && ! Auth::user()->hasRole($item['permission'])) {
            return false;
        }

        return $item;
    }
}