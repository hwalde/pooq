# POOQ - PHP Object Oriented Querying

POOQ generates PHP code from your database and lets you build type safe SQL queries through its fluent API. 

[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.3-8892BF.svg?style=flat-square)](https://php.net/)
<br><br>
WARNING: POOQ is currently in **alpha** stage! Expect API changes. Expect bugs! Do not use it in production!
 
## Benefits

- It creates PHP code from your database.
- It can map the result of queries to objects.
- It allows you to write type-safe sql-queries. 
- You won't run into typical ORM problems when it comes to more complicated queries (unexpected behavior or performance problems which need deep insight knowledge into your ORM software to solve)

## Requirements

- PDO with MySQL (and should work with MariaDB as well)

## Installation

I recommend to use [Composer](https://getcomposer.org/) for installation:
```bash
composer require hwalde/pooq
```

Then use [Composers autoload functionality](https://getcomposer.org/doc/01-basic-usage.md#autoloading) to include POOQ into your projects:
```php
require __DIR__ . '/vendor/autoload.php';
```

## Example usage

### Querying

#### Initialize for querying
Before querying you need to initialize POOQ once. 

```php
\POOQ\POOQ::initialize('database_name', 'username', 'password', 'hostname', 3306);
```
Alternatively an existing [PDO](https://www.php.net/manual/en/book.pdo.php) can be used:
```php
\POOQ\POOQ::initializeUsingExistingPdo($yourPdoObject);
```

#### Creating a select query
```php
$t = Thread::as('t'); // Creates an alias name

echo select($t->title(), $t->lastPoster(), $t->postUserName(), $t->replyCount(), $t->threadId())
    ->from($t)
    ->where($t->forumId()->eq(value($forumId)))
    ->order($t->threadId()->desc())
    ->limit(10)
    ->offset(0)
    ->getSql();
```
Outputs:
```sql
SELECT `t`.`title` as `title`, `t`.`lastposter` as `lastposter`, `t`.`postusername` as `postusername`, `t`.`replycount` as `replycount`, `t`.`threadid` as `threadid` 
FROM thread t 
WHERE `t`.`forumid` = 2 
ORDER BY `t`.`threadid` DESC 
LIMIT 10 
OFFSET 0
```

Alias names are optional. You can always use the models directly:
```php
echo select(Thread::title(), Thread::lastPoster())
    ->from(Thread::class)
    // ...
```
Outputs:
```sql
SELECT `thread`.`title` as `title`, `thread`.`lastposter` as `lastposter`
FROM `thread`
...
```

#### Mapping results to objects (so called Records)
```php
$f = Forum::as('f'); 
$t = Thread::as('t'); 

$resultList = select($f, $t->title()) // Select all fields of Forum and the title field of Thread
    ->from($t)
    ->innerJoin($f)->on($f->forumid()->eq($t->forumid())) // "INNER JOIN forum f ON `f`.`forumid` = `t`.`forumid`"
    ->fetchAll(); // Executes the query and returns ResultList
    
/** @var ThreadRecord[] $threadRecordList */
$threadRecordList = $resultList->into($t); // Maps all Thread fields of ResultList into ThreadRecordList

foreach($threadList as $thread) { // for each row
    echo $thread->getTitle(); // output the title
}
```

#### Records can be optionally inserted/updated/refreshed/deleted in an ORM fashion
```php
// note: this is only supported for tables containing a primary-key

$record->refresh(); // Reload the record from the database
$record->store(); // insert or update the record to the database
$record->delete(); // deletes this record in the database
```

#### Converting Records to arrays
```php
$array = $recordList->toAssoc();  // Maps entire record-list
$array = $record->toAssoc(); // Maps only a single record
```

POOQ can handle queries with overlapping column names (from different tables). For example, if forum and thread would both have a column "title" then each value would still be mapped to the correct object. 

#### Execute update queries

```php
update(Forum::class) 
    ->set(Forum::title(), 'New title')
    ->set(Forum::description(), 'Lorem ipsum ..')
    ->where(Forum::forumId()->eq(value(123)))
    ->execute();
```
executes query:
```sql
UPDATE forum 
SET `title` = 'New title',
SET `description` = 'Lorem ipsum ..'
WHERE `id` = 123
```

Using subqueries in update:
```php
update(Thread::class) 
    ->set(Thread::forumId(), 
        select(Forum::forumId())
            ->from(Forum::class)
            ->where(Forum::title()->eq(value('Sample title')))
    )
    ->where(Thread::threadId()->eq(value(123)))
    ->execute();
```
executes query:
```sql
UPDATE forum 
SET `forumId` = (SELECT `forum`.forumId` FROM `forum` WHERE `forum`.`title` = 'Sample title')
WHERE `id` = 123
```

#### Custom WHERE-clauses
Not everything is implemented yet.. so being able to write custom where clauses is quite useful:
```php
delete(Session::class) 
    ->where(new SimpleCondition(Session::creationDatetime()->toSql() . ' < ' . value($dateTime->format('Y-m-d H:i:s'))->toSql()))
    ->execute();  
```

### Code Generation
```php
$config = new \POOQ\CodeGeneration\CodeGeneratorConfig(__DIR__.DIRECTORY_SEPARATOR.'gensrc');
$config->setCopyrightInformation(
<<<END
/**
 * Your custom copyright text!
 */
END
);

// This is optional:
$config->setNameMap(
    [
        // column or tablename => camel-case-name starting lowercase
        'userid' => 'userId',
        'display_order' => 'displayOrder',
        'accessmask' => 'accessMask',
        'product' => 'product',
    ]
);

// This is optional:
$businesslogicFolderPath = __DIR__.'/test/businesslogic';
$config->setModelName2NamespaceMap(new \POOQ\CodeGeneration\ModelName2NamespaceMap(
    [
        // Name of model class => NamespaceObject-object
        'Post' => new \POOQ\CodeGeneration\NamespaceObject(
            'businesslogic\\post',
            $businesslogicFolderPath.'/post'
        ),
        'Forum' => new \POOQ\CodeGeneration\NamespaceObject(
            'businesslogic\\forum',
            $businesslogicFolderPath.'/forum'
        ),
    ]
));
$generator = new \POOQ\CodeGeneration\CodeGenerator($config);
$generator->convertDatabase('database_name', 'username', 'password', 'hostname', 3306);
```

## Contribution

Feel free to improve POOQ and send me your Pull Requests.
Don't worry about code style or anything else. I can still adapt your changes if necessary.  
I'm happy about every contribution.

Feel free to ask me anything!

Have a nice day!