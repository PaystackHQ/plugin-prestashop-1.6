<?php
/*
 * 2016 Paystack
 *
 *  @author kendysonD
 */

if (!defined('_CAN_LOAD_FILES_'))
	exit;

class PrestaPaystack extends PaymentModule{
	private $_postErrors = array();

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
    $controller = new $controller_name($this, __FILE__,$this->_path);

    // Return the controller
    return $controller;
  }
  public function install(){
    // if (!parent::install() ||
    //    !$this->registerHook('displayPayment') ||
    //   !$this->registerHook('displayPaymentReturn'))
    //     return false;
		$this->registerHook('orderConfirmation');
		$this->registerHook('return');
		if (!parent::install() || !$this->registerHook('payment') || !$this->registerHook('return') || !$this->registerHook('orderConfirmation') ||
			!Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'vogu_token` (
            `id_cart` int(10) NOT NULL,
			`token` varchar(32) DEFAULT NULL,
			`status` varchar(20) DEFAULT NULL,
			PRIMARY KEY  (`id_cart`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;')) // prod | test
			return false;

		if (!$this->installOrderState())
			return false;
      return true;
  }
	public function installOrderState()
	{
		if (Configuration::get('PS_OS_PRESTAPAYSTACK_PAYMENT') < 1)
		{
			$order_state = new OrderState();
			$order_state->send_email = false;
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
			foreach (LanguageCore::getLanguages() as $l)
				$order_state->template[$l['id_lang']] = 'prestapaystack';

			// We copy the mails templates in mail directory
			foreach (LanguageCore::getLanguages() as $l)
			{
				$module_path = dirname(__FILE__).'/views/templates/mails/'.$l['iso_code'].'/';
				$application_path = dirname(__FILE__).'/../../mails/'.$l['iso_code'].'/';
				if (!copy($module_path.'prestapaystack.txt', $application_path.'prestapaystack.txt') ||
					!copy($module_path.'prestapaystack.html', $application_path.'prestapaystack.html'))
					return false;
			}

			if ($order_state->add())
			{
				// We save the order State ID in Configuration database
				Configuration::updateValue('PS_OS_PRESTAPAYSTACK_PAYMENT', $order_state->id);

				// We copy the module logo in order state logo directory
				copy(dirname(__FILE__).'/logo.png', dirname(__FILE__).'/../../img/os/'.$order_state->id.'.gif');
				copy(dirname(__FILE__).'/logo.png', dirname(__FILE__).'/../../img/tmp/order_state_mini_'.$order_state->id.'.gif');
			}
			else
				return false;
		}
		return true;
	}
  // public function getContent()
  // {
  //   $this->processConfiguration();
  //   $this->assignConfiguration();
  //   return $this->display(__FILE__, 'getContent.tpl');
  // }
	public function getContent()
	{
	  $controller = $this->getHookController('getContent');
	  return $controller->run();
	}
  public function hookDisplayPayment($params)
  {
    // $this->context->controller->addCSS($this->_path.'views/css/prestapaystack.css', 'all');
    // return $this->display(__FILE__, 'displayPayment.tpl');
    $controller = $this->getHookController('displayPayment');
    return $controller->run($params);
  }
	public function hookDisplayPaymentReturn($params)
	{
		$controller = $this->getHookController('displayPaymentReturn');
		return $controller->run($params);
	}
}
