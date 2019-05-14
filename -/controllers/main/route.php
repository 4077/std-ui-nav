<?php namespace std\ui\nav\controllers\main;

class Route extends \Controller
{
    private $route;

    public function __create()
    {
        $this->route = \std\ui\nav\models\Route::find($this->s('~:selected_route_id|'));
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        if ($route = $this->route) {
            $routeXPack = xpack_model($route);

            $v->assign('table', [
                'ID'                        => $route->id,
                'NAME_TXT'                  => $this->c('\std\ui txt:view', [
                    'path'              => '>xhr:updateName|',
                    'data'              => [
                        'route' => $routeXPack
                    ],
                    'class'             => 'name_txt',
                    'fitInputToClosest' => '.cell',
                    'content'           => $route->name
                ]),
                'ROUTE_TXT'                 => $this->c('\std\ui txt:view', [
                    'path'              => '>xhr:updateRoute|',
                    'data'              => [
                        'route' => $routeXPack
                    ],
                    'class'             => 'route_txt',
                    'fitInputToClosest' => '.cell',
                    'content'           => $route->route
                ]),
                'PERMISSIONS_TXT'           => $this->c('\std\ui txt:view', [
                    'path'              => '>xhr:updatePermissions|',
                    'data'              => [
                        'route' => $routeXPack
                    ],
                    'class'             => 'route_txt',
                    'fitInputToClosest' => '.cell',
                    'content'           => '[' . $route->collected_permissions . '] ' . $route->permissions,
                    'contentOnInit'     => $route->permissions
                ]),
                'CLICKABLE_PERMISSIONS_TXT' => $this->c('\std\ui txt:view', [
                    'path'              => '>xhr:updateClickablePermissions|',
                    'data'              => [
                        'route' => $routeXPack
                    ],
                    'class'             => 'route_txt',
                    'fitInputToClosest' => '.cell',
                    'content'           => $route->clickable_permissions
                ]),
                'ENABLED_SWITCHER'          => $this->c('\std\ui\switcher~:view', [
                    'path'    => '>xhr:toggleEnabled|',
                    'data'    => [
                        'route' => $routeXPack
                    ],
                    'value'   => $route->enabled,
                    'class'   => 'enabled_switcher',
                    'buttons' => [
                        [
                            'label' => 'Да',
                            'value' => true,
                            'class' => 'yes'
                        ],
                        [
                            'label' => 'Нет',
                            'value' => false,
                            'class' => 'no'
                        ]
                    ]
                ]),
                'CLICKABLE_SWITCHER'        => $this->c('\std\ui\switcher~:view', [
                    'path'    => '>xhr:toggleClickable|',
                    'data'    => [
                        'route' => $routeXPack
                    ],
                    'value'   => $route->clickable,
                    'class'   => 'clickable_switcher',
                    'buttons' => [
                        [
                            'label' => 'Да',
                            'value' => true,
                            'class' => 'yes'
                        ],
                        [
                            'label' => 'Нет',
                            'value' => false,
                            'class' => 'no'
                        ]
                    ]
                ]),
            ]);

            $this->e('std/nav/routes/delete|' . $this->_nodeInstance(), ['route_id' => $route->id])->rebind(':reload|');
        }

        $this->css();

        return $v;
    }
}
