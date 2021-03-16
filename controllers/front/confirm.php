<?php

/**
 * 2016 Paystack
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/MIT
 *
 * @author     Paystack Payments <support@paystack.com>
 * @copyright  2016 Paystack
 * @license    https://opensource.org/licenses/MIT  MIT License
 */
require_once __DIR__ . '/paystack-plugin-tracker.php';
class PrestaPaystackConfirmModuleFrontcontroller extends ModuleFrontController
{
    public $php_self = 'confirm.php';
    public $ssl = true;
    public $display_column_left = false;

    public function verifyTxn($code)
    {
        $test_secretkey = Configuration::get('PAYSTACK_TEST_SECRETKEY');
        $live_secretkey = Configuration::get('PAYSTACK_LIVE_SECRETKEY');
        $mode = Configuration::get('PAYSTACK_MODE');

        if ($mode == 'test') {
            $key = $test_secretkey;
        } else {
            $key = $live_secretkey;
        }
        $key = str_replace(' ', '', $key);

        $contextOptions = array(
            'http' => array(
                'method' => "GET",
                'header' => array("Authorization: Bearer " . $key . "\r\n")
            )
        );

        $context = stream_context_create($contextOptions);
        $url = 'https://api.paystack.co/transaction/verify/' . $code;
        $request = Tools::file_get_contents($url, false, $context);
        $result = Tools::jsonDecode($request);
        return $result;
    }
    // public function verifyTxn($code){
    //   $test_secretkey = Configuration::get('PAYSTACK_TEST_SECRETKEY');
    //   $live_secretkey = Configuration::get('PAYSTACK_LIVE_SECRETKEY');
    //   $mode = Configuration::get('PAYSTACK_MODE');

    //   if ($mode == 'test') {
    //     $key = $test_secretkey;
    //   }else{
    //     $key = $live_secretkey;
    //   }
    //   $key = str_replace(' ', '', $key);

    //     $url = 'https://api.paystack.co/transaction/verify/' . urlencode($code);
    //     $data = array();


    //     //open connection
    //     $ch = curl_init();

    //     //set the url, and the header
    //     curl_setopt($ch, CURLOPT_URL, $url);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    //     // Paystack's servers require TLSv1.2
    //     // Force CURL to use this
    //     if (!defined('CURL_SSLVERSION_TLSV1_2')) {
    //         define('CURL_SSLVERSION_TLSV1_2', 6);
    //     }
    //     curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSV1_2);

    //     curl_setopt(
    //         $ch, CURLOPT_HTTPHEADER, [
    //         'Authorization: Bearer ' . $key]
    //     );

    //     //execute post
    //     $result = curl_exec($ch);

    //     //close connection
    //     curl_close($ch);

    //     if ($result) {
    //         $data = Tools::jsonDecode($result);
    //     }
    //   return $data;
    // }
    public function initParams()
    {
        $params = array();
        // $transaction = array();
        $nbProducts = $this->context->cart->nbProducts(); //self::$cart->nbProducts();
        $this->context->smarty->assign('nb_products', $nbProducts);

        if ($nbProducts  > 0 && Tools::getValue('txn_code') !== '') {
            $txn_code = Tools::getValue('txn_code');
            if (Tools::getValue('txn_code') == "") {
                $txn_code = Tools::getValue('paystack-trxref');
            }
            $amount = Tools::getValue('amounttotal');
            $email = Tools::getValue('email');
            $verification = $this->VerifyTxn($txn_code);

            $paystack = new PrestaPaystack();
            if (($verification->status === false) || (!property_exists($verification, 'data')) || ($verification->data->status !== 'success')) {
                // request to paystack failed
                $date = date("Y-m-d h:i:sa");
                $email = $email;
                $total = $amount;
                // $verification->message;
                $status = 'failed';
            } else {
                //PSTK-Logger
                $test_pk = Configuration::get('PAYSTACK_TEST_PUBLICKEY');
                $live_pk = Configuration::get('PAYSTACK_LIVE_PUBLICKEY');
                $mode = Configuration::get('PAYSTACK_MODE');

                if ($mode == 'test') {
                    $pk = $test_pk;
                } else {
                    $pk = $live_pk;
                }
                $pk = str_replace(' ', '', $pk);
                $pstk_logger = new presta_1_6_paystack_plugin_tracker('presta-1.6', $pk);
                $pstk_logger->log_transaction_success($txn_code);
                //-----------------


                $email = $verification->data->customer->email;
                $date = $verification->data->transaction_date;
                $total = $verification->data->amount / 100;
                $status = 'approved';
            }

            $transaction_id = $txn_code;

            if (trim(Tools::strtolower($status)) == 'approved') {
                $params = array(
                    array('value' => $email, 'name' => 'Email'),
                    array('value' => $total, 'name' => 'Total'),
                    array('value' => $date, 'name' => 'Date'),
                    array('value' => $transaction_id, 'name' => 'Transaction ID'),
                    array('value' => $status, 'name' => 'Status'),
                );
                $paystack->validation($verification);
            } else {
                $params = array(
                    array('value' => $email, 'name' => 'Email'),
                    array('value' => $total, 'name' => 'Total'),
                    array('value' => $date, 'name' => 'Date'),
                    array('value' => $transaction_id, 'name' => 'Transaction ID'),
                    array('value' => $status, 'name' => 'Status'),
                );
                $paystack->validation($verification);
            }
            $this->context->smarty->assign('status', $status);
            $return_url = __PS_BASE_URI__ . 'order-history';
            $this->context->smarty->assign('return_url', $return_url);
        }
        return $params;
    }

    public function initContent()
    {
        parent::initContent();

        $param = $this->initParams();
        $this->context->smarty->assign('params', $param);

        $this->setTemplate('return.tpl');
    }
}
