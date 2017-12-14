<?php
/**
 * Created by PhpStorm.
 * User: Meem
 * Date: 29/11/17
 * Time: 11:18 PM
 */

namespace Meem\DroppedCheckout\Ui\DataProvider;
use Magento\Framework\App\RequestInterface;
use Magento\Quote\Model\ResourceModel\Quote\CollectionFactory;
use Magento\Framework\App\ResourceConnection;

class QuoteListDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * QuoteListDataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param ResourceConnection $resourceConnection
     * @param RequestInterface $request
     * @param array $addFieldStrategies
     * @param array $addFilterStrategies
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        ResourceConnection $resourceConnection,
        RequestInterface $request,
        array $addFieldStrategies = [],
        array $addFilterStrategies = [],
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->resourceConnection = $resourceConnection;
        $salesQuotes = $this->getSalesOrderQuotes();
        $this->collection = $collectionFactory->create();
        if ($salesQuotes){
            $this->collection->addFieldToFilter('main_table.entity_id', ['nin' => $salesQuotes]);
        }
        $this->collection->addFieldToSelect(['created_at', 'updated_at', 'quote_currency_code', 'grand_total', 'items_qty', 'customer_firstname', 'customer_email', 'dropped_status' => 'is_active']);
        /*$this->collection->getSelect()->joinLeft(
            ['address' => $this->collection->getTable('quote_address')],
            "address.quote_id = main_table.entity_id",
            ['firstname', 'email']
        );*/
        //$this->collection->getSelect()->where("address.firstname IS NOT NULL AND address.address_type = 'shipping'");
        $this->collection->getSelect()->group("main_table.entity_id");
        $this->collection->getSelect()->joinLeft(
            ['quote_status' => $this->collection->getTable('meem_quote_data')],
            "quote_status.quote_id = main_table.entity_id",
            ["status_id"]
        );

    }


    /**
     * @return array
     */
    public function getSalesOrderQuotes()
    {
        $quotes = [];
        $connection = $this->getConnection();
        $sales = $connection->fetchAll("SELECT quote_id FROM sales_order WHERE quote_id != '' AND quote_id IS NOT NULL");
        foreach ($sales as $sale){
            $quotes[] = $sale['quote_id'];
        }

        return $quotes;
    }

    /**
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private function getConnection()
    {
        return $this->resourceConnection->getConnection(ResourceConnection::DEFAULT_CONNECTION);
    }
}