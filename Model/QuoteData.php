<?php
/**
 * Created by PhpStorm.
 * User: Meem
 * Date: 29/11/17
 * Time: 11:13 PM
 */

namespace Meem\DroppedCheckout\Model;

class QuoteData extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(\Meem\DroppedCheckout\Model\ResourceModel\QuoteData::class);
    }
}