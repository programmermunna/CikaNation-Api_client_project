<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{

    private $permissions = [];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $routelist = Route::getRoutes();

        $permissions = [];

        foreach ($routelist as $route) {

            $prefix = 'api/' . config('app.api_version');

            if ($route->getPrefix() !== $prefix) continue;

            $index       = $this->getIndex($route);
            $moduleName  = $this->getModuleName($route);

            $routeName   = $this->getRouteName($route);

            $displayName = str_replace('_',$routeName, ' ');


            $this->permissions[$moduleName][] = [
                'name'          => $routeName,
                'module_name'   => $moduleName,
                'display_name'  => $displayName
            ];


        }


        $this->insertPermission();


    }



    private function insertPermission()
    {
        foreach($this->permissions as $moduleName => $values){
            $parent = Permission::create([
                'name' => $moduleName,
                'module_name' => $moduleName,
                'display_name' => $moduleName,
            ]);

            foreach($values as $child){

                if(Permission::where('name',$child['name'])->count()) continue;

                Permission::create([
                    'parent_id' => $parent->id,
                    'name' => $child['name'],
                    'display_name' => $child['display_name'],
                    'module_name'  => $child['module_name'],
                ]);
            }
        }
    }



    private function getRouteName($route)
    {
        $index    = $this->getIndex($route);
        $method   = explode('@',$route->getActionName())[1];
        $routeArr = explode('.', $route->getName());
        $name     = $routeArr[$index];
        $ability  = config('abilities')[$method] ?? $method;

        if($ability == $name){
            return $name;
        }
        return $ability . "_" . $name;
    }



    private function getModuleName($route)
    {
        $index = $this->getIndex($route);
        return explode('.', $route->getName())[$index];
    }



    private function getIndex($route)
    {
        $routes =  explode('.', $route->getName());
        $length = count($routes);

        return [
            '1' => 0,
            '2' => 0,
            '3' => 1,
            '4' => 2,
            '5' => 3,
            '6' => 4,
            '7' => 5
        ][$length];
    }
}