<?php

namespace App;

class Goods extends Base
{
//     protected $table = 'goods';

    public function getRec()
    {
        $where = [
            [ 'type', '=', 1 ],
        ];
        $lists = $this->where($where)->orderBy('create_time', 'desc')->limit(8)->get()->toArray();
        $lists = pre($lists);
        return $lists;
    }
    
    public function getHot()
    {
        $where = [
            [ 'status', '=', 1 ],
        ];
        $lists = $this->where($where)->orderBy('praise', 'desc')->limit(8)->get()->toArray();
        $lists = pre($lists);
        return $lists;
    }
}
