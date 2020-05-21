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

if (!defined('_PS_VERSION_')) {
    exit;
}

require(dirname(__FILE__).'/categories2seller.class.php');

class categoriesToSeller extends Module
{
	protected $_errors = array();
	protected $_html = '';
	
	public function __construct()
    {
        $this->name = 'categoriestoseller';
        $this->tab = 'front_office_features';
        $this->version = '1.0';
        $this->author = 'monkPL';

        $this->bootstrap = true;
		
        parent::__construct();

        $this->displayName = $this->l('CategoriesToSeller');
        $this->description = $this->l('Przypisuje kategorie do poszczególnych sprzedawców');
        $this->ps_versions_compliancy = array('min' => '1.7.1.0', 'max' => _PS_VERSION_);
    }
	
	public function install($delete_params = true)
    {
        if (!parent::install()) {
            return false;
        }

        if ($delete_params) {
            if (!$this->installDb()) {
                return false;
            }
        }
		
		$this->registerHook('actionValidateOrder');
		$this->registerHook('actionAdminOrdersListingFieldsModifier');
		$this->registerHook('actionAdminOrdersListingResultsModifier');
		$this->registerHook('displayAdminOrderTop');
		$this->registerHook('actionAdminProductsListingFieldsModifier');
		$this->registerHook('displayProductActions');

        return true;
    }
	
	public function hookDisplayProductActions()
	{
		$cookie = new Cookie('psAdmin', '', (int)Configuration::get('PS_COOKIE_LIFETIME_BO'));

		if($cookie->id_employee)
		{
			$employee = new Employee((int)$cookie->id_employee);
			if (Validate::isLoadedObject($employee) && $employee->checkPassword((int)$cookie->id_employee, $cookie->passwd) && (!isset($cookie->remote_addr) || $cookie->remote_addr == ip2long(Tools::getRemoteAddr()) || !Configuration::get('PS_COOKIE_CHECKIP')))
			{
				$token = Tools::getAdminToken('AdminProducts'. (int) Tab::getIdFromClassName('AdminProducts'). (int) $cookie->id_employee);
				$this->context->smarty->assign(array(
					'admtoken' => $token,
					'product_id' => (int) Tools::getValue('id_product')
				));	
			}
		}
		return $this->display(__FILE__, 'undercart.tpl');
	}
	
	public function hookDisplayAdminOrderTop(array $params)
	{
		$id_order = $params['id_order'];
		
		$this->context->smarty->assign(array(
			//'subtitle'=> $subtitle
		));
		
		return $this->display(__FILE__, 'order_top.tpl');
	}
	
	public function hookActionValidateOrder(array $params)
	{
		$main_cat = Cart::getCartMainCategory($params['cart']->id);
		$results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
                'UPDATE `' . _DB_PREFIX_ . 'orders` SET main_cat = '.(int)$main_cat.' WHERE id_order = '.(int)$params['order']->id
            );
	}
	
	public function hookActionAdminProductsListingFieldsModifier($params)
	{
		
		$params['sql_select']['marza'] = array(
			'select' => 'ROUND((100 - ((p.wholesale_price*100)/p.price)),2)'
			);
	}

	public function hookActionAdminOrdersListingResultsModifier($params)
	{
		//foreach($params['list'])
	}
	
	public function hookActionAdminOrdersListingFieldsModifier($params)
	{
		
		$emp_id = (int)$this->context->employee->id;
		$categories_emp = Categories2Seller::getSellerCategories($emp_id); 
		
	    if (isset($params['select'])) {
	    	$params['join'] .= ' LEFT JOIN '._DB_PREFIX_.'category_lang clng ON (a.main_cat = clng.id_category AND clng.id_lang = 1 AND clng.id_shop = 1)';
			if($categories_emp)
			{
				$params['where'] .= ' AND a.main_cat IN ('.implode(',',$categories_emp).')';
			}

	        $params['select'] .= ', clng.name as cat_name, a.main_cat as main_cat';

	    }
	}
	
	
	public function installDb()
    {
        return (Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'categoriestoseller` (
			`id_stc` int(11) NOT NULL AUTO_INCREMENT,
  			`seller_id` int(11) NOT NULL,
  			`category_id` int(11) NOT NULL,
			INDEX (`id_stc`)
		) ENGINE = '._MYSQL_ENGINE_.' CHARACTER SET utf8 COLLATE utf8_general_ci;'));
    }
	
	public function uninstall($delete_params = true)
    {
        if (!parent::uninstall()) {
            return false;
        }

        if ($delete_params) {
            if (!$this->uninstallDB()) {
                return false;
            }
        }

        return true;
    }

    protected function uninstallDb()
    {
        Db::getInstance()->execute('DROP TABLE `'._DB_PREFIX_.'categoriestoseller`');
        return true;
    }

    public function reset()
    {
        if (!$this->uninstall(false)) {
            return false;
        }
        if (!$this->install(false)) {
            return false;
        }

        return true;
    }
	
	public function getContent()
    {
    	//$this->registerHook('displayProductActions');
    	//$this->registerHook('actionAdminProductsListingFieldsModifier');
    	//$this->registerHook('displayAdminOrderTop');
		//$this->registerHook('actionAdminOrdersListingResultsModifier');
    	//$this->registerHook('actionAdminOrdersListingFieldsModifier');
    	//$this->registerHook('actionValidateOrder');
    	$id_lang = (int)Context::getContext()->language->id;
        $languages = $this->context->controller->getLanguages();
        $default_language = (int)Configuration::get('PS_LANG_DEFAULT');
		
		$cts = new Categories2Seller;
		
		if (Tools::isSubmit('module_cat2sell'))
        {
            $cts->saveValues($_POST);
			$cts->saveValuesCategories($_POST);
			$redirect_uri = $this->module_url;
            Tools::redirectAdmin($redirect_uri);
        }
		
		
		$sprawdawcy = $cts->getSprzedawcy();
		$kategorie = $cts->getMainCategories();
		$przypisane = $cts->getCategoriesToSeller();
		$zamowienia_znak = $cts->getCategoriesToSellerCategories();
		
		$cat2sellers = array();
		foreach($przypisane as $row)
		{
			$cat2sellers[$row['seller_id']][] = $row['category_id'];
		}
		
		$cat2sellers_cat = array();
		foreach($zamowienia_znak as $row)
		{
			$cat2sellers_cat[$row['category_id']] = $row['orderchar'];
		}
		
		$this->context->smarty->assign('res', array(
			'sprzedawcy' => $sprawdawcy,
			'kategorie' => $kategorie,
			'przypisane' => $cat2sellers,
			'oznaczenia' => $cat2sellers_cat 
		));
        return $this->context->smarty->fetch('module:categoriestoseller/categoriestoseller.tpl');

	}
	
	public function fetchTemplate($name)
    {
        return $this->display(__FILE__, $name);
    }
	
}