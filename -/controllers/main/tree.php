<?php namespace std\ui\nav\controllers\main;

class Tree extends \Controller
{
    private $nav;

    public function __create()
    {
        $this->nav = $this->data('nav') or
        $this->nav = $this->c('~svc:getNav', ['instance' => $this->_instance()]);
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');
        $s = $this->s('~|');

        $nav = $this->nav;

        $rootNode = $this->c('~svc:getRootRoute', ['instance' => $this->_instance()]);

        $treeInstance = $this->_nodeInstance();

        $v->assign([
                       'CONTENT' => $this->c('\std\ui\tree~:view|' . $treeInstance, [
                           'default'          => [
                               'query_builder' => $this->_abs('>app:queryBuilder|', [
                                   'nav' => pack_model($nav)
                               ])
                           ],
                           'node_control'     => $this->_abs('>nodeControl:view|', [
                               'root_route_id' => $rootNode->id,
                               'nav'           => pack_model($nav),
                               'route'         => '%model'
                           ]),
                           'root_node_id'     => $rootNode->id,
                           'selected_node_id' => $s['selected_route_id'],
                           'movable'          => true,
                           'sortable'         => true
                       ])
                   ]);

        $this->css(':\css\std~');

        $eventData = [
            'root_route_id' => $rootNode->id,
            'nav'           => pack_model($nav)
        ];

        $this->e('std/nav/routes/update/name|' . $treeInstance)->rebind('>nodeControl:reload|', $eventData);
        $this->e('std/nav/routes/update/enabled|' . $treeInstance)->rebind('>nodeControl:reload|', $eventData);
        $this->e('std/nav/routes/update/clickable|' . $treeInstance)->rebind('>nodeControl:reload|', $eventData);

        $this->e('std/nav/routes/create|' . $treeInstance, ['nav_id' => $nav->id])->rebind(':reload|');
        $this->e('std/nav/routes/delete|' . $treeInstance, ['nav_id' => $nav->id])->rebind(':reload|');
        $this->e('std/nav/routes/import|' . $treeInstance, ['nav_id' => $nav->id])->rebind(':reload|');

        return $v;
    }
}
