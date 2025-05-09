<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductOptionPrice\Setup;

use Exception;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class InstallSchema implements InstallSchemaInterface
{
    private static $tables = [
        'quote_item',
        'sales_order_item'
    ];

    private static $columns = [
        'options_price'                   => 'Options price',
        'base_options_price'              => 'Base options price',
        'row_options_price'               => 'Row options price',
        'base_row_options_price'          => 'Base row options price',
        'options_price_incl_tax'          => 'Options price incl tax',
        'base_options_price_incl_tax'     => 'Base options price incl tax',
        'row_options_price_incl_tax'      => 'Row options price incl tax',
        'base_row_options_price_incl_tax' => 'Base row options price incl tax'
    ];

    /**
     * @throws Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $connection = $setup->getConnection();

        foreach (self::$tables as $tableName) {
            $tableName = $connection->getTableName($tableName);

            foreach (static::$columns as $column => $comment) {
                if (! $connection->tableColumnExists(
                    $tableName,
                    $column
                )) {
                    $connection->addColumn(
                        $tableName,
                        $column,
                        $this->getColumnDefinition($comment)
                    );
                }
            }
        }

        $setup->endSetup();
    }

    private function getColumnDefinition(string $comment): array
    {
        return [
            'type'     => Table::TYPE_DECIMAL,
            'length'   => '20,4',
            'nullable' => true,
            'default'  => '0.0000',
            'comment'  => $comment
        ];
    }
}
