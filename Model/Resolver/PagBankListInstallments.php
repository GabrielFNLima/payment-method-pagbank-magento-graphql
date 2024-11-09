<?php


namespace Devgfnl\PagBankGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Model\MaskedQuoteIdToQuoteIdInterface;
use PagBank\PaymentMagento\Api\Data\CreditCardBinInterface;
use PagBank\PaymentMagento\Api\ListInstallmentsManagementInterface;

class PagBankListInstallments implements ResolverInterface
{

    /** @var ListInstallmentsManagementInterface */
    protected ListInstallmentsManagementInterface $installmentsManagement;
    /** @var MaskedQuoteIdToQuoteIdInterface */
    protected MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdToQuoteId;
    /** @var CreditCardBinInterface */
    protected CreditCardBinInterface $creditCardBin;
    /** @var PriceCurrencyInterface */
    protected PriceCurrencyInterface $priceCurrency;

    public function __construct(
        ListInstallmentsManagementInterface $installmentsManagement,
        MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdToQuoteId,
        CreditCardBinInterface $creditCardBin,
        PriceCurrencyInterface $priceCurrency
    )
    {
        $this->installmentsManagement = $installmentsManagement;
        $this->maskedQuoteIdToQuoteId = $maskedQuoteIdToQuoteId;
        $this->creditCardBin = $creditCardBin;
        $this->priceCurrency = $priceCurrency;
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
    ): array
    {
        if (empty($args['cart_id'])) {
            throw new GraphQlInputException(__('Required parameter "cart_id" is missing'));
        }

        if (empty($args['credit_card_bin'])) {
            throw new GraphQlInputException(__('Required parameter "credit_card_bin" is missing'));
        }
        $cartId = $this->maskedQuoteIdToQuoteId->execute($args['cart_id']);
        $creditCardBin = $this->creditCardBin;
        $creditCardBin->setCreditCardBin($args['credit_card_bin']);

        $installments = $this->installmentsManagement->generateListInstallments($cartId, $creditCardBin);

        $listFormatted = [];

        foreach ($installments as $installment) {
            $supplementaryText = __('in total of %1', $this->getFormattedPrice($installment['amount']['value'] / 100));

            if($installment['interest_free']){
                $supplementaryText = __('not interest');
            }

            $label = __('%1x of %2 %3', $installment['installments'], $this->getFormattedPrice($installment['installment_value'] / 100), $supplementaryText);

            $listFormatted[] = [
                "value" => $installment['installments'],
                "label" => $label
            ];
        }

        return $listFormatted;
    }

    /**
     * @param $amount
     * @return string
     */
    private function getFormattedPrice($amount):string
    {
        return strip_tags($this->priceCurrency->format($amount));
    }
}
