<?php

namespace App;

use Illuminate\Support\Facades\DB;

class Punch extends Base
{
    public function info($uid, $time=0)
    {
        return $this->getDay($uid, $time);
    }
    
    public function punch($uid, $cid)
    {
        if (!UID) define('UID', $uid);
        if (!CID) define('CID', $cid);
        
        $day = $this->getDay($uid);
        $err_code = 0;
        $err_msg = '已打卡';
        if ($day){
            $err_code = -1;
            $err_msg = '今天已经打过卡了';
        } else {
            $r = 0;
            $day = $this->getDay($uid, time()-86400);
            
            // 读取配置
            $config = get_config(CONFIG_PUNCH)['val'];
            $jf = intval($config[0]);
            $max = intval($config[1]);
            $scale = intval($config[2]);
            
            DB::beginTransaction();
                
            // 当打卡已经到达翻倍天数时重置
            if ($day == $max)
            {
                $this->where('uid', $uid)->update(['status'=>0]);
                $day = 0;
            }
            // 当本次打卡到达翻倍天数
            if ($day + 1 >= $max)
            {
                $jf *= $scale;
            }
                
            if ($day)
            {
                $where = [ ['uid', '=', $uid], ['status', '=', 1] ];
                $data = array(
                    'day'=>$day + 1,
                    'punch_time'=>time()
                );
                $r = $this->where($where)->update($data);
            } else {
                $day = 0;
                $r = 0;
                $this->where('uid', $uid)->update(['status'=>0]);
                $data = array(
                    'cid'   => $cid,
                    'uid'   =>$uid,
                    'day'   =>1,
                    'status'=>1,
                    'punch_time' => time()
                );
                $r = $this->insert($data);
            }
                
            // 增加积分
            $rM = DB::table('member')->where('id', $uid)->increment('jf', $jf);
            
            // 增加记录
            $title = '打卡送积分';
            $data = array(
                'cid'=>$cid,
                'uid'=>$uid,
                'jf'=>$jf,
                'title'=>$title,
                'create_time'=>time(),
            );
            $rR = DB::table('record')->insert($data);
            
            if ($r && $rM && $rR)
            {
                DB::commit();
            } else {
                $err_code = -2;
                $err_msg = '操作异常';
                DB::rollBack();
            }
//                 jf_msg($uid, session('openid'), $jf, $title);
        }
        return [ 'err_code'=>$err_code, 'err_msg'=>$err_msg ];
    }
    
    private function getDay($uid, $time=0)
    {
        $time = dv($time, time());
        $border = time_border($time);
        
        $where = [
            ['uid', '=', $uid],
            ['status', '=', 1],
        ];
        $day = $this->where($where)->whereBetween('punch_time', [$border['min'], $border['max']])->select('day')->first();
        return $day ? $day->toArray()['day'] : 0;
    }
}
