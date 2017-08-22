<?php

namespace App\Http\Controllers\WApi;

use App\Gift;

class GiftController extends WApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new Gift();
        $this->table = 'gift';
    }
    
    public function red()
    {
        $data = $this->model->open($this->uid, $this->cid);
        $this->response($data);
    }
    
    public function record()
    {
        $extend = [
            'where'     => [
                ['gr.uid', '=', $this->uid],
                ['gr.gid', '>', 0]
            ],
            'select'    => [ 'gr.gid', 'gr.status', 'gr.create_time', 'g.name' ],
            'join'      => ['goods as g', 'g.id', 'gr.gid'],
            'orderBy'   => ['gr.create_time', 'desc']
        ];
        $lists = $this->getLists('gift_record as gr', $extend);
        $data = ['lists'=>$lists];
        $this->response($data);
    }
    
}
