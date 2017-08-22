<?php

namespace App;

class Car extends Base
{
    //
    public function toCar($data)
    {
        extract($data);
        
        $where = [
            ['uid', '=', $uid],
            ['sids', '=', $sids]
        ];
        if ($this->where($where)->first())
        {
            $res = $this->where($where)->increment('count', $count);
        }
        else
        {
            $res = $this->insert($data);
        }
        $return = [ 'err_code' => 0, 'err_msg' => '' ];
        
        if (!$res)
        {
            $return = [ 'err_code' => -1, 'err_msg' => '操作异常' ];
        }
        return $return;
    }
}
