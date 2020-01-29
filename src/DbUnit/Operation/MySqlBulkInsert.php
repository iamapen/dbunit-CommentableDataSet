<?php declare(strict_types=1);
namespace Iamapen\CommentableDataSet\DbUnit\Operation;

use PHPUnit\DbUnit\Database\Connection;
use PHPUnit\DbUnit\DataSet\IDataSet;
use PHPUnit\DbUnit\DataSet\ITable;
use PHPUnit\DbUnit\DataSet\ITableMetadata;

/**
 * Bulk Insert (only MySQL)
 *
 * いずれ、バルクのチャンクサイズを指定できるようにしたい
 * @version 0.0.1
 */
class MySqlBulkInsert implements \PHPUnit\DbUnit\Operation\Operation
{
    protected $operationName = 'MYSQL_BULK_INSERT';

    protected function buildOperationQuery(ITableMetadata $databaseTableMetaData, ITable $table, Connection $connection, $rowCount)
    {
        $columnCount = \count($table->getTableMetaData()->getColumns());

        if ($columnCount > 0) {
            $placeHolders = \implode(', ', \array_fill(0, $columnCount, '?'));

            $columns = '';
            foreach ($table->getTableMetaData()->getColumns() as $column) {
                $columns .= $connection->quoteSchemaObject($column) . ', ';
            }

            $columns = \substr($columns, 0, -2);

            $query = "
                INSERT INTO {$connection->quoteSchemaObject($table->getTableMetaData()->getTableName())}
                ({$columns})
                VALUES
                ({$placeHolders})
            ";

            $bulk = '';
            for ($i=1; $i<$rowCount; $i++) {
                $bulk .= ",({$placeHolders})\n                ";
            }
            $query .= $bulk;

            return $query;
        }

        return false;
    }

    protected function buildOperationArguments(ITableMetadata $databaseTableMetaData, ITable $table, $row)
    {
        $args = [];
        foreach ($table->getTableMetaData()->getColumns() as $columnName) {
            $args[] = $table->getValue($row, $columnName);
        }

        return $args;
    }

    protected function disablePrimaryKeys(ITableMetadata $databaseTableMetaData, ITable $table, Connection $connection)
    {
        if (\count($databaseTableMetaData->getPrimaryKeys())) {
            return true;
        }

        return false;
    }

    /**
     * @param Connection $connection
     * @param IDataSet   $dataSet
     */
    public function execute(Connection $connection, IDataSet $dataSet): void
    {
        $databaseDataSet = $connection->createDataSet();

        $dsIterator = $dataSet->getIterator();

        foreach ($dsIterator as $table) {
            $rowCount = $table->getRowCount();

            if ($rowCount == 0) {
                continue;
            }

            /* @var $table ITable */
            $databaseTableMetaData = $databaseDataSet->getTableMetaData($table->getTableMetaData()->getTableName());

            $disablePrimaryKeys    = $this->disablePrimaryKeys($databaseTableMetaData, $table, $connection);

            if ($disablePrimaryKeys) {
                $connection->disablePrimaryKeys($databaseTableMetaData->getTableName());
            }


            $bulkI = 0;
            while ($bulkI < $rowCount) {
                $chunkLen = 0;
                $args = [];
                for ($i=0; $i<100; $i++) {
                    $rowNum = $bulkI + $i;
                    if ($rowNum >= $rowCount) {
                        break;
                    }
                    $args = array_merge($args, $this->buildOperationArguments($databaseTableMetaData, $table, $rowNum));
                    $chunkLen++;
                }

                $query = $this->buildOperationQuery($databaseTableMetaData, $table, $connection, $chunkLen);
                if ($query === false) {
                    if ($table->getRowCount() > 0) {
                        throw new \PHPUnit\DbUnit\Operation\Exception($this->operationName, '', [], $table, 'Rows requested for insert, but no columns provided!');
                    }
                    continue;
                }

                if ($disablePrimaryKeys) {
                    $connection->disablePrimaryKeys($databaseTableMetaData->getTableName());
                }

                try {
                    $statement = $connection->getConnection()->prepare($query);
                    $statement->execute($args);
                } catch (\Exception $e) {
                    throw new \PHPUnit\DbUnit\Operation\Exception(
                        $this->operationName, $query, $args, $table, $e->getMessage()
                    );
                }

                $bulkI += $i;
            }

            if ($disablePrimaryKeys) {
                $connection->enablePrimaryKeys($databaseTableMetaData->getTableName());
            }
        }
    }
}
