<?php

class PrestaPaystackConfirmModuleFrontcontroller extends ModuleFrontController{
    public $php_self = 'confirm.php';
    public $ssl = true;
    public $display_column_left = false;


    public function verify_txn($code){
      $test_secretkey = Configuration::get('PAYSTACK_TEST_SECRETKEY');
      $live_secretkey = Configuration::get('PAYSTACK_LIVE_SECRETKEY');
      $mode = Configuration::get('PAYSTACK_MODE');

      if ($mode == 'test') {
        $key = $test_secretkey;
      }else{
        $key = $live_secretkey;
      }
      $contextOptions = array(
          'ssl' => array(
              'verify_peer' => true,
              'cafile' => '/Applications/AMPPS/php-7.0/etc/cacert.pem',
              'ciphers' => 'HIGH:!SSLv2:!SSLv3',
          ),
          'http'=>array(
     		    'method'=>"GET",
            'header'=> array("Authorization: Bearer ".$key."\r\n","Connection: close\r\n","User-Agent: test\r\n)")
     		  )
      );

      $context = stream_context_create($contextOptions);
      $url = 'https://api.paystack.co/transaction/verify/'.$code;
      $request = file_get_contents($url, false, $context);
      $result = json_decode($request);
      return $result;
    }
	public function initParams(){
    $params = [];
    $transaction = array();
		if(Tools::getValue('txn_code') !== ''){
      $txn_code = Tools::getValue('txn_code');
      $amount = Tools::getValue('amounttotal');
      // $verification = $this->verify_txn($txn_code);
      // echo "<pre>";
      // echo $txn_code;
      // print_r($result);
      // die();
			// $xml = file_get_contents('https://voguepay.com/?v_transaction_id='.$_REQUEST['transaction_id']);
			// $xml_elements = new SimpleXMLElement($xml);
			// $transaction = array();
			// $t = array();
			// foreach($xml_elements as $key => $value)
			// {
			// 	$transaction[$key]=$value;
			// }
			$paystack = new PrestaPaystack();
			$email = 'email@email.com';
			$total = $amount;
			$date = '';
			$order_id = $txn_code;
			$status = 'approved';
                        //$v_transaction_id = $transaction['transaction_id'];
			$transaction_id = $txn_code;
      $p = implode('<br/>', $transaction);

			if (trim(strtolower($status)) == 'approved'){
				$return_url = __PS_BASE_URI__.'order-history';
				$params = array(
					array('value' => 'approved', 'name' => 'State'),
					array('value' => $email, 'name' => 'Email'),
					array('value' => $total, 'name' => 'Total'),
					array('value' => $date, 'name' => 'Date'),
					array('value' => $transaction_id, 'name' => 'Transaction ID'),
					array('value' => $status, 'name' => 'Status'),
					array('value' => $return_url, 'name' => 'return_url')
				);
        $paystack->validation();
			}elseif(trim(strtolower($status)) == 'pending'){
				$return_url = __PS_BASE_URI__.'order-history';
				$params = array(
					array('value' => 'pending', 'name' => 'State'),
					array('value' => $email, 'name' => 'Email'),
					array('value' => $total, 'name' => 'Total'),
					array('value' => $date, 'name' => 'Date'),
					array('value' => $transaction_id, 'name' => 'Transaction ID'),
					array('value' => $status, 'name' => 'Status'),
					array('value' => $return_url, 'name' => 'return_url')
				);
        $paystack->validation();

			}elseif(trim(strtolower($status)) != 'approved'){
				$return_url = __PS_BASE_URI__.'order-history';
				$params = array(
					array('value' => 'failed', 'name' => 'State'),
					array('value' => $email, 'name' => 'Email'),
					array('value' => $total, 'name' => 'Total'),
					array('value' => $date, 'name' => 'Date'),
					array('value' => $transaction_id, 'name' => 'Transaction ID'),
					array('value' => $status, 'name' => 'Status'),
					array('value' => $return_url, 'name' => 'return_url')
				);
        $paystack->validate();

        // $paystack->validateOrder((int)self::$cart->id, (int)Configuration::get('PS_OS_ERROR'), (float)self::$cart->getOrderTotal(),$paystack->displayName, NULL, array(), NULL, false, self::$cart->secure_key);
                                //$p = implode('<br/>', $params);
                                //$this->module->recordTransaction((int)self::$cart->id,$p,$status );
			}

                        //$this->module->recordTransaction((int)self::$cart->id,$p,$status );

		}
		return $params;
	}

	public function initContent()
	{
            parent::initContent();

            //if ($voguepay->active)
		//	{



				$param = $this->initParams();
				$this->context->smarty->assign(array(
				'formLink' => 'https://voguepay.com/pay/',
				'vogURedirection' => $param,
				// 'return_url' => $return_url
			));
			$this->setTemplate('return.tpl');

		//	}

	}
}
