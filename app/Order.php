<?php

namespace App;

use Illuminate\Support\Facades\DB;
use App\Http\Logic\ApiLogic;

class Order extends Base
{
    const DELAY_TIME = 900;			      // 支付时间15分钟
    const CONFIRM_TIME = 604800;		  // 自动确认时间7天
    const STATUS = array(
        'wait'=>'待付款',
        'send'=>'待发货',
        'sending'=>'已发货',
        'refund'=>'已退款',
        'cancel'=>'已取消',
        'ok'=>'已完成',
    );
    
    public $incrementing = false;
    
    public function _detail()
    {
        return $this->hasMany('App\OrderDetail', 'oid', 'id');
    }
    
    public function getInfo($id)
    {
        $err_code = 0;
        $err_msg = '';
        $info = $this->find($id);
        if ($info)
        {
            $detail = $info->_detail()->where('oid', $id)->get()->toArray();
            $detail = pre($detail);
            $info = $info->toArray();
            $info['_detail'] = $detail;
            $info['consignee'] = unserialize($info['consignee']);
        }
        else
        {
            $err_code = -1;
            $err_msg = '订单不存在';
        }
        $return = [ 'err_code'=>$err_code, 'err_msg'=>$err_msg, 'info'=>$info ];
        return $return;
    }
    
    // 更新订单状态
    public function updateStatus($id, $uid, $status)
    {
        $where = [
            ['id', '=', $id],
            ['uid', '=', $uid]
        ];
        $info = $this->where($where)->select('id', 'status')->first();
        $err_code = 0;
        $err_msg = '';
        if ($info)
        {
            $info = $info->toArray();
            if ($this->validStatus($info, $status) === 1)
            {
                $res = $this->where($where)->update(['status'=>$status]);
                if (!$res)
                {
                    $err_code = -3;
                    $err_msg = '操作异常';
                }
            } else {
                $err_code = -2;
                $err_msg = '非法操作';
            }
        } else {
            $err_code = -1;
            $err_msg = '订单不存在';
        }
        
        return ['err_code'=>$err_code, 'err_msg'=>$err_msg];
    }
    
    // 验证状态改变是否合法
    private function validStatus($info, $status)
    {
        $currentStatus = $info['status'];
        $valid = 0;
        switch ($currentStatus)
        {
            case 'wait':
                if ($status === 'send' || $status === 'cancel')
                {
                    $valid = 1;
                }
                break;
            case 'send':
                if ($status === 'sending')
                {
                    $valid = 1;
                }
                break;
            case 'sending':
                if ($status === 'ok')
                {
                    $valid = 1;
                }
                break;
        }
        return $valid;
    }
    
    // 设置地址
    public function setAdd($id, $post)
    {
        extract($post);
        $err_code = 0;
        $err_msg = '';
        if (isset($aid) && $aid)
        {
            $info = Address::select('username', 'phone', 'province','city','district','detail', 'on_time')->find($aid)->concat();
//             $info = DB::table('Address')->select('*')->find($aid);
            if (!$info)
            {
                $err_code = -1;
                $err_msg = '地址不存在';
            }
            $data = [
                'fare'      => get_config(CONFIG_FARE)['val'],
                'consignee' => serialize($info)
            ];
        }
        if (isset($sid) && $sid)
        {
            $api = new ApiLogic();
            $info = $api->getStoreById($sid)['info'];
            if (!$info)
            {
                $err_code = -2;
                $err_msg = '门店不存在';
            }
            $temp = [
                'sid'       => $sid,
                'name'      => $info['name'],
                'address'   => $info['province'].$info['city'].$info['area'].$info['address'],
            ];
            $data = [
                'fare'      => 0,
                'consignee' => serialize($temp)
            ];
        }
        
        if ($data)
        {
            $data['status'] = 'wait';
            $data['delay_time'] = time() + self::DELAY_TIME;
            $res = $this->where('id', $id)->update($data);
            
            if ($res)
            {
                // 锁定兑换券
                $res and $this->lockGift($id);
            }
            else
            {
                $err_code = -3;
                $err_msg = '操作异常';
            }
        }
        
        $return = [ 'err_code'=>$err_code, 'err_msg'=>$err_msg ];
        return $return;
    }
    
    /**
     * 锁定兑换券
     * @param string $id
     * @author : panfeng <89688563@qq.com>
     * time : 2017-5-10下午7:27:17
     */
    private function lockGift($id)
    {
        $model = new OrderDetail();
        $grid = $model->where('oid', $id)->select('grid')->get()->toArray();
        if ($grid)
        {
            $temp = '';
            foreach ($grid as $v)
            {
                $v['grid'] and $temp .= ','.$v['grid'];
            }
            
            if ($temp)
            {
                $where = [ ['id', 'in', $temp] ];
                DB::table('gift_record')->where($where)->update(['status'=>1]);
            }
        }
    }
    
    // 创建订单
    public function addOrder($uid)
    {
        $modelCar = DB::table('car as c');
        $total = $count = $jf = $rebate = 0;
        $_detail = [];
        $err_code = 0;
        $err_msg = '';
        
        $carInfo = $modelCar->join('goods as g', 'c.gid', 'g.id')
                            ->where('c.uid', $uid)
                            ->select('g.price', 'g.corver', 'g.name', 'g.jf', 'g.rebate', 'c.count', 'c.gid', 'c.grid', 'c.sids', 'c.standard')
                            ->get()->toJson();
        $carInfo = json_decode($carInfo, true);
        $id = $this->generateID();
                            
        // 开启事物
        DB::beginTransaction();
        if ($carInfo)
        {
            foreach ($carInfo as &$v)
            {
                $c = $v['count'];
                $count += $c;
                
                if ($v['grid'])
                {
                    $rG = DB::table('gift_record')->where('id', $v['grid'])->update(['status'=>1]);
                    if (!$rG)
                    {
                        $err_code = -1;
                        $err_msg = '兑换券已被使用过';
                        DB::rollBack();
                    }
                }
                else
                {
                    $total += $c * $v['price'];
                    $jf += $c * $v['jf'];
                    $rebate += $c * $v['rebate'];
                }
                
                $v['oid'] = $id;
                $v['create_time'] = time();
            }
            unset($v);
            
            $data = [
                'id'            => $id,
                'cid'           => CID,
                'uid'           => $uid,
                'total'         => $total,
                'jf'            => $jf,
                'rebate'        => $rebate,
                'count'         => $count,
                'status'        => '',
                'create_time'   => time(),
            ];
            
            $rO = $this->insert($data);
            $orderDetailModel = $this->find($id);
            $rOD = $orderDetailModel->_detail()->insert($carInfo);
            
            if ($rO && $rOD)
            {
                DB::table('car')->where('uid', $uid)->delete();
                DB::commit();
            }
            else
            {
                DB::rollBack();
                $err_code = -3;
                $err_msg = '下单失败';
            }
        }
        else
        {
            $err_code = -2;
            $err_msg = '购物车为空';
        }
        $return = [ 'err_code'=>$err_code, 'err_msg'=>$err_msg, 'id'=>$id ];
        return $return;
    }
    
    private function generateID(){
        return 'JFDD'.time().rand(1000, 9999);
    }
}
