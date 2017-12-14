<?php
/**
 * Copyright © 2015 Measurement. All rights reserved.
 */

namespace Meem\DroppedCheckout\Model\ResourceModel\History;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Meem\DroppedCheckout\Model\History::class, \Meem\DroppedCheckout\Model\ResourceModel\History::class);
    }
}
