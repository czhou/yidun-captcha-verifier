<?php
namespace Czhou\Yidun;

/**
 * 易盾验证码二次校验SDK
 * @author Charles Zhou
 */
class CaptchaVerifier {
    public static $YIDUN_CAPTCHA_API_VERSION = 'v2';
    public static $YIDUN_CAPTCHA_API_TIMEOUT = 5;
    public static $YIDUN_CAPTCHA_API_URL = 'http://c.dun.163yun.com/api/v2/verify';
    /**
     * 构造函数
     * @param $captchaId 验证码id
     * @param $secretPair 密钥对
     */
    public function __construct($captchaId, SecretPair $secretPair) {
        $this->captchaId  = $captchaId;
        $this->secretPair = $secretPair;
    }

    /**
     * 发起二次校验请求
     * @param $validate 二次校验数据
     * @param $user 用户信息
     */
    public function verify($validate, $user) {
        $params = array();
        $params["captchaId"] = $this->captchaId;
        $params["validate"] = $validate;
        $params["user"] = $user;
        // 公共参数
        $params["secretId"] = $this->secretPair->secretId;
        $params["version"] = self::$YIDUN_CAPTCHA_API_VERSION;
        $params["timestamp"] = sprintf("%d", round(microtime(true)*1000));// time in milliseconds
        $params["nonce"] = sprintf("%d", rand()); // random int
        $params["signature"] = $this->sign($this->secretPair->secretKey, $params);

        $result = $this->send_http_request($params);
        return array_key_exists('result', $result) ? $result['result'] : false;
    }

    /**
     * 计算参数签名
     * @param $secretKey 密钥对key
     * @param $params 请求参数
     */
    private function sign($secretKey, $params){
        ksort($params); // 参数排序
        $buff="";
        foreach($params as $key=>$value){
            $buff .=$key;
            $buff .=$value;
        }
        $buff .= $secretKey;
        return md5(mb_convert_encoding($buff, "utf8", "auto"));
    }

    /**
     * 发送http请求
     * @param $params 请求参数
     */
    private function send_http_request($params){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::$YIDUN_CAPTCHA_API_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::$YIDUN_CAPTCHA_API_TIMEOUT);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::$YIDUN_CAPTCHA_API_TIMEOUT);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));

        /*
         * Returns TRUE on success or FALSE on failure.
         * However, if the CURLOPT_RETURNTRANSFER option is set, it will return the result on success, FALSE on failure.
         */
        $result = curl_exec($ch);
        // var_dump($result);

        if(curl_errno($ch)){
            $msg = curl_error($ch);
            curl_close($ch);
            return array("error"=>500, "msg"=>$msg, "result"=>false);
        }else{
            curl_close($ch);
            return json_decode($result, true);
        }
    }
}
