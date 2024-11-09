<?php


namespace Devgfnl\PagBankGraphQl\Model\Resolver;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Quote\Api\Data\CartInterface;
use PagBank\PaymentMagento\Api\Data\InstallmentSelectedInterface;

class PagBankInterest implements ResolverInterface
{
    /**
     * @inheritDoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ): array
    {
        if (!isset($value['model'])) {
            throw new LocalizedException(__('"model" value should be specified'));
        }
        /** @var CartInterface $cart */
        $cart = $value['model'];

        return [
            "value"=>(float) $cart->getData(InstallmentSelectedInterface::PAGBANK_INTEREST_AMOUNT),
            "currency"=>"BRL"
        ];
    }
}
