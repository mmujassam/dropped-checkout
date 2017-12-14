<?php
/**
 * Created by PhpStorm.
 * User: Meem
 * Date: 8/12/17
 * Time: 2:51 AM
 */

namespace Meem\DroppedCheckout\Model;

use Magento\Framework\Exception\LocalizedException;
use Meem\DroppedCheckout\Model\{History, ResourceModel\History as ResourceHistory};
class StatusHistoryRepository
{
    /**
     * @var \Meem\DroppedCheckout\Model\History
     */
    private $history;
    /**
     * @var ResourceHistory
     */
    private $resourceHistory;

    /**
     * StatusHistoryRepository constructor.
     * @param \Meem\DroppedCheckout\Model\History $history
     * @param ResourceHistory $resourceHistory
     */
    function __construct(
        History $history,
        ResourceHistory $resourceHistory
    )
    {
        $this->history = $history;
        $this->resourceHistory = $resourceHistory;
    }

    public function saveHistory($data, $id = null)
    {
        $history = $this->history;
        $history->setData($data);
        if ($id){
            $history->setId($id);
        }
        try{
            $this->resourceHistory->save($history);
        }catch (LocalizedException $exception){
            // to handle the exception not needed now
        }catch (\Exception $exception){
            // to handle the exception not needed now
        }
        return $history;
    }

}