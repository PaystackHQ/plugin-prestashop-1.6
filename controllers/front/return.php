<?php

class PrestaPaystackReturnModuleFrontcontroller extends ModuleFrontController{
    public $php_self = 'return.php';
    public $ssl = true;
    public $display_column_left = false;


    public function confirm_payment($code){
      $test_secretkey = Configuration::get('PAYSTACK_TEST_SECRETKEY');
      $test_publickey = Configuration::get('PAYSTACK_TEST_PUBLICKEY');
      $mode = Configuration::get('PAYSTACK_MODE');

  		header( 'HTTP/1.1 200 OK' );
  		$opts = array(
        "ssl"=>array(
              "verify_peer"=> false,
              // "verify_peer_name"=> false,
          ),
         'http'=>array(
  		    'method'=>"GET",
  		    'header'=>"Authorization: Bearer ".$test_secretkey."\r\n"
  		  )
  		);

      $context = stream_context_create($opts);
      $url = 'https://api.paystack.co/transaction/verify/'.$code;
      $result = file_get_contents($url, false, $context);

  		print_r($result);
      print_r($opts);
      die();

  		if ($result->data->status == "success") {
  			$paid = $result->data->amount/100;
  			$result = ['result' => 'success','amount' => $paid];

  		}else{
  			$result = ['result' => 'failed'];

  		}
  		return 	$result;
  	}
	public function initParams(){
            $params = [];
            //global $smarty;JFAZH8S
            //$transaction = array();
            $transaction = array();
		if(Tools::getValue('txn_code') !== '')
		{
      $txn_code = Tools::getValue('txn_code');
      $result = $this->confirm_payment($txn_code);
      echo "<pre>";
      echo $txn_code;
      print_r($result);
      die();
			$xml = file_get_contents('https://voguepay.com/?v_transaction_id='.$_REQUEST['transaction_id']);
			$xml_elements = new SimpleXMLElement($xml);
			$transaction = array();
			$t = array();
			foreach($xml_elements as $key => $value)
			{
				$transaction[$key]=$value;
			}
			$voguepay = new VoguePay();
			$email = $transaction['email'];
			$total = $transaction['total'];
			$date = $transaction['date'];
			$order_id = $transaction['merchant_ref'];
			$status = $transaction['status'];
                        //$v_transaction_id = $transaction['transaction_id'];
			$transaction_id = $transaction['transaction_id'];
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
                                $voguepay->validateOrder((int)self::$cart->id, (int)Configuration::get('PS_OS_PAYMENT'), (float)self::$cart->getOrderTotal(),$voguepay->displayName, NULL, array(), NULL, false, self::$cart->secure_key);
                                //$p = implode('<br/>', $params);
                                //$this->module->recordTransaction((int)self::$cart->id,$p,$status );

			}
			elseif(trim(strtolower($status)) == 'pending'){
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
                                $voguepay->validateOrder((int)self::$cart->id, (int)Configuration::get('VOGU_WAITING_PAYMENT'), (float)self::$cart->getOrderTotal(),$voguepay->displayName, NULL, array(), NULL, false, self::$cart->secure_key);
                                //$p = implode('<br/>', $params);
                                //$this->module->recordTransaction((int)self::$cart->id,$p,$status );

			}
			elseif(trim(strtolower($status)) != 'approved'){
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
                                $voguepay->validateOrder((int)self::$cart->id, (int)Configuration::get('PS_OS_ERROR'), (float)self::$cart->getOrderTotal(),$voguepay->displayName, NULL, array(), NULL, false, self::$cart->secure_key);
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
				'return_url' => $return_url
			));
			$this->setTemplate('return.tpl');

		//	}

	}
}
