<?php
/**
 * Created by PhpStorm.
 * User: mmjsm
 * Date: 14/12/17
 * Time: 11:45 PM
 */

namespace Meem\DroppedCheckout\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\ResourceModel\Quote as ResourceQuote;
use Magento\Quote\Model\QuoteFactory;


class DroppedState extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var ResourceQuote
     */
    private $resourceQuote;
    /**
     * @var QuoteFactory
     */
    private $quote;

    /**
     * DroppedStep constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param ResourceQuote $resourceQuote
     * @param QuoteFactory $quote
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ResourceQuote $resourceQuote,
        QuoteFactory $quote,
        array $components = [],
        array $data = []
    )
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->resourceQuote = $resourceQuote;
        $this->quote = $quote;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item[$this->getData('name')])) {
                    if ($this->getDropState($item['entity_id'])){
                        $item[$this->getData('name')]
                            = $this->getDropState($item['entity_id']);
                    }
                }
            }
        }
        return $dataSource;
    }

    public function getDropState($quoteId)
    {
        $quote = $this->quote->create();
        $this->resourceQuote->load($quote, $quoteId);
        $payment = $this->isPaymentState($quote);
        if ($payment){
            return $payment;
        }

        $shipping = $this->isShippingState($quote);
        if ($shipping){
            return $shipping;
        }

        return "<span class='state-medium'>Cart</span>";
    }


    /**
     * @param Quote $quote
     * @return bool|string
     */
    public function isPaymentState($quote)
    {
        try{
            $payment = $quote->getPayment()->getMethodInstance();
            if ($payment->getTitle()) return "<span class='state-danger'>Billing &amp; Payment</span>";
            return false;
        }catch (\Exception $exception){
            return false;
        }
    }

    /**
     * @param Quote $quote
     * @return bool|string
     */
    public function isShippingState($quote)
    {
        try{
            $address = $quote->getShippingAddress();
            if ($address->getFirstname()) return "<span class='state-high'>Shipping</span>";
            return false;
        }catch (\Exception $exception){
            return false;
        }
    }

}