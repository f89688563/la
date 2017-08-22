<?php
namespace App\Http\Controllers\WApi;

class AdController extends WApiController
{
    public function index()
    {
        $extend = [
            'where' => [
                ['cid', '=', CID],
                ['status', '=', 1]
            ],
            'paginate'  => 0
        ];
        $data = $this->getLists('ad', $extend);
        $data = pre($data, 'corver', img_pre());
        $this->response(['lists'=>$data]);
    }
}
