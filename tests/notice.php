<?php
/**
 * Author: EnHe <i@microio.cn>
 * Date: 2018/5/29
 * Time: 下午12:10
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

$data = isset($_GET['data']) && $_GET['data'] ? $_GET['data']
    : ((isset($_POST['data']) && $_POST['data']) ? $_POST['data'] : "");

if (!$dataString = \XianFeng\Utils\AES::decrypt($data, $config['mer_rsa_key'])) {
    var_dump("解密失败");
    return;
}

$reqArray = json_decode($dataString, true);

if (json_last_error() > 0) {
    var_dump("转数组失败");
    return;
}

$signData = \XianFeng\Utils\RSA::sign($reqArray, 'sign');

if (! \XianFeng\Utils\RSA::verify($signData, $reqArray['sign'], $config['mer_rsa_key'])) {
    var_dump("验签失败");
    return;
}

// 回调测试

$msg = 'NOTICE :: ' . date('Y-m-d H:i:s') . ' [merchantNo => ' . $reqArray['merchantNo'] . '] :: ' . json_encode($reqArray) . PHP_EOL;

file_put_contents('notice.log', charsetToGBK($msg), FILE_APPEND);

return "SUCCESS";