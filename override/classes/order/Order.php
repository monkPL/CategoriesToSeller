<?php
/**
  * SQL:
 * -- ALTER TABLE `ps_orders` ADD `main_cat` INT NOT NULL AFTER `date_upd`; 
 */

Class Order extends OrderCore
{
	
	public $main_cat;
	
	public function __construct($id = null, $id_lang = null){
		self::$definition['fields']['main_cat'] = array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId');
		
		parent::__construct($id, $id_lang);
	}
	
	/**
	* generate order number format: [date]-[main category symbol][random number]
	*/
	public static function generateReference()
	{
		
		$data = date('y-m-d');
		$random_code = rand(1000,9999);
		$main_category_symbol = null;
		
        $context = Context::getContext();
		
		
		if($main_cat = Cart::getCartMainCategory($context->cart->id))
		{
			$main_category_symbol = Order::getMainCatChar($main_cat);
		}
		if(!$main_category_symbol)
			$main_category_symbol = 'g';	// set default value

		$gotowy_numer = $data.'-'.$main_category_symbol.$random_code;

		if (Order::isOrderNumberExist($gotowy_numer))
        {
            return $gotowy_numer;
        }
        else
        {
           	Order::generateReference();
        }

	}
  
	public function isOrderNumberExist($order_number)
	{
	    $res = Db::getInstance()->getValue(
            'SELECT `reference`
            FROM `' . _DB_PREFIX_ . 'orders`
            WHERE `reference` = "' .  $order_number .'"'
        );
		
		if(!$res)
			return TRUE;
			
		return FALSE;
	}
	
	public function getMainCatChar($main_cat_id)
	{

		$res = Db::getInstance()->getValue(
            'SELECT `orderchar`
            FROM `' . _DB_PREFIX_ . 'categoriestoseller_categories`
            WHERE `category_id` = "' .  $main_cat_id .'"'
        );
		
		return $res;
	}
}


