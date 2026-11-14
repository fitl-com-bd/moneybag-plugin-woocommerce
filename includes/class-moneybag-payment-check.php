<?php
/**
 * Confirms a verified Moneybag payment belongs to a specific WooCommerce order.
 */

if (! defined('ABSPATH') && ! defined('MONEYBAG_TESTS')) {
    exit;
}

class Moneybag_Payment_Check
{
    /**
     * Moneybag order IDs for one WooCommerce order share this prefix; only the
     * retry suffix after it changes.
     *
     * @param string $site_url     The store URL.
     * @param string $order_number The WooCommerce order number.
     * @return string
     */
    public static function order_id_prefix($site_url, $order_number)
    {
        return strtoupper(substr(md5($site_url), 0, 4)) . '-' . $order_number . '-';
    }

    /**
     * Return the first reason a verified payment cannot complete an order.
     *
     * @param array{verified: mixed, status: mixed, order_id: mixed, amount: mixed, currency: mixed} $payment
     * @param string $expected_prefix Result of order_id_prefix() for the order.
     * @param string $order_total     The order total.
     * @param string $order_currency  The order currency.
     * @return string|null Null when the payment matches.
     */
    public static function mismatch(array $payment, $expected_prefix, $order_total, $order_currency)
    {
        if (true !== $payment['verified']) {
            return 'not verified';
        }
        if ('SUCCESS' !== $payment['status'] && 'COMPLETED' !== $payment['status']) {
            return 'status';
        }
        if (0 !== strpos((string) $payment['order_id'], $expected_prefix)) {
            return 'order';
        }
        if (! is_numeric($payment['amount']) || abs((float) $payment['amount'] - (float) $order_total) >= 0.01) {
            return 'amount';
        }
        if (strtoupper((string) $payment['currency']) !== strtoupper((string) $order_currency)) {
            return 'currency';
        }

        return null;
    }
}
