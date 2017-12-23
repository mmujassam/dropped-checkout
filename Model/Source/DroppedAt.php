<?php
/**
 * Created by PhpStorm.
 * User: mmjsm
 * Date: 23/12/17
 * Time: 10:18 PM
 */

namespace Meem\DroppedCheckout\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class DroppedAt implements OptionSourceInterface
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray(){
        $options = ['value' => '', 'label' => ''];
        $stores = $this->toArray();
        foreach ($stores as $id => $name){
            $options[] = ['value' => $id, 'label' => $name];
        }

        return $options;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $options = [
            0 => "Cart",
            1 => "Shipping",
            2 => "Payment"
        ];

        foreach ($options as $key => $option){
            $options[$key] = __($option);
        }

        return $options;
    }


}