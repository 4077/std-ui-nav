<?php namespace std\ui\nav\schemas;

class Nav extends \Schema
{
    public $table = 'std_ui_navs';

    public function blueprint()
    {
        return function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('instance')->default('');
            $table->string('name')->default('');
        };
    }
}
