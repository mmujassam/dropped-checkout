<?php
/**
 * Created by PhpStorm.
 * User: Meem
 * Date: 8/12/17
 * Time: 2:30 AM
 */

namespace Meem\DroppedCheckout\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Meem\DroppedCheckout\Model\{QuoteData, ResourceModel\QuoteData as ResourceQuote};

class QuoteDataRepository
{
    /**
     * @var \Meem\DroppedCheckout\Model\QuoteData
     */
    private $quoteData;
    /**
     * @var ResourceQuote
     */
    private $resourceQuote;

    /**
     * QuoteDataRepository constructor.
     * @param \Meem\DroppedCheckout\Model\QuoteData $quoteData
     * @param ResourceQuote $resourceQuote
     */
    function __construct(
        QuoteData $quoteData,
        ResourceQuote $resourceQuote
    )
    {
        $this->quoteData = $quoteData;
        $this->resourceQuote = $resourceQuote;
    }

    /**
     * @param $value
     * @param $column
     * @return \Meem\DroppedCheckout\Model\QuoteData
     */
    public function loadByColumnId($value, $column)
    {
        $quoteData = $this->quoteData;
        try{
            $this->resourceQuote->load($quoteData, $value, $column);
        }catch (NoSuchEntityException $exception){

        }catch (\Exception $exception){

        }
        return $quoteData;
    }

    public function saveData($data, $id = null)
    {
        $quoteData = $this->quoteData;
        $loadQuote = $this->loadByColumnId($data['quote_id'], 'quote_id');
        if ($loadQuote->getId()){
            $id = $loadQuote->getId();
        }
        $quoteData->setId($id);
        $quoteData->addData($data);
        try{
            $this->resourceQuote->save($quoteData);
        }catch (NoSuchEntityException $exception){
            //print_r($exception->getMessage());die;

        }catch (\Exception $exception){
           // print_r($exception->getMessage());die;
        }
        return $quoteData;
    }

}