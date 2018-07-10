<?php
/**
 * Author: EnHe <i@microio.cn>
 * Date: 2018/5/29
 * Time: 上午11:00
 */
namespace XianFeng;

use XianFeng\Gateways\ContractGateway;
use XianFeng\Exceptions\XianFengException;
use XianFeng\Gateways\BatchContractGateway;

/**
 * Class Contract
 * @package XianFeng
 */
class Contract
{
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $config;

    /**
     * @var array
     */
    protected $gateways = [
        'batch'     => BatchContractGateway::class,     // 批量签约
        'default'   => ContractGateway::class,          // 单次
    ];

    /**
     * Contract constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->config = collect($config);
    }

    /**
     * @param string $gateway
     * @return BatchContractGateway | ContractGateway
     * @throws XianFengException
     */
    public function gateway($gateway = 'batch')
    {
        if (! array_key_exists($gateway, $this->gateways)) {
            throw new XianFengException("unsupported gateway => $gateway. [ default | batch ]");
        }

        return new $this->gateways[(string) $gateway]($this->config);
    }
}