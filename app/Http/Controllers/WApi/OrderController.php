<?php

namespace App\Http\Controllers\WApi;

use App\Order;
use App\Http\Logic\ApiLogic;
use Illuminate\Support\Facades\DB;
use App\Goods;

class OrderController extends WApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'order';
        $this->model = new Order();
    }
    
    public function index()
    {
        $uid = request('uid');
        $type = request('type');
        $where = [
            ['uid', '=', $uid]
        ];
        $type and $where[] = ['status', '=', $type];
        $lists = $this->model->with('_detail')->where($where)->orderBy('create_time', 'desc')->paginate(10)->toArray();
        $status = $this->model::STATUS;
        $data = [ 'err_code'=>0, 'lists'=>pre($lists['data']), 'status'=>$status ];
        $this->response($data);
    }
    
    public function show($id)
    {
        $data = $this->model->getInfo($id);
        $this->response($data);
    }
    
    // 生成订单
    public function store()
    {
        $uid = request('uid');
        $data = $this->model->addOrder($uid);
        $this->response($data);
    }
    
    // 完善订单信息
    public function update($id)
    {
        $post = request()->input();
        switch (request()->method())
        {
            case 'PUT':
                // 设置地址
                $data = $this->model->setAdd($id, $post);
                break;
            case 'PATCH':
                // 修改状态
                $status = request('status');
                $data = $this->model->updateStatus($id, $this->uid, $status);
                break;
        }
        $this->response($data);
    }
    
    // 加载额外信息
    public function extra()
    {
        $fare = get_config(CONFIG_FARE)['val'];
        $scale = get_config(CONFIG_SCALE)['val'];
        $scale = intval($scale[1]) / intval($scale[0]);
        
        // 店铺信息
        $logic = new ApiLogic();
        $stores = $logic->getStoreByCid(CID);
        
        // 加载地址信息
        $uid = request('uid');
        $address = DB::table('address')->where('uid', $uid)->orderBy('create_time', 'desc')->first();
        
        $data= [
            'err_code'      => 0,
            'fare'          => floatval($fare),
            'scale'         => $scale,
            'stores'        => $stores['lists'],
            'address'       => json_decode(json_encode($address), true)
        ];
        $this->response($data);
    }
    
}
;