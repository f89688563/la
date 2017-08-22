<?php

namespace App;

class ConfigModel extends Base
{
    public function getConfig($name='')
    {
        $where = [
            ['cid', '=', CID]
        ];
        $name and $where[] = ['name', '=', $name];
        $config = $this->where($where)->select('name','id','type','val')->get()->toJson();
        $config = json_decode($config, true);
        
        if ($config){
            foreach ($config as $v){
                switch ($v['type']){
                    case 'exp':
                        $v['val'] = explode(',', $v['val']);
                        break;
                    case 'value':
                    default:
                        break;
                }
                $temp[$v['name']] = $v;
            }
            $config = $temp;
        }
        
        return $name ? $config[$name] : $config;
    }
}
