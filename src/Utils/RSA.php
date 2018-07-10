<?php
/**
 * Author: EnHe <i@microio.cn>
 * Date: 2018/5/28
 * Time: 下午10:26
 */
namespace XianFeng\Utils;

class RSA
{
    /**
     * @param $originalData
     * @param $publicKey
     * @return string
     */
    public static function encrypt($originalData, $publicKey)
    {
        $encryptData    = '';
        $pem            = chunk_split($publicKey,64,"\n"); //转换为pem格式的私钥
        $pem            = "-----BEGIN PUBLIC KEY-----\n".$pem."-----END PUBLIC KEY-----\n";

        openssl_public_encrypt($originalData, $encryptData, $pem);

        return base64_encode($encryptData);
    }

    /**
     * @param $originalData
     * @param $publicKey
     * @return string
     */
    public static function decrypt($originalData, $publicKey)
    {
        $decryptData    = '';
        $pem            = chunk_split($publicKey,64,"\n"); // 转换为pem格式的私钥
        $pem            = "-----BEGIN PUBLIC KEY-----\n".$pem."-----END PUBLIC KEY-----\n";

        openssl_public_decrypt(base64_decode($originalData), $decryptData, $pem);

        return $decryptData;
    }

    /**
     * @param $originalData
     * @param $signature
     * @param $publicKey
     * @return bool
     */
    public static function verify($originalData, $signature, $publicKey)
    {
        $signValue  = self::decrypt($signature, $publicKey);
        $signData   = strtolower(md5($originalData));

        return $signValue == $signData;
    }

    /**
     * @param $originalArr
     * @param $signName
     * @return string
     */
    public static function sign($originalArr, $signName)
    {
        $data = "";

        if(sizeof($originalArr) > 0)
        {
            $dataArr = $originalArr;

            ksort($dataArr);

            if(! empty($signName)) $dataArr = self::arrayRemove($dataArr,$signName);
            
            $data = urldecode(http_build_query($dataArr));
        }

        return $data;
    }

    /**
     * @param $data
     * @param $key
     * @return mixed
     */
    private static function arrayRemove($data, $key)
    {
        if(!array_key_exists($key, $data)){
            return $data;
        }

        $keys   = array_keys($data);
        $index  = array_search($key, $keys);

        if($index !== false){
            array_splice($data, $index, 1);
        }

        return $data;
    }
}