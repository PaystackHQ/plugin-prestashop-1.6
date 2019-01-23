<?php
/**
 * 2016 Paystack
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/MIT
 *
 * @author     Paystack Payments <support@paystack.com>
 * @copyright  2016 Paystack
 * @license    https://opensource.org/licenses/MIT  MIT License
 */

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
        'table'   => 'paystack_txncodes',
        'primary' => 'id',
        'fields'  => array(
            'cart_id' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'code'    => array('type' => self::TYPE_STRING, 'db_type' => 'varchar(255)', 'required' => true),
        ),
    );
    public function generateNewCode($length = 10)
    {
        $characters = 'RSTUVW01234ABCDEFGHIJ56789KLMNOPQXYZ';
        $charactersLength = Tools::strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    public function checkCode($code)
    {
        $o_exist = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'paystack_txncodes`  WHERE `code` = "'.$code.'"');//Rproduct::where('code', '=', $code)->first();

        if (count($o_exist) > 0) {
            $result = true;
        } else {
            $result = false;
        }

        return $result;
    }
    public function generateCode()
    {
        $code = 0;
        $check = true;

        while ($check) {
            $code = $this->generateNewCode();
            $check = $this->checkCode($code);
        }

        return $code;
    }
}
