<?php
/*
 * 2016 Paystack
 *
 *  @author kendysonD
 */

if (!defined('_CAN_LOAD_FILES_'))
	exit;

class PrestaPaystack extends PaymentModule{
  public function __construct(){
      $this->name = 'prestapaystack';
      $this->tab = 'payments_gateways';
      $this->version = '0.1';
      $this->bootstrap = true;
      $this->author = 'Douglas Kendyson';
      $this->description = 'Paystack for prestashop';

      parent::__construct();

      $this->displayName = 'Paystack for Prestashop';

  }
  public function getHookController($hook_name){
    // Include the controller file
    require_once(dirname(__FILE__).'/controllers/hook/'.$hook_name.'.php');

    // Build the controller name dynamically
    $controller_name = $this->name.$hook_name.'Controller';

    // Instantiate controller
    $controller = new $controller_name();

    // Return the controller
    return $controller;
  }
  public function install(){
  if (!parent::install() ||
     !$this->registerHook('displayPayment') ||
    !$this->registerHook('displayPaymentReturn'))
      return false;
    return true;
  }
  public function assignConfiguration()
  {
    $test_secretkey = Configuration::get('PAYSTACK_TEST_SECRETKEY');
    $test_publickey = Configuration::get('PAYSTACK_TEST_PUBLICKEY');
    $mode = Configuration::get('PAYSTACK_MODE');
    $this->context->smarty->assign('test_secretkey', $test_secretkey);
    $this->context->smarty->assign('test_publickey', $test_publickey);
    $this->context->smarty->assign('mode', $mode);
  }
  public function processConfiguration()
  {
    if (Tools::isSubmit('save_settings')){
      $test_publickey = Tools::getValue('test_publickey');
      $test_secretkey = Tools::getValue('test_secretkey');
      $mode = Tools::getValue('mode');
      Configuration::updateValue('PAYSTACK_TEST_SECRETKEY', $test_secretkey);
      Configuration::updateValue('PAYSTACK_MODE', $mode);
      Configuration::updateValue('PAYSTACK_TEST_PUBLICKEY', $test_publickey);
      $this->context->smarty->assign('confirmation', 'ok');
    }
  }
  public function getContent()
  {
    $this->processConfiguration();
    $this->assignConfiguration();
    return $this->display(__FILE__, 'getContent.tpl');
  }
  public function hookDisplayPayment($params)
  {
    $this->context->controller->addCSS($this->_path.'views/css/prestapaystack.css', 'all');
    return $this->display(__FILE__, 'displayPayment.tpl');
  }

}
