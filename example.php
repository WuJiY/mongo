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
