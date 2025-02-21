<?php
/**
 * Copyright since 2007 Carmine Di Gruttola
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
 * @author    cdigruttola <c.digruttola@hotmail.it>
 * @copyright Copyright since 2007 Carmine Di Gruttola
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class LoginAsCustomer extends Module
{
    private $_html = '';
    private $_postErrors = [];

    public function __construct()
    {
        $this->name = 'loginascustomer';
        $this->tab = 'back_office_features';
        $this->version = '1.0.0';
        $this->author = 'cdigruttola';
        $this->controllers = ['login'];

        $this->bootstrap = true;
        parent::__construct();

        $this->ps_versions_compliancy = ['min' => '1.7.1.0', 'max' => _PS_VERSION_];
        $this->displayName = $this->trans('Login As Customer', [], 'Modules.Loginascustomer.Main');
        $this->description = $this->trans('Allows you login as customer', [], 'Modules.Loginascustomer.Main');
    }

    public function isUsingNewTranslationSystem()
    {
        return true;
    }

    public function install()
    {
        if (!parent::install() || !$this->registerHook('displayAdminCustomers')) {
            return false;
        }

        return true;
    }

    public function hookDisplayAdminCustomers($request)
    {
        $customer = new CustomerCore($request['id_customer']);
        $link = $this->context->link->getModuleLink($this->name, 'login', ['id_customer' => $customer->id, 'xtoken' => $this->makeToken($customer->id)]);

        if (!Validate::isLoadedObject($customer)) {
            return;
        }

        $this->smarty->assign(['link' => $link]);

        return $this->fetch('module:loginascustomer/views/templates/hook/displayAdminCustomers.tpl');
    }

    public function makeToken($id_customer)
    {
        return md5(_COOKIE_KEY_ . $id_customer . date('Ymd'));
    }
}
