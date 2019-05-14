<?php namespace std\ui\nav\controllers\main\tree;

class NodeControl extends \Controller
{
    private $route;

    private $viewInstance;

    public function __create()
    {
        $this->route = $this->data['route'];

        $this->viewInstance = $this->route->id;
    }

    public function reload()
    {
        $this->jquery('|' . $this->viewInstance)->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|' . $this->viewInstance);

        $isRootRoute = $this->data['root_route_id'] == $this->route->id;

        $route = $this->route;
        $routeXPack = xpack_model($route);

        $nav = $route->nav;
        $name = $isRootRoute ? $nav->instance . ' :' . $route->id : ($route->name ? $route->name : '...');

        $v->assign([
                       'ROOT_CLASS'            => $isRootRoute ? 'root' : '',
                       'ENABLED_CLASS'         => $route->enabled ? 'enabled' : '',
                       'CLICKABLE_CLASS'       => $route->clickable ? 'clickable' : '',
                       'NAME'                  => $name,
                       'TOGGLE_ENABLED_BUTTON' => $this->c('\std\ui button:view', [
                           'visible' => !$isRootRoute,
                           'path'    => '~route/xhr:toggleEnabled|',
                           'data'    => [
                               'route' => $routeXPack
                           ],
                           'class'   => 'button toggle_enabled ' . ($route->enabled ? 'enabled' : ''),
                           'title'   => $route->enabled ? 'Выключить' : 'Включить',
                           'content' => '<div class="icon"></div>'
                       ]),
                       'CREATE_BUTTON'         => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:create|',
                           'data'    => [
                               'route' => $routeXPack
                           ],
                           'class'   => 'button create',
                           'title'   => 'Создать',
                           'content' => '<div class="icon"></div>'
                       ]),
                       'EXCHANGE_BUTTON'       => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:exchangeDialog|',
                           'data'    => [
                               'route' => $routeXPack
                           ],
                           'class'   => 'button exchange',
                           'title'   => 'Импорт/экспорт',
                           'content' => '<div class="icon"></div>'
                       ]),
                       'DUPLICATE_BUTTON'      => $this->c('\std\ui button:view', [
                           'visible' => !$isRootRoute,
                           'path'    => '>xhr:duplicate|',
                           'data'    => [
                               'route' => $routeXPack
                           ],
                           'class'   => 'button duplicate',
                           'title'   => 'Создать копию',
                           'content' => '<div class="icon"></div>'
                       ]),
                       'DELETE_BUTTON'         => $this->c('\std\ui button:view', [
                           'visible' => !$isRootRoute,
                           'path'    => '>xhr:delete|',
                           'data'    => [
                               'route' => $routeXPack
                           ],
                           'class'   => 'button delete',
                           'title'   => 'Удалить',
                           'content' => '<div class="icon"></div>'
                       ])
                   ]);

        $this->css(':\js\jquery\ui icons');

        if (!$isRootRoute) {
            $this->c('\std\ui button:bind', [
                'selector' => $this->_selector('|' . $this->viewInstance),
                'path'     => '>xhr:select|',
                'data'     => [
                    'route' => $routeXPack
                ]
            ]);
        }

        return $v;
    }
}
