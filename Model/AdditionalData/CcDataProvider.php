<?php

namespace Devgfnl\PagBankGraphQl\Model\AdditionalData;

use Magento\QuoteGraphQl\Model\Cart\Payment\AdditionalDataProviderInterface;
use PagBank\PaymentMagento\Gateway\Config\ConfigCc;


class CcDataProvider implements AdditionalDataProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getData(array $data): array
    {
        return $data[ConfigCc::METHOD] ?? [];
    }
}
