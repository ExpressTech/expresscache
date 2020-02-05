<?php
/**
* 2007-2015 PrestaShop.
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

if (!defined('_PS_VERSION_')) {
    exit;
}

class Expresscache extends Module
{
    public $switch = 'switch';

    public function __construct()
    {
        $this->name = 'expresscache';
        $this->tab = 'front_office_features';
        $this->version = '3.5.1';
        $this->author = 'Express Tech';
        $this->need_instance = 0;
        $this->module_key = 'd687f9aa9748aca86046971d8d39a0f8';
        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Express Cache 3');
        $this->description = $this->l('Boost your Prestashop speed - upto 5x faster!');

        //for 1.5 helpforms doesn't understand "switch"
        if (version_compare(_PS_VERSION_, '1.6.0', '<')) {
            $this->switch = 'radio';
        }
    }

    public function install()
    {
        if (parent::install()
                && $this->registerHook('actionProductUpdate')
                && $this->registerHook('actionProductDelete')
                && $this->registerHook('actionCategoryUpdate')
                && $this->registerHook('actionCategoryDelete')
                && $this->registerHook('actionPaymentConfirmation')
                && $this->registerHook('actionObjectCmsUpdateAfter')
                && $this->registerHook('actionObjectCmsDeleteAfter')
                // && $this->registerHook('header')
                ) {
            if (_PS_VERSION_ > '1.6.0.0') {
                $this->registerHook('dashboardZoneTwo') == false;
                    // $this->registerHook('actionAdminControllerSetMedia') == false;
            }

            // Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'express_cache`');

            Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' ._DB_PREFIX_.'express_cache` (
                    `id_express_cache` int(11) unsigned AUTO_INCREMENT,
                    `page_id` varchar(32) NOT NULL,
                    `page_url` varchar(255) NOT NULL,
                    `id_currency` int(10) unsigned,
                    `id_language` int(10) unsigned,
                    `id_country` int(10) unsigned,
                    `id_shop` int(11) unsigned,
                    `cache` MEDIUMTEXT NOT NULL,
                    `cache_size` int(10) unsigned,
                    `hits` int(10) unsigned,
                    `miss` int(10) unsigned,
                    `hit_time` float(10,5) unsigned,
                    `miss_time` float(10,5) unsigned,
                    `entity_type` varchar(30) NOT NULL,
                    `id_entity` int(11) unsigned,
                    `is_mobile` int(1) unsigned,
                    `last_updated` datetime DEFAULT NULL,
                    `customer_groups` varchar(255) DEFAULT NULL,
              UNIQUE KEY `page_id_cache` (`page_id`, `id_currency`, `id_language`, `id_country`, `id_shop`, `is_mobile`, `customer_groups`),
              PRIMARY KEY (`id_express_cache`),
              INDEX (`page_id`), 
              INDEX (`id_currency`), 
              INDEX (`id_language`), 
              INDEX (`id_country`), 
              INDEX (`id_shop`), 
              INDEX (`last_updated`), 
              INDEX (`id_entity`), 
              INDEX (`entity_type`),
              INDEX (`is_mobile`),
              INDEX (`customer_groups`)

            )  ENGINE=' ._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;');

            // if(Db::getInstance()->getNumberError()) {
            //     $msg = Db::getInstance()->getMsgError();
            //     $this->warning = $this->l('Unable to install cache table. Error: ');
            //     $this->warning .= $msg;
            //     return false;
            // }
            //echo Db::getInstance()->getMsgError();

            Configuration::updateValue('EXPRESSCACHE_TIMEOUT', 60);

            // Configuration::updateValue('EXPRESSCACHE_PROFILING', 0);
            Configuration::updateValue('EXPRESSCACHE_CONTROLLERS', 'category,search,bestsales,pricesdrop,newproducts,manufacturer,supplier,product,index,cms');
            Configuration::updateValue('EXPRESSCACHE_STORE_IN_DB', 0);
            Configuration::updateValue('EXPRESSCACHE_TRIGGER_PRODUCT', 1);
            Configuration::updateValue('EXPRESSCACHE_TRIGGER_CATEGORY', 1);
            Configuration::updateValue('EXPRESSCACHE_TRIGGER_ORDER', 1);
            Configuration::updateValue('EXPRESSCACHE_GZIP', 1);
            Configuration::updateValue('EXPRESSCACHE_MOBILE', 0);
            Configuration::updateValue('EXPRESSCACHE_SEOEXP', 1);
            Configuration::updateValue('EXPRESSCACHE_UNIQUECOUNTRY', 1);
            Configuration::updateValue('EXPRESSCACHE_ENABLE_CUSTGROUP', false);
            Configuration::updateValue('EXPRESSCACHE_PRECACHE_LIMIT', 500);
            Configuration::updateValue('EXPRESSCACHE_PRECACHE_COUNTRY', $this->context->country->id);
            Configuration::updateValue('EXPRESSCACHE_PRECACHE_CURRENCY', $this->context->currency->id);
            Configuration::updateValue('EXPRESSCACHE_STORAGE_LIMIT', 0);
            Configuration::updateValue('ADVCACHEMGMT', 0);
            Configuration::updateValue('ADVCACHEMGMT_PATH', $_SERVER['HTTP_HOST'].__PS_BASE_URI__.'modules/'.$this->name.'/');

            Configuration::updateValue('EXPRESSCACHE_LOGGEDIN_SKIP', 0);

            Configuration::updateValue('EXPRESSCACHE_URLVARS', 'gclid,utm_content,utm_keyword,utm_medium,utm_source,utm_term');
            Configuration::updateValue('EXPRESSCACHE_ACTIVE_WIDGETS', '');

            Configuration::updateValue('EXPRESSCACHE_TRIGGER_CMS', 1);
            Configuration::updateValue('EXPRESSCACHE_TRIGGER_HOME', 1);
            Configuration::updateValue('EXPRESSCACHE_TRIGGER_LINKED', 0);

            $activehooks = array();

            //Install default dynamic hooks
            if (version_compare(_PS_VERSION_, '1.7.0', '>=')) {
                // $activehooks = $this->getModuleIdByHook('displayLeftColumn', 'blockviewed', $activehooks);
                $activehooks = $this->getModuleIdByHook('displayNav2', 'ps_customersignin', $activehooks);

                $activehooks = $this->getModuleIdByHook('displayNav2', 'ps_shoppingcart', $activehooks);
            } elseif (version_compare(_PS_VERSION_, '1.6.0', '>=')) {
                $activehooks = $this->getModuleIdByHook('displayLeftColumn', 'blockviewed', $activehooks);
                $activehooks = $this->getModuleIdByHook('displayNav', 'blockuserinfo', $activehooks);

                $activehooks = $this->getModuleIdByHook('displayTop', 'blockcart', $activehooks);
            } else {
                $activehooks = $this->getModuleIdByHook('displayLeftColumn', 'blockviewed', $activehooks);
                $activehooks = $this->getModuleIdByHook('displayTop', 'blockuserinfo', $activehooks);

                $activehooks = $this->getModuleIdByHook('displayTop', 'blockcart', $activehooks);
            }
            //dynamic hooks for third party modules.

            //uecookie
            if (Module::isInstalled('uecookie')) {
                $activehooks = $this->getModuleIdByHook('displayFooter', 'uecookie', $activehooks);
            }

            //eucookielawnotice
            if (Module::isInstalled('eucookielawnotice')) {
                $activehooks = $this->getModuleIdByHook('displayFooter', 'eucookielawnotice', $activehooks);
            }

            Configuration::updateValue('EXPRESSCACHE_ACTIVEHOOKS', serialize($activehooks));

            //install quick access start
            Db::getInstance()->insert('quick_access', array('new_window' => 0, 'link' => 'index.php?controller=AdminModules&configure=expresscache'));

            $id = Db::getInstance()->Insert_ID();
            $languages = Language::getLanguages(false);
            foreach ($languages as $lang) {
                Db::getInstance()->insert('quick_access_lang', array('id_quick_access' => $id, 'id_lang' => $lang['id_lang'], 'name' => 'Express Cache'));
            }

            Configuration::updateValue('EXPRESSCACHE_LINKID', $id);

            //install quick access end

            return true;
        }

        return false;
    }

    private function showETAd()
    {
        if (Tools::getIsset('close_et_ad')) {
            // echo md5($this->name.$this->version);
            Configuration::updateValue('EXPRESSTECH_AD'.Tools::substr(md5($this->name.$this->version), 0, 18), 'hide');
        }

        if (Configuration::get('EXPRESSTECH_AD'.Tools::substr(md5($this->name.$this->version), 0, 18)) !== 'hide') {
            $protocol = strpos(Tools::strtolower($_SERVER['SERVER_PROTOCOL']), 'https') === false ? 'http' : 'https';
            $host = $_SERVER['HTTP_HOST'];
            $script = $_SERVER['SCRIPT_NAME'];
            $params = $_SERVER['QUERY_STRING'];

            $url = $protocol.'://'.$host.$script.'?'.$params;

            $query = parse_url($url, PHP_URL_QUERY);

            $url .= '&close_et_ad=1';

            $this->context->smarty->assign(
                array(
                    'EXPRESSTECH_MODULE_NAME' => $this->name,
                    'EXPRESSTECH_MODULE_URL' => $url,
                )
            );

            if (file_exists($this->local_path.'views/templates/admin/etad_banner.tpl')) {
                return $this->context->smarty->fetch($this->local_path.'views/templates/admin/etad_banner.tpl');
            } else {
                return '';
            }
        } else {
            return '';
        }
    }

    private function showETToolbarAd()
    {
        $this->context->smarty->assign(
            array(
                'EXPRESSTECH_MODULE_NAME' => $this->name,
            )
        );

        if (file_exists($this->local_path.'views/templates/admin/etad_banner.tpl')) {
            return $this->context->smarty->fetch($this->local_path.'views/templates/admin/etad_toolbar.tpl');
        } else {
            return '';
        }
    }

    private function getModuleIdByHook($hook_name, $module_name, $activehooks)
    {
        $hook_modules = Hook::getHookModuleExecList($hook_name);
        foreach ($hook_modules as $module) {
            if ($module['module'] == $module_name) {
                $activehooks[$module['id_module']][] = $hook_name;
            }
        }

        return $activehooks;
    }

    public function uninstall()
    {

        // Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'express_cache');

        //delete quickaccess link
        $id = Configuration::get('EXPRESSCACHE_LINKID');

        if ($id) {
            Db::getInstance()->delete('quick_access', 'id_quick_access = '.$id);
            Db::getInstance()->delete('quick_access_lang', 'id_quick_access = '.$id);
        }

        Configuration::deleteByName('EXPRESSCACHE_LINKID');

        Configuration::deleteByName('EXPRESSTECH_AD');

        if (!parent::uninstall()) {
            return false;
        }

        return true;
    }

    /*
    Deprecated - 3.0.1

    public function hookHeader($params)
    {

        if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE')) {
            $path = Tools::getProtocol(Tools::usingSecureMode()).Configuration::get('ADVCACHEMGMT_PATH');

            if ($path[Tools::strlen($path) - 1] != '/') {
                $path = $path.'/';
            }
        } else {
            $path = $this->_path;
        }

        $this->context->controller->addJS($path.'views/js/expresscache.js');
    }
    */

    public function hookActionObjectCmsUpdateAfter($params)
    {
        if (Configuration::get('EXPRESSCACHE_TRIGGER_CMS')) {
            $cms = $params['object'];
            $id_cms = $cms->id;
            if ($id_cms) {
                $this->refreshEntityCache('cms', $id_cms);
            }
        }
    }

    public function hookActionObjectCmsDeleteAfter($params)
    {
        if (Configuration::get('EXPRESSCACHE_TRIGGER_CMS')) {
            $this->hookActionObjectCmsUpdateAfter($params);
        }
    }

    public function hookActionProductUpdate($params)
    {
        if (Configuration::get('EXPRESSCACHE_TRIGGER_PRODUCT')) {
            if (version_compare(_PS_VERSION_, '1.6.0', '>=')) {
                $id_product = $params['id_product'];
            } else {
                $product = $params['product'];
                $id_product = $product->id;
            }

            $this->refreshEntityCache('product', $id_product);
        }
    }

    public function hookActionProductDelete($params)
    {
        $this->hookActionProductUpdate($params);
    }

    public function hookActionCategoryUpdate($params)
    {
        if (Configuration::get('EXPRESSCACHE_TRIGGER_CATEGORY')) {
            $cat = $params['category'];
            $id_category = $cat->id;

            $this->refreshEntityCache('category', $id_category);
        }
    }

    public function hookActionCategoryDelete($params)
    {
        $this->hookActionCategoryUpdate($params);
    }

    public function hookActionPaymentConfirmation($params)
    {
        if (Configuration::get('EXPRESSCACHE_TRIGGER_ORDER')) {
            $id_order = $params['id_order'];
            $order = new Order((int) $id_order);
            foreach ($order->getProducts() as $product) {
                $this->refreshEntityCache('product', (int) $product['product_id']);
            }
        }
    }

    public function hookDashboardZoneTwo($params)
    {
        $id_shop = (int) $this->context->shop->id;
        $cache_entries = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            'SELECT count(*) 
            FROM ' ._DB_PREFIX_."express_cache where cache!='NULL' and id_shop = {$id_shop}"
        );

        if (!$cache_entries) {
            $cache_entries = 0;
        }

        $expresscachelink = $this->context->link->getAdminLink('AdminModules', true).'&configure='.$this->name;

        $this->context->smarty->assign(array(
            'expresscachelink' => $expresscachelink,
            'cache_entries' => $cache_entries,
            'showHitPercentage' => $this->showHitPercentage(),
            'showTimeSaved' => $this->showTimeSaved(),
            'showSpaceUsed' => $this->showSpaceUsed()
        ));
        return $this->display(__FILE__, 'views/templates/hook/dashboard.tpl');
    }

    private function refreshEntityCache($entity_type, $id_entity)
    {
        Db::getInstance()->execute(
            'UPDATE '._DB_PREFIX_."express_cache set cache='NULL', cache_size=0 
            where entity_type='$entity_type' and id_entity = $id_entity"
        );

        if (Configuration::get('EXPRESSCACHE_TRIGGER_HOME')) {
            Db::getInstance()->execute('UPDATE '._DB_PREFIX_."express_cache set cache='NULL', cache_size=0 
                    where entity_type='index'");
        }

        if (Configuration::get('EXPRESSCACHE_TRIGGER_LINKED') && $entity_type == 'product') {
            $product = new Product((int) $id_entity);
            if ($product) {
                $categories = $product->getCategories();
                foreach ($categories as $id_category) {
                    Db::getInstance()->execute(
                        'UPDATE '._DB_PREFIX_."express_cache set cache='NULL', cache_size=0 
                        where entity_type='category' and id_entity = $id_category"
                    );
                }
            }
        }
    }


    private function sanitizeForm($form)
    {
        $form = str_replace('<script>', "\n\n", $form);
        $form = str_replace('</script>', '', $form);
        return $form;
        $form = preg_replace('#^\s*//.+$#m', "", $form);
        $form = str_replace(array("\r", "\t"), "", $form);
        // $form =  htmlentities($form);
        $form = str_replace(array("\n"), " \\\n", $form);

        // return json_encode(utf8_encode($form));
        //aahhh
        // $form = str_replace(array("\r","\n"),"",$form);
        $form = str_replace('"', '\"', $form);
        $form = str_replace('</script>', '', $form);
        return $form;
    }

    public function getContent()
    {

         // $this->registerHook('dashboardZoneTwo') == false;
        
        $html = '';

        $id_shop = (int) $this->context->shop->id;

        //Display errors if there is a configuration mistake in the shop itself

        if (Configuration::get('PS_DISABLE_OVERRIDES') === '1') {
            $html .= $this->displayError($this->l('Overrides are disabled. Express Cache will not work'));
        }

        // If we try to delete the cache
        if ((bool) Tools::isSubmit('clearStats') == true) {
            Db::getInstance()->execute('UPDATE '._DB_PREFIX_."express_cache set hits=0, miss=0, hit_time=0, miss_time=0 where id_shop = {$id_shop}");

            $html .= $this->displayConfirmation($this->l('Stats cleared'));
        }

        if (Tools::getValue('getStats', 0)) {
            echo $this->showStats(true);
            exit;
        }

        if ((bool) Tools::isSubmit('clearCache')) {
            //delete cached db rows

            Db::getInstance()->execute('UPDATE '._DB_PREFIX_."express_cache set cache='NULL', cache_size=0 where id_shop = {$id_shop}");

            //delete cached files from the filesystem as well
            $cache_dir = _PS_MODULE_DIR_.'expresscache/cache/';

            //delete specific files in a multi store environment.
            if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE')) {
                $cache_dir .= $id_shop.'/';
            }

            $files = glob($cache_dir.'*');
             // get all file names
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                } elseif (is_dir($file)) {
                    $this->rrmdir($file);
                }
            }

            $html .= $this->displayConfirmation($this->l('Cache cleared'));
        }

        $cache_entries = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            'SELECT count(*) 
            FROM ' ._DB_PREFIX_."express_cache where cache!='NULL' and id_shop = {$id_shop}"
        );

        if (!$cache_entries) {
            $cache_entries = 0;
        }

        //remove for PS addons
        //$html .= $this->showETAd();
        //$html .= $this->showETToolbarAd();

        

        Configuration::updateValue('ADVCACHEMGMT_PATH', $_SERVER['HTTP_HOST'].__PS_BASE_URI__.'modules/'.$this->name.'/');

        


        $output = '';

        if (((bool) Tools::isSubmit('submitExpresscache')) == true) {
            $this->_postValidation();
            if (count($this->_postErrors) == 0) { //Validation
                $this->postProcess();
                $html .= $this->displayConfirmation($this->l('Settings Saved'));
            } else {
                foreach ($this->_postErrors as $err) {
                    $html .= $this->displayError($err);
                }
            }
        }

        $module_url = Tools::getProtocol(Tools::usingSecureMode()).$_SERVER['HTTP_HOST'].$this->getPathUri();

        $cron_url = $module_url.'expresscache-clearcache.php'.'?token='.Tools::substr(Tools::encrypt('expresscache/index'), 0, 10).'&id_shop='.$this->context->shop->id;

        $precache_cron_url = $module_url.'precache-cron.php'.'?token='.Tools::substr(Tools::encrypt('expresscache/index'), 0, 10).'&id_lang=IDLANG&id_country=IDCOUNTRY&id_currency=IDCURRENCY&num_products=NUMPRODUCTS&id_shop='.$this->context->shop->id;

        // echo $precache_cron_url;exit;
        //remove -
        Configuration::updateValue('EXPRESSCACHE_PRECACHE_CRONURL', $precache_cron_url);

        $this->context->smarty->assign(array(
            'action' => '',
            'cache_entries' => $cache_entries,
            'showHitPercentage' => $this->showHitPercentage(),
            'showTimeSaved' => $this->showTimeSaved(),
            'showSpaceUsed' => $this->showSpaceUsed(),
            'cron_url' => $cron_url,
            'precache_cron_url' => $precache_cron_url,
            'EXPRESSCACHE_showStats' => $this->showStats(false),
            'multishop' => Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE'),
            'shopname' => $this->context->shop->name,
        ));

        $this->showDynHooks();


        $selected_tab = Tools::getValue('selected_tab', 0);
        $this->context->smarty->assign('selected_tab', $selected_tab);

        //dynamic widgets

        $active_widgets = unserialize(Configuration::get('EXPRESSCACHE_ACTIVE_WIDGETS'));

        $this->context->smarty->assign(array(
            'active_widgets_arr' => $active_widgets
        ));


        // $html .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/settings.tpl');


        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitExpresscache';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        $form_basic = $this->sanitizeForm($helper->generateForm(array($this->getConfigFormBasic())));


        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        $form_advanced = $this->sanitizeForm($helper->generateForm(array($this->getConfigFormAdvanced())));


        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        $form_precaching = $this->sanitizeForm($helper->generateForm(array($this->getConfigFormPreCaching())));

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        $form_triggers = $this->sanitizeForm($helper->generateForm(array($this->getConfigFormTriggers())));

        //convert the form in a single piece
        
        $this->context->smarty->assign(
            array(
                    "forms" => array(
                    "form_basic" => $form_basic,
                    "form_advanced" => $form_advanced,
                    "form_precaching" => $form_precaching,
                    "form_triggers" => $form_triggers
                )
            )
        );

        $ps15 = 0;

        if (version_compare(_PS_VERSION_, '1.6.0', '<')) {
            $ps15 = 1;
        }

        $this->context->smarty->assign('ps15', $ps15);

        $ps17 = 0;

        if (version_compare(_PS_VERSION_, '1.7.0', '>=')) {
            $ps17 = 1;
        }

        $this->context->smarty->assign('ps17', $ps17);
        

        $html .= $this->display(__FILE__, 'views/templates/admin/newsettings.tpl');

        return $html.$this->display(__FILE__, 'views/templates/admin/prestui/ps-tags.tpl');
       
        return $html;
    }

    protected function _postValidation()
    {
        $this->_postErrors = array();

        $int_vars = array( $this->l('Cache timeout') => 'EXPRESSCACHE_TIMEOUT', $this->l('Cron Product Limit') => 'EXPRESSCACHE_PRECACHE_LIMIT', $this->l('Cache storage limit') => 'EXPRESSCACHE_STORAGE_LIMIT');

        foreach ($int_vars as $field_name => $var) {
            if (!Validate::isInt(Tools::getValue($var))) {
                $this->_postErrors[] = $field_name.$this->l(' should be a integer value');
            }
        }
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigFormBasic()
    {
        return array(
            'form' => array(
                
                'input' => array(
                    array(
                        'col' => 2,
                        'type' => 'text',
                        'desc' => $this->l('Display live page after this timeout'),
                        'name' => 'EXPRESSCACHE_TIMEOUT',
                        'label' => $this->l('Cache timeout'),
                        // 'class' => 'fixed-width-sm',
                        'suffix' => 'mins'
                    ),
                    array(
                        'type'      => 'radio',
                        'label'     => $this->l('Live Cache Editor'),
                        'desc'      => $this->l('Set visibility of live cache editor. You will be able to see and manage live cache status from front office'),
                        'name'      => 'ADVCACHEMGMT',
                        'required'  => true,
                        'class'     => 't',
                        // 'is_bool'   => true,
                        'values' => array(
                            array(
                              'id'    => 'active_all',
                              'value' => 2,
                              'label' => $this->l('Visible to All')
                            ),
                            array(
                              'id'    => 'active_current',
                              'value' => 1,
                              'label' => $this->l('Visible to Current Employee')
                            ),
                            array(
                              'id'    => 'active_disabled',
                              'value' => 0,
                              'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => $this->switch,
                        'class'     => 't',
                        'is_bool'   => true,
                        'label' => $this->l('Skip caching for logged in users'),
                        'name' => 'EXPRESSCACHE_LOGGEDIN_SKIP',
                        'is_bool' => true,
                        'desc' => $this->l('You can disable caching for logged in users by enabling this. Useful when \'blockuserinfo\' module is not configured with  dynamic hook.'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => $this->switch,
                        'class'     => 't',
                        'label' => $this->l('Disable caching on mobile devices'),
                        'name' => 'EXPRESSCACHE_MOBILE',
                        'is_bool' => true,
                        'desc' => $this->l('If caching for mobile pages is not working use this option.'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => $this->switch,
                        'class'     => 't',
                        'label' => $this->l('Never expire cache for bots'),
                        'name' => 'EXPRESSCACHE_SEOEXP',
                        'is_bool' => true,
                        'desc' => $this->l('If a cache entry exist (even if timeout is over), serve the page to a search engine bot. Improves SEO.'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ),
                        ),

                    ),

                    
                    
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }


    /**
     * Create the structure of your form.
     */
    protected function getConfigFormAdvanced()
    {
        // var_dump($currencies);exit;
        return array(
            'form' => array(
                
                'input' => array(
                    array(
                        'type' => $this->switch,
                        'class'     => 't',
                        'label' => $this->l('Unique Cache per Country'),
                        'name' => 'EXPRESSCACHE_UNIQUECOUNTRY',
                        'is_bool' => true,
                        'desc' => $this->l('Enable this if your shop shows different content based on the country a user is visiting from.'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => $this->switch,
                        'class'     => 't',
                        'label' => $this->l('Enable Customer Group Caching'),
                        'name' => 'EXPRESSCACHE_ENABLE_CUSTGROUP',
                        'is_bool' => true,
                        'desc' => $this->l('If your shop offers unique discounts or view for customer groups, enable this.'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'col' => 8,
                        'rows' => 5, //PS 1.5
                        'cols' => 60, //PS 1.5
                        'type' => 'textarea',
                        'desc' => $this->l('Comma seperated listed of controllers to Cache'),
                        'name' => 'EXPRESSCACHE_CONTROLLERS',
                        'label' => $this->l('What to Cache?'),
                    ),
                    array(
                        'col' => 8,
                        'type' => 'textarea',
                        'rows' => 5, //PS 1.5
                        'cols' => 60, //PS 1.5
                        'desc' => $this->l('Ignore these URL parameters while creating a unique cache'),
                        'name' => 'EXPRESSCACHE_URLVARS',
                        'label' => $this->l('Ignore URL variables'),
                    ),

                    // array(
                    //     'col' => 8,
                    //     'type' => 'textarea',
                    //     // 'autoload_rte' => true,
                    //     'rows' => 5, //PS 1.5
                    //     'cols' => 60, //PS 1.5
                    //     'desc' => $this->l('Enter each module_name,hook_name combination as new line to make the combination as active widget.'),
                    //     'name' => 'EXPRESSCACHE_ACTIVE_WIDGETS',
                    //     'label' => $this->l('Dynamic Widgets'),
                    // ),
                    array(
                        'type' => $this->switch,
                        'class'     => 't',
                        'label' => $this->l('Compress Cache'),
                        'name' => 'EXPRESSCACHE_GZIP',
                        'is_bool' => true,
                        'desc' => $this->l('Store cache files in compressed format (gzip)'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'col' => 2,
                        'type' => 'text',
                        'desc' => $this->l('0 = No limit'),
                        'name' => 'EXPRESSCACHE_STORAGE_LIMIT',
                        'label' => $this->l('Cache storage limit'),
                        // 'class' => 'fixed-width-sm',
                        'suffix' => 'MB'
                    ),

                    
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }


     /**
     * Create the structure of your form.
     */
    protected function getConfigFormTriggers()
    {
        return array(
            'form' => array(
                
                'input' => array(
                    
                    array(
                        'type' => $this->switch,
                        'class'     => 't',
                        'label' => $this->l('Refresh Cache on Product Updation'),
                        'name' => 'EXPRESSCACHE_TRIGGER_PRODUCT',
                        'is_bool' => true,
                        'desc' => $this->l('Refreshes the corresponding cache if a product is updated or deleted'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => $this->switch,
                        'class'     => 't',
                        'label' => $this->l('Refresh Cache for Linked Categories'),
                        'name' => 'EXPRESSCACHE_TRIGGER_LINKED',
                        'is_bool' => true,
                        'desc' => $this->l('Refreshes the categories cache when product is updated or deleted'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => $this->switch,
                        'class'     => 't',
                        'label' => $this->l('Refresh Cache on Category Updation'),
                        'name' => 'EXPRESSCACHE_TRIGGER_CATEGORY',
                        'is_bool' => true,
                        'desc' => $this->l('Refreshes the corresponding cache if a category is updated or deleted'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => $this->switch,
                        'class'     => 't',
                        'label' => $this->l('Refresh Cache on Order Confirmation'),
                        'name' => 'EXPRESSCACHE_TRIGGER_ORDER',
                        'is_bool' => true,
                        'desc' => $this->l('Refreshes the corresponding products cache when a order is confirmed'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => $this->switch,
                        'class'     => 't',
                        'label' => $this->l('Refresh Home Page Cache'),
                        'name' => 'EXPRESSCACHE_TRIGGER_HOME',
                        'is_bool' => true,
                        'desc' => $this->l('Refreshes the home (index) page cache when a product / category / order / cms is updated or deleted'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => $this->switch,
                        'class'     => 't',
                        'label' => $this->l('Refresh CMS Cache'),
                        'name' => 'EXPRESSCACHE_TRIGGER_CMS',
                        'is_bool' => true,
                        'desc' => $this->l('Refreshes the CMS page cache when a cms page is added, updated or deleted'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    
                    
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigFormPreCaching()
    {
        $selected_country = Configuration::get('EXPRESSCACHE_PRECACHE_COUNTRY', 0);
        $countries = Country::getCountries((int) $this->context->language->id, true);
        $id_shop = (int) $this->context->shop->id;

        $option_countries = array();
        foreach ($countries as $id_country => $country) {
            // var_dump($country);
            $selected = '';
            if ($selected_country != 0 && $id_country == $selected_country) {
                $selected = ' selected';
            }
            $option_countries[] = array(
                'id_country' => $id_country,
                'country_name' => $country['name']
            );
        }

        $currencies = Currency::getCurrencies(true, true);
        $selected_currency = Configuration::get('EXPRESSCACHE_PRECACHE_CURRENCY', 0);
        $option_currencies = array();
        // var_dump($currencies);
        foreach ($currencies as $currency) {
            // var_dump($id_currency);
            $selected = '';
            if ($selected_currency != 0 && $currency->id == $selected_currency) {
                $selected = ' selected';
            }
            // $option_currencies[$currency->id] = array('selected' => $selected, 'iso_code' => $currency->iso_code);
            $option_currencies[] = array(
                'id_currency' => $currency->id,
                'iso_code' => $currency->iso_code
            );
        }


        $languages = Language::getLanguages(true, $id_shop);
        // var_dump($languages);exit;
        $selected_language = Configuration::get('EXPRESSCACHE_PRECACHE_LANGUAGE', 0);
        $option_languages = array();
        // var_dump($currencies);
        foreach ($languages as $language) {
            // var_dump($language);
            // $selected = '';
            // if ($selected_language != 0 && $language->id == $selected_language) {
            //     $selected = ' selected';
            // }
            // $option_currencies[$currency->id] = array('selected' => $selected, 'iso_code' => $currency->iso_code);
            $option_languages[] = array(
                'id_lang' => $language['id_lang'],
                'name' => $language['name']
            );
        }

        // var_dump($option_languages);exit;


        // var_dump($currencies);exit;
        return array(
            'form' => array(
                
                'input' => array(
                    array(
                        'col' => 2,
                        'type' => 'text',
                        'desc' => $this->l('Number of products to pre-cache on each execution of pre-caching CRON url.'),
                        'name' => 'EXPRESSCACHE_PRECACHE_LIMIT',
                        'label' => $this->l('Cron Product Limit'),
                        // 'class' => 'fixed-width-sm',
                        'suffix' => 'products'
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Cron Bot Country'),
                        'name' => 'EXPRESSCACHE_PRECACHE_COUNTRY',
                        // 'desc' => $this->l('Dark or Light'),
                        'options' => array(
                            'query' => $option_countries,
                            'id' => 'id_country',
                            'name' => 'country_name',
                        ),
                    ),

                    array(
                        'type' => 'select',
                        'label' => $this->l('Cron Bot Currency'),
                        'name' => 'EXPRESSCACHE_PRECACHE_CURRENCY',
                        // 'desc' => $this->l('Dark or Light'),
                        'options' => array(
                            'query' => $option_currencies,
                            'id' => 'id_currency',
                            'name' => 'iso_code',
                        ),
                    ),

                    array(
                        'type' => 'select',
                        'label' => $this->l('Cron Bot Language'),
                        'name' => 'EXPRESSCACHE_PRECACHE_LANGUAGE',
                        // 'desc' => $this->l('Dark or Light'),
                        'options' => array(
                            'query' => $option_languages,
                            'id' => 'id_lang',
                            'name' => 'name',
                        ),
                    ),

                    array(
                        'type' => 'textarea',
                        'rows' => 3, //PS 1.5
                        'cols' => 120, //PS 1.5
                        'label' => $this->l('Cron URL'),
                        'name' => 'EXPRESSCACHE_PRECACHE_CRONURL',
                        // 'desc' => $this->l('Dark or Light'),
                    ),

                    
                    
                ),
                // 'submit' => array(
                //     'title' => $this->l('Save'),
                // ),
            ),
        );
    }


    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            if (Tools::getIsset($key)) { //since we have multiple forms in the configuration panes
                Configuration::updateValue($key, Tools::getValue($key));
            }
        }

        if (Tools::getIsset('ADVCACHEMGMT')) {
            $ec_cookie = new Cookie('ec_cookie');
            // $expresscache_cookie = $ec_cookie->advcachemgmt ? $ec_cookie->advcachemgmt : 0;

            $expresscache_cookie = Tools::getValue('ADVCACHEMGMT', 0);
            $ec_cookie->advcachemgmt = $expresscache_cookie;
        }

        //update settings for dynamic hooks

        if (is_array(Tools::getValue('chk_activehook', 0))) {
            $activehooks = Tools::getValue('chk_activehook');

            $config_activehook = array();
            foreach ($activehooks as $mod_hook) {
                $mod_hook = explode('_', $mod_hook);
                $config_activehook[$mod_hook[0]][] = $mod_hook[1];
            }
            Configuration::updateValue('EXPRESSCACHE_ACTIVEHOOKS', serialize($config_activehook));
        }

        //update settings for dynamic widgets

        if (is_array(Tools::getValue('activewidgets', 0))) {
            $activewidgets = Tools::getValue('activewidgets');

            $config_activewidget = array();
            foreach ($activewidgets as $mod_hook) {
                if($mod_hook) {
                    // $mod_hook = explode(',', $mod_hook);
                    // $config_activewidget[] = array($mod_hook[0] => $mod_hook[1]);
                    $config_activewidget[] = $mod_hook;
                }
                
            }

            // var_dump($config_activewidget);exit;
            Configuration::updateValue('EXPRESSCACHE_ACTIVE_WIDGETS', serialize($config_activewidget));
        }

        // else {
        //     Configuration::updateValue('EXPRESSCACHE_ACTIVEHOOKS', '');
        // }
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        // var_dump(Configuration::get('EXPRESSCACHE_ACTIVE_WIDGETS'));exit;
        return array(
            'EXPRESSCACHE_TIMEOUT' => Configuration::get('EXPRESSCACHE_TIMEOUT'),
            // 'ADVCACHEMGMT' => Tools::getValue('ADVCACHEMGMT', Configuration::get('ADVCACHEMGMT')) == 2 && $expresscache_cookie ? 'checked="checked" ' : ''),
            // 'ADVCACHEMGMT_emp' => (Tools::getValue('ADVCACHEMGMT', Configuration::get('ADVCACHEMGMT')) == 1 || $expresscache_cookie == 1 ? 'checked="checked" ' : ''),
            // 'ADVCACHEMGMT_off' => (!Tools::getValue('ADVCACHEMGMT', Configuration::get('ADVCACHEMGMT')) || !$expresscache_cookie ? 'checked="checked" ' : ''),
            'ADVCACHEMGMT' => Configuration::get('ADVCACHEMGMT'),
            'EXPRESSCACHE_LOGGEDIN_SKIP' => Configuration::get('EXPRESSCACHE_LOGGEDIN_SKIP'),
            'EXPRESSCACHE_MOBILE' => Configuration::get('EXPRESSCACHE_MOBILE'),
            'EXPRESSCACHE_SEOEXP' => Configuration::get('EXPRESSCACHE_SEOEXP'),
            'EXPRESSCACHE_CONTROLLERS' => Configuration::get('EXPRESSCACHE_CONTROLLERS'),
            'EXPRESSCACHE_URLVARS' => Configuration::get('EXPRESSCACHE_URLVARS'),
            //'EXPRESSCACHE_ACTIVE_WIDGETS' => Configuration::get('EXPRESSCACHE_ACTIVE_WIDGETS'),
            'EXPRESSCACHE_PRECACHE_LIMIT' => Configuration::get('EXPRESSCACHE_PRECACHE_LIMIT'),
            'EXPRESSCACHE_PRECACHE_LANGUAGE' => Configuration::get('EXPRESSCACHE_PRECACHE_LANGUAGE'),
            'EXPRESSCACHE_PRECACHE_CURRENCY' => Configuration::get('EXPRESSCACHE_PRECACHE_CURRENCY'),
            'EXPRESSCACHE_PRECACHE_COUNTRY' => Configuration::get('EXPRESSCACHE_PRECACHE_COUNTRY'),
            'EXPRESSCACHE_PRECACHE_CRONURL' => Configuration::get('EXPRESSCACHE_PRECACHE_CRONURL'),
            'EXPRESSCACHE_STORAGE_LIMIT' => Configuration::get('EXPRESSCACHE_STORAGE_LIMIT'),
            'EXPRESSCACHE_UNIQUECOUNTRY' =>  Configuration::get('EXPRESSCACHE_UNIQUECOUNTRY'),
            'EXPRESSCACHE_ENABLE_CUSTGROUP' =>  Configuration::get('EXPRESSCACHE_ENABLE_CUSTGROUP'),
            // 'EXPRESSCACHE_OPTION_COUNTRIES' => $option_countries,
            // 'EXPRESSCACHE_OPTION_CURRENCIES' => $option_currencies,
            // 'EXPRESSCACHE_showDynHooks' => $this->showDynHooks(),
            'EXPRESSCACHE_TRIGGER_PRODUCT' => Configuration::get('EXPRESSCACHE_TRIGGER_PRODUCT'),
            'EXPRESSCACHE_TRIGGER_LINKED' => Configuration::get('EXPRESSCACHE_TRIGGER_LINKED'),
            'EXPRESSCACHE_TRIGGER_CATEGORY' => Configuration::get('EXPRESSCACHE_TRIGGER_CATEGORY'),
            'EXPRESSCACHE_TRIGGER_ORDER' => Configuration::get('EXPRESSCACHE_TRIGGER_ORDER'),
            'EXPRESSCACHE_TRIGGER_HOME' => Configuration::get('EXPRESSCACHE_TRIGGER_HOME'),
            'EXPRESSCACHE_TRIGGER_CMS' => Configuration::get('EXPRESSCACHE_TRIGGER_CMS'),
            'EXPRESSCACHE_GZIP' => Configuration::get('EXPRESSCACHE_GZIP'),
            'EXPRESSCACHE_showStats' => $this->showStats(false),
        );
    }

    private function showDynHooks()
    {
        Cache::clean('hook_module_list');
        $module_hooks = Hook::getHookModuleList();
        $html = '';
        $mod_hook_array = array();
        $modules = array();
        $mod_logos = array();
        $activehooks = unserialize(Configuration::get('EXPRESSCACHE_ACTIVEHOOKS'));

        foreach ($module_hooks as $module_hook) {
            foreach ($module_hook as $m) {
                if (Module::isEnabled($m['name'])) {
                    $hook_name = Hook::getNameById($m['id_hook']);
                    //skip action based hooks since Express Cache does hinder at that point.
                    if (strpos($hook_name, 'action') !== false || strpos($hook_name, 'action') === 0) {
                        continue;
                    }

                    //skip backoffice based hooks since it Express Cache does hinder at that point.
                    if (strpos($hook_name, 'displayAdminStats') !== false || strpos($hook_name, 'BackOffice') !== false) {
                        continue;
                    }
                    // echo $activehooks[$m['id_module']];
                    $checked = $m['id_module'] && is_array($activehooks) && array_key_exists($m['id_module'], $activehooks) && in_array($hook_name, $activehooks[$m['id_module']]) ? ' checked' : '';

                    $module_fullname = Module::getModuleName($m['name']);

                    $mod_hook_array[$m['name'].'|'.$module_fullname][] = array('id_module' => $m['id_module'], 'hook_name' => $hook_name, 'checked' => $checked, 'hook_name' => $hook_name);

                    if(is_readable('../modules/'.$m['name'].'/logo.gif')) {
                        $mod_logos[$m['name']] = '../modules/'.$m['name'].'/logo.gif';
                    } elseif(is_readable('../modules/'.$m['name'].'/logo.png')) {
                        $mod_logos[$m['name']] = '../modules/'.$m['name'].'/logo.png';
                    } else {
                        $mod_logos[$m['name']] = '';
                    }
                    

                    $modules[$m['id_module']] = $m['name'];
                }
            }
        }

       

        $this->context->smarty->assign(
            array(
                'mod_hook_array' => $mod_hook_array,
                'mod_logos' => $mod_logos
            )
        );

        return;
    }

    private function showStats($renderPartial)
    {
        $id_shop = (int) $this->context->shop->id;
        $where_shop = '';
        if ($id_shop) {
            $where_shop = " and id_shop = $id_shop ";
        }
        $html = '';
        //var_dump($this->context->shop);
        // $html .= $id_shop;
        $cache = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT page_url, sum(hits) as hits, sum(miss) as miss, max(last_updated), is_mobile 
            FROM ' ._DB_PREFIX_."express_cache where (cache!='NULL' or cache_size>0 )
            $where_shop group by page_url, is_mobile order by hits desc limit 0,100"
        );

        // echo Db::getInstance()->getMsgError();exit;

        if (!$cache) {
            return $this->l('Cache is empty');
        } else {
            return Tools::jsonEncode($cache);
        }
        

        // if (count($cache) >= 100) {
        //     $html .= '<br>Showing Top 100 entries';
        // }

        // return $html;
    }

    private function showHitPercentage()
    {

        // $html = '';
        $hits_miss = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
                        SELECT sum(hits) as s_hits, sum(miss) as s_miss
                        FROM ' ._DB_PREFIX_."express_cache where cache!='NULL'");
        if (($hits_miss['s_miss'] + $hits_miss['s_hits']) == 0) {
            return '0%';
        }

        $hitper = $hits_miss['s_hits'] / ($hits_miss['s_miss'] + $hits_miss['s_hits']);

        return round($hitper * 100, 2).'%';
    }

    private function showTimeSaved()
    {
        $hits_miss = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
                        SELECT sum(hits*hit_time) as s_hits, sum(hits*miss_time) as s_miss
                        FROM ' ._DB_PREFIX_."express_cache where cache!='NULL'");

        $timesaved = round(abs($hits_miss['s_miss'] - $hits_miss['s_hits']), 2);

        return $this->secstoh($timesaved);
    }

    private function showSpaceUsed()
    {
        $cache = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
                        SELECT sum(cache_size) as cache_size
                        FROM ' ._DB_PREFIX_.'express_cache');
        if ($cache['cache_size'] == null) {
            $cache['cache_size'] = 0;
        }
        $cache_size = $this->getSize($cache['cache_size']);

        return $cache_size;
    }

    private function getSize($bytes)
    {

        //$bytes = sprintf('%u', filesize($path));

        if ($bytes > 0) {
            $unit = (int) log($bytes, 1024);
            $units = array('B', 'KB', 'MB', 'GB');

            if (array_key_exists($unit, $units) === true) {
                return sprintf('%d %s', $bytes / pow(1024, $unit), $units[$unit]);
            }
        }

        return $bytes;
    }

    private function rrmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != '.' && $object != '..') {
                    if (filetype($dir.'/'.$object) == 'dir') {
                        $this->rrmdir($dir.'/'.$object);
                    } else {
                        unlink($dir.'/'.$object);
                    }
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    private function secstoh($secs)
    {
        $units = array('yr' => 12 * 30 * 24 * 3600, 'mo' => 30 * 24 * 3600, 'wk' => 7 * 24 * 3600, 'days' => 24 * 3600, 'hr' => 3600, 'min' => 60, 's' => 1);

        // specifically handle zero
        //if ( $secs == 0 ) return "0 s";
        if ($secs < 1) {
            return "$secs s";
        }
        $s = '';
        $plus = '';
        foreach ($units as $name => $divisor) {
            if ($quot = (int) ($secs / $divisor)) {
                if ($name != 's') {
                    $plus = '+';
                }

                $s .= "$quot$plus $name";
                $s .= (abs($quot) > 1 && $name != 's' ? 's' : '');
                 // . ", ";
                $secs -= $quot * $divisor;
                break;
            }
        }

        return Tools::substr($s, 0);
         //, -2);
    }
}
