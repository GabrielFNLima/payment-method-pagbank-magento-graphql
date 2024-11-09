<?php

namespace Devgfnl\PagBankGraphQl\Model\Resolver;

use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\UrlInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use PagBank\PaymentMagento\Gateway\Config\ConfigPix;

class PagBankPixOrderSuccess implements ResolverInterface
{

    /** @var ConfigPix */
    protected ConfigPix $config;
    /** @var OrderRepositoryInterface */
    protected OrderRepositoryInterface $orderRepository;
    /** @var StoreManagerInterface */
    protected StoreManagerInterface $storeManager;
    /** @var SearchCriteriaBuilderFactory */
    protected SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory;
    /** @var UrlInterface */
    protected UrlInterface $_urlBuilder;

    public function __construct(
        ConfigPix $config,
        OrderRepositoryInterface $orderRepository,
        StoreManagerInterface $storeManager,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        UrlInterface $urlBuilder
    )
    {

        $this->orderRepository = $orderRepository;
        $this->storeManager = $storeManager;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->_urlBuilder = $urlBuilder;
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!isset($args['input']['orderId'])) {
            throw new GraphQlInputException(__('"orderId" should be specified'));
        }

        $order = $this->getOrder($args['input']['orderId']);
        $payment = $order->getPayment();

        return $this->formatter($payment);
    }

    /**
     * @param OrderPaymentInterface $payment
     * @return array
     */
    private function formatter(OrderPaymentInterface $payment): array
    {

        return [
            ConfigPix::METHOD => $this->getPixInformation($payment)
        ];
    }

    /**
     * @param OrderPaymentInterface $payment
     * @return array|null
     */
    private function getPixInformation(OrderPaymentInterface $payment)
    {
        if ($payment->getMethod() === ConfigPix::METHOD) {
            $qrCodeImage = $payment->getAdditionalInformation("qr_code_image");
            return [
                "title" => $this->config->getTitle() ?? null,
                "code" => $payment->getMethod() ?? null,
                "qr_code" => $payment->getAdditionalInformation("qr_code") ?? null,
                "qr_code_image" => $this->getImageQrCode($qrCodeImage) ?? null,
                "expiration_date" => $payment->getAdditionalInformation("expiration_date") ?? null,
                "payer_name" => $payment->getAdditionalInformation("payer_name") ?? null,
                "payer_tax_id" => $payment->getAdditionalInformation("payer_tax_id") ?? null,
            ];
        }
        return null;
    }

    /**
     * Retrieve order based on order number
     *
     * @param string $number
     * @return OrderInterface
     * @throws GraphQlNoSuchEntityException
     * @throws NoSuchEntityException
     */
    private function getOrder(string $number): OrderInterface
    {
        $searchCriteria = $this->searchCriteriaBuilderFactory->create()
            ->addFilter('increment_id', $number)
            ->addFilter('store_id', $this->storeManager->getStore()->getId())
            ->create();

        $orders = $this->orderRepository->getList($searchCriteria)->getItems();
        if (empty($orders)) {
            throw new GraphQlNoSuchEntityException(__('We couldn\'t locate an order with the information provided.'));
        }

        return reset($orders);
    }

    /**
     * Get Url to Image Qr Code.
     *
     * @param string|string[] $qrCode
     *
     * @return string
     */
    private function getImageQrCode($qrCode): string
    {
        return $this->_urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]) . $qrCode;
    }
}
