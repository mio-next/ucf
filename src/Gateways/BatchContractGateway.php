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
 * 银行卡批量签约(白名单导入)API-V1.0
 * Class BatchContractGateway
 * @package XianFeng\Gateways
 */
class BatchContractGateway extends Gateway
{
    /**
     * BatchContractGateway constructor.
     * @param Collection $config
     */
    public function __construct(Collection $config)
    {
        $this->config = $config;
    }

    /**
     * 批量签约
     * @param array $batch
     * @param  string $merchantNo
     * @return mixed
     * @throws \Exception
     */
    public function contract($batch = [], $merchantNo)
    {
        $signArray = [
            "service" => "REQ_CONTRACT_BATCH",
            "secId" => 'RSA',
            "version" => '4.0.0',
            "reqSn" => Serial::createUnRepeatCode($this->config->get('mer_id'), 'REQ_CONTRACT_BATCH', $merchantNo),
            "merchantId" => $this->config->get('mer_id'),
            "data" => AES::encrypt(json_encode($batch, 1), $this->config->get('mer_rsa_key'))
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
     * 批量签约查询
     * @param $batchNo 批次号
     * @return mixed
     * @throws \Exception
     */
    public function query($batchNo)
    {
        $signArray = [
            "service" => "REQ_CONTRACT_BATCH_QUERY",
            "secId" => 'RSA',
            "version" => '4.0.0',
            "reqSn" => Serial::createUnRepeatCode($this->config->get('mer_id'), 'REQ_CONTRACT_BATCH', $merchantNo = Serial::getMicrosecond()),
            "merchantId" => $this->config->get('mer_id'),
            "batchNo" => $batchNo
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