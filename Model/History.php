<?php
/**
 * Created by PhpStorm.
 * User: Meem
 * Date: 8/12/17
 * Time: 12:21 AM
 */

namespace Meem\DroppedCheckout\Model;


class History extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(\Meem\DroppedCheckout\Model\ResourceModel\History::class);
    }
}