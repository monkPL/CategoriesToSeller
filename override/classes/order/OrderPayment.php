<?php
/**
 * TODO:
 * - przypisanie opiekunów do kategorii (moduł?)
 * - pobranie listy produktów i znalezienie opiekuna zamówienia
 */

Class OrderPayment extends OrderPaymentCore
{
	public function __construct(){
		self::$definition['fields']['order_reference'] = array('type' => self::TYPE_STRING, 'validate' => 'isAnything', 'size' => 16);
		
		parent::__construct();
	}
}
	