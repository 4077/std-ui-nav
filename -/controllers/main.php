<?php namespace std\ui\nav\controllers;

class Main extends \Controller
{
    private $nav;

    public function __create()
    {
        $this->nav = $this->c('>svc:getNav', ['instance' => $this->_instance()]);

        $this->s('|', [
            'selected_route_id' => false
        ]);
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $v->assign([
                       'TREE'  => $this->c('>tree:view|', [
                           'nav' => $this->nav
                       ]),
                       'ROUTE' => $this->c('>route:view|')
                   ]);

        $this->c('\std\ui\dialogs~:addContainer:std/ui/nav');

        $this->css();

        return $v;
    }
}
