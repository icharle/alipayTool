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
    const POSTCHARSET = 'UTF-8';

    /**
     * 签名类型
     */
    const SIGNTYPE = 'RSA';

    const FILECHARSET = 'UTF-8';

    /**
     * @var
     * 应用APP_ID  $appId
     */
    private $appId;

    /**
     * @var
     * 支付宝公钥
     */
    private $alipayrsaPublicKey;

    /**
     * @var
     * 应用私钥
     */
    private $rsaPrivateKey;

    public $encryptKey;

    public $encryptType = "AES";

    public function __construct($appId, $rsaPrivateKey, $alipayrsaPublicKey)
    {
        $this->appId = $appId;
        $this->rsaPrivateKey = $rsaPrivateKey;
        $this->alipayrsaPublicKey = $alipayrsaPublicKey;
    }

}