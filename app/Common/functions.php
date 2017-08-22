<?php
use App\ConfigModel;
use App\Http\Logic\DateLogic;

if (!function_exists('img_pre'))
{
    function img_pre()
    {
        return config('common.QINIU_CONFIG.IMG_URL');
    }
}

if (!function_exists('mobile_type'))
{
    
    function mobile_type()
    {
        $type = array(
            '苹果',
            '三星',
            '华为',
            'vivo',
            'oppo',
            '金立',
            '魅族',
            '小米',
            '其他'
        );
        return $type;
    }
}

if (!function_exists('birth'))
{
    /**
     * 注册时设置生日
     * @param unknown $birth
     * @return string
     * @author : panfeng <89688563@qq.com>
     * time : 2017-5-10下午1:47:14
     */
    function birth($birthday, $birthtype)
    {
        $birth = '';
        $time = strtotime($birthday);
        if ($birthtype == 1)
        {
            $dateLogic = new DateLogic($time);
            $time = $dateLogic->getTime();
        }
        $m = date('m', $time);
        $d = date('d', $time);
        $birth = "$m-$d";
        return $birth;
    }
}

if (!function_exists('first_charter'))
{
    // 获取首字母
    function first_charter($str)
    {
        if (empty($str))
        {
            return '';
        }
        $fchar = ord($str{0});
        if ($fchar >= ord('A') && $fchar <= ord('z')) return strtoupper($str{0});
        $s1 = iconv('UTF-8', 'gb2312', $str);
        $s2 = iconv('gb2312', 'UTF-8', $s1);
        $s = $s2 == $str ? $s1 : $str;
        $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
        if ($asc >= -20319 && $asc <= -20284) return 'A';
        if ($asc >= -20283 && $asc <= -19776) return 'B';
        if ($asc >= -19775 && $asc <= -19219) return 'C';
        if ($asc >= -19218 && $asc <= -18711) return 'D';
        if ($asc >= -18710 && $asc <= -18527) return 'E';
        if ($asc >= -18526 && $asc <= -18240) return 'F';
        if ($asc >= -18239 && $asc <= -17923) return 'G';
        if ($asc >= -17922 && $asc <= -17418) return 'H';
        if ($asc >= -17417 && $asc <= -16475) return 'J';
        if ($asc >= -16474 && $asc <= -16213) return 'K';
        if ($asc >= -16212 && $asc <= -15641) return 'L';
        if ($asc >= -15640 && $asc <= -15166) return 'M';
        if ($asc >= -15165 && $asc <= -14923) return 'N';
        if ($asc >= -14922 && $asc <= -14915) return 'O';
        if ($asc >= -14914 && $asc <= -14631) return 'P';
        if ($asc >= -14630 && $asc <= -14150) return 'Q';
        if ($asc >= -14149 && $asc <= -14091) return 'R';
        if ($asc >= -14090 && $asc <= -13319) return 'S';
        if ($asc >= -13318 && $asc <= -12839) return 'T';
        if ($asc >= -12838 && $asc <= -12557) return 'W';
        if ($asc >= -12556 && $asc <= -11848) return 'X';
        if ($asc >= -11847 && $asc <= -11056) return 'Y';
        if ($asc >= -11055 && $asc <= -10247) return 'Z';
        return '';
    }
}

// 获取系统配置
if (!function_exists('get_config'))
{
    function get_config($name='')
    {
        $cache_name = 'config_'.CID;
        
        $config = cache($cache_name);
        
        if (!$config){
            $model = new ConfigModel();
            $config = $model->getConfig();
            cache([$cache_name => $config], 7200);
        }
        
        return $name ? $config[$name] : $config;
    }
}

if (!function_exists('time_border'))
{
    function time_border($time=0)
    {
        $time = dv($time, time());
        
        $min = mktime(0, 0, 0, date('m', $time), date('d', $time), date('Y', $time));
        $max = $min + 24 * 60 * 60 - 1;
        return array('min'=>$min, 'max'=>$max);
    }
}

/**
 * 图片添加前缀
 * @param array|string $data
 * @param string $field
 * @param string $pre
 * @return string
 * @author : panfeng <89688563@qq.com>
 * time : 2017-4-17下午10:32:12
 */
if (!function_exists('pre'))
{
    function pre($data, $field='corver', $pre='')
    {
        if (!$data) return '';
        if (!$field) return $data;
        
        $pre = dv($pre, img_pre());
        $fields = explode(',', $field);
        if (is_string($data))
        {
            if (strpos($data, ',') !== false)
            {
                $data = explode(',', $data);
                foreach ($data as &$v)
                {
                    $v = pre($v, $field, $pre);
                }
                unset($v);
                return $data;
            }
            else 
            {
                return $pre . $data;
            }
        } elseif(is_array($data))
        {
            foreach ($fields as $v)
            {
                if (isset($data[$v]))
                {
                    $data[$v] = pre($data[$v], $v, $pre);
                }
                else
                {
                    foreach ($data as &$val)
                    {
                        is_array($val) and $val = pre($val, $v, $pre);
                    }
                    unset($val);
                }
            }
            return $data;
        }
    }
}

if (!function_exists('dv'))
{
    function dv($value, $default='')
    {
        return $value ? $value : $default;
    }
}