<?php namespace std\ui\nav\controllers\main\tree;

class App extends \Controller
{
    public function queryBuilder()
    {
        $nav = $this->unpackModel('nav');

        return $nav->routes()->orderBy('position');
    }
}
