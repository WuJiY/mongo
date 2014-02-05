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
