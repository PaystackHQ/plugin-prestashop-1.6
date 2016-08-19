<?php
/**
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
class Paystackcode extends ObjectModel
{
	/** @var string Name */
	public $cart_id;
	public $code;
	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'paystack_txncodes',
		'primary' => 'id',
		// 'multilang' => true,
		'fields' => array(
      'cart_id' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
      'code' => array('type' => self::TYPE_STRING, 'db_type' => 'varchar(255)', 'required' => true),
      // 'lastname' => array('type' => self::TYPE_STRING, 'validate' => 'isName', 'size' => 20),
			// /* Lang fields */
			// 'cart_id' => 		array('type' => self::TYPE_INT, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 64),
			// 'lorem' => 		array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => false, 'size' => 64),
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
