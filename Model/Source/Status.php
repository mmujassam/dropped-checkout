<?php
/**
 * Created by PhpStorm.
 * User: Meem
 * Date: 8/12/17
 * Time: 12:07 AM
 */

namespace Meem\DroppedCheckout\Model\Source;


use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
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
            0 => "Default",
            1 => "Not Interested",
            2 => "Converted",
            3 => "Closed",
            4 => "Call Back",
            5 => "Others"
        ];

        foreach ($options as $key => $option){
            $options[$key] = __($option);
        }

        return $options;
    }


}