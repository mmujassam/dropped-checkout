<?php
/**
 * Created by PhpStorm.
 * User: Meem
 * Date: 2/12/17
 * Time: 2:00 AM
 */

namespace Meem\DroppedCheckout\Block\Adminhtml\Quote;

use Magento\Backend\Block\Template;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Catalog\Helper\Product\Configuration;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\{Quote, ResourceModel\Quote as ResourceQuote};
use Magento\Customer\Model\Address\Config as AddressConfig;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Meem\DroppedCheckout\Model\{QuoteDataRepository as QuoteData, ResourceModel\History\CollectionFactory, Source\Status};
use Magento\User\Model\{UserFactory, ResourceModel\User as ResourceUser};


class Info extends Template
{
    /**
     * @var Quote
     */
    private $quote;
    /**
     * @var ResourceQuote
     */
    private $resourceQuote;
    /**
     * @var GroupRepositoryInterface
     */
    private $groupRepository;
    /**
     * @var AddressConfig
     */
    private $addressConfig;
    /**
     * @var EventManager
     */
    private $eventManager;
    /**
     * @var Status
     */
    private $status;
    /**
     * @var CollectionFactory
     */
    private $history;
    /**
     * @var QuoteData
     */
    private $quoteData;
    /**
     * @var UserFactory
     */
    private $user;
    /**
     * @var ResourceUser
     */
    private $resourceUser;
    /**
     * @var Configuration
     */
    private $configurationHelper;
    /**
     * @var ProductAttributeRepositoryInterface
     */
    private $productAttributeRepository;

    /**
     * Info constructor.
     * @param Template\Context $context
     * @param Quote $quote
     * @param ResourceQuote $resourceQuote
     * @param GroupRepositoryInterface $groupRepository
     * @param AddressConfig $addressConfig
     * @param EventManager $eventManager
     * @param Status $status
     * @param CollectionFactory $history
     * @param QuoteData $quoteData
     * @param UserFactory $user
     * @param ResourceUser $resourceUser
     * @param Configuration $configurationHelper
     * @param ProductAttributeRepositoryInterface $productAttributeRepository
     * @internal param array $data
     * @internal param Renderer $addressRenderer
     */
    function __construct(
        Template\Context $context,
        Quote $quote,
        ResourceQuote $resourceQuote,
        GroupRepositoryInterface $groupRepository,
        AddressConfig $addressConfig,
        EventManager $eventManager,
        Status $status,
        CollectionFactory $history,
        QuoteData $quoteData,
        UserFactory $user,
        ResourceUser $resourceUser,
        Configuration $configurationHelper,
        ProductAttributeRepositoryInterface $productAttributeRepository,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->quote = $quote;
        $this->resourceQuote = $resourceQuote;
        $this->groupRepository = $groupRepository;
        $this->addressConfig = $addressConfig;
        $this->eventManager = $eventManager;
        $this->status = $status;
        $this->history = $history->create();
        $this->quoteData = $quoteData;
        $this->user = $user->create();
        $this->resourceUser = $resourceUser;
        $this->configurationHelper = $configurationHelper;
        $this->productAttributeRepository = $productAttributeRepository;
    }

    public function getQuoteId()
    {
        return $this->getRequest()->getParam('entity_id');
    }

    public function getQuote()
    {
        $quote = $this->quote;
        $this->resourceQuote->load($quote, $this->getQuoteId());
        return $quote;
    }

    /**
     * Get object created at date affected with object store timezone
     *
     * @param mixed $store
     * @param string $createdAt
     * @return \DateTime
     */
    public function getCreatedAtStoreDate($store, $createdAt)
    {
        return $this->_localeDate->scopeDate($store, $createdAt, true);
    }

    /**
     * Get timezone for store
     *
     * @param mixed $store
     * @return string
     */
    public function getTimezoneForStore($store)
    {
        return $this->_localeDate->getConfigTimezone(
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store->getCode()
        );
    }

    public function getFormattedPrice($price)
    {
        $quoteCurrency = $this->getQuote()->getQuoteCurrencyCode();
        $price = $quoteCurrency." ".round($price, 2);
        return $price;
    }

    /**
     * Get object created at date
     *
     * @param string $createdAt
     * @return \DateTime
     */
    public function getQuoteAdminDate($createdAt)
    {
        return $this->_localeDate->date(new \DateTime($createdAt));
    }

    /**
     * Returns string with formatted address
     *
     * @param Quote\Address $address
     * @return null|string
     */
    public function getFormattedAddress(Quote\Address $address)
    {
        if ($address->getFirstname()){
            $formatType = $this->addressConfig->getFormatByCode('html');
            if (!$formatType || !$formatType->getRenderer()) {
                return null;
            }
            $this->eventManager->dispatch('customer_address_format', ['type' => $formatType, 'address' => $address]);
            return $formatType->getRenderer()->renderArray($address->getData());
        }
        return "No Address Found";
    }

    /**
     * Check if is single store mode
     *
     * @return bool
     */
    public function isSingleStoreMode()
    {
        return $this->_storeManager->isSingleStoreMode();
    }

    /**
     * Get order store name
     *
     * @return null|string
     */
    public function getOrderStoreName()
    {
        if ($this->getQuote()) {
            $storeId = $this->getQuote()->getStoreId();
            $store = $this->_storeManager->getStore($storeId);
            $name = [$store->getWebsite()->getName(), $store->getGroup()->getName(), $store->getName()];
            return implode('<br/>', $name);
        }
        return null;
    }

    /**
     * Whether Customer IP address should be displayed on sales documents
     *
     * @return bool
     */
    public function shouldDisplayCustomerIp()
    {
        return !$this->_scopeConfig->isSetFlag(
            'sales/general/hide_customer_ip',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getQuote()->getStoreId()
        );
    }

    /**
     * Get URL to edit the customer.
     *
     * @return string
     */
    public function getCustomerViewUrl()
    {
        if ($this->getQuote()->getCustomerIsGuest() || !$this->getQuote()->getCustomerId()) {
            return '';
        }

        return $this->getUrl('customer/index/edit', ['id' => $this->getQuote()->getCustomerId()]);
    }

    /**
     * @return string
     */
    public function getCustomerName()
    {
        $quote = $this->getQuote();
        if ($quote->getCustomerFirstname()) {
            $customerName = $quote->getCustomerFirstname() . ' ' . $quote->getCustomerLastname();
        } else {
            $customerName = (string)__('Guest');
        }
        return $customerName;
    }

    /**
     * Return name of the customer group.
     *
     * @return string
     */
    public function getCustomerGroupName()
    {
        if ($this->getQuote()) {
            $customerGroupId = $this->getQuote()->getCustomerGroupId();
            try {
                if ($customerGroupId !== null) {
                    return $this->groupRepository->getById($customerGroupId)->getCode();
                }
            } catch (NoSuchEntityException $e) {
                return '';
            }
        }

        return '';
    }

    public function getPaymentMethod()
    {
        try{
            return $this->getQuote()->getPayment()->getMethodInstance()->getTitle();
        }catch (\Exception$exception){
            return __('No Payment Details');
        }
    }

    /**
     * Get items html
     *
     * @return string
     */
    public function getItemsHtml()
    {
        return $this->getChildHtml('quote_items');
    }

    /**
     * Retrieve gift options container block html
     *
     * @return string
     */
    public function getGiftOptionsHtml()
    {
        return $this->getChildHtml('gift_options');
    }

    public function getDiscountData()
    {
        $quote = $this->getQuote();
        $discountAmount = $quote->getSubtotal() - $quote->getSubtotalWithDiscount();
        $subTotal = $quote->getSubtotal() > 0 ?$quote->getSubtotal():1;
        $discountPercent = ( $discountAmount / $subTotal) * 100;
        return ['discount_amount' => $this->getFormattedPrice($discountAmount), 'discount_percent' => round($discountPercent, 2)];
    }

    /**
     * @return array
     */
    public function getStatuses()
    {
        return $this->status->toArray();
    }

    /**
     * @return \Meem\DroppedCheckout\Model\QuoteData
     */
    public function getStatusByQuote()
    {
        $quoteId = $this->getQuoteId();
        $statusData = $this->quoteData->loadByColumnId($quoteId, 'quote_id');
        return $statusData;
    }

    public function getCommentUser($userId)
    {
        $user = $this->user;
        try{
            $this->resourceUser->load($user, $userId);
        }catch (\Exception $exception){
        }
        return $user;
    }

    public function getHistory()
    {
        $quoteData = $this->getStatusByQuote();
        $history = $this->history->addFieldToFilter('meem_quote_id', $quoteData->getId());
        $history->setOrder('created_at');
        return $history;
    }


    /**
     * @param $item
     * @return array
     */
    public function getOptions($item)
    {
        return $this->configurationHelper->getCustomOptions($item);
    }

    /**
     * @param $attrId
     * @return \Magento\Catalog\Api\Data\ProductAttributeInterface
     */
    public function getConfigOptions($attrId)
    {
        return $this->productAttributeRepository->get($attrId);
    }

}