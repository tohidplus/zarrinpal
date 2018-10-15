<?php
/**
 * Created by PhpStorm.
 * User: TD-PLUS
 * Date: 10/13/2018
 * Time: 10:59 PM
 */

namespace Tohidplus\Zarrinpal\Facades;


use Illuminate\Support\Facades\Facade;

class Zarrinpal extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'zarrinpal';
    }
}
