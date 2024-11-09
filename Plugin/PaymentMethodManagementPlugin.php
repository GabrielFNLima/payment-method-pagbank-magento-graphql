<?php

namespace Devgfnl\PagBankGraphQl\Plugin;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Model\PaymentMethodManagement;
use PagBank\PaymentMagento\Api\Data\InstallmentSelectedInterface;

class PaymentMethodManagementPlugin
{
    protected CartRepositoryInterface $quoteRepository;

    public function __construct(
        CartRepositoryInterface $quoteRepository
    )
    {
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param PaymentMethodManagement $subject
     * @param callable $proceed
     * @param int $cartId
     * @param PaymentInterface $method
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function aroundSet(PaymentMethodManagement $subject, callable $proceed, $cartId, PaymentInterface $method): mixed
    {
        $quote = $this->quoteRepository->get($cartId);

        $quote->setData(InstallmentSelectedInterface::PAGBANK_INTEREST_AMOUNT, null);
        $quote->setData(InstallmentSelectedInterface::BASE_PAGBANK_INTEREST_AMOUNT, null);

        return $proceed($cartId, $method);
    }
}
