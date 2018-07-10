<?php
/**
 * Author: EnHe <info@wowphp.cn>
 * Date: 2018/5/28
 * Time: 下午10:33
 */
namespace XianFeng\Utils;

class Serial
{
    /**
     * @param $merchantId
     * @param $service
     * @param $merchantNo
     * @return string
     */
    public static function createUnRepeatCode($merchantId, $service, $merchantNo) {

        if (is_null($merchantId) || (empty($merchantId))) return "";

        if (is_null($service) || (empty($service))) return "";

        if (is_null($merchantNo) || (empty($merchantNo))) {
            $merchantNo = self::getMillisecond();
        }

        $randomVal  = self::getUuid();
        $reqSn      = $merchantId . $service . $merchantNo . $randomVal;

        return strtoupper(md5($reqSn));
    }

    /**
     * @return string
     */
    public static function getUuid() {
        mt_srand (( double ) microtime () * 10000); //optional for php 4.2.0 and up.随便数播种，4.2.0以后不需要了。
        $charid = strtoupper( md5 ( uniqid (rand (), true ))); //根据当前时间（微秒计）生成唯一id.
        $hyphen = chr ( 45 ); // "-"
        $uuid = ''.substr ( $charid, 0, 8 ) . $hyphen . substr ( $charid, 8, 4 ) . $hyphen . substr ( $charid, 12, 4 ) . $hyphen . substr ( $charid, 16, 4 ) . $hyphen . substr ( $charid, 20, 12 );
        return $uuid;
    }

    /**
     * @return string
     */
    public static function getMillisecond() {
        list($usec, $sec) = explode(' ', microtime());

        return $sec.ceil(($usec * 1000));
    }

    /**
     * @return string
     */
    public static function getMicrosecond() {
        list($usec, $sec)   = explode(" ", microtime());
        $millisecond        = round($usec*1000);
        $millisecond        = str_pad($millisecond, 3, '0', STR_PAD_RIGHT);

        return date("YmdHis") . $millisecond;
    }
}