<?php declare(strict_types=1);
namespace Iamapen\CommentableDataSet\Unittest;
use Iamapen\CommentableDataSet\DbUnit\DataSet\ExcelCsvDataSet;

class ExcelCsvDataSetTest extends \PHPUnit\Framework\TestCase
{
    protected $expectedDataSet;

    protected $orgLcCtype;

    protected function setUp(): void
    {
        parent::setUp();

        // ライブラリ内に実装するか迷ったが、Windows+PHP7でfgetcsv() を使う箇所すべての問題であること、
        // ライブラリ内で勝手にlocaleを変える挙動の是非から、アプリレイヤに任せることにした。
        if (version_compare(PHP_VERSION, '5.3.0', '>') && 0 === strpos(PHP_OS, 'WIN')) {
            $this->orgLcCtype = setlocale(LC_CTYPE, '0');
            if (0 === strpos(PHP_OS, 'WIN')) {
                setlocale(LC_CTYPE, 'C');
            }
        }
    }

    protected function tearDown(): void
    {
        if (version_compare(PHP_VERSION, '5.3.0', '>') && 0 === strpos(PHP_OS, 'WIN')) {
            setlocale(LC_CTYPE, $this->orgLcCtype);
        }

        parent::tearDown();
    }

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

        $csvDataSet = new \PHPUnit\DbUnit\DataSet\CsvDataSet();
        $csvDataSet->addTable('table1', dirname(__FILE__).'/../../fixtures/CsvDataSets/table1.csv');
        $csvDataSet->addTable('table2', dirname(__FILE__).'/../../fixtures/CsvDataSets/table2.csv');

        \PHPUnit\DbUnit\TestCase::assertDataSetsEqual($expectedDataSet, $csvDataSet);
    }

    function testHoge() {
        $dataSet = new ExcelCsvDataSet();
        $dataSet->addTable('table1', dirname(__FILE__).'/../../fixtures/excelCsv.csv');

        $table1MetaData = new \PHPUnit\DbUnit\DataSet\DefaultTableMetadata(
            'table1', ['列1', '列2', '列3']
        );
        $table1 = new \PHPUnit\DbUnit\DataSet\DefaultTable($table1MetaData);
        $table1->addRow([
            'table1_id' => 1,
            '列1' => 'あ',
            '列2' => 'い',
            '列3' => '髙',
        ]);
        $table1->addRow([
            'table1_id' => 1,
            '列1' => 'ア',
            '列2' => 'イ',
            '列3' => 'ウ',
        ]);
        $expectedDataSet = new \PHPUnit\DbUnit\DataSet\DefaultDataSet([$table1]);

        \PHPUnit\DbUnit\TestCase::assertDataSetsEqual($expectedDataSet, $dataSet);
    }
}
