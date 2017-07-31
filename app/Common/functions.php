<?php
if (!function_exists('img_pre'))
{
    function img_pre()
    {
        return config('common.QINIU_CONFIG.IMG_URL');
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
        if (!$field) return '';
        
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
                        $val = pre($val, $v, $pre);
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