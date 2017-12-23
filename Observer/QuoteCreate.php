<?php
/**
 * Created by PhpStorm.
 * User: mmjsm
 * Date: 13/12/17
 * Time: 2:59 AM
 */

namespace Meem\DroppedCheckout\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;
use Meem\DroppedCheckout\Model\QuoteDataRepository;

class QuoteCreate implements ObserverInterface
{
    /**
     * @var QuoteDataRepository
     */
    private $quoteDataRepository;

    /**
     * QuoteCreate constructor.
     * @param QuoteDataRepository $quoteDataRepository
     */
    function __construct(
        QuoteDataRepository $quoteDataRepository
    )
    {
        $this->quoteDataRepository = $quoteDataRepository;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var Quote $quoteItem*/
        try{
            $quoteItem = $observer->getEvent()->getQuote();
            $quoteId = $quoteItem->getId();
            $droppedAt = 0;
            if ($quoteItem->getPayment()->getMethod()){
                $droppedAt = 2;
            }elseif ($quoteItem->getShippingAddress()->getFirstname()){
                $droppedAt = 1;
            }

            $data = ['quote_id' => $quoteId, 'status_id' => 0, 'dropped_at' => $droppedAt];
            $this->quoteDataRepository->saveData($data);
        }catch (\Exception $exception){

        }
    }
}