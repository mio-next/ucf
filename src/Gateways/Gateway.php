<?php
/**
 * Author: EnHe <info@wowphp.cn>
 * Date: 2018/5/28
 * Time: 下午10:39
 */
namespace XianFeng\Gateways;

use XianFeng\Utils\AES;
use XianFeng\Utils\RSA;
use Illuminate\Support\Collection;

/**
 * Class Gateway
 * @package XianFeng\Gateways
 */
abstract class Gateway
{
    /**
     * @var array
     */
    protected $replyCodes =  [
        '00000'  => '成功',
        '00002'  => '订单处理中',

        '10000'  => '参数不合法',
        '10001'  => '参数值传入错误',
        '10002'  => '业务不支持',
        '10003'  => '渠道未开通',
        '10004'  => '银行返回失败',
        '10005'  => '订单重复提交',
        '10006'  => '订单已超时关闭',
        '10007'  => '用户或商户编号不存在',
        '10009'  => '交易记录不存在',
        '10011'  => '未开通无卡支付',
        '10024'  => '姓名、身份证、卡号不一致',
        '10025'  => '超银行限额',
        '10026'  => '账户不存在',
        '10027'  => '银行通讯异常',
        '10028'  => '账户状态异常',

        '10031'  => '产品编码配置异常',
        '10032'  => '验证码校验失败',
        '10035'  => '商户状态异常',
        '10036'  => '订单状态异常',
        '10040'  => '暂不支持该银行卡',

        '20000'  => '系统内部错误',
        '20001'  => '服务调用超时',
        '20002'  => '支付平台内部服务调用错误',
        '20003'  => '通讯异常',
        '20004'  => '短信校验次数超限',
        '20005'  => '短信校验失败',
        '20006'  => '短信校验失败',
        '20007'  => '短信发送失败',
        '20009'  => '风控校验不通过',

        '30000'  => '鉴权失败，文案不固定，会返回具体失败原因描述',

        '99016'  => '服务请求参数无效',
        '99020'  => '验签失败',
        '99021'  => 'service不存在',
        '99022'  => 'sign key 不存在',
        '99023'  => 'verify sign failure',
        '99024'  => '服务调用超时',
        '99025'  => '转发URL异常',
        '99026'  => '参数处理异常',
        '99027'  => 'service为空',
        '99028'  => 'merchantId 为空',
        '99029'  => '商户密钥不存在',
        '99030'  => 'reqSn重复',
        '99031'  => '服务版本号version非法',
        '99032'  => '请求IP非法',
        '99034'  => '数据加密失败',
        '99999'  => '未定义错误类型'
    ];

    /**
     * @var string
     */
    private $api = 'https://mapi.ucfpay.com/gateway.do';

    /**
     * @var string
     */
    private $sandBoxApi = 'http://sandbox.firstpay.com/security/gateway.do';

    /**
     * @var Collection $config
     */
    protected $config;

    /**
     * 统一异步通知处理
     * @param null $request
     * @return mixed
     */
    public function notify($request = null)
    {
        $data = isset($_GET['data']) && $_GET['data'] ? $_GET['data']
            : ((isset($_POST['data']) && $_POST['data']) ? $_POST['data'] : []);

        if (!$dataString = AES::decrypt($data, $this->config->get('mer_rsa_key'))) {
            $this->logger(" 数据解密失败");
            return;
        }

        $notice = json_decode($dataString, true);

        if (json_last_error() > 0) {
            $this->logger("转数组失败");
            return;
        }

        $signData = RSA::sign($notice, 'sign');

        if (! RSA::verify($signData, $notice['sign'], $this->config->get('mer_rsa_key'))) {
            $this->logger("验签失败");
            return;
        }

        echo "SUCCESS";

        return $notice;
    }

    /**
     * @param $signArray
     * @return mixed
     */
    protected function sign($signArray)
    {
        $signData =  RSA::sign($signArray, '');

        $signArray['sign'] = RSA::encrypt(strtolower(md5($signData)), $this->config->get('mer_rsa_key'));

        return $signArray;
    }

    /**
     * @param string $code
     * @return mixed
     */
    public function getMessage($code = '99999')
    {
        if (! array_key_exists($code, $this->replyCodes)) {
            $code = '99999';
        }

        return $this->replyCodes[(string) $code];
    }

    /**
     * @param $key
     * @param array $value
     */
    protected function logger($key, $value = [])
    {
        if (function_exists('info')) {
            info($key, $value);
        }
    }

    /**
     * @return string
     */
    protected function getApi()
    {
        return $this->config->get('test')
            ? $this->sandBoxApi : $this->api;
    }
}