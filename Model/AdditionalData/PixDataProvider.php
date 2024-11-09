<?php

namespace Devgfnl\PagBankGraphQl\Model\AdditionalData;

use Magento\QuoteGraphQl\Model\Cart\Payment\AdditionalDataProviderInterface;
use PagBank\PaymentMagento\Gateway\Config\ConfigPix;


class PixDataProvider implements AdditionalDataProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getData(array $data): array
    {
        return $data[ConfigPix::METHOD] ?? [];
    }
}
