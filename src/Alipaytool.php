<?php
/**
 * Created by PhpStorm.
 * User: icharle
 * Date: 2019/3/10
 * Time: 下午2:24
 */

namespace Icharle\Alipaytool;

class Alipaytool
{
    /**
     * 网关
     */
    const GATEWAYURL = 'https://openapi.alipay.com/gateway.do';

    /**
     * SDK 版本
     */
    const SDK_VERSION = 'alipay-sdk-php-20180705';

    /**
     * API 版本
     */
    const API_VERSION = '1.0';

    /**
     * 返回数据格式
     */
    const FORMAT = 'json';

    /**
     * 表单提交字符集编码
     */
    const CHARSET_UTF8 = 'UTF-8';

    /**
     * 签名类型
     */
    const SIGNTYPE_RSA = 'RSA';

    const SIGNTYPE_RSA2 = 'RSA2';

    const FILECHARSET = 'UTF-8';

    /**
     * 换取授权访问令牌
     */
    const API_METHOD_AUTH_TOKEN = 'alipay.system.oauth.token';

    /**
     * 支付宝会员授权信息查询接口
     */
    const API_METHOD_GET_USER_INFO = 'alipay.user.info.share';

    /**
     * @var
     * 应用APP_ID  $appId
     */
    private static $appId;

    public function __construct()
    {
        self::$appId = config('alipaytool.ALIPAY_APP_ID');
    }

    public static function getUserInfoByAccessToken($access_token)
    {
        $param = self::buildUserInfoParams($access_token);;
        return self::curl(http_build_query($param));
    }

    public static function getAccessToken($authCode)
    {
        $param = self::buildAuthCodeParams($authCode);
        return self::curl(http_build_query($param));
    }

    public static function buildUserInfoParams($token)
    {
        $UserInfoParams = [
            'auth_token' => $token,
        ];
        $Param = self::buildSign(static::API_METHOD_GET_USER_INFO, $UserInfoParams);
        return $Param;
    }

    public static function buildAuthCodeParams($code, $refreshToken = '')
    {
        $AuthCodeParams = [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'refresh_token' => $refreshToken,
        ];
        $Param = self::buildSign(static::API_METHOD_AUTH_TOKEN, $AuthCodeParams);
        return $Param;
    }

    /**
     * @param $method 接口名称(API_METHOD_AUTH_TOKEN、API_METHOD_GET_USER_INFO)
     * @return $Params 公共参数
     */
    public static function buildCommonParams($method)
    {
        $commonParams["app_id"] = static::$appId;
        $commonParams["method"] = $method;
        $commonParams["format"] = static::FORMAT;
        $commonParams["charset"] = static::CHARSET_UTF8;
        $commonParams["sign_type"] = static::SIGNTYPE_RSA2;
        $commonParams["timestamp"] = date("Y-m-d H:i:s");
        $commonParams["version"] = static::API_VERSION;

        return $commonParams;
    }

    /**
     * 签名生成sign值
     * @param $apiMethod 接口名称
     * @param $businessParams 业务特殊参数
     * @return array
     */
    public static function buildSign($apiMethod, $businessParams)
    {
        $pubParam = self::buildCommonParams($apiMethod);
        $businessParams = array_merge($pubParam, $businessParams);
        $signContent = self::getSignContent($businessParams);
        $sign = (new Rsasign())::generateSignature($signContent);
        $businessParams['sign'] = $sign;
        return $businessParams;
    }

    /**
     * 筛选并排序&&拼接
     * @param $params 所有参数
     * @return string 待签名字符串
     * @see https://docs.open.alipay.com/291/106118 自行实现签名
     */
    public static function getSignContent($params)
    {
        ksort($params);

        $stringToBeSigned = "";
        $i = 0;
        foreach ($params as $k => $v) {
            if (false === self::checkEmpty($v) && "@" != substr($v, 0, 1)) {

                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "=" . "$v";
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . "$v";
                }
                $i++;
            }
        }

        unset ($k, $v);
        return $stringToBeSigned;
    }

    /**
     * 校验$value是否非空
     *  if not set ,return true;
     *  if is null , return true;
     **/
    public static function checkEmpty($value)
    {
        return $value === null || trim($value) === '';
    }

    protected static function curl($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $headers = array('content-type: application/x-www-form-urlencoded;charset=utf8');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $reponse = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch), 0);
        } else {
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (200 !== $httpStatusCode) {
                throw new Exception($reponse, $httpStatusCode);
            }
        }
        curl_close($ch);
        return $reponse;
    }
}