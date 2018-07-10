<?php
/**
 * Author: EnHe <info@wowphp.cn>
 * Date: 2018/5/28
 * Time: 下午10:21
 */
namespace XianFeng\Utils;

class AES
{
    /**
     * @param $input
     * @param $key
     * @return string
     */
    public static function encrypt($input, $key)
    {
        $md5key     = strtoupper(md5($key));
        $key        = hex2bin($md5key);
        $size       = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $input      = self::pkcs5_pad($input, $size);
        $td         = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
        $iv         = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);

        mcrypt_generic_init($td, $key, $iv);

        $data       = mcrypt_generic($td, $input);

        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);

        return base64_encode($data);
    }

    /**
     * @param $encrypted
     * @param $key
     * @return bool|string
     */
    public static function decrypt($encrypted, $key)
    {
        $md5key     = strtoupper(md5($key));
        $key        = hex2bin($md5key);
        $decrypted  = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, base64_decode($encrypted), MCRYPT_MODE_ECB);
        $dec_s      = strlen($decrypted);
        $padding    = ord($decrypted[$dec_s-1]);

        return substr($decrypted, 0, -$padding);
    }

    /**
     * @param $text
     * @param $blocksize
     * @return string
     */
    private static function pkcs5_pad($text, $blocksize)
    {
        $pad = $blocksize - (strlen($text) % $blocksize);

        return $text . str_repeat(chr($pad), $pad);
    }
}