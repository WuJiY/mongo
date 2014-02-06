<?php
require __DIR__.'/mongo.php';

$conn = mongo_init('mongodb://localhost', 'mydb');

mongo_index($conn, 'projects', 'name');
mongo_index($conn, 'projects', [
  'author' => 1,
  'license' => 1
]);

$doc = mongo_create($conn, 'projects', [
  'name' => 'noodlehaus/mongo',
  'author' => 'noodlehaus',
  'url' => 'http://github.com/noodlehaus/mongo.git'
]);

$doc = mongo_find_one($conn, 'projects', ['author' => $doc['author']]);

mongo_update(
  $conn,
  'projects',
  ['_id' => $doc['_id']],
  ['$set' => ['license' => 'MIT']]
);

mongo_remove($conn, 'projects', ['name' => 'noodlehaus/mongo']);
?>
