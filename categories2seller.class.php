<?php
/**
 * 2020 monkPL
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * prestashop@dpd.com.pl so we can send you a copy immediately.
 *
 *  @author    monkPL
 *  @copyright 2020 monkPL
 *  @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class Categories2Seller
{
	
	public function getSprzedawcy()
	{
		$results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
                'SELECT  id_employee, lastname, firstname, email FROM `' . _DB_PREFIX_ . 'employee` WHERE id_profile = 4'
            );
		return $results;
	}
	
	public function getMainCategories()
	{
		$results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
                'SELECT  c.id_category,cl.name FROM `' . _DB_PREFIX_ . 'category` as c JOIN `ps_category_lang` as cl ON c.id_category = cl.id_category AND cl.id_lang = 1 WHERE c.active = 1 AND c.id_parent=2'
            );
		return $results;
	}
	
	public function getCategoriesToSeller()
	{
		$results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
                'SELECT  * FROM `' . _DB_PREFIX_ . 'categoriestoseller`'
            );
		return $results;
	}
	
	public function getSellerCategories($seller_id)
	{
		$ret = array();
		$results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
                'SELECT category_id FROM `' . _DB_PREFIX_ . 'categoriestoseller` WHERE seller_id = '.(int)$seller_id
            );
		if($results)
		{
			foreach($results as $row)
			{
				$ret[] = $row['category_id'];
			}
		}
		return $ret;
	}
	
	public function saveValues($post)
	{
		Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('DELETE FROM `' . _DB_PREFIX_ . 'categoriestoseller`');
		if(isset($post['cat2sel']))
		{
			foreach ($post['cat2sel'] as $id_employee => $categories) {
				foreach($categories as $category_id)
				{
					DB::getInstance()->execute('
					INSERT INTO `' . _DB_PREFIX_ . 'categoriestoseller`
						(`seller_id`, `category_id`)
					VALUES
						("' . (int)$id_employee . '", "' . (int)$category_id . '")
				');
				}
			}
		}
		
	}
	
	public function getCategoriesToSellerCategories()
	{
		$results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
                'SELECT  * FROM `' . _DB_PREFIX_ . 'categoriestoseller_categories`'
            );
		return $results;
	}
	
	public function saveValuesCategories($post)
	{
		Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('DELETE FROM `' . _DB_PREFIX_ . 'categoriestoseller_categories`');
		if(isset($post['cat2sel_c']))
		{
			foreach ($post['cat2sel_c'] as $id_category => $orderchar) {
				DB::getInstance()->execute('
				INSERT INTO `' . _DB_PREFIX_ . 'categoriestoseller_categories`
					(`orderchar`, `category_id`)
				VALUES
					("' . $orderchar . '", "' . (int)$id_category . '")
			');
			}
		}
		
	}
	
}