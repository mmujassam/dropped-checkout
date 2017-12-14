<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Meem\DroppedCheckout\Block\Adminhtml\Quote\View;

use Magento\Backend\Block\Template\Context;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Customer\Model\Address\Config as AddressConfig;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\ResourceModel\Quote as ResourceQuote;
use Meem\DroppedCheckout\Block\Adminhtml\Quote\Info;
use Meem\DroppedCheckout\Model\{QuoteDataRepository as QuoteData, ResourceModel\History\CollectionFactory, Source\Status};
use Magento\User\Model\{UserFactory, ResourceModel\User as ResourceUser};
/**
 * Adminhtml order items grid
 */
class Items extends Info
{
    /**
     * Items constructor.
     * @param Context $context
     * @param Quote $quote
     * @param ResourceQuote $resourceQuote
     * @param GroupRepositoryInterface $groupRepository
     * @param AddressConfig $addressConfig
     * @param EventManager $eventManager
     * @param Status $status
     * @param QuoteData $quoteData
     * @param CollectionFactory $history
     * @param UserFactory $user
     * @param ResourceUser $resourceUser
     * @param array $data
     */
    function __construct(
        Context $context,
        Quote $quote,
        ResourceQuote $resourceQuote,
        GroupRepositoryInterface $groupRepository,
        AddressConfig $addressConfig,
        EventManager $eventManager,
        Status $status,
        QuoteData $quoteData,
        CollectionFactory $history,
        UserFactory $user,
        ResourceUser $resourceUser,
        array $data = []
    )
    {
        parent::__construct($context, $quote,
            $resourceQuote, $groupRepository, $addressConfig,
            $eventManager, $status, $history, $quoteData, $user,
            $resourceUser, $data
        );
    }

    public function getItemsCollection()
    {
        return $this->getQuote()->getItemsCollection();
    }

    public function getAllVisibleItems()
    {
        return $this->getQuote()->getAllVisibleItems();
    }

}
