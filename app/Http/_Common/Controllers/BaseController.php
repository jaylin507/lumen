<?php
/**
 * Created by PhpStorm.
 * User: songjialin
 * Date: 2018/8/30
 * Time: 下午5:02
 */

namespace Common\Controllers;


use Laravel\Lumen\Routing\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BaseController extends Controller
{


    /**
     * @param $msg
     * @param array $data
     * @param string $statusCode
     * @return array
     * 失败返回
     */
    public function error($msg, $data = [], $statusCode = '500')
    {
        $msg = $msg ?: '服务器错误，请稍后重试';
        return $this->success($data, $msg, $statusCode);
    }

    /**
     * @param array $data
     * @param string $msg
     * @param string $statusCode
     * @return array
     * 成功返回
     */
    public function success($data = [], $msg = 'OK', $statusCode = '200')
    {
        //文件下载
        if ($data instanceof BinaryFileResponse) {
            return $data;
        }

        $return = [
            'status_code' => $statusCode,
            'message' => $msg,
            'data' => $data,
        ];
        $this->clearNull($return['data']);
        return $return;
    }

    /**
     * @param string $data
     * 递归使为null或false的值改为空字符串
     */
    public function clearNull(&$data = '')
    {
        $data = json_decode(json_encode($data), true);
        if ($data === null || $data === false) {
            $data = '';
        }
        if (is_array($data) && !empty($data)) {
            foreach ($data as &$v) {
                if ($v === null || $v === false) {
                    $v = '';
                } elseif (is_array($v)) {
                    $this->clearNull($v);
                }
                trim($v);
            }
        }
    }


}