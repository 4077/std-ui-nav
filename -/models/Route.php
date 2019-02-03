<?php namespace std\ui\nav\models;

class Route extends \Model
{
    protected $table = 'std_ui_navs_routes';

    public function nav()
    {
        return $this->belongsTo(Nav::class);
    }

    public function nested()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
}

class RouteObserver
{
    public function creating($model)
    {
        $position = Route::max('position') + 10;

        $model->position = $position;
    }
}

Route::observe(new RouteObserver);
