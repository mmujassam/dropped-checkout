<?php
/**
 * Created by PhpStorm.
 * User: mmjsm
 * Date: 23/12/17
 * Time: 10:21 PM
 */

namespace Meem\DroppedCheckout\Setup;


use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * Upgrades DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $connection = $setup->getConnection();
        if (!$connection->tableColumnExists('meem_quote_data', 'dropped_at')){
            $connection->addColumn(
                'meem_quote_data',
                'dropped_at',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'nullable' => false,
                    'comment' => 'dropped_at'
                ]
            );
        }
    }
}