dbunit Commentable DataSet
===============

phpunit/dbunit 用の DataSet や Operation の詰め合わせ。  
[iamapen/excel-friendly-data-set](https://packagist.org/packages/iamapen/excel-friendly-data-set) の後継。

DBレコードを「1列目がコメントのCSV」で表現するための CsvDataSet など。  


Install
=======

UT用なので `require --dev` になる。

```bash
composer require --dev iamapen/commentable-data-set
```

手動で composer.json に書く場合
```
require-dev: {
  "iamapen/commentable-data-set ": "^2.0"
}
```

Usage
=====

## DataSet

### DataSet/CommentableDataSet
CSVの左n列をコメント列扱いとして、無視する(取り込まない) 機能を持つ CsvDataSet。  
テストデータに対してのコメント列として使える。

```php
$ds = new \Iamapen\CommentableDataSet\DbUnit\DataSet\CommentableCsvDataSet();
$ds->addTable('users', '/PATH/TO/users.csv');
$ds->setIgnoreColumnCount(1);    // 1列目をコメント列とする
```
CSVの例
```csv
最初の列はコメント,id,user_name
男性ユーザ,1,taro soramame
女性女性ユーザ,2,arare norimaki
1ヶ月以上ログインしてないユーザ,3,akane kimidori
削除されるユーザ,4,gajira norimaki
```

もちろん他の DataSet との比較もできる
```php
class XxxTest extends \PHPUnit\DbUnit\TestCase {
  function testXXX() {
      $this->assertDataSetsEqual($ds1, $ds2);
  }
}
```


### DataSet/ExcelCsvDataSet (deprecated)

最初に作成した DataSet。  
Excelで扱いやすい UTF-16-LE(+BOM) のCSVを UTF-8 に変換しながら取り込むもの。  
現在は出番はないと思われる。

2007年作成当時は UTF-8 の CSVを満足に編集できるソフトが少ないという経緯で作られた。  
しかし現在は LibreOffice-Calc 等のエディタでUTF8のCSVを容易に編集できるため、
わざわざExcel用に UTF-16 で保存しておくこともない。


以下、旧 README のまま。

> Excelでは UTF-8 のcsvをまともに編集(とくに保存)ができないが、
UTF-16-LE(+BOM) にすれば「D&Dで開く」「Ctrl+S」で保存など比較的自然に編集でき、
テストデータにUnicode文字も使える。
> 
> 代わりにテストコード内で UTF-16 -> UTF-8 変換が必要になるので、これを行う。
> 
> 
> なおExcelからは"Unicode", sakuraエディタでは"Unicode", vimでは"utf16le", mbstringでは"UTF-16"で扱える。
> 新規CSV作成時はテキストエディタでUTF-16のファイルを作成してそれをExcelにD&Dするのが楽そう。
> 
> xxTest.php
> ```php
> $ds = new Iamapen\ExcelFriendlyDataSet\Database\DataSet\ExcelCsvDataSet();
> ```


## Operation

### Operation/MySqlBulkInsert
`PHPUnit_Extensions_Database_Operation_Insert` のバルクインサート版。  
MySQL専用。(一応SQLiteでも動く)

あまりに入力CSVが大きいと、`max_allowed_packet` の制限にかかる可能性がある。これは課題。

```php
use Iamapen\CommentableDataSet\DbUnit\DataSet\CommentableCsvDataSet;
use Iamapen\CommentableDataSet\DbUnit\Operation\MySqlBulkInsert;

// DataSet
$ds = new CommentableCsvDataSet();
$ds->addTable('/PATH/TO/CSV');

// 実行 (TRUNCATE -> BULK INSERT)
$con = new \PHPUnit\DbUnit\Database\DefaultConnection($pdo);
PHPUnit_Extensions_Database_Operation_Factory::TRUNCATE()->execute($con, $ds);
(new MySqlBulkInsert())->execute($con, $ds);
```


# 注意点・課題
- Operation/MySqlBulkInsert
  - あまりに入力CSVが大きいと `max_allowed_packet` の制限にかかる可能性がある。

- DataSet/ExcelCsvDataSet
  - もはや非推奨。  
    UTF-8 で保存して、UTF-8 対応のエディタで編集するのがよい。例えば LibreOffice の Calc でできる。  
    Excelでやろうというのは、まともなエディタが存在しなかった時代の古いアプローチ。
  - 文字コード変換をストリームでやったほうがいい

- 正式なプロダクトでの運用実績が少ないため、品質は趣味レベル。


# バージョン
|             | php        | phpunit  | dbunit |
|-------------|------------|----------|--------|
| 4.0.x       | 7.1+       | 7.x      | 4.x    |
| 3.0.x       | 7.0+       | 6.x      | 3.x    |
| 2.0.x       | 5.4+, 7.0+ | 4.x, 5.x | 2.x    |
| 1.1.x       | 5.3+       | 3.x, 4.x | 1.x    |
| 1.0.x (EOL) | 5.3+       | 3.x      | 1.x    |

