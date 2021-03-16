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

if (!defined('_CAN_LOAD_FILES_')) {
    exit;
}
include_once(_PS_MODULE_DIR_ . 'prestapaystack/classes/paystackcode.php');

class PrestaPaystack extends PaymentModule
{
    private $_postErrors = array();

    public function __construct()
    {
        $this->name = 'prestapaystack';
        $this->tab = 'payments_gateways';
        $this->version = '1.0.6';
        $this->bootstrap = true;
        $this->author = 'Paystack';
        $this->description = 'Paystack for PrestaShop. Accept online card payments on your store.';
        $this->ps_versions_compliancy = array('min' => '1.5', 'max' => 1.6);
        $this->module_key = '7bd648045911885fe8a9a3c6f550d76e';
        parent::__construct();

        $this->displayName = 'Paystack';

        require(_PS_MODULE_DIR_ . 'prestapaystack/backward_compatibility/backward.php');
        $this->context->smarty->assign('base_dir', __PS_BASE_URI__);
    }
    public function install()
    {
        $this->registerHook('orderConfirmation');
        $this->registerHook('return');
        if (
            !parent::install() || !$this->registerHook('displayPayment') || !$this->registerHook('displayPaymentReturn') || !$this->registerHook('orderConfirmation') ||
            !Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'paystack_txncodes` (
						`id` int(10) NOT NULL,
			`cart_id` int(11) NOT NULL,
			`code` varchar(32) DEFAULT NULL
			) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;ALTER TABLE `' . _DB_PREFIX_ . 'paystack_txncodes`
			  ADD PRIMARY KEY (`id`);ALTER TABLE `' . _DB_PREFIX_ . 'paystack_txncodes`
				  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=24;')
        ) { // prod | test
            return false;
        }

        if (!$this->installOrderState()) {
            return false;
        }
        return true;
    }

    public function installOrderState()
    {
        if (Configuration::get('PS_OS_PRESTAPAYSTACK_PAYMENT') < 1) {
            $order_state = new OrderState();
            $order_state->send_email = true;
            $order_state->module_name = $this->name;
            $order_state->invoice = false;
            $order_state->color = '#98c3ff';
            $order_state->logable = true;
            $order_state->shipped = false;
            $order_state->unremovable = false;
            $order_state->delivery = false;
            $order_state->hidden = false;
            $order_state->paid = false;
            $order_state->deleted = false;
            $order_state->name = array((int)Configuration::get('PS_LANG_DEFAULT') => pSQL($this->l('Paystack - Awaiting payment')));
            $order_state->template = array();
            foreach (LanguageCore::getLanguages() as $l) {
                $order_state->template[$l['id_lang']] = 'prestapaystack';
            }

            // We copy the mails templates in mail directory
            foreach (LanguageCore::getLanguages() as $l) {
                $module_path = dirname(__FILE__) . '/views/templates/mails/' . $l['iso_code'] . '/';
                $application_path = dirname(__FILE__) . '/../../mails/' . $l['iso_code'] . '/';
                if (
                    !copy($module_path . 'prestapaystack.txt', $application_path . 'prestapaystack.txt') ||
                    !copy($module_path . 'prestapaystack.html', $application_path . 'prestapaystack.html')
                ) {
                    return false;
                }
            }

            if ($order_state->add()) {
                // We save the order State ID in Configuration database
                Configuration::updateValue('PS_OS_PRESTAPAYSTACK_PAYMENT', $order_state->id);

                // We copy the module logo in order state logo directory
                copy(dirname(__FILE__) . '/logo.png', dirname(__FILE__) . '/../../img/os/' . $order_state->id . '.gif');
                copy(dirname(__FILE__) . '/logo.png', dirname(__FILE__) . '/../../img/tmp/order_state_mini_' . $order_state->id . '.gif');
            } else {
                return false;
            }
        }
        return true;
    }

    public function uninstall()
    {
        // Uninstall parent and unregister Configuration
        // Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'paystack_txncodes`');
        // $orderState = new OrderState((int)Configuration::get('VOGU_WAITING_PAYMENT'));
        // $orderState->delete();
        // Configuration::deleteByName('VOGU_WAITING_PAYMENT');
        if (!parent::uninstall()) {
            return false;
        }
        return true;
    }

    public function getHookController($hook_name)
    {
        // Include the controller file
        require_once(dirname(__FILE__) . '/controllers/hook/' . $hook_name . '.php');

        // Build the controller name dynamically
        $controller_name = $this->name . $hook_name . 'Controller';

        // Instantiate controller
        $controller = new $controller_name($this, __FILE__, $this->_path);

        // Return the controller
        return $controller;
    }

    public function hookReturn($params)
    {
        $this->smarty->assign(array('vogURedirection' => Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . 'modules/' . $this->name . '/'));

        return $this->display(__FILE__, 'return.tpl');
    }

    public function validation($verification)
    {
        // $transaction = array();
        // $t = array();
        if (Tools::getValue('txn_code') !== '') {
            $txn_code = Tools::getValue('txn_code');
            $amount = Tools::getValue('amounttotal');
            $email = Tools::getValue('email');
            $o_exist = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'paystack_txncodes`  WHERE `code` = "' . $txn_code . '"'); //Rproduct::where('code', '=', $code)->first();

            if (count($o_exist) > 0) {
                $idCart = @$o_exist[0][cart_id];
                $this->context->cart = @new Cart((int)$idCart);
            }
            if ($verification->status == 'success') {
                $email = $verification->data->customer->email;
                // $date = $verification->data->transaction_date;
                $total = $verification->data->amount / 100;
                $status = 'approved';
            } else {
                // $date = date("Y-m-d h:i:sa");
                $email = $email;
                $total = $amount;
                $status = 'failed';
            }
            $transaction_id = $txn_code;
        }

        if (Validate::isLoadedObject($this->context->cart)) {
            if ($this->context->cart->getOrderTotal() != $total) {
                Logger::AddLog('[Paystack] The shopping card ' . (int)$idCart . ' doesn\'t have the correct amount expected during payment validation', 2, null, null, null, true);
            } else {
                // $currency = new Currency((int)$this->context->cart->id_currency);
                if (trim(Tools::strtolower($status)) == 'approved') {
                    $this->validateOrder((int)$this->context->cart->id, (int)Configuration::get('PS_OS_PAYMENT'), (float)$this->context->cart->getOrderTotal(), $this->displayName, $transaction_id, array(), null, false, $this->context->cart->secure_key);
                    $new_order = new Order((int)$this->currentOrder);
                    if (Validate::isLoadedObject($new_order)) {
                        $payment = $new_order->getOrderPaymentCollection();
                        $payment[0]->transaction_id = $transaction_id;
                        $payment[0]->update();
                    } else {
                        Logger::AddLog('[Paystack] The shopping card ' . (int)$idCart . ' has an incorrect token given from Paystack during payment validation', 2, null, null, null, true);
                    }
                } else {
                    Logger::AddLog('[Paystack] The shopping card ' . (int)$idCart . ' has an incorrect token given from Paystack during payment validation', 2, null, null, null, true);
                }
            }
        } else {
            Logger::AddLog('[Paystack] The shopping card ' . (int)$idCart . ' was not found during the payment validation step', 2, null, null, null, true);
        }
    }
    public function hookDisplayPayment($params)
    {
        if (!$this->active || Configuration::get('PAYSTACK_MODE') == '') {
            return false;
        }
        $controller = $this->getHookController('displayPayment');
        return $controller->run($params);
    }
    public function hookDisplayPaymentReturn($params)
    {
        $controller = $this->getHookController('displayPaymentReturn');
        return $controller->run($params);
    }
    public function getContent()
    {
        $controller = $this->getHookController('getContent');
        return $controller->run();
    }
}
