<?php namespace std\ui\nav\schemas;

class Route extends \Schema
{
    public $table = 'std_ui_navs_routes';

    public function blueprint()
    {
        return function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('parent_id')->default(0);
            $table->integer('nav_id')->default(0);
            $table->integer('position')->default(0);
            $table->boolean('enabled')->default(false);
            $table->boolean('clickable')->default(false);
            $table->text('route')->nullable();
            $table->string('name')->default('');
            $table->text('permissions');
            $table->text('clickable_permissions');
            $table->text('collected_permissions');
            $table->text('collected_clickable_permissions');
        };
    }
}
