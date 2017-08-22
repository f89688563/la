<?php

namespace App\Http\Controllers\WApi;
use App\Address;

class AddressController extends WApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'address';
        $this->model = new Address();
    }
    
    public function index()
    {
        $uid = request('uid');
        $extend = [
            'where'     => [ ['uid', '=', $uid] ],
            'orderBy'   => [ 'create_time', 'desc' ]
        ];
        $lists = $this->getLists($this->table, $extend);
        $data = ['err_code'=>0, 'lists'=>$lists];
        $this->response($data);
    }
    
    public function update($id)
    {
        $data = request()->input();
        $city = explode(' ', $data['city']);
        $data['province'] = $city[0];
        $data['city'] = $city[1];
        $data['district'] = $city[2];
        $res = $this->model->where('id', $id)->update($data);
        if ($res)
        {
            $return = [ 'err_code'=>0 ];
        }
        else
        {
            $return = [ 'err_code'=>-1, 'err_msg'=>'操作失败' ];
        }
        $this->response($return);
    }
    
    public function store()
    {
        $data = request()->input();
        $city = explode(' ', $data['city']);
        $data['province'] = $city[0];
        $data['city'] = $city[1];
        $data['district'] = $city[2];
        $data['cid'] = CID;
        $data['create_time'] = time();
        $res = $this->model->insert($data);
        if ($res)
        {
            $return = [ 'err_code'=>0 ];
        }
        else
        {
            $return = [ 'err_code'=>-1, 'err_msg'=>'操作失败' ];
        }
        $this->response($return);
    }
    
}
