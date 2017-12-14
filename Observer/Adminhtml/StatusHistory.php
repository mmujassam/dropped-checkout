<?php
/**
 * Created by PhpStorm.
 * User: Meem
 * Date: 8/12/17
 * Time: 2:51 AM
 */

namespace Meem\DroppedCheckout\Observer\Adminhtml;

use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Meem\DroppedCheckout\Model\StatusHistoryRepository;

class StatusHistory implements ObserverInterface
{
    /**
     * @var StatusHistoryRepository
     */
    private $historyRepository;
    /**
     * @var Session
     */
    private $authSession;

    /**
     * StatusHistory constructor.
     * @param StatusHistoryRepository $historyRepository
     * @param Session $authSession
     */
    function __construct(
        StatusHistoryRepository $historyRepository,
        Session $authSession
    )
    {
        $this->historyRepository = $historyRepository;
        $this->authSession = $authSession;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $event = $observer->getEvent();
        $statusData = $event->getStatusData();
        $comment = $event->getComment();
        $user = $this->authSession->getUser();
        $data = ['meem_quote_id' => $statusData->getId(), 'status_id' => $statusData->getStatusId(), 'comment' => $comment, 'user_id' => $user->getId()];
        $this->historyRepository->saveHistory($data);
    }
}