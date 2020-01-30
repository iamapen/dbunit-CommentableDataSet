<?php declare(strict_types=1);
namespace Iamapen\CommentableDataSet\Unittest;
use Iamapen\CommentableDataSet\DbUnit\DataSet\CommentableCsvDataSet;

class CommentableCsvDataSetTest extends \PHPUnit\Framework\TestCase
{
    protected $expectedDataSet;

    public function testCSVDataSet()
    {
        $table1MetaData = new \PHPUnit\DbUnit\DataSet\DefaultTableMetadata(
            'table1', ['table1_id', 'column1', 'column2', 'column3', 'column4']
        );
        $table2MetaData = new \PHPUnit\DbUnit\DataSet\DefaultTableMetadata(
            'table2', ['table2_id', 'column5', 'column6', 'column7', 'column8']
        );

        $table1 = new \PHPUnit\DbUnit\DataSet\DefaultTable($table1MetaData);
        $table2 = new \PHPUnit\DbUnit\DataSet\DefaultTable($table2MetaData);

        $table1->addRow([
            'table1_id' => 1,
            'column1' => 'tgfahgasdf',
            'column2' => 200,
            'column3' => 34.64,
            'column4' => 'yghkf;a  hahfg8ja h;'
        ]);
        $table1->addRow([
            'table1_id' => 2,
            'column1' => 'hk;afg',
            'column2' => 654,
            'column3' => 46.54,
            'column4' => '24rwehhads'
        ]);
        $table1->addRow([
            'table1_id' => 3,
            'column1' => 'ha;gyt',
            'column2' => 462,
            'column3' => 1654.4,
            'column4' => 'asfgklg'
        ]);

        $table2->addRow([
            'table2_id' => 1,
            'column5' => 'fhah',
            'column6' => 456,
            'column7' => 46.5,
            'column8' => 'fsdb, ghfdas'
        ]);
        $table2->addRow([
            'table2_id' => 2,
            'column5' => 'asdhfoih',
            'column6' => 654,
            'column7' => 'blah',
            'column8' => '43asd "fhgj" sfadh'
        ]);
        $table2->addRow([
            'table2_id' => 3,
            'column5' => 'ajsdlkfguitah',
            'column6' => 654,
            'column7' => 'blah',
            'column8' => 'thesethasdl
asdflkjsadf asdfsadfhl "adsf, halsdf" sadfhlasdf'
        ]);

        $expectedDataSet = new \PHPUnit\DbUnit\DataSet\DefaultDataSet([$table1, $table2]);

        $csvDataSet = new CommentableCsvDataSet();
        $csvDataSet->addTable('table1', dirname(__FILE__).'/../../fixtures/CsvDataSets/table1.csv');
        $csvDataSet->addTable('table2', dirname(__FILE__).'/../../fixtures/CsvDataSets/table2.csv');

        \PHPUnit\DbUnit\TestCase::assertDataSetsEqual($expectedDataSet, $csvDataSet);
    }

    function testCSVDataSet_commentable() {
        $table1MetaData = new \PHPUnit\DbUnit\DataSet\DefaultTableMetadata(
            'table1', ['column1', 'column2', 'column3', 'column4']
        );
        $table2MetaData = new \PHPUnit\DbUnit\DataSet\DefaultTableMetadata(
            'table2', ['column5', 'column6', 'column7', 'column8']
        );

        $table1 = new \PHPUnit\DbUnit\DataSet\DefaultTable($table1MetaData);
        $table2 = new \PHPUnit\DbUnit\DataSet\DefaultTable($table2MetaData);

        $table1->addRow([
            'column1' => 'tgfahgasdf',
            'column2' => 200,
            'column3' => 34.64,
            'column4' => 'yghkf;a  hahfg8ja h;'
        ]);
        $table1->addRow([
            'column1' => 'hk;afg',
            'column2' => 654,
            'column3' => 46.54,
            'column4' => '24rwehhads'
        ]);
        $table1->addRow([
            'column1' => 'ha;gyt',
            'column2' => 462,
            'column3' => 1654.4,
            'column4' => 'asfgklg'
        ]);

        $table2->addRow([
            'column5' => 'fhah',
            'column6' => 456,
            'column7' => 46.5,
            'column8' => 'fsdb, ghfdas'
        ]);
        $table2->addRow([
            'column5' => 'asdhfoih',
            'column6' => 654,
            'column7' => 'blah',
            'column8' => '43asd "fhgj" sfadh'
        ]);
        $table2->addRow([
            'column5' => 'ajsdlkfguitah',
            'column6' => 654,
            'column7' => 'blah',
            'column8' => 'thesethasdl
asdflkjsadf asdfsadfhl "adsf, halsdf" sadfhlasdf'
        ]);

        $expectedDataSet = new \PHPUnit\DbUnit\DataSet\DefaultDataSet([$table1, $table2]);

        $csvDataSet = new CommentableCsvDataSet();
        $csvDataSet->setIgnoreColumnCount(1);
        $csvDataSet->addTable('table1', dirname(__FILE__).'/../../fixtures/CsvDataSets/table1.csv');
        $csvDataSet->addTable('table2', dirname(__FILE__).'/../../fixtures/CsvDataSets/table2.csv');

        \PHPUnit\DbUnit\TestCase::assertDataSetsEqual($expectedDataSet, $csvDataSet);
    }
}
