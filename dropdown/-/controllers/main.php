<?php namespace std\ui\nav\dropdown\controllers;

class Main extends \Controller
{
    private $nav;

    private $rootRoute;

    private $paddingLeft;

    private $maxDepth;

    public function __create()
    {
        if ($this->nav = \std\ui\nav\models\Nav::where('instance', $this->_instance())->first()) {
            $this->rootRoute = $this->getRootRoute();

            $this->paddingLeft = $this->data('padding_left') or
            $this->paddingLeft = 0;

            $this->maxDepth = $this->data('max_depth');
        } else {
            $this->lock();
        }
    }

    private function getRootRoute()
    {
        $builder = $this->nav->routes();

        if ($rootRoute = $this->data('root_route')) {
            $builder->where('route', $rootRoute);
        } else {
            $builder->where('parent_id', 0);
        }

        return $builder->first();
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    private $hasVisibleItems = false;

    public function view()
    {
        $v = $this->v('|');

        $v->assign([
                       'UL'    => $this->treeView(),
                       'CLASS' => $this->data('class')
                   ]);

        $this->css('|', [
            'collapseWidth' => $this->data('collapse_width') ? $this->data['collapse_width'] : 1
        ]);

        $toggleButtonSelector = "." . $this->_nodeId() . "__toggle_button[instance='" . $this->_instance() . "']";

        $this->widget(':|', [
            'collapseWidth'        => $this->data('collapse_width'),
            'sticky'               => $this->data('sticky') ? (array)$this->data['sticky'] : false,
            'toggleButtonSelector' => $toggleButtonSelector,
            'instance'             => $this->_instance()
        ]);

        if ($this->hasVisibleItems) {
            $this->c('\plugins\flexMenu~:bind', [
                'selector' => $this->_selector('|') . ' > ul',
                'options'  => [
                    'linkText'    => '...',
                    'linkTitle'   => '',
                    'showOnHover' => true
                ]
            ]);
        }

        if ($this->data('sticky')) {
            $this->c('\plugins\sticky~:load');
        }

        return $v;
    }

    private function treeView()
    {
        $tree = \ewma\Data\Tree::get($this->nav->routes()->orderBy('position'));

        return $this->treeViewRecursion($tree, $this->rootRoute->id);
    }

    private $level = 0;

    private function treeViewRecursion(\ewma\Data\Tree $tree, $id)
    {
        $subnodes = $tree->getSubnodes($id);

        if ($subnodes) {
            $v = $this->v('>ul');

            $v->assign('ul', [
                'LEVEL_CLASS' => $this->level == 0 ? 'first_level' : 'not_first_level',
                'LEVEL'       => $this->level
            ]);

            if (!$this->maxDepth || $this->level < $this->maxDepth) {
                $this->level++;

                foreach ($subnodes as $subnode) {
                    if ($subnode->enabled) {
                        $allowed = true;

                        if ($subnode->permissions || $subnode->collected_permissions) {
                            $allowed = false;

                            $permissions = [];

                            merge($permissions, l2a($subnode->permissions));
                            merge($permissions, l2a($subnode->collected_permissions));
                            diff($permissions, '');

                            foreach ($permissions as $permission) {
                                if ($this->a($permission)) {
                                    $allowed = true;

                                    break;
                                }
                            }
                        }

                        if ($allowed) {
                            $tmp = $tree;

                            if (preg_match('/:(?<id>\d+)/', $subnode->route, $match)) {
                                $nestedId = $match['id'];

                                if ($otherNavRoute = \std\ui\nav\models\Route::find($match['id'])) {
                                    $tree = \ewma\Data\Tree::get($otherNavRoute->nav->routes()->orderBy('position'));
                                }
                            } else {
                                $nestedId = $subnode->id;
                            }

                            $v->assign('ul/li', [
                                'CONTROL' => $this->getControl($subnode),
                                'LEVEL'   => $this->level,
                                'UL'      => $this->treeViewRecursion($tree, $nestedId)
                            ]);

                            $this->hasVisibleItems = true;

                            $tree = $tmp;
                        }
                    }
                }

                $this->level--;
            }

            return $v;
        }
    }

    private function getControl($node)
    {
        $clickable = $node->clickable;

        $allowed = true;
        if ($node->clickable_permissions) {
            $allowed = false;

            foreach (l2a($node->clickable_permissions) as $permission) {
                if ($this->a($permission)) {
                    $allowed = true;

                    break;
                }
            }
        }

        $clickable = $clickable && $allowed;

        if ($clickable) {
            $attrs = [
                'href'  => abs_url($node->route),
                'class' => 'node clickable ' . ($node->route == $this->data('selected_route') ? 'selected' : '')
            ];

            if ($this->level > 1) {
                $attrs['style'] = 'padding-left: ' . $this->level * $this->paddingLeft . 'px';
            }

            return $this->c('\std\ui tag:view:a', [
                'attrs'   => $attrs,
                'content' => $node->name
            ]);
        } else {
            return $this->c('\std\ui tag:view', [
                'attrs'   => [
                    'class' => 'node not_clickable ' . ($node->route == $this->data('selected_route') ? 'selected' : '')
                ],
                'content' => $node->name
            ]);
        }
    }
}
