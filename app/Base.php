<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Base extends Model
{
    public function __construct()
    {
        $className = get_class($this);
        $table = substr($className, strripos($className, '\\') + 1);
        $this->table = lcfirst($table);
    }
    // create时不对created_at和updated_at做操作
    public $timestamps = false;
}
