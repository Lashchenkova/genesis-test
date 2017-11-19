<?php

require __DIR__ . '/prod.php';

$app['debug'] = true;
$app['db.options'] = array(
  'driver' => 'pdo_sqlite',
  'path' => realpath(ROOT_PATH . '/app.db'),
);
