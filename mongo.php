<?php
/**
 * mongo is a procedural mongodb wrapper for doing CRUD on a single db
 *
 * @author Jesus A. Domingo <jesus.domingo@gmail.com>
 * @license MIT
 */

/**
 * Initialize mongo with a MongoClient() instance
 *
 * @param MongoClient client connection to use for all mongo_ calls
 *
 * @return MongoClient the connection if called with no params
 */
function mongo($dsn = null, $db = null) {

  static $conn = null;

  if (func_num_args() < 2)
    return $conn;

  $conn = (new MongoClient($dsn))->selectDB($db);
}

/**
 * Create a document under $type collection. Fields are added for
 * timestamps (created_at, updated_at).
 *
 * @param string $type name of collection
 * @param array|object $obj document to create
 * @param array $opt optional, options passed to insert()
 *
 * @return array object created
 */
function mongo_create($type, $obj, $opt = []) {

  $col = mongo()->selectCollection($type);

  $obj['created_at'] = new MongoDate();
  $obj['updated_at'] = new MongoDate();

  $res = $col->insert((array) $obj, $opt);

  return $obj;
}

/**
 * Find one document from a collection
 *
 * @param string $type collection to seach from
 * @param array|object $query mongodb query to use
 *
 * @return array matching document
 */
function mongo_find_one($type, $query) {
  return mongo()
    ->selectCollection($type)
    ->findOne((array) $query);
}

/**
 * Get all matching documents from a collection
 *
 * @param string $type collection to search from
 * @param array|object $query mongodb query to use
 * @param int $limit optional, how many matches to return
 * @param int $skip optional, how many documents to skip
 *
 * @return array documents that match
 */
function mongo_find($type, $query, $limit = 0, $skip = 0) {

  $col = mongo()->selectCollection($type);
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
 * @param string $type collection to use
 * @param string $field field to get distinct values for
 * @param array|object $query query to use for filtering the docs
 *
 * @return array list of distinct values
 */
function mongo_distinct($type, $field, $query) {
  $col = mongo()->selectCollection($type);
  $res = $col->distinct($field, $query);
  return $res;
}

/**
 * Updates matching documents. Timestamp field updated_at is
 * also automatically updated
 *
 * @param string $type collection to use
 * @param array|object $query filter to use against documents
 * @param array|object $instr update instructions
 * @param array $opt options to pass to update()
 *
 * @return void
 */
function mongo_update($type, $query, $instr, $opt = []) {

  $opt['multiple'] = isset($opt['multiple']) ? $opt['multiple'] : true;
  $instr['$set']['updated_at'] = new MongoDate();

  return mongo()
    ->selectCollection($type)
    ->update($query, $instr, $opt);
}

/**
 * Removes matching documents
 *
 * @param string $type collection to remove docs from
 * @param array|object $query query to use as filter
 *
 * @return void
 */
function mongo_remove($type, $query) {
  return mongo()
    ->selectCollection($type)
    ->remove($query);
}

/**
 * Creates an index for a document field(s)
 *
 * @param string $type collection to use
 * @param string|array $keys field(s) to create indices for
 * @param array $opt options passed to ensureIndex()
 *
 * @return void
 */
function mongo_index($type, $keys, $opt = []) {
  return mongo()
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
