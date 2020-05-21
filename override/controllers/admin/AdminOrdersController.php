<?php


class AdminOrdersController extends AdminOrdersControllerCore
{
    public function __construct()
    {
        parent::__construct();
		
		$this->fields_list['cat_name'] = array(
	            'title' => 'Dział',
	            'type' => 'select',
	            'list' => $this->getListofMainCategories(),
	            'filter_key' => 'main_cat',
	            'filter_type' => 'int',
                'order_key' => 'cat_name'
	        );
			
	}
	
	public function getListofMainCategories()
	{
		$ret = array();
		$cats = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
                'SELECT  c.id_category,cl.name FROM `' . _DB_PREFIX_ . 'category` as c JOIN `ps_category_lang` as cl ON c.id_category = cl.id_category AND cl.id_lang = 1 WHERE c.active = 1 AND c.id_parent=2'
            );
		foreach ($cats as $row) {
            $ret[$row['id_category']] = $row['name'];
        }
		
		return $ret;
	}
}