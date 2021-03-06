<?php declare(strict_types=1);
namespace Iamapen\CommentableDataSet\Unittest;
use Iamapen\CommentableDataSet\DbUnit\Operation\MySqlBulkInsert;

require_once dirname(dirname(__FILE__)) . "/../fixtures" . '/DatabaseTestUtility.php';

/**
 * @since      File available since Release 1.0.0
 */
class Extensions_Database_Operation_OperationsTest extends \PHPUnit\DbUnit\TestCase
{
    protected function setUp(): void
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('PDO/SQLite is required to run this test.');
        }

        parent::setUp();
    }

    public function getConnection()
    {
        return new \PHPUnit\DbUnit\Database\DefaultConnection(\DBUnitTestUtility::getSQLiteMemoryDB(), 'sqlite');
    }

    public function getDataSet()
    {
        return new \PHPUnit\DbUnit\DataSet\FlatXmlDataSet(
            dirname(__FILE__).'/../../fixtures/XmlDataSets/OperationsTestFixture.xml'
        );
    }

    public function testMysqlBulkInsert() {
        $operation = new MySqlBulkInsert();
        $operation->execute(
            $this->getConnection(),
            new \PHPUnit\DbUnit\DataSet\FlatXmlDataSet(dirname(__FILE__).'/../../fixtures/XmlDataSets/InsertOperationTest.xml')
        );
        $this->assertDataSetsEqual(
            new \PHPUnit\DbUnit\DataSet\FlatXmlDataSet(dirname(__FILE__).'/../../fixtures/XmlDataSets/InsertOperationResult.xml'),
            $this->getConnection()->createDataSet()
        );
    }
}
