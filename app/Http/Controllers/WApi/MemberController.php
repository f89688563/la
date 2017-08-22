<?php
namespace App\Http\Controllers\WApi;
use App\Member;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\DB;
use App\Http\Logic\ApiLogic;

class MemberController extends WApiController
{
    
    public function __construct()
    {
        parent::__construct();
        $this->model = new Member();
        $this->table = 'member';
    }
    
    // 注册
    public function store()
    {
        $post = request()->input();
        $data = $this->model->register($post);
        $this->response($data);
    }
    
    // 获取门店相关信息
    public function stores()
    {
        $mobile_type= mobile_type();
        $logic = new ApiLogic();
        $stores = $logic->getStoreByCid($this->cid);
        $data = ['mobile_type'=>$mobile_type, 'stores'=>$stores['lists']];
        $this->response($data);
    }
    
    // 获取店员信息
    public function employs()
    {
        $sid = request('sid');
        $logic = new ApiLogic();
        $employs = $logic->getEmploysBySid($sid);
        $this->response($employs);
    }
    
    // 个人信息
    public function show($id)
    {
        $this->model->updateVInfo($id);
        $info = $this->model->select('username', 'nickname', 'phone', 'jf', 'avatar', 'vname', 'cid')->find($id)->toArray();
        !defined('CID') and define('CID', $info['cid']);
        $info['kf'] = get_config(CONFIG_KF)['val'];
        
        $data = [ 'err_code'=>0, 'info'=>$info];
        $this->response($data);
    }
    
    public function update($id)
    {
        $err_code = 0;
        $err_msg = '';
        $post = request()->input();
        switch (request()->method())
        {
            case 'PUT':
                break;
            case 'PATCH':
                $data = $this->model->updatePsw($id, $post);
                break;
        }
        $this->response($data);
    }
    
    // 用户反馈
    public function feedback()
    {
        $content = request('content');
        $uid = request('uid');
        $err_code = 0;
        $err_msg = '';
        
        if ($content && $uid)
        {
            $data = [
                'cid'           => CID,
                'uid'           => $uid,
                'content'       => $content,
                'create_time'   => time()
            ];
            $res = DB::table('feedback')->insert($data);
            if (!$res)
            {
                $err_code = -2;
                $err_msg = '操作异常';
            }
        }
        else
        {
            $err_code = -1;
            $err_msg = '参数错误';
        }
        $return = [ 'err_code'=>$err_code, 'err_msg'=>$err_msg ];
        $this->response($return);
    }
    
    // 公司信息
    public function about()
    {
        $info = get_config(CONFIG_ABOUT)['val'];
        $data = [ 'info'=>$info ];
        $this->response($data);
    }
    
    // 登陆
    public function login()
    {
        $user = request('user');
        $psw = request('psw');
        
        if (!$user || !$psw)
        {
            $this->response(['err_code'=>-1, 'err_msg'=>'参数错误']);
        }
        
        $res = $this->model->login($user, $psw);
        $this->response($res);
        
    }
    
}
