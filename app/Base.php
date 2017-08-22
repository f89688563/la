<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Base extends Model
{
    public $error = '';
    
    public function __construct()
    {
        if (!$this->table)
        {
            $this->table = $this->initTableName();
        }
    }
    // create时不对created_at和updated_at做操作
    public $timestamps = false;
    
    // 初始化表名
    private function initTableName()
    {
        $className = get_class($this);
        $table = substr($className, strripos($className, '\\') + 1);
        $table = str_replace('Model', '', $table);
        $temp = preg_split('/(?=[A-Z])/', $table);
        $arr = array_map(function($item){
            return lcfirst($item);
        }, $temp);
        
        return trim(implode('_', $arr), '_');
    }
    
    public function getError()
    {
        return $this->error;
    }
    
}
