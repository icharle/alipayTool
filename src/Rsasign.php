<?php
/**
 * Created by PhpStorm.
 * User: icharle
 * Date: 2019/3/11
 * Time: 上午12:22
 */

namespace Icharle\Alipaytool;
use Illuminate\Support\Facades\Storage;

class Rsasign
{
    /**
     * @var
     * 支付宝公钥
     */
    private static $alipayrsaPublicKey;

    /**
     * @var
     * 应用私钥
     */
    private static $rsaPrivateKey;


    public function __construct()
    {
        self::$rsaPrivateKey=file_get_contents(storage_path(config('alipaytool.APP_PRIVATE_KEY')));
        self::$alipayrsaPublicKey = file_get_contents(storage_path(config('alipaytool.ALIPAY_PUBLIC_KEY')));
    }

    /**
     * 获取私钥
     * @return bool|resource
     */
    private static function getPrivateKey()
    {
        $privKey = self::$rsaPrivateKey;
        $privKey = "-----BEGIN RSA PRIVATE KEY-----" . PHP_EOL . wordwrap($privKey, 64, PHP_EOL, true) . PHP_EOL . "-----END RSA PRIVATE KEY-----";
        ($privKey) or die('您使用的私钥格式错误，请检查RSA私钥配置');
        return openssl_pkey_get_private($privKey);
    }

    /**
     * 获取公钥
     * @return bool|resource
     */
    private static function getPublicKey()
    {
        $publicKey = self::$alipayrsaPublicKey;
        $publicKey = "-----BEGIN RSA PRIVATE KEY-----" . PHP_EOL . wordwrap($publicKey, 64, PHP_EOL, true) . PHP_EOL . "-----END RSA PRIVATE KEY-----";
        return openssl_pkey_get_public($publicKey);
    }

    /**
     * 签名
     * @param $data
     * @return null|string
     *
     * @see https://docs.open.alipay.com/291/106118
     */
    public static function generateSignature($data)
    {
        if (!is_string($data)) {
            return null;
        }
        $result = openssl_sign($data, $sign, self::getPrivateKey(), OPENSSL_ALGO_SHA256);
        if ($result === false) {
            return null;
        }

        return base64_encode($sign);
    }

    /**
     * 验签（验证 Sign 值）
     * @param $data
     * @param $sign
     * @return bool
     *
     * @see https://docs.open.alipay.com/200/106120
     */
    public static function verifySignature($data, $sign)
    {
        if (!is_string($sign) || !is_string($sign)) {
            return false;
        }
        $decodedSign = base64_decode($sign, true);
        if ($decodedSign === false) {
            return false;
        }
        return (bool)openssl_verify($data, $decodedSign, self::getPublicKey(), OPENSSL_ALGO_SHA256);
    }
}