<?php


namespace Devgfnl\PagBankGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Model\MaskedQuoteIdToQuoteIdInterface;
use PagBank\PaymentMagento\Api\Data\CreditCardBinInterface;
use PagBank\PaymentMagento\Api\Data\InstallmentSelectedInterface;
use PagBank\PaymentMagento\Api\InterestManagementInterface;

class PagBankCcApplyInstallments implements ResolverInterface
{

    /** @var MaskedQuoteIdToQuoteIdInterface */
    protected MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdToQuoteId;
    /** @var CreditCardBinInterface */
    protected CreditCardBinInterface $creditCardBin;
    /** @var PriceCurrencyInterface */
    protected PriceCurrencyInterface $priceCurrency;
    /** @var InterestManagementInterface */
    protected InterestManagementInterface $installmentsManagement;
    /** @var InstallmentSelectedInterface */
    protected InstallmentSelectedInterface $installmentSelected;

    public function __construct(
        InterestManagementInterface $installmentsManagement,
        MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdToQuoteId,
        CreditCardBinInterface $creditCardBin,
        InstallmentSelectedInterface $installmentSelected,
        PriceCurrencyInterface $priceCurrency
    )
    {
        $this->maskedQuoteIdToQuoteId = $maskedQuoteIdToQuoteId;
        $this->creditCardBin = $creditCardBin;
        $this->priceCurrency = $priceCurrency;
        $this->installmentsManagement = $installmentsManagement;
        $this->installmentSelected = $installmentSelected;
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
    ): bool
    {
        if (empty($args['cart_id'])) {
            throw new GraphQlInputException(__('Required parameter "cart_id" is missing'));
        }

        if (empty($args['credit_card_bin'])) {
            throw new GraphQlInputException(__('Required parameter "credit_card_bin" is missing'));
        }

        if (empty($args['installment_selected'])) {
            throw new GraphQlInputException(__('Required parameter "installment_selected" is missing'));
        }

        $cartId = $this->maskedQuoteIdToQuoteId->execute($args['cart_id']);
        $creditCardBin = $this->creditCardBin;
        $creditCardBin->setCreditCardBin($args['credit_card_bin']);
        $installmentSelected = $this->installmentSelected;
        $installmentSelected->setInstallmentSelected($args['installment_selected']);

        $installmentApplied = $this->installmentsManagement->generatePagBankInterest($cartId, $creditCardBin, $installmentSelected);


        return count($installmentApplied->getItems()) > 0;
    }

    /**
     * @param $amount
     * @return string
     */
    private function getFormatedPrice($amount)
    {
        return strip_tags($this->priceCurrency->format($amount));
    }
}
