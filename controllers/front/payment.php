<?php
class PrestaPaystackPaymentModuleFrontController extends ModuleFrontController
{
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

    // Validate order
    $this->module->validateOrder($cart->id, Configuration::get('PS_OS_PRESTAPAYSTACK_PAYMENT'), $total,$this->module->displayName, NULL, $extra_vars,(int)$currency->id, false, $customer->secure_key);

    // Set template
    // $this->setTemplate('payment.tpl');
  }
}
