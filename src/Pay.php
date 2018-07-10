<?php
/**
 * Author: EnHe <info@wowphp.cn>
 * Date: 2018/5/28
 * Time: 下午10:43
 */
namespace XianFeng;

use XianFeng\Exceptions\XianFengException;
use XianFeng\Gateways\DefaultPaymentGateway;

class Pay
{
    /**
     * @var array
     */
    protected $gateways = [
        'default'   => DefaultPaymentGateway::class,
    ];

    /**
     * @var \Illuminate\Support\Collection
     */
    private $config;

    /**
     * Pay constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = collect($config);
    }

    /**
     * @param string $gateway
     * @throws XianFengException
     * @return BatchGateway | DefaultGateway
     */
    public function gateway($gateway = "default")
    {
        if (! array_key_exists($gateway, $this->gateways)) {
            throw new XianFengException("unsupported gateway => $gateway. [ default ]");
        }

        return new $this->gateways[(string) $gateway]($this->config);
    }
}