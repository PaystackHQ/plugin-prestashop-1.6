<?php

class PrestaPaystackValidationModuleFrontController extends ModuleFrontController {

  public function postProcess()
  {
    // Check if cart exists and all fields are set
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

    ////
    // echo "string";
    Tools::redirect('index.php?controller=order-confirmation&id_cart='.$cart->id.'&id_module='.$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$customer->secure_key);

  }
}
