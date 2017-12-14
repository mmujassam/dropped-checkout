<?php
/**
 * Created by PhpStorm.
 * User: Meem
 * Date: 8/12/17
 * Time: 1:01 AM
 */

namespace Meem\DroppedCheckout\Controller\Adminhtml\Update;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;
use Meem\DroppedCheckout\Model\{QuoteData, ResourceModel\QuoteData as ResourceQuote};
use Magento\Framework\Controller\Result\JsonFactory;

class Status extends Action
{
    /**
     * @var QuoteData
     */
    private $quoteData;
    /**
     * @var ResourceQuote
     */
    private $resourceQuote;
    /**
     * @var JsonFactory
     */
    private $jsonFactory;

    /**
     * @param Action\Context $context
     * @param QuoteData $quoteData
     * @param ResourceQuote $resourceQuote
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Action\Context $context,
        QuoteData $quoteData,
        ResourceQuote $resourceQuote,
        JsonFactory $jsonFactory
    )
    {
        parent::__construct($context);
        $this->quoteData = $quoteData;
        $this->resourceQuote = $resourceQuote;
        $this->jsonFactory = $jsonFactory;
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $isAjax = $this->getRequest()->isXmlHttpRequest();
        $response = ['status' => false, 'message' => __("Please Fill the required fields")];
        if ($params['quote_id'] && $params['status_id'] && $isAjax){
            $quoteData = $this->quoteData;
            $quoteData->setData($params);
            if ($params['data_id']){
                $quoteData->setId($params['data_id']);
            }
            try{
                $this->resourceQuote->save($quoteData);
                $this->_eventManager->dispatch('status_update_save_after', ['status_data' => $quoteData, 'comment' => $params['comment']]);
                $response = ['status' => true, 'message' => __("Status updated successfully")];
            }catch (LocalizedException $exception){
                $response = ['status' => false, 'message' => $exception->getMessage()];
            }catch (\Exception $exception){
                $response = ['status' => false, 'message' => __($exception->getMessage())];
            }
        }
        $data = $this->jsonFactory->create();
        return $data->setData($response);
    }
}