<?php
/**
 * Author: EnHe <i@microio.cn>
 * Date: 2018/5/28
 * Time: 下午10:16
 * Desc: 先锋支付 / 批量签约测试
 */
require __DIR__ . '/../vendor/autoload.php';

$config = [
    'mer_id' => 'M200000550',
    'contract_no' => 'P2016080900002662', //'XYZF201803060001',
    'mer_rsa_key' => 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQChFetx5+VKDoEXzZ+5Wozt3MfWMM/TiKMlWmAKXBViv8/e6j6SU/lSlWkMajd59aiWczs+qf9dMuRpe/l9Qke9DnVMn24JNLXjWD+y+w3yKRwd3CTtF7gx8/ToZl5XqFIT5YB1QfQCdAf8Z18IdQrJIijs8ssczY/RfqKZLo+KLQIDAQAB',
    'sec_id' => 'RSA',
//    'return_url' => 'http://d339d4a1.ngrok.io/notice.php',
    'notice_url' => 'http://d339d4a1.ngrok.io/notice.php',
    'test' => 1
];

$ucf = (new \XianFeng\Contract($config))->gateway('batch');

$batch = [
    'batchNo' => $batchNo = date('YmdHis') . mt_rand(100000, 999999),
    'noticeUrl' => $config['notice_url'],
    'memo' => '',
    'orders' => json_encode($cards = [
        // // 同一批次传递条数不大于1000条，报文内容不大于5M
        [
            'merchantNo' => $merchantNo = \XianFeng\Utils\Serial::getMicrosecond(),
            'memo' => '',
            'accountNo' => '6222021001115704287',
            'accountName' => '王泽武',
            'certificateType' => '0',
            'certificateNo' => '420621199012133824',
            'mobileNo' => '18100000000',
        ]
    ], true),
    'count' => count($cards),
];


// 批量提交处理
// 查询
// 异步通知 $ufc->notify();
var_dump(
    $batchNo,
    $merchantNo,
    $ucf->contract($batch, $merchantNo),
    $ucf->query($batchNo)
);

/*
string(20) "20180529175718507959"
string(17) "20180529175718670"
array(6) {
  ["batchNo"]=>
  string(20) "20180529175718507959"
  ["resCode"]=>
  string(5) "00000"
  ["acceptCount"]=>
  string(1) "1"
  ["resMessage"]=>
  string(6) "成功"
  ["merchantId"]=>
  string(10) "M200000550"
  ["orders"]=>
  string(79) "[{"resCode":"00001","resMessage":"已受理","merchantNo":"20180529175718670"}]"
}
array(6) {
  ["batchNo"]=>
  string(20) "20180529175718507959"
  ["resCode"]=>
  string(5) "00000"
  ["acceptCount"]=>
  string(1) "1"
  ["resMessage"]=>
  string(6) "成功"
  ["merchantId"]=>
  string(10) "M200000550"
  ["orders"]=>
  string(49) "[{"status":"I","merchantNo":"20180529175718670"}]"
}

NOTICE :: 2018-05-29 17:58:00 [merchantNo => ] :: {"sign":"Hd0jGc8DRxSjrRrslSe\/p2R9fTxrSrnjdpCNFLVkzk5VgVFPegUXKTrqdoFUuGO18Mm03O6+O2RyhP8nxQkDZxgFUKQU0xvdt7\/NeTbjp4l4edOoXHehJz72tbYnceZHdbgLw2KD8T6450VZA0fF\/sT9EoHaY5m8XCtg9+3jcUc=","batchNo":"20180529175718507959","memo":"","acceptCount":"1","merchantId":"M200000550","orders":"[{\"contractNo\":\"XYZF-20180511134423-20180511134334106\",\"memo\":\"\",\"merchantNo\":\"20180529175718670\",\"resCode\":\"00000\",\"resMessage\":\"\u6210\u529f\",\"status\":\"S\",\"tradeNo\":\"20180529175757447\",\"tradeTime\":\"20180529175757\"}]"}
 */