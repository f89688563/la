<?php

namespace App;

use Illuminate\Support\Facades\DB;

class Gift extends Base
{
    public function open($uid, $cid)
    {
        !defined('CID') and define('CID', $cid);
        $pid = get_config(CONFIG_GIFT)['val'];
        
        $err_msg = '';
        $err_code = 0;
        $info = [];
        if ($pid) {
            if ($this->has($uid, $pid))
            {
                $err_code = -2;
                $err_msg= '您今天已经抽过奖了';
            } else {
                // 获取中奖的数组
                $gift = $this->award($cid, $pid);
                // 添加抽奖纪录
                $data = array(
                    'cid'=>$cid,
                    'uid'=>$uid,
                    'pid'=>$pid,
                    'gid'=>$gift['gid'],
                    'create_time'=>time()
                );
                
                DB::beginTransaction();
                $rGr = DB::table('gift_record')->insert($data);
                
                $id = $gift['id'];
                $rG = 1;
                $id and $rG = $this->where('id', $id)->decrement('count');
                
                if ($rG && $rGr)
                {
                    $gid = $gift['gid'];
                    if ($gid > 0)
                    {
                        $info = DB::table('goods')->select('id', 'name')->find($gid);
                        $info = json_decode(json_encode($info), true);
//                         $info and $info = $info->toArray();
                    }
                    DB::commit();
                } else {
                    DB::rollBack();
                }
            }
        } else {
            $err_code = -1;
            $err_msg = '商家未设置抽奖';
        }
        return ['err_code'=>$err_code, 'err_msg'=>$err_msg, 'info'=>$info];
    }
    
    /**
     * 抽奖
     * @param string $type
     * @return Ambigous <string>
     * @author : panfeng <89688563@qq.com>
     * time : 2017-4-8下午2:32:32
     */
    private function award($cid, $pid)
    {
        $where = [
            ['cid', '=', $cid],
            ['pid', '=', $pid],
            ['count', '>', 0],
        ];
        $total = $this->where($where)->sum('rate');
        $temp = $this->where($where)->select('id', 'gid', 'rate')->get();
        
        $gift[0] = array('id'=>'0', 'gid'=>'0', 'rate'=>''.(100-$total));
        if ($temp)
        {
            $temp = $temp->toArray();
            foreach ($temp as $v)
            {
                $gift[$v['id']] = $v;
            }
        }
        
        $temp = [];
        foreach ($gift as $v){
            $temp[$v['id']] = $v['rate'];
        }
        $r = $this->getRand($temp);
        return $gift[$r];
    }
    
    /**
     * 是否抽过奖了
     * @param string $type
     * @return boolean
     * @author : panfeng <89688563@qq.com>
     * time : 2017-4-8下午2:32:12
     */
    private function has($uid, $pid)
    {
        $border = time_border();
        $where = [
            ['uid', '=', $uid],
            ['pid', '=', $pid]
        ];
        $between = [$border['min'], $border['max']];
        return !empty(DB::table('gift_record')->where($where)->whereBetween('create_time', $between)->first());
    }
    
    
    /**
     * 随机抽奖
     * @param array $proArr
     * @return Ambigous <string, unknown>
     * @author : panfeng <89688563@qq.com>
     * time : 2017-4-8下午2:31:28
     */
    private function getRand($proArr)
    {
        $result = '';
        
        //概率数组的总概率精度
        $proSum = array_sum($proArr);
        
        //概率数组循环
        foreach ($proArr as $key => $proCur)
        {
            $randNum = mt_rand(1, $proSum);
            if ($randNum <= $proCur) {
                $result = $key;
                break;
            } else {
                $proSum -= $proCur;
            }
        }
        unset ($proArr);
        
        return $result;
    }
}
