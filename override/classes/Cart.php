<?php
/*
 * 
 */

Class Cart extends CartCore
{
	
	public function getCartMainCategory($cart_id)
	{

		if (!$cart_id) {
            return 11;	// default category ID
        }
		
				
		$sql = 'SELECT cp.id_category,count(*) as cnt FROM ' . _DB_PREFIX_ . 'cart_product as c LEFT JOIN ' . _DB_PREFIX_ . 'category_product as cp ON c.id_product = cp.id_product WHERE c.id_cart = '.(int)$cart_id.' AND cp.id_category IN (SELECT category_id FROM ' . _DB_PREFIX_ . 'categoriestoseller_categories) GROUP BY cp.id_category ORDER BY cnt DESC';
		
		
		$result = Db::getInstance()->getRow($sql);
		
		return $result['id_category'];
		
	}
}