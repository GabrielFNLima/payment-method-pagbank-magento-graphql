<?php


namespace Devgfnl\PagBankGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use PagBank\PaymentMagento\Gateway\Config\Config as ConfigBase;

class PagBankPublicKey implements ResolverInterface
{
    /** @var ConfigBase */
    protected ConfigBase $configBase;

    public function __construct(
        ConfigBase $configBase
    )
    {
        $this->configBase = $configBase;
    }

    /**
     * @inheritDoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ): ?string
    {
        $storeId = $value['id'];

        return $this->configBase->getMerchantGatewayPublicKey($storeId) ?: null;
    }
}
