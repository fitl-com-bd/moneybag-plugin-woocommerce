<?php
// Run: php tests/test_payment_check.php

define('MONEYBAG_TESTS', true);
require __DIR__ . '/../includes/class-moneybag-payment-check.php';

$prefix = Moneybag_Payment_Check::order_id_prefix('https://shop.test', '12');
$good = array('verified' => true, 'status' => 'SUCCESS', 'order_id' => $prefix . '4821', 'amount' => '1500.00', 'currency' => 'BDT');
$cases = array(
    'matching payment' => array($good, null),
    'COMPLETED status' => array(array('status' => 'COMPLETED') + $good, null),
    'amount formatted differently' => array(array('amount' => '1500') + $good, null),
    'lowercase currency' => array(array('currency' => 'bdt') + $good, null),
    'not verified' => array(array('verified' => false) + $good, 'not verified'),
    'failed status' => array(array('status' => 'FAILED') + $good, 'status'),
    'another order' => array(array('order_id' => Moneybag_Payment_Check::order_id_prefix('https://shop.test', '13') . '4821') + $good, 'order'),
    'order number sharing leading digits' => array(array('order_id' => Moneybag_Payment_Check::order_id_prefix('https://shop.test', '123') . '4821') + $good, 'order'),
    'another site' => array(array('order_id' => Moneybag_Payment_Check::order_id_prefix('https://other.test', '12') . '4821') + $good, 'order'),
    'smaller amount' => array(array('amount' => '15.00') + $good, 'amount'),
    'missing amount' => array(array('amount' => null) + $good, 'amount'),
    'other currency' => array(array('currency' => 'USD') + $good, 'currency'),
);

$failed = 0;
foreach ($cases as $name => $case) {
    $actual = Moneybag_Payment_Check::mismatch($case[0], $prefix, '1500.00', 'BDT');
    if ($actual !== $case[1]) {
        $failed++;
        echo "FAIL {$name}: " . var_export($actual, true) . "\n";
    }
}
echo (count($cases) - $failed) . '/' . count($cases) . " passed\n";
exit($failed ? 1 : 0);
