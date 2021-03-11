<?php
// 应用公共文件
/**
 * 统一失败编码及语言文字返回
 * @param $code       [编码]
 * @param $msg        [文字提示，为空时读语言包]
 * @param array $data /数组内data参数
 * @return array
 */
function return_fail($code, $msg = '', $data = array()): array{
    $arr         = array();
    $arr['code'] = $code;
    if (empty($msg)) {
        $msg = \think\facade\Lang::get($code);
    }
    $arr['msg']  = $msg;
    $arr['time'] = time();
    $arr['data'] = $data;
    return $arr;
}

/**
 * 统一成功返回
 * @param string $msg
 * @param array $data
 * @return array
 */
function return_success($msg = '', $data = array()): array{
    $arr         = array();
    $arr['code'] = 0;
    $arr['msg']  = $msg;
    $arr['time'] = time();
    $arr['data'] = $data;
    return $arr;
}

/**
 * 统一日志写入
 * @param $title        /功能或操作名称
 * @param Exception $ex /异常信息(可为null)
 */
function setErrLog($title, $ex = null){
    if ($ex != null) {
        trace($title.'异常', 'error');
        trace('行数'.$ex->getLine(), 'error');
        trace($ex->getMessage(), 'error');
    } else {
        trace($title, 'error');
    }
}