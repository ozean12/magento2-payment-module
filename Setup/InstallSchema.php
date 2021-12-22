<?php

namespace Billiepayment\BilliePaymentMethod\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $tableName = $setup->getTable('billie_transaction_log');

        if ($setup->getConnection()->isTableExists($tableName) != true) {
            $table = $setup->getConnection()
                ->newTable($tableName)
                ->addColumn('entity_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                ], 'Log Id')
                ->addColumn('store_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [
                    'unsigned' => true,
                ], 'Store Id')
                ->addColumn('order_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [
                    'unsigned' => true,
                ], 'Order Id')
                ->addColumn('reference_id', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 64, [
                    'nullable' => false,
                ], 'Billie Reference Ids')
                ->addColumn('transaction_tstamp', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 64, [
                    'nullable' => true,
                ], 'transaction at')
                ->addColumn(
                    'created_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    ['default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                    'created at'
                )
                ->addColumn('customer_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [
                    'unsigned' => true,
                ], 'Customer Id')
                ->addColumn('mode', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 12, [
                    'nullable' => true,
                ], 'transaction mode')
                ->addColumn('billie_state', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 12, [
                    'nullable' => true,
                ], 'billie state')
                ->addColumn('request', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [
                    'nullable' => false,
                ], 'transaction at')
                ->addIndex(
                    $setup->getIdxName('billie_transaction_log', ['customer_id']),
                    ['customer_id']
                )
                ->addForeignKey(
                    $setup->getFkName('billie_transaction_log', 'customer_id', 'customer/entity', 'entity_id'),
                    'customer_id',
                    $setup->getTable('customer_entity'),
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL
                );
            $setup->getConnection()->createTable($table);
        }

        $setup->endSetup();
    }
}
