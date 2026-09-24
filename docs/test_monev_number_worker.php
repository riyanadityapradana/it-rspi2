<?php
// Worker restricted to the disposable integration-test database.
if (PHP_SAPI !== 'cli' || !preg_match('/^it_rspi2_monev_test_[a-f0-9]{12}$/D', $argv[1] ?? '')) exit(1);
ob_start();
require __DIR__.'/../config/koneksi.php';
require __DIR__.'/../staff/unit/monev/helpers.php';
ob_end_clean();
$config->select_db($argv[1]);
$config->begin_transaction();
$number = mn_form_number('2040-01-03', true);
usleep(150000);
$config->commit();
echo $number;
