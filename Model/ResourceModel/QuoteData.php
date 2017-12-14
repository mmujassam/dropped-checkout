<?php
/**
 * Created by PhpStorm.
 * User: Meem
 * Date: 29/11/17
 * Time: 11:13 PM
 */

namespace Meem\DroppedCheckout\Model\ResourceModel;

class QuoteData extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('meem_quote_data', 'entity_id');
    }
}