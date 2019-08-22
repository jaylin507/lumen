<?php
/**
 * Created by PhpStorm.
 * User: jaylin
 * Date: 2018/8/4
 * Time: 17:05
 */

namespace App\Libraries;

use App\Exceptions\TokenException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Support\Facades\Log;

class Token
{
    /**
     * @param $uid
     * @param string $extend
     * @return string
     */
    public static function generateToken($uid, $extend = 'token')
    {
        $key = config('app.jwt_key');
        $timeOut = config('app.jwt_time_out');
        $time = time(); //当前时间

        $data = [
            'uid' => $uid,
            'extend' => $extend,
            'iat' => $time, //签发时间
            'exp' => $time + $timeOut, //过期时间
        ];
        $token = JWT::encode($data, $key);
        return $token;
    }

    /**
     * @param $token
     * @return array
     * @throws \Exception
     */
    public static function verifyToken($token)
    {
        $key = config('app.jwt_key');
        $uid = -1;
        $extend = -1;

        try {
            $decoded = JWT::decode($token, $key, ['HS256']);
            $uid = ((array)$decoded)['uid'];
            $extend = ((array)$decoded)['extend'];
        } catch (SignatureInvalidException $e) {  //签名不正确
            Log::warning('Token被篡改签名 异常Token请求', (array)$e->getMessage());
            throw new TokenException('非法操作', 501);
        } catch (BeforeValidException $e) {  // 签名在某个时间点之后才能用
            Log::warning('签名在某个时间点之后才能用 异常Token请求', (array)$e->getMessage());
            throw new TokenException('Token未生效', 501);
        } catch (ExpiredException $e) {  // token过期
            throw new TokenException('登录已过期, 请重新登录', 501);
        } catch (\Exception $e) {
            Log::warning('异常Token请求', (array)$e->getMessage());
            throw new TokenException('无效Token, 请重新登录', 501);
        }

        return ['uid' => $uid, 'extend' => $extend];
    }

}