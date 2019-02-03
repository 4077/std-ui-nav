<?php namespace std\ui\nav\controllers\app;

class Exchange extends \Controller
{
    private $exportOutput = [];

    public function export()
    {
        $route = $this->unpackModel('route') or
        $route = \std\ui\nav\models\Route::find($this->data('route_id'));

        if ($route) {
            $tree = \ewma\Data\Tree::get(\std\ui\nav\models\Route::orderBy('position'));

            $this->exportOutput['route_id'] = $route->id;
            $this->exportOutput['routes'] = $tree->getFlattenData($route->id);

//            $this->exportRecursion($tree, $route);

            return $this->exportOutput;
        }
    }

    private function exportRecursion(\ewma\Data\Tree $tree, $route)
    {
        // ...

        $subRoutes = $tree->getSubnodes($route->id);
        foreach ($subRoutes as $subRoute) {
            $this->exportRecursion($tree, $subRoute);
        }
    }

    public function import()
    {
        $targetRoute = $this->unpackModel('route') or
        $targetRoute = \std\ui\nav\models\Route::find($this->data('route_id'));

        $importData = $this->data('data');
        $sourceRouteId = $importData['route_id'];

        $this->importRecursion($targetRoute, $importData, $sourceRouteId, $this->data('skip_first_level'));

        $this->e('std/nav/routes/import')->trigger();
    }

    private function importRecursion($targetRoute, $importData, $routeId, $skipFirstLevel = false)
    {
        $newRouteData = $importData['routes']['nodes_by_id'][$routeId];

        $newRouteData['nav_id'] = $targetRoute->nav_id;

        if ($skipFirstLevel) {
            $newRoute = $targetRoute;
        } else {
            $newRoute = $targetRoute->nested()->create($newRouteData);
        }

        if (!empty($importData['routes']['ids_by_parent'][$routeId])) {
            foreach ($importData['routes']['ids_by_parent'][$routeId] as $sourceRouteId) {
                $this->importRecursion($newRoute, $importData, $sourceRouteId);
            }
        }
    }
}
