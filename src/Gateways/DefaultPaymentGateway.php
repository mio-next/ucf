<?php
/**
 * Author: EnHe <i@microio.cn>
 * Date: 2018/5/28
 * Time: 下午10:39
 */
namespace XianFeng\Gateways;

use XianFeng\Utils\AES;
use XianFeng\Utils\Serial;
use GuzzleHttp\Client as HTTP;
use Illuminate\Support\Collection;

class DefaultPaymentGateway extends Gateway
{
    public function __construct(Collection $config)
    {
        $this->config = $config;
    }

    /**
     * @param array $order
     * @return array
     * @throws \Exception
     */
    public function pay($order = [])
    {
        $signArray = [
            "service" => "REQ_PROTOCOL_PAY_NS",
            "secId" => 'RSA',
            "version" => '4.0.0',
            "reqSn" => Serial::createUnRepeatCode($this->config->get('mer_id'), 'REQ_PROTOCOL_PAY_NS', $merchantNo = Serial::getMicrosecond()),
            "merchantId" => $this->config->get('mer_id'),
            "data" => AES::encrypt(json_encode($this->getData($order)), $this->config->get('mer_rsa_key'))
        ];

        try {
            $response   = (new HTTP())->post($this->getApi(), ['form_params' => $this->sign($signArray) ?: []]);
            $result     = AES::decrypt($response->getBody()->getContents(), $this->config->get('mer_rsa_key'));

            return json_decode($result, 1);
        } catch (\Exception $exception) {
            if (function_exists('info')) {
                info(__METHOD__, [$exception->getMessage() . ' : ' . $exception->getTraceAsString()]);
            }

            throw $exception;
        }
    }

    /**
     * @param $serial 商户流水号
     * @return mixed
     * @throws \Exception
     */
    public function query($serial)
    {
        $signArray = [
            "service" => "REQ_PROTOCOL_QUERY_BY_ID",
            "secId" => 'RSA',
            "version" => '4.0.0',
            "reqSn" => Serial::createUnRepeatCode($this->config->get('mer_id'), 'REQ_PROTOCOL_QUERY_BY_ID', $serial),
            "merchantId" => $this->config->get('mer_id'),
            "merchantNo" => $serial,
        ];

        try {
            $response   = (new HTTP())->post($this->getApi(), ['form_params' => $this->sign($signArray)]);
            $result     = AES::decrypt($response->getBody()->getContents(), $this->config->get('mer_rsa_key'));

            return json_decode($result, 1);

        } catch (\Exception $exception) {
            if (function_exists('info')) {
                info(__METHOD__, [$exception->getMessage() . ' : ' . $exception->getTraceAsString()]);
            }

            throw $exception;
        }
    }

    /**
     * @param array $data
     * @return array
     */
    private function getData($data = [])
    {
        // TODO 所有可传入参数
        return array_merge([
            'merchantNo' => '',
            'contractNo' => $this->config->get('contract_no'),
            'amount' => 0,
            'transCur' => '156',
            'certificateType' => '0',
            'certificateNo' => '',
            'accountNo' => '',
            'accountName' => '',
            'cvn2' => '',
            'validDate' => '',
            'mobileNo' => '',
            'bankId' => '',
            'bankName' => '',
            'productName' => '',
            'productInfo' => '',
            'noticeUrl' => $this->config->get('notice_url'),
            'expireTime' => '',
            'memo' => '',
        ], $data);
    }
}