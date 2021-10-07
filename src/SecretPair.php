<?php
namespace Czhou\Yidun;
/**
 * 密钥对
 * @author Charles Zhou
 */
class SecretPair {
	public $secretId;
	public $secretKey;

	/**
	 * 构造函数
	 * @param $secretId 密钥对id
	 * @param $secretKey 密钥对key
	 */
    public function __construct($secretId, $secretKey) {
        $this->secretId  = $secretId;
        $this->secretKey = $secretKey;
    }
}