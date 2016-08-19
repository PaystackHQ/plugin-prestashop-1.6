<?php

class PrestaPaystackConfirmModuleFrontcontroller extends ModuleFrontController{
    public $php_self = 'confirm.php';
    public $ssl = true;
    public $display_column_left = false;

    // public function __construct(){
    //   $params = [];
    //   $transaction = array();
  	// 	  $txn_code = 'Cool';//Tools::getValue('txn_code');
    //     $amount = '123343';
    //     $email = 'email@add.com';
    //     $verification = $this->verify_txn($txn_code);
    //
    //     if ($verification->status == 'success') {
    //       $email = $verification->data->customer->email;
    //       $date = $verification->data->transaction_date;
    //       $amount = $verification->data->amount/100;
    //       $status = 'approved';
    //     }else{
    //       $date = date("Y-m-d h:i:sa");
    //       $email = $email;
    //       $total = $amount;
    //       $status = 'failed';
    //     }
    //     echo "<pre>";
    //     echo $email."<br />";
    //     echo $date."<br />";
    //     echo $amount."<br />";
    //     echo $status."<br />";
    //
    //     print_r($verification);
    //     die();
    //
    //
  	// 	return $params;
  	// }
    public function verify_txn($code){
      $test_secretkey = Configuration::get('PAYSTACK_TEST_SECRETKEY');
      $live_secretkey = Configuration::get('PAYSTACK_LIVE_SECRETKEY');
      $mode = Configuration::get('PAYSTACK_MODE');

      if ($mode == 'test') {
        $key = $test_secretkey;
      }else{
        $key = $live_secretkey;
      }
      // $contextOptions = array(
      //     'ssl' => array(
      //         'verify_peer' => true,
      //         'cafile' => '/Applications/AMPPS/php-7.0/etc/cacert.pem',
      //         'ciphers' => 'HIGH:!SSLv2:!SSLv3',
      //     ),
      //     'http'=>array(
     // 		    'method'=>"GET",
      //       'header'=> array("Authorization: Bearer ".$key."\r\n","Connection: close\r\n","User-Agent: test\r\n)")
     // 		  )
      // );

      // $context = stream_context_create($contextOptions);
      // $url = 'https://api.paystack.co/transaction/verify/'.$code;
      // $request = file_get_contents($url, false, $context);
      // $result = json_decode($request);
      $result = json_decode('{
          "status": true,
          "message": "Verification successful",
          "data": {
            "amount": 168054,
            "transaction_date": "2016-08-19T14:16:44.000Z",
            "status": "success",
            "reference": "'.$code.'",
            "domain": "test",
            "authorization": {
              "authorization_code": "AUTH_2mnfo76b",
              "card_type": "visa",
              "last4": "1381",
              "exp_month": "01",
              "exp_year": "2020",
              "bank": "TEST BANK",
              "channel": "card",
              "reusable": true
            },
            "customer": {
              "first_name": "",
              "last_name": "",
              "email": "kendyson@kendyson.com"
            },
            "plan": null
          }
        }');

      // $result = json_decode('{
      //     "status": false,
      //     "message": "Invalid transaction reference"
      //   }');
      return $result;
    }
  	public function initParams(){
      $params = [];
      $transaction = array();
  		if(Tools::getValue('txn_code') !== ''){
        $txn_code = Tools::getValue('txn_code');
        $amount = Tools::getValue('amounttotal');
        $email = Tools::getValue('email');
        $verification = $this->verify_txn($txn_code);

  			$paystack = new PrestaPaystack();
        if ($verification->status == 'success') {
          $email = $verification->data->customer->email;
          $date = $verification->data->transaction_date;
          $total = $verification->data->amount/100;
          $status = 'approved';
        }else{
          $date = date("Y-m-d h:i:sa");
          $email = $email;
          $total = $amount;
          $status = 'failed';
        }

        $transaction_id = $txn_code;
        $p = implode('<br/>', $transaction);

  			if (trim(strtolower($status)) == 'approved'){
  				$params = array(
  					// array('value' => 'approved', 'name' => 'State'),
  					array('value' => $email, 'name' => 'Email'),
  					array('value' => $total, 'name' => 'Total'),
  					array('value' => $date, 'name' => 'Date'),
  					array('value' => $transaction_id, 'name' => 'Transaction ID'),
  					array('value' => $status, 'name' => 'Status'),
  				);
          $paystack->validation($verification);
  			}else{
  				$params = array(
  					// array('value' => 'failed', 'name' => 'Failed'),
  					array('value' => $email, 'name' => 'Email'),
  					array('value' => $total, 'name' => 'Total'),
  					array('value' => $date, 'name' => 'Date'),
  					array('value' => $transaction_id, 'name' => 'Transaction ID'),
  					array('value' => $status, 'name' => 'Status'),
  				);
          $paystack->validation($verification);

        }
        $this->context->smarty->assign('status', $status);
        $return_url = __PS_BASE_URI__.'order-history';
        $this->context->smarty->assign('return_url', $return_url);

    	}
  		return $params;
  	}

  	public function initContent(){
        parent::initContent();

				$param = $this->initParams();
				$this->context->smarty->assign(array(
  				'formLink' => 'https://voguepay.com/pay/',
  				'vogURedirection' => $param,
  			));
  			$this->setTemplate('return.tpl');
    }
}