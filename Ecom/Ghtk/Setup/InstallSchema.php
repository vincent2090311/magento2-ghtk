<?php

namespace Ecom\Ghtk\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Sales\Model\Order;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritDoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $connection = $setup->getConnection();
        $tables = ['quote_address', 'quote', 'sales_order', 'sales_invoice', 'sales_creditmemo'];
        foreach ($tables as $tbl) {
            $table = $setup->getTable($tbl);
            $connection->addColumn(
                $table, 
                'insurance', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false, 
                    'default' => '0.0000',
                    'comment' => 'Insurance Amount'
                ]
            );
            $connection->addColumn(
                $table, 
                'base_insurance', [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false, 
                    'default' => '0.0000',
                    'comment' =>'Base Insurance Amount'
                ]
            );
        }

        $connection
            ->addColumn($setup->getTable('sales_order'), 'tracking_label', [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length'=> 255,
                'visible' => false,
                'nullable' => true,
                'comment' => 'Tracking Label'
            ]);

        $setup->endSetup();
    }
}