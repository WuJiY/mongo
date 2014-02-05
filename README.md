`mongo()` is a procedural mongodb wrapper for doing CRUD on a single db.

example:

```php
<?php
require __DIR__.'/mongo.php';

mongo('mongodb://localhost', 'mydb');

mongo_index('projects', 'name');
mongo_index('projects', [
  'author' => 1,
  'license' => 1
]);

$doc = mongo_create('projects', [
  'name' => 'noodlehaus/mongo',
  'author' => 'noodlehaus',
  'url' => 'http://github.com/noodlehaus/mongo.git'
]);

$doc = mongo_find_one('projects', ['author' => $doc['author']]);

mongo_update(
  'projects',
  ['_id' => $doc['_id']],
  ['$set' => ['license' => 'MIT']]
);

mongo_remove('projects', ['name' => 'noodlehaus/mongo']);
?>
```

function list:

```php
function mongo($dsn = null, $db = null);
function mongo_create($type, $obj, $opt = []);
function mongo_find_one($type, $query);
function mongo_find($type, $query, $limit = 0, $skip = 0);
function mongo_distinct($type, $field, $query);
function mongo_update($type, $query, $instr, $opt = []);
function mongo_remove($type, $query);
function mongo_index($type, $keys, $opt = []);
function mongo_id($id);
```

`mongo()` uses the MIT license <http://noodlehaus.mit-license.org>
