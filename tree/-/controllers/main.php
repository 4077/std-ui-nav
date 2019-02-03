<?php namespace std\ui\nav\tree\controllers;

class Main extends \Controller
{
    private $nav;

    private $rootRoute;

    private $paddingLeft;

    private $maxDepth;

    private $baseRoute;

    public function __create()
    {
        if ($this->nav = \std\ui\nav\models\Nav::where('instance', $this->_instance())->first()) {
            $this->rootRoute = $this->getRootRoute();

            $this->paddingLeft = $this->data('padding_left') or
            $this->paddingLeft = 15;

            $this->maxDepth = $this->data('max_depth');
            $this->baseRoute = $this->data('base_route');
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

        return $v;
    }

    /**
     * @var \ewma\Data\Tree
     */
    private $tree;

    private function treeView()
    {
        $this->tree = \ewma\Data\Tree::get($this->nav->routes()->orderBy('position'));

        return $this->treeViewRecursion($this->rootRoute->id);
    }

    private $level = 0;

    private function treeViewRecursion($id)
    {
        $subnodes = $this->tree->getSubnodes($id);

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
                            $v->assign('ul/li', [
                                'CONTROL' => $this->getControl($subnode),
                                'LEVEL'   => $this->level,
                                'UL'      => $this->treeViewRecursion($subnode->id)
                            ]);
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

        $targetRoute = path($this->baseRoute, $node->route);

        if ($clickable) {
            $attrs = [
                'href'  => abs_url($targetRoute),
                'class' => 'node clickable ' . ($targetRoute == $this->data('selected_route') ? 'selected' : '')
            ];

            if ($this->level > 1) {
                $attrs['style'] = 'padding-left: ' . $this->level * $this->paddingLeft . 'px';
            }

            return $this->c('\std\ui tag:view:a', [
                'attrs'   => $attrs,
                'content' => $node->name
            ]);
        } else {
            $attrs['class'] = 'node not_clickable ' . ($targetRoute == $this->data('selected_route') ? 'selected' : '');

            if ($this->level > 1) {
                $attrs['style'] = 'padding-left: ' . $this->level * $this->paddingLeft . 'px';
            }

            return $this->c('\std\ui tag:view', [
                'attrs'   => $attrs,
                'content' => $node->name
            ]);
        }
    }
}
