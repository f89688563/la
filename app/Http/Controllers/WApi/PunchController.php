<?php

namespace App\Http\Controllers\WApi;

use App\Punch;

class PunchController extends WApiController
{
    
    public function __construct()
    {
        parent::__construct();
        $this->model = new Punch();
        $this->table = 'punch';
    }
    
    // 打卡
    public function store()
    {
        $data = $this->model->punch($this->uid, $this->cid);
        $this->response($data);
    }
    
    public function show($id)
    {
        $pre = $day = $this->model->info($this->uid);
        !$pre and $pre = $this->model->info($this->uid, time() - 86400);
        $config = get_config(CONFIG_PUNCH)['val'];
        
        $data = ['day'=>$day, 'pre'=>$pre, 'config'=>$config];
        $this->response($data);
    }
    
}
