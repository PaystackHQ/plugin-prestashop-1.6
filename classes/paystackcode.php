<?php

class Paystackcode extends ObjectModel
{
	/** @var string Name */
	public $id;
	public $cart_id;
	public $code;
	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'paystack_txncodes',
		'primary' => 'id',
		'fields' => array(
      'cart_id' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
      'code' => array('type' => self::TYPE_STRING, 'db_type' => 'varchar(255)', 'required' => true),
    ),
	);
  public function generate_new_code($length = 7){
    $characters = 'RSTUVW01234ABCDEFGHIJ56789KLMNOPQXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
  }
  public function check_code($code){
      $o_exist = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'paystack_txncodes`  WHERE `code` = "'.$code.'"');//Rproduct::where('code', '=', $code)->first();

      if (count($o_exist) > 0) {
          $result = true;
      } else {
          $result = false;
      }

      return $result;
  }
  public function generate_code(){
      $code = 0;
      $check = true;
          // $code = $this->generate_new_code();
          //2FEMZ8Q
          // $check = $this->check_code('2FEMZ8Q');

      while ($check) {
          $code = $this->generate_new_code();
          $check = $this->check_code($code);
      }

      return $code;
      // return $check;
  }
}
