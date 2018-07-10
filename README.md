# ucf 协议支付（免短验标准版）API-V1.0， 银行卡批量签约（白名单导入）API-V1.0， 银行卡签约VPI-V1.0 SDK

## 银行卡签约API-V1.0
```$php
$config = [ // 公共配置
    'mer_id' => 'M200000550', // 商户号
    'contract_no' => 'P2016080900002662', //'XYZF201803060001', // 签约号, 签约号按实际情况动态传人
    'mer_rsa_key' => 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQChFetx5+VKDoEXzZ+5Wozt3MfWMM/TiKMlWmAKXBViv8/e6j6SU/lSlWkMajd59aiWczs+qf9dMuRpe/l9Qke9DnVMn24JNLXjWD+y+w3yKRwd3CTtF7gx8/ToZl5XqFIT5YB1QfQCdAf8Z18IdQrJIijs8ssczY/RfqKZLo+KLQIDAQAB',
    'sec_id' => 'RSA', // 加密方式
    // 'return_url' => 'http://d339d4a1.ngrok.io/notice.php',
    'notice_url' => 'http://d339d4a1.ngrok.io/notice.php', // 异步通知地址
];

$ucf = (new \XianFeng\Contract($config))
    ->gateway('default');

$contract = [ // 签约参数
    'merchantNo' => $merchantNo = \XianFeng\Utils\Serial::getMicrosecond(), // 订单号
    'accountNo' => '6212250200000000000',    // 银行卡号
    'accountName' => '李四',                  // 持卡人姓名
    'certificateType' => '0',                // 证件类型，目前只支持身份证 = 0
    'certificateNo' => '152323198903046619', // 证件号码
    'mobileNo' => '18101333903',            // 银行预留手机号
    'memo' => '',                           // 商户预留字段，原样返回
    'notice_url' => $config['notice_url']   // 异步通知地址
];

$confirm = [ // 签约确认参数
   'merchantNo' => $merchantNo, // 订单号
   'checkCode' => '404089'      // 短信验证码
];

var_dump(
    // 1. 签约
    $result = $ucf->contract($contract);
    array(6) {
      ["status"]=>
      string(1) "I"
      ["tradeNo"]=>
      string(17) "20180529151306419"
      ["resCode"]=>
      string(5) "00000"
      ["resMessage"]=>
      string(6) "成功"
      ["merchantId"]=>
      string(10) "M200000550"
      ["merchantNo"]=>
      string(17) "20180529151306962"
    }

    // 2. 签约确认
    $result = $ucf->confirm($confirm);
    array(8) {
       ["status"]=>
       string(1) "S"
       ["tradeNo"]=>
       string(17) "20180529151027417"
       ["tradeTime"]=>
       string(14) "20180529151111"
       ["resCode"]=>
       string(5) "00000"
       ["contractNo"]=>
       string(37) "XYZF-20180511134423-20180511134334106"
       ["resMessage"]=>
       string(6) "成功"
       ["merchantId"]=>
       string(10) "M200000550"
       ["merchantNo"]=>
       string(17) "20180529151028329"
    }

    // 3. 重发短信
    $result = $ucf->reSend($merchantNo);
    array(2) {
      ["resCode"]=>
      string(5) "00000"
      ["resMessage"]=>
      string(6) "成功"
    }

    // 4. 查询单笔订单
    $result = $ucf->query($merchantNo = "20180529131641449");
    array(8) {
      ["status"]=>
      string(1) "S"
      ["tradeNo"]=>
      string(17) "20180529151027417"
      ["tradeTime"]=>
      string(14) "20180529151111"
      ["resCode"]=>
      string(5) "00000"
      ["contractNo"]=>
      string(37) "XYZF-20180511134423-20180511134334106"
      ["resMessage"]=>
      string(6) "成功"
      ["merchantId"]=>
      string(10) "M200000550"
      ["merchantNo"]=>
      string(17) "20180529151028329"
    }
);

// 异步回调处理
if ($result = $ucf->notify()) {
    // 正常逻辑
    var_dump($result);
}
```

## 银行卡批量签约(白名单导入)API-V1.0

```$php

$ucf = (new \XianFeng\Contract($config))->gateway('batch');

// 同一批次传递条数不大于1000条，报文内容不大于5M
$cards = [ // 卡片四要素信息
    [
        'merchantNo' => \XianFeng\Utils\Serial::getMicrosecond(),
        'accountNo' => '6251108888888888',
        'accountName' => '王晓丽',
        'certificateType' => '0',
        'certificateNo' => '330226197102250020',
        'mobileNo' => '18100000000',
        'memo' => ''
    ]
];

$batch = [ // 请求参数数据
    'batchNo' => $batchNo = date('YmdHis') . mt_rand(100000, 999999), // 批次号
    'count' => count($cards),                   // 卡片数量
    'noticeUrl' => $config['notice_url'],       // 异步通知地址
    'memo' => '',                               // 商户保留域， 原样回传
    'orders' => json_encode($cards)             // 卡片信息
];

var_dump(
    $batchNo, // 批次号
    // 签约
    array(6) {
      ["batchNo"]=>
      string(20) "20180529160949616779"
      ["resCode"]=>
      string(5) "00000"
      ["acceptCount"]=>
      string(1) "1"
      ["resMessage"]=>
      string(6) "成功"
      ["merchantId"]=>
      string(10) "M200000550"
      ["orders"]=>
      string(79) "[{"resCode":"00001","resMessage":"已受理","merchantNo":"20180529160949452"}]"
    }
    $result = $ucf->contract($batch),

    // 查询
    $result = $ucf->query($batchNo = "批次号")
    array(6) {
      ["batchNo"]=>
      string(20) "20180529173250602285"
      ["resCode"]=>
      string(5) "00000"
      ["acceptCount"]=>
      string(1) "1"
      ["resMessage"]=>
      string(6) "成功"
      ["merchantId"]=>
      string(10) "M200000550"
      ["orders"]=>
      string(201) "[{"tradeTime":"20180529173302","status":"S","tradeNo":"20180529173257440","contractNo":"XYZF-20180529173302-20180529173257440","resCode":"00000","resMessage":"成功","merchantNo":"20180529173250823"}]"
    }
);

# 异步通知

if ($result = $ucf->notify()) {
    // 正常逻辑
    // {"sign":"O59F9hpJP64ytVPQPsNZX9jEIMpm4TdSyYPGKCustZPPPXaklNr4LFb5Vw3VpkEadfdZhf78Btc
    6xyzkb2+v0LIIX04hm3HFbrSpssjLGAXKzpE3k3oN7eoS2qDz46fLKs61J4hNyzTNTsaZZ2S3p0sb+aS1c1+x+M
    uAgW5NDrw=","batchNo":"20180529173250602285","memo":"","acceptCount":"1","merchantId":"
    M200000550","orders":"[{\"contractNo\":\"XYZF-20180529173302-20180529173257440\",\"memo
    \":\"\",\"merchantNo\":\"20180529173250823\",\"resCode\":\"00000\",\"resMessage\":\"\u6
    210\u529f\",\"status\":\"S\",\"tradeNo\":\"20180529173257440\",\"tradeTime\":\"20180529
    173302\"}]"}

    var_dump($result);
}
```

## 新协议支付(免短验标准版)API-V1.0
```$php

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
    'memo'          => 'order=189'
];


var_dump(
    // 支付
    array(10) {
      ["transCur"]=>
      string(3) "156"
      ["memo"]=>
      string(9) "order=189"
      ["tradeTime"]=>
      string(14) "20180529162301"
      ["tradeNo"]=>
      string(30) "201805291623001031610002731007"
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
      string(17) "20180529162300587"
    }
    $result = $ucf->pay($order);
    // 查询
    array(8) {
      ["memo"]=>
      string(9) "order=189"
      ["tradeTime"]=>
      string(14) "20180529162301"
      ["tradeNo"]=>
      string(30) "201805291623001031610002731007"
      ["status"]=>
      string(1) "S"
      ["resMessage"]=>
      string(6) "成功"
      ["resCode"]=>
      string(5) "00000"
      ["merchantId"]=>
      string(10) "M200000550"
      ["merchantNo"]=>
      string(17) "20180529162300587"
    }
    $result = $ucf->query($merchantNo);
);

// 异步回调处理
if ($result = $ucf->notify()) {
    // 正常逻辑
    // {"sign":"jZJ89t15kANxszLWOeR1FtmlguXYrBizC+nQ+x1giQiHjfYRbt7ws5\/
    5EfLuh+hl+ITMlT6ph1R3+BsLfDQWmokbw\/R2Fsm6nRFCaa6j3f9+g1HmzdA+cyBurQ
    fsCftGlp0HfezvDrbLJyne44e7lRyr6k6+9ygDimz5mQR+wbM=","amount":"1","tra
    nsCur":"156","memo":"order=189","tradeNo":"20180529162300103161000273
    1007","status":"S","tradeTime":"20180529162301","resCode":"00000","re
    sMessage":"\u6210\u529f","merchantId":"M200000550","merchantNo":"2018
    0529162300587"}

    var_dump($result);
}

```
