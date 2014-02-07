`mongo()` is a procedural mongodb wrapper for doing CRUD on a single db.

example:

```php
<?php
require __DIR__.'/mongo.php';

$conn = mongo_init('mongodb://localhost', 'mydb');

mongo_index($conn, 'projects', 'name');
mongo_index($conn, 'projects', array(
  'author' => 1,
  'license' => 1
));

$doc = mongo_create($conn, 'projects', array(
  'name' => 'noodlehaus/mongo',
  'author' => 'noodlehaus',
  'url' => 'http://github.com/noodlehaus/mongo.git'
));

$doc = mongo_find_one($conn, 'projects', array('author' => $doc['author']));

mongo_update(
  $conn,
  'projects',
  array('_id' => $doc['_id']),
  array('$set' => array('license' => 'MIT'))
);

mongo_remove($conn, 'projects', array('name' => 'noodlehaus/mongo'));
?>
```

function list:

```php
<?php
function mongo_init($db_or_dsn, $db = null);
function mongo_close($conn);
function mongo_create($conn, $type, $obj, $opt = array());
function mongo_find_one($conn, $type, $query);
function mongo_find($conn, $type, $query, $limit = 0, $skip = 0);
function mongo_distinct($conn, $type, $field, $query);
function mongo_update($conn, $type, $query, $instr, $opt = array());
function mongo_remove($conn, $type, $query);
function mongo_index($conn, $type, $keys, $opt = array());
function mongo_id($id);
?>
```

`mongo()` uses the MIT license <http://noodlehaus.mit-license.org>
