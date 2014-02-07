<?php
/**
 * mongo is a procedural mongodb wrapper for doing CRUD on a single db
 *
 * @author Jesus A. Domingo <jesus.domingo@gmail.com>
 * @license MIT
 */

/**
 * Create a mongoclient-mongodb tuple to be used for all mongo
 * functions.
 *
 * example calls:
 *
 *    $cn = mongo_init('mydb');
 *    $cn = mongo_init('mongodb://localhost:27017', 'mydb');
 *
 * @return array MongoClient instance at [0], MongoDB at [1].
 */
function mongo_init() {

  if (func_num_args() == 0)
    trigger_error('mongo_init() requires at least 1 argument', E_USER_ERROR);

  $arg = func_get_args();
  $dbn = array_pop($arg);
  $dsn = array_pop($arg);

  if ($dsn)
    $client = new MongoClient($dsn);
  else
    $client = new MongoClient('mongodb://localhost:27017/');

  return array($client, $client->selectDB($dbn));
}

/**
 * Closes a mongo_init() connection tuple
 *
 * @param array mongo_init() connection
 *
 * @return void
 */
function mongo_close($conn) {
  return $conn[0]->close();
}

/**
 * Create a document under $type collection. Fields are added for
 * timestamps (created_at, updated_at).
 *
 * @param array mongo_init() connection tuple
 * @param string $type name of collection
 * @param array|object $obj document to create
 * @param array $opt optional, options passed to insert()
 *
 * @return array object created
 */
function mongo_create($conn, $type, $obj, $opt = array()) {

  $col = $conn[1]->selectCollection($type);

  $obj['created_at'] = new MongoDate();
  $obj['updated_at'] = new MongoDate();

  $res = $col->insert((array) $obj, $opt);

  return $obj;
}

/**
 * Find one document from a collection
 *
 * @param array mongo_init() connection tuple
 * @param string $type collection to seach from
 * @param array|object $query mongodb query to use
 *
 * @return array matching document
 */
function mongo_find_one($conn, $type, $query) {
  return $conn[1]
    ->selectCollection($type)
    ->findOne((array) $query);
}

/**
 * Get all matching documents from a collection
 *
 * @param array mongo_init() connection tuple
 * @param string $type collection to search from
 * @param array|object $query mongodb query to use
 * @param int $limit optional, how many matches to return
 * @param int $skip optional, how many documents to skip
 *
 * @return array documents that match
 */
function mongo_find($conn, $type, $query, $limit = 0, $skip = 0) {

  $col = $conn[1]->selectCollection($type);
  $cur = $col->find($query);

  if ($skip > 0)
    $cur = $cur->skip($skip);

  if ($limit > 0)
    $cur = $cur->limit($limit);

  return $cur;
}

/**
 * Returns distinct values for a particular field in a collection
 *
 * @param array mongo_init() connection tuple
 * @param string $type collection to use
 * @param string $field field to get distinct values for
 * @param array|object $query query to use for filtering the docs
 *
 * @return array list of distinct values
 */
function mongo_distinct($conn, $type, $field, $query) {
  $col = $conn[1]->selectCollection($type);
  $res = $col->distinct($field, $query);
  return $res;
}

/**
 * Updates matching documents. Timestamp field updated_at is
 * also automatically updated
 *
 * @param array mongo_init() connection tuple
 * @param string $type collection to use
 * @param array|object $query filter to use against documents
 * @param array|object $instr update instructions
 * @param array $opt optional, options to pass to update()
 *
 * @return void
 */
function mongo_update($conn, $type, $query, $instr, $opt = array()) {

  $opt['multiple'] = isset($opt['multiple']) ? $opt['multiple'] : true;
  $instr['$set']['updated_at'] = new MongoDate();

  return $conn[1]
    ->selectCollection($type)
    ->update($query, $instr, $opt);
}

/**
 * Removes matching documents
 *
 * @param array mongo_init() connection tuple
 * @param string $type collection to remove docs from
 * @param array|object $query query to use as filter
 *
 * @return void
 */
function mongo_remove($conn, $type, $query) {
  return $conn[1]
    ->selectCollection($type)
    ->remove($query);
}

/**
 * Creates an index for a document field(s)
 *
 * @param array mongo_init() connection tuple
 * @param string $type collection to use
 * @param string|array $keys field(s) to create indices for
 * @param array $opt options passed to ensureIndex()
 *
 * @return void
 */
function mongo_index($conn, $type, $keys, $opt = array()) {
  return $conn[1]
    ->selectCollection($type)
    ->ensureIndex($keys, $opt);
}

/**
 * Creates a mongoid out of the string. Just for
 * consistency.
 *
 * @param string id to wrap
 *
 * @return MongoId mongo id instance
 */
function mongo_id($id) {
  return new MongoId($id);
}
?>
