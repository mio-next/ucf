<?php
/**
 * Author: EnHe <i@microio.cn>
 * Date: 2018/5/28
 * Time: 下午10:16
 */

require __DIR__ . '/../vendor/autoload.php';

$config = [
    'mer_id' => 'M200000550',
    'contract_no' => 'XYZF-20180511134423-20180511134334106', //'XYZF201803060001',
    'mer_rsa_key' => 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQChFetx5+VKDoEXzZ+5Wozt3MfWMM/TiKMlWmAKXBViv8/e6j6SU/lSlWkMajd59aiWczs+qf9dMuRpe/l9Qke9DnVMn24JNLXjWD+y+w3yKRwd3CTtF7gx8/ToZl5XqFIT5YB1QfQCdAf8Z18IdQrJIijs8ssczY/RfqKZLo+KLQIDAQAB',
    'sec_id' => 'RSA',
//    'return_url' => 'http://d339d4a1.ngrok.io/notice.php',
    'notice_url' => 'http://d339d4a1.ngrok.io/notice.php',
    'test' => 1
];

$ucf = (new \XianFeng\Pay($config))->gateway();

$order = [
    'merchantNo'    => $merchantNo = \XianFeng\Utils\Serial::getMicrosecond(),
    'amount'        => 1,
    'certificateNo' => '420621199012133824',
    'bankName'      => '工商银行',
    'bankId'        => 'ICBC',
    'accountNo'     => '6222021001115704287',
    'accountName'   => '王泽武',
    'mobileNo'      => '18100000000',
    'productName'   => '测试充值',
    'memo'          => 'order=189',
    'expireTime'    => '',
];

// 免短验测试
var_dump(
    $ucf->pay($order),
    $ucf->query($merchantNo)
);

/*
array(10) {
  ["transCur"]=>
  string(3) "156"
  ["memo"]=>
  string(9) "order=189"
  ["tradeTime"]=>
  string(14) "20180529175953"
  ["tradeNo"]=>
  string(30) "201805291759521031610002731112"
  ["status"]=>
  string(1) "S"
  ["resMessage"]=>
  string(6) "成功"
  ["amount"]=>
  string(1) "1"
  ["resCode"]=>
  string(5) "00000"
  ["merchantId"]=>
  string(10) "M200000550"
  ["merchantNo"]=>
  string(17) "20180529175952834"
}
array(8) {
  ["memo"]=>
  string(9) "order=189"
  ["tradeTime"]=>
  string(14) "20180529175953"
  ["tradeNo"]=>
  string(30) "201805291759521031610002731112"
  ["status"]=>
  string(1) "S"
  ["resMessage"]=>
  string(6) "成功"
  ["resCode"]=>
  string(5) "00000"
  ["merchantId"]=>
  string(10) "M200000550"
  ["merchantNo"]=>
  string(17) "20180529175952834"
}


NOTICE :: 2018-05-29 17:59:55 [merchantNo => 20180529175952834] :: {"sign":"AvwwCyUPOjcdt74HSAqW2cnz5qsQDOCzc\/vUGYMbLkM8sDAtOa8J0scBIy\/zALthUE1PDTI1EgX6\/2\/1a1cqrSdNhVfEGRMA31+HQbg7e8w2b6lDTObDIrZi5BI9obyMqalp+xG486w0ks8xSP1uOC+Cec3jNuSBkluVejBvSno=","amount":"1","transCur":"156","memo":"order=189","tradeNo":"201805291759521031610002731112","status":"S","tradeTime":"20180529175953","resCode":"00000","resMessage":"\u6210\u529f","merchantId":"M200000550","merchantNo":"20180529175952834"}
 */