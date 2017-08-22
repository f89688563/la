<?php
namespace App\Http\Controllers\WApi;
use App\Goods;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BaseController;

class GoodsController extends WApiController
{
    
    public function __construct()
    {
        parent::__construct();
        $this->model = new Goods();
        $this->table = 'goods';
    }
    
	// 商品信息
    public function info()
    {
        $id = request('id');
		$info = $this->model->info($id);
		
		$standard = [];
		// 加载所有类型
		$temp = DB::table('standard')->select('id', 'pid', 'name')->where('cid', CID)->get();//->toJson();
		$temp = json_decode($temp->toJson(), true);
		if($temp)
		{
		    foreach($temp as $v)
		    {
		        $standard[$v['id']] = $v;
		    }
		}
		
        $data = [ 'info' => $info, 'standard'=>$standard ];
        $this->response($data);
    }
    
	// 获取额外信息
    public function extra()
    {
        $uid = request('uid');
        $gid = request('gid');
		
		// 查看是否点赞
		$wPraise = [
		    ['gid', '=', $gid],
		    ['uid', '=', $uid]
		];
		$praised = DB::table('praise')->where($wPraise)->first() ? 1 : 0;
		
		// 获取购物车信息
		$carCount= DB::table('car')->where('uid', $uid)->sum('count');
		
        $data = [
            'praised'   => $praised,
            'carCount'  => (int)$carCount
        ];
        $this->response($data);
    }
	
	// 点赞
	public function praise()
	{
	    $uid = request('uid');
	    $gid = request('gid');
		$res = $this->model->praise($gid, $uid);
		$this->response($res);
	}
    
	// 获取商品列表
    public function lists()
    {
        $extend = [
            'where' => [ ['cid', '=', CID], ['status', '=', 1] ]
//             'paginate' => 10
        ];
        $lists = $this->getLists('goods', $extend);
        $data = ['lists'=>pre($lists)];
        $this->response($data);
    }
    
	// 获取热门商品
    public function hot()
    {
        $lists = $this->model->getHot();
        $data = ['lists' => $lists];
        $this->response($data);
    }
    
	// 获取推荐商品
    public function rec()
    {
        $lists = $this->model->getRec();
        $data = ['lists' => $lists];
        $this->response($data);
    }
}
