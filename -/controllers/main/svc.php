<?php namespace std\ui\nav\controllers\main;

class Svc extends \Controller
{
    public $singleton = true;

    private $navsByInstances = [];

    public function getNav()
    {
        $instance = $this->data('instance');

        if (!isset($this->navsByInstances[$instance])) {
            if (!$nav = \std\ui\nav\models\Nav::where('instance', $instance)->first()) {
                $nav = \std\ui\nav\models\Nav::create(['instance' => $instance]);
            }

            $this->navsByInstances[$instance] = $nav;
        }

        return $this->navsByInstances[$instance];
    }

    private $rootRoutesByInstances = [];

    public function getRootRoute()
    {
        $instance = $this->data('instance');

        if (!isset($this->rootRoutesByInstances[$instance])) {
            $nav = $this->getNav();

            if (!$rootRoute = $nav->routes()->where('parent_id', 0)->first()) {
                $rootRoute = $nav->routes()->create(['parent_id' => 0]);
            }

            $this->rootRoutesByInstances[$instance] = $rootRoute;
        }

        return $this->rootRoutesByInstances[$instance];
    }
}
