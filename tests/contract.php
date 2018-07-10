<?php
/**
 * Author: EnHe <info@wowphp.cn>
 * Date: 2018/5/28
 * Time: 下午10:16
 * Desc: 先锋支付 / 签约测试
 */
require __DIR__ . '/../vendor/autoload.php';

$config = [
    'mer_id' => 'M200000550',
    'contract_no' => 'P2016080900002662', //'XYZF201803060001',
    'mer_rsa_key' => 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQChFetx5+VKDoEXzZ+5Wozt3MfWMM/TiKMlWmAKXBViv8/e6j6SU/lSlWkMajd59aiWczs+qf9dMuRpe/l9Qke9DnVMn24JNLXjWD+y+w3yKRwd3CTtF7gx8/ToZl5XqFIT5YB1QfQCdAf8Z18IdQrJIijs8ssczY/RfqKZLo+KLQIDAQAB',
    'sec_id' => 'RSA',
    'return_url' => 'http://d339d4a1.ngrok.io/notice.php',
    'notice_url' => 'http://d339d4a1.ngrok.io/notice.php',
    'test' => 1 // 测试， 正常不指定此参数即可
];

$ucf = (new \XianFeng\Contract($config))->gateway('default');

// 签约参数
$contract = [
    'merchantNo' => $merchantNo = \XianFeng\Utils\Serial::getMicrosecond(),
    'accountNo' => '6222021001115704287',
    'accountName' => '王泽武',
    'certificateType' => '0',
    'certificateNo' => '420621199012133824',
    'mobileNo' => '18100000000',
    'memo' => '',
    'notice_url' => $config['notice_url'],
];

// 签约确认参数
$confirm = [
    'merchantNo' => $merchantNo,
    'checkCode' => '404089'
];

var_dump(
    // 签约 // 订单号: 20180529175127358 -> 签约号：XYZF-20180511134423-20180511134334106
    $ucf->contract($contract),
    // 重发短信
    $ucf->reSend($merchantNo),
    // 签约确认, 只能确认一次
    $ucf->confirm($confirm),
    // 查询单笔订单
    $ucf->query($merchantNo)
);

// 问题清单
// 1. 沙盒环境不发短信

/*
array(6) {
  ["status"]=>
  string(1) "I"
  ["tradeNo"]=>
  string(17) "20180529175454446"
  ["resCode"]=>
  string(5) "00000"
  ["resMessage"]=>
  string(6) "成功"
  ["merchantId"]=>
  string(10) "M200000550"
  ["merchantNo"]=>
  string(17) "20180529175455120"
}

array(8) {
  ["status"]=>
  string(1) "S"
  ["tradeNo"]=>
  string(17) "20180529175454446"
  ["tradeTime"]=>
  string(14) "20180529175533"
  ["resCode"]=>
  string(5) "00000"
  ["contractNo"]=>
  string(37) "XYZF-20180511134423-20180511134334106"
  ["resMessage"]=>
  string(6) "成功"
  ["merchantId"]=>
  string(10) "M200000550"
  ["merchantNo"]=>
  string(17) "20180529175455120"
}

array(8) {
  ["status"]=>
  string(1) "S"
  ["tradeNo"]=>
  string(17) "20180529175454446"
  ["tradeTime"]=>
  string(14) "20180529175533"
  ["resCode"]=>
  string(5) "00000"
  ["contractNo"]=>
  string(37) "XYZF-20180511134423-20180511134334106"
  ["resMessage"]=>
  string(6) "成功"
  ["merchantId"]=>
  string(10) "M200000550"
  ["merchantNo"]=>
  string(17) "20180529175455120"
}
 */