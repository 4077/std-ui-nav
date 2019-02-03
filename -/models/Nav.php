<?php namespace std\ui\nav\models;

class Nav extends \Model
{
    protected $table = 'std_ui_navs';

    public function routes()
    {
        return $this->hasMany(Route::class);
    }
}
