<?php

namespace App;

use Illuminate\Support\Facades\DB;

class Member extends Base
{
    protected $guarded = [];
//     protected $fillable = ['sid', 'cid'];
    
    // 更新会员信息
    public function updateVInfo($id)
    {
        $info = $this->find($id)->toArray();
        
        $vips = DB::table('vip')->where('cid', $info['cid'])->orderBy('jf', 'asc')->get()->toJson();
        $vips = json_decode($vips, true);
        
        if ($vips){
            $name = $vips[0]['name'];
            $rights = $vips[0]['rights'];
            foreach ($vips as $v){
                if ($info['jf'] >= $v['jf']){
                    $name = $v['name'];
                    $rights = $v['rights'];
                    break;
                }
            }
            $data = array(
                'vname'=>$name,
                'rights'=>$rights
            );
            $this->where('id', $id)->update($data);
        }
    }
    
    public function register($data)
    {
        $err_code = 0;
        $err_msg = '';
        
        $data['rand'] = $rand = mt_rand(100000, 999999);
        $data['psw'] = $this->encrypt(dv($data['psw'], 123456), $rand);
        isset($data['birthday']) and $data['birth'] = birth($data['birthday'], $data['birthtype']);
        isset($data['birthday']) and $data['birthday'] = strtotime($data['birthday']);
        $data['create_time'] = time();
        
        $name = isset($data['username']) ? $data['username'] : ( isset($data['nickname']) ? $data['nickname'] : '' );
        $data['index'] = first_charter($name);
        
        $cid = $data['cid'];
        !defined('CID') and define('CID', $cid);
        
        $config = get_config(CONFIG_REG)['val'];
        $jf = dv($config, 0);
        $data['jf'] = $jf;
        
        $id = $this->insertGetId($data);
        if ($id)
        {
            $title = '注册送积分';
            $recordData = [
                'uid'=>$id,
                'cid'=>$cid,
                'title'=>$title,
                'jf'=>$jf,
                'create_time'=>time()
            ];
            DB::table('record')->insert($recordData);
        }
        
        if (!$id)
        {
            $err_code = -1;
            $err_msg = '注册失败';
        }
        
        return ['err_code'=>$err_code, 'err_msg'=>$err_msg, 'id'=>$id];
    }
    
	public function login($user, $psw='')
	{
	    $err = 0;
	    $msg = '';
	    $info = $this->where('openid', $user)->first();//->toArray();
	    $info and $info = $info->toArray();
	    
	    if (!$info)
	    {
            $info = $this->where('id', $user)->orwhere('phone', $user)->first();//->toArray();
    	    $info and $info = $info->toArray();
            if ($info)
            {
                if ($this->encrypt($psw, $info['rand']) !== $info['psw'])
                {
                    $err = -2;
                    $msg = '账号密码错误';
                }
            }
            else
            {
                $err = -1;
                $msg = '用户不存在';
            }
	    }
	    
	    $return = array(
	        'err_code'=>$err,
	        'err_msg'=>dv($msg, $this->getError())
	    );
	    
	    if (isset($info['psw'])) unset($info['psw']);
	    if (isset($info['rand'])) unset($info['rand']);
	    if (isset($info['create_time'])) unset($info['create_time']);
	    
	    $err == 0 and $return['info'] = $info;
	    return $return;
	}
	
	public function updatePsw($id, $data)
	{
	    extract($data);
	    $err_code = 0;
	    $err_msg = '';
	    if (isset($opsw) && isset($npsw) && isset($cpsw))
	    {
	        if ($npsw !== $cpsw)
	        {
	            $err_code = -2;
	            $err_msg = '两次密码不一致';
	        }
	        else
	        {
        	    $info = $this->select('psw', 'rand')->find($id)->toArray();
        	    if ($info)
        	    {
//         	        dd($info, $npsw);
        	        if ($info['psw'] === $this->encrypt($opsw, $info['rand']))
        	        {
            	        $npsw = $this->encrypt($npsw, $info['rand']);
        	            $res = $this->where('id', $id)->update(['psw'=>$npsw]);
        	            if (!$res)
        	            {
            	            $err_code = -5;
            	            $err_msg = '操作异常';
        	            }
        	        }
        	        else
        	        {
        	            $err_code = -4;
        	            $err_msg = '旧密码错误';
        	        }
        	    }
        	    else
        	    {
    	            $err_code = -3;
    	            $err_msg = '用户不存在';
        	    }
	        }
	    }
	    else
	    {
	        $err_code = -1;
	        $err_msg = '参数错误';
	    }
	    $return = [ 'err_code'=>$err_code, 'err_msg'=>$err_msg ];
	    return $return;
	}
	
	private function encrypt($psw, $rand=''){
	    return md5(md5($psw).$rand);
	}
}
