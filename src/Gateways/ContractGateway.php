<?php
/**
 * Author: EnHe <i@microio.cn>
 * Date: 2018/5/28
 * Time: 下午10:40
 */
namespace XianFeng\Gateways;

use XianFeng\Utils\AES;
use XianFeng\Utils\Serial;
use GuzzleHttp\Client as HTTP;
use Illuminate\Support\Collection;

/**
 * 银行卡签约 VPI-V1.0
 * Class DefaultContractGateway
 * @package XianFeng\Gateways
 */
class ContractGateway extends Gateway
{
    /**
     * DefaultContractGateway constructor.
     * @param Collection $config
     */
    public function __construct(Collection $config)
    {
        $this->config = $config;
    }

    /**
     * 银行卡签约申请
     * @param array $card [4要素卡片信息]
     * @return mixed
     * @throws \Exception
     */
    public function contract($card = [])
    {
        $signArray = [
            "service" => "REQ_CONTRACT_APPLY",
            "secId" => 'RSA',
            "version" => '4.0.0',
            "reqSn" => Serial::createUnRepeatCode($this->config->get('mer_id'), 'REQ_CONTRACT_APPLY', $card['merchantNo']),
            "merchantId" => $this->config->get('mer_id'),
            "data" => AES::encrypt(json_encode($card), $this->config->get('mer_rsa_key'))
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
     * 单笔订单查询
     * @param $merchantNo 订单号
     * @return mixed
     * @throws \Exception
     */
    public function query($merchantNo)
    {
        $signArray = [
            "service" => "REQ_CONTRACT_QUERY",
            "secId" => 'RSA',
            "version" => '4.0.0',
            "reqSn" => Serial::createUnRepeatCode($this->config->get('mer_id'), 'REQ_CONTRACT_QUERY', $merchantNo),
            "merchantId" => $this->config->get('mer_id'),
            "merchantNo" => $merchantNo
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
     * 银行卡签约确认
     * @param array $data [短信验证码，订单号]
     * @return mixed
     * @throws \Exception
     */
    public function confirm($data = [])
    {
        $signArray = [
            "service" => "REQ_CONTRACT_CONFIRM",
            "secId" => 'RSA',
            "version" => '4.0.0',
            "reqSn" => Serial::createUnRepeatCode($this->config->get('mer_id'), 'REQ_CONTRACT_CONFIRM', $data['merchantNo']),
            "merchantId" => $this->config->get('mer_id'),
            'data' => AES::encrypt(json_encode($data), $this->config->get('mer_rsa_key'))
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
     * 重发短信
     * @param $merchantNo 订单号
     * @return mixed
     * @throws \Exception
     */
    public function reSend($merchantNo)
    {
        $signArray = [
            "service" => "REQ_CONTRACT_RESEND",
            "secId" => 'RSA',
            "version" => '4.0.0',
            "reqSn" => Serial::createUnRepeatCode($this->config->get('mer_id'), 'REQ_CONTRACT_RESEND', $merchantNo),
            "merchantId" => $this->config->get('mer_id'),
            'merchantNo' => $merchantNo
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
}