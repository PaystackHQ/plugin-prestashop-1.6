<?php

include_once(_PS_MODULE_DIR_.'prestapaystack/classes/paystackcode.php');

class PrestaPaystackPaymentModuleFrontController extends ModuleFrontController
{
  public $display_column_left = false;

  public $ssl = true;

  public function initContent()
  {
    // Call parent init content method
    parent::initContent();

    $this->display_column_left = false;
    $this->display_column_right = false;
      $this->context->smarty->assign(array(
      'nb_products' => $this->context->cart->nbProducts(),
      'cart_currency' => $this->context->cart->id_currency,
      'currencies' => $this->module->getCurrency((int)$this->context->cart->id_currency),
      'total_amount' =>$this->context->cart->getOrderTotal(true, Cart::BOTH),
      'path' => $this->module->getPathUri(),
    ));
    $test_secretkey = Configuration::get('PAYSTACK_TEST_SECRETKEY');
    $test_publickey = Configuration::get('PAYSTACK_TEST_PUBLICKEY');
    $mode = Configuration::get('PAYSTACK_MODE');
    $this->context->smarty->assign('test_secretkey', $test_secretkey);
    $this->context->smarty->assign('test_publickey', $test_publickey);
    $this->context->smarty->assign('mode', $mode);
    $cart = $this->context->cart;
    $cart_id = $cart->id;

    $pcode = $this->getPaystackcode($cart_id);
    // die($cart_id);
    if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 ||!$this->module->active){
      Tools::redirect('index.php?controller=order&step=1');
    }
    /////
    $authorized = false;
    foreach (Module::getPaymentModules() as $module){
      if ($module['name'] == $this->module->name){
       $authorized = true;
      }
    }
    if (!$authorized){
      die('This payment method is not available.');
    }
    $customer = new Customer($cart->id_customer);
    if (!Validate::isLoadedObject($customer)){
      Tools::redirect('index.php?controller=order&step=1');
    }
    //////////
    $currency = $this->context->currency;
    $total = (float)$cart->getOrderTotal(true, Cart::BOTH);
    $extra_vars = array();
    $all_products = self::$cart->getProducts();
    $this->context->smarty->assign(array(
      'nbProducts' => $cart->nbProducts(),
      'email' => $this->context->customer->email,
      'code' => $pcode->code,
      'products' => $all_products,

			));

    $this->setTemplate('payment.tpl');
  }

  private function getPaystackcode($cart_id){
    $o_exist = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'paystack_txncodes`  WHERE `cart_id` = "'.$cart_id.'"');

    if (count($o_exist) == 0) {
      $pcode = new Paystackcode();
      $pcode->id = NULL;
      $pcode->cart_id = (int)$cart_id;
      $pcode->code = $pcode->generate_code();
      $pcode->add();

    } else {
      $pcode = @new Paystackcode((int)$o_exist[0][id]);
    }


    return $pcode;
  }
}
