<?php
/**
 * テストデータを一気に投入するサンプル
 *
 * dbunit単体で利用可。
 */

 /*
CREATE DATABASE commentable_ds DEFAULT CHARACTER SET 'utf8mb4';
CREATE USER IF NOT EXISTS 'dbuser'@'localhost' IDENTIFIED BY 'dbpass';
GRANT ALL ON commentable_ds.* TO 'dbuser'@'localhost';
use commentable_ds;

CREATE TABLE users(
  id int AUTO_INCREMENT,
  last_name varchar(100),
  first_name varchar(100),
  nickname varchar(100),
  PRIMARY KEY(id)
);
*/

use Iamapen\CommentableDataSet\DbUnit\DataSet\CommentableCsvDataSet;
use Iamapen\CommentableDataSet\DbUnit\Operation\MySqlBulkInsert;

require __DIR__ . '/vendor/autoload.php';

// fgetcsv() on Windows + PHP7 の都合
if (version_compare(PHP_VERSION, '5.3.0', '>') && 0 === strpos(PHP_OS, 'WIN')) {
    if (0 === strpos(PHP_OS, 'WIN')) {
        setlocale(LC_CTYPE, 'C');
    }
}

$host = '127.0.0.1';
$port = '3306';
$user = 'dbuser';
$pw = 'dbpass';
$dbname = 'commentable_ds';

$pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $user, $pw);
$pdo->query('SET SESSION FOREIGN_KEY_CHECKS=0;');

// CSV を DataSet として読み込み
$con = new \PHPUnit\DbUnit\Database\DefaultConnection($pdo);
$csvDs = new CommentableCsvDataSet();
$csvDs->setIgnoreColumnCount(1);    // 1列目はコメントとする
$csvDs->addTable('users', __DIR__.'/unittest/fixtures/CsvDataSets/users.csv');

// replae "<null>" to null
$ds = new \PHPUnit\DbUnit\DataSet\ReplacementDataSet($csvDs);
$ds->addFullReplacement('<null>', null);

// TRUNCATE -> INSERT の例
\PHPUnit\DbUnit\Operation\Factory::CLEAN_INSERT()->execute($con, $ds);

// TRUNCATE -> BULK INSERT の例
\PHPUnit\DbUnit\Operation\Factory::TRUNCATE()->execute($con, $ds);
(new MySqlBulkInsert())->execute($con, $ds);

