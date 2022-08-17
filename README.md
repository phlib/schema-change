# phlib/schema-change

[![Code Checks](https://img.shields.io/github/workflow/status/phlib/schema-change/CodeChecks?logo=github)](https://github.com/phlib/schema-change/actions/workflows/code-checks.yml)
[![Codecov](https://img.shields.io/codecov/c/github/phlib/schema-change.svg?logo=codecov)](https://codecov.io/gh/phlib/schema-change)
[![Latest Stable Version](https://img.shields.io/packagist/v/phlib/schema-change.svg?logo=packagist)](https://packagist.org/packages/phlib/schema-change)
[![Total Downloads](https://img.shields.io/packagist/dt/phlib/schema-change.svg?logo=packagist)](https://packagist.org/packages/phlib/schema-change)
![Licence](https://img.shields.io/github/license/phlib/schema-change.svg)

Library for performing MySQL DDL operations, using either simple SQL or Percona Tools Online Schema Change.

This library is designed to be able to be used in any existing migrations management tool.

## Usage

### Setup

```php
$db = new Phlib\Db\Adapter([
    'host' => '127.0.0.1',
    'port' => '3306',
    'username' => 'root',
    'password' => '',
    'dbname' => 'base_schema',
]);


$schemaChange = new \Phlib\SchemaChange\SchemaChange(
    $db,
    new \Phlib\SchemaChange\OnlineChangeRunner('/usr/local/bin/pt-online-schema-change')
);

$schemaChange->mapNames(new class implements \Phlib\SchemaChange\NameMapper {
    public function mapTableName(string $table): string
    {
        return 'prefix_' . $table;
    }
});
```


### Create a table

```php
$create = $schemaChange->create('widget');

$create->addColumn('id', 'int(11)')->unsigned()->notNull()->autoIncrement();
$create->addColumn('folder_id', 'int(11)')->notNull();
$create->addColumn('name', 'varchar(255)')->notNull();
$create->addColumn('data', 'text')->notNull()->defaultTo('');
$create->addColumn('create_ts', 'timestamp')->notNull()->defaultRaw('CURRENT_TIMESTAMP')

$create->primary('id');
$create->addIndex('folder_id', 'name')->unique();
$create->attribute('DEFAULT CHARSET', 'ascii');

$schemaChange->execute($create);
```

### Alter a table

```php
$alter = $schemaChange->alter('widget')
    ->onlineChange();

$alter->removeColumn('data');
$alter->addColumn('alias', 'varchar(100)')->nullable()->after('name');
$alter->addColumn('update_ts', 'timestamp')->after('create_ts')->notNull()
    ->defaultRaw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');

$alter->removeIndex('widget_folder_id_name_idx');

$schemaChange->execute($alter);
```

### Drop a table

```php
$drop = $schemaChange->drop('widget');
$schemaChange->execute($drop);
```
