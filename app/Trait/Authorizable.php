<?php

namespace App\Trait;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Spatie\Permission\Models\Permission;

trait Authorizable
{

    private $abilities = [];

    /**
     * Override of callAction to perform the authorization before.
     *
     * @param $method
     * @param $parameters
     * @return mixed
     */

    public function callAction($method, $parameters)
    {
        if ($ability = $this->getAbility($method)) {
            $this->authorize($ability);
        }

        return parent::callAction($method, $parameters);
    }

    public function getAbility($method)
    {
        $routeName   = explode('.', Request::route()->getName());

        $action      = Arr::get($this->getAbilities(), $method) ?? $method;

        $actionIndex = count($routeName) == 1 ? 0 : (count($routeName) == 2 ? 0 : 1);

        $index =  [
            '1' => 0,
            '2' => 0,
            '3' => 1,
            '4' => 2,
            '5' => 3,
            '6' => 4,
            '7' => 5
        ][count($routeName)];


        if($action == $routeName[$index]){
            return $action;
        }

        return $action ? $action . '_' . @$routeName[$index] : null;
    }

    private function getAbilities()
    {
        return config('abilities');
    }

    public function setAbilities($abilities)
    {
        $this->abilities = $abilities;
    }
}
