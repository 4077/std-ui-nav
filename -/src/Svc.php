<?php namespace std\ui\nav;

class Svc
{
    public static function updateParentsCollectedPermissions(\std\ui\nav\models\Route $route)
    {
        static::$collected = [];

        if ($parent = $route->parent) {
            static::updateParentsCollectedPermissionsRecursion($parent);
        }
    }

    private static $collected = [];

    private static function updateParentsCollectedPermissionsRecursion(\std\ui\nav\models\Route $route)
    {
        $nested = $route->nested;

        foreach ($nested as $nestedRoute) {
            merge(static::$collected, l2a($nestedRoute->permissions));
            merge(static::$collected, l2a($nestedRoute->collected_permissions));
        }

        diff(static::$collected, '');

        sort(static::$collected);

        $route->collected_permissions = a2l(static::$collected);
        $route->save();

        if ($parent = $route->parent) {
            static::updateParentsCollectedPermissionsRecursion($parent);
        }
    }
}