<?php namespace std\ui\nav\controllers\main\tree\nodeControl;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function select()
    {
        if ($route = $this->unxpackModel('route')) {
            $s = &$this->s('~|');

            $s['selected_route_id'] = $route->id;

            $this->c('<<:reload|');
            $this->c('<<<route:reload|');
        }
    }

    public function create()
    {
        if ($route = $this->unpackModel('route')) {
            $newRoute = $route->nested()->create(['nav_id' => $route->nav_id]);

            $s = &$this->s('~|');
            $s['selected_route_id'] = $newRoute->id;

            $this->e('std/nav/routes/create', [
                'nav_id'   => $route->nav_id,
                'route_id' => $route->id
            ])->trigger(['route' => $route]);
            $this->c('<<<route:reload|');
        }
    }

    public function duplicate()
    {
        if ($route = $this->unpackModel('route')) {
            $newRoute = \std\ui\nav\models\Route::create($route->toArray());

            $s = &$this->s('~|');
            $s['selected_route_id'] = $newRoute->id;

            $this->e('std/nav/routes/create', ['route_id' => $route->id])->trigger(['route' => $route]);
            $this->c('<<<route:reload|');
        }
    }

    public function delete()
    {
        if ($this->data('discarded')) {
            $this->c('\std\ui\dialogs~:close:deleteConfirm|std/ui/nav');
        } else {
            if ($route = $this->unxpackModel('route')) {
                if ($this->data('confirmed')) {
                    \ewma\Data\Tree::delete($route);

                    $this->c('\std\ui\dialogs~:close:deleteConfirm|std/ui/nav');

                    $this->e('std/nav/routes/delete', ['nav_id' => $route->nav_id])->trigger(['nav' => $route->nav]);
                } else {
                    $this->c('\std\ui\dialogs~:open:deleteConfirm|std/ui/nav', [
                        'path'          => '\std dialogs/confirm~:view',
                        'data'          => [
                            'confirm_call' => $this->_abs([':delete', ['route' => $this->data['route']]]),
                            'discard_call' => $this->_abs([':delete', ['route' => $route->data['route']]]),
                            'message'      => 'Удалить пункт <b>' . ($route->route ? $route->route : '...') . '</b>?'
                        ],
                        'pluginOptions' => [
                            'resizable' => 'false'
                        ]
                    ]);
                }
            }
        }
    }

    public function exchangeDialog()
    {
        if ($route = $this->unxpackModel('route')) {
            $this->c('\std\ui\dialogs~:open:exchange|std/ui/nav', [
                'default'             => [
                    'pluginOptions/width' => 500
                ],
                'path'                => '\std\data\exchange~:view|std/ui/nav',
                'data'                => [
                    'target_name' => '#' . $route->id,
                    'import_call' => $this->_abs('app/exchange:import', ['route' => pack_model($route)]),
                    'export_call' => $this->_abs('app/exchange:export', ['route' => pack_model($route)])
                ],
                'pluginOptions/title' => 'nav'
            ]);
        }
    }
}
