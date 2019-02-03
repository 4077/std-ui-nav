<?php namespace std\ui\nav\controllers\main\route;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function updateName()
    {
        if ($route = $this->unxpackModel('route')) {
            $txt = \std\ui\Txt::value($this);

            $route->name = $txt->value;
            $route->save();

            $txt->response();

            $this->e('std/nav/routes/update/name')->trigger(['route' => $route]);
        }
    }

    public function updateRoute()
    {
        if ($route = $this->unxpackModel('route')) {
            $txt = \std\ui\Txt::value($this);

            $route->route = $txt->value;
            $route->save();

            $txt->response();
        }
    }

    public function updatePermissions()
    {
        if ($route = $this->unxpackModel('route')) {
            $txt = \std\ui\Txt::value($this);

            $route->permissions = $txt->value;
            $route->save();

            $txt->response('[' . $route->collected_permissions . '] ' . $route->permissions, $route->permissions);

            \std\ui\nav\Svc::updateParentsCollectedPermissions($route);
        }
    }

    public function updateClickablePermissions()
    {
        if ($route = $this->unxpackModel('route')) {
            $txt = \std\ui\Txt::value($this);

            $route->clickable_permissions = $txt->value;
            $route->save();

            $txt->response();
        }
    }

    public function toggleEnabled()
    {
        if ($route = $this->unxpackModel('route')) {
            $route->enabled = !$route->enabled;
            $route->save();

            \std\ui\nav\Svc::updateParentsCollectedPermissions($route);

            $this->c('<:reload|');

            $this->e('std/nav/routes/update/enabled')->trigger(['route' => $route]);
        }
    }

    public function toggleClickable()
    {
        if ($route = $this->unxpackModel('route')) {
            $route->clickable = !$route->clickable;
            $route->save();

            $this->c('<:reload|');

            $this->e('std/nav/routes/update/clickable')->trigger(['route' => $route]);
        }
    }
}
