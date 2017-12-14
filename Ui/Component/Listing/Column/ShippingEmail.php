<?php
/**
 * Created by PhpStorm.
 * User: mmjsm
 * Date: 13/12/17
 * Time: 1:34 AM
 */

namespace Meem\DroppedCheckout\Ui\Component\Listing\Column;


use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\App\ResourceConnection;

class ShippingEmail extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * ShippingName constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param ResourceConnection $resourceConnection
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ResourceConnection $resourceConnection,
        array $components = [],
        array $data = []
    )
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item[$this->getData('name')])) {
                    if ($this->getQuoteAddress($item['entity_id'])){
                        $item[$this->getData('name')]
                            = $this->getQuoteAddress($item['entity_id']);
                    }
                }
            }
        }
        return $dataSource;
    }

    public function getQuoteAddress($id)
    {
        $connection = $this->getConnection();
        $email = $connection->fetchOne("SELECT email FROM quote_address WHERE quote_id = $id AND address_type = 'shipping'");
        return $email;
    }

    private function getConnection()
    {
        return $this->resourceConnection->getConnection(ResourceConnection::DEFAULT_CONNECTION);
    }
}