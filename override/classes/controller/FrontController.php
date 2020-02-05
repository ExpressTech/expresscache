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


class FrontController extends FrontControllerCore
{
    protected function smartyOutputContent($content)
    {
        global $smarty;

        


        $cache_time = false;
        
        if (!Module::isInstalled('expresscache') || !Module::isEnabled('expresscache')) {
            parent::smartyOutputContent($content);

            return;
        }

        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {

            include_once(_PS_MODULE_DIR_.'/expresscache/includes/ps17_smarty_widget.php');
        
        }

        // $this->displayMaintenancePage();

        //maintainance mode disables express cache.
        if (!(int)Configuration::get('PS_SHOP_ENABLE')) {
            if (!in_array(Tools::getRemoteAddr(), explode(',', Configuration::get('PS_MAINTENANCE_IP')))) {
                parent::smartyOutputContent($content);
                
                return;
            }
        }
        
        //smartyOutputContent is different for 1.6.0.0 - 1.6.0.6 and then changes competely.

        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            // $this->context->cookie->write();

            $html = '';

            if (is_array($content)) {
                foreach ($content as $tpl) {
                    $html .= $this->context->smarty->fetch($tpl, null, $this->getLayout());
                }
            } else {
                

                $html = $this->context->smarty->fetch($content, null, $this->getLayout());
            }

            $template = trim($html);

        } elseif (version_compare(_PS_VERSION_, '1.6.0.0', '>=') && version_compare(_PS_VERSION_, '1.6.0.6', '<=')) {
            if (is_array($content)) {
                foreach ($content as $tpl) {
                    $html = $this->context->smarty->fetch($tpl);
                }
            } else {
                $html = $this->context->smarty->fetch($content);
            }

            $html = trim($html);

            if ($this->controller_type == 'front' && !empty($html) && $this->getLayout()) {
                $dom_available = extension_loaded('dom') ? true : false;
                if ($dom_available) {
                    $html = Media::deferInlineScripts($html);
                }
                $html = trim(str_replace(array('</body>', '</html>'), '', $html))."\n";
                $this->context->smarty->assign(array(
                    'js_def' => Media::getJsDef(),
                    'js_files' => array_unique($this->js_files),
                    'js_inline' => $dom_available ? Media::getInlineScript() : array(),
                ));
                $javascript = $this->context->smarty->fetch(_PS_ALL_THEMES_DIR_.'javascript.tpl');
                $template = $html.$javascript."\t</body>\n</html>";
            } else {
                $template = $html;
            }
        } elseif (version_compare(_PS_VERSION_, '1.6.0.7', '>=')) {
            //edited to work upto PS 1.6.1.1.

            $html = '';
            $js_tag = 'js_def';
            $this->context->smarty->assign($js_tag, $js_tag);

            if (is_array($content)) {
                foreach ($content as $tpl) {
                    $html .= $this->context->smarty->fetch($tpl);
                }
            } else {
                $html = $this->context->smarty->fetch($content);
            }

            $html = trim($html);

            if (in_array($this->controller_type, array('front', 'modulefront')) && !empty($html) && $this->getLayout()) {
                $live_edit_content = '';

                $dom_available = extension_loaded('dom') ? true : false;
                $defer = (bool) Configuration::get('PS_JS_DEFER');

                if ($defer && $dom_available) {
                    $html = Media::deferInlineScripts($html);
                }
                $html = trim(str_replace(array('</body>', '</html>'), '', $html))."\n";

                $this->context->smarty->assign(array($js_tag => Media::getJsDef(), 'js_files' => $defer ? array_unique($this->js_files) : array(), 'js_inline' => ($defer && $dom_available) ? Media::getInlineScript() : array()));

                $javascript = $this->context->smarty->fetch(_PS_ALL_THEMES_DIR_.'javascript.tpl');
                // $template = ($defer ? $html.$javascript : preg_replace('/(?<!\$)'.$js_tag.'/', $javascript, $html)).$live_edit_content.((!Tools::getIsset($this->ajax) || ! $this->ajax) ? '</body></html>' : '');

                if ($defer && (!Tools::getIsset($this->ajax) || !$this->ajax)) {
                    $template = $html.$javascript;
                } else {
                    $template = preg_replace('/(?<!\$)'.$js_tag.'/', $javascript, $html);
                }

                $template .= $live_edit_content.((!Tools::getIsset($this->ajax) || !$this->ajax) ? '</body></html>' : '');

                // $template = ($defer ? $html . $javascript : str_replace($js_tag, $javascript, $html)) . $live_edit_content . ((!Tools::getIsset($this->ajax) || !$this->ajax) ? '</body></html>' : '');
            } else {
                $template = $html;
            }
        } else {
            $template = $this->context->smarty->fetch($content, null, null, null);
        }

        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && Tools::strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        $isLogged = $this->context->customer ? $this->context->customer->isLogged() && Configuration::get('EXPRESSCACHE_LOGGEDIN_SKIP') : false;

        $isMobile = !Configuration::get('EXPRESSCACHE_MOBILE') && $this->context->getMobileDevice() ? 1 : 0;

        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            $proto = 'https'.':'.'/'.'/';
        } else {
            $proto = 'http'.':'.'/'.'/';
        }
        //merge two arrays 1- static params to be removed and 2 - dynamic ones from the config.
        $ignore_params_1 = array('refresh_cache', 'no_cache');
        $ignore_params = Configuration::get('EXPRESSCACHE_URLVARS');
        $ignore_params = explode(',', $ignore_params);
        $ignore_params = array_merge($ignore_params, $ignore_params_1);

        list($urlpart, $qspart) = array_pad(explode('?', $_SERVER['REQUEST_URI']), 2, '');
        parse_str($qspart, $query);
        foreach ($ignore_params as $i_param) {
            $i_param = trim($i_param);
            unset($query[$i_param]);
        }

        $queryString = http_build_query($query);
        if ($queryString == '') {
            $url = $proto.$_SERVER['HTTP_HOST'].$urlpart;
        } else {
            $url = $proto.$_SERVER['HTTP_HOST'].$urlpart.'?'.$queryString;
        }

        $cache_processed = false;
        if (!Tools::isSubmit('no_cache') && !$isAjax && !$isLogged) {
            $page_name = Dispatcher::getInstance()->getController();
            $c_controllers = explode(',', Configuration::get('EXPRESSCACHE_CONTROLLERS'));
            if (in_array($page_name, $c_controllers) && !Tools::isSubmit('live_edit') && !Tools::isSubmit('live_configurator_token')) {
                $page_id = md5($url);
                $page_url = $url;
                
                $customer_groups_check = Configuration::get('EXPRESSCACHE_ENABLE_CUSTGROUP');
                $customer_groups = '';
                if ($customer_groups_check) {
                    $customer_groups = implode('|', $this->getCurrentCustomerGroups());
                    // $customer_groups_sql = ' and customer_groups = '.$customer_groups;
                }

                if ($_SERVER['HTTP_USER_AGENT'] && preg_match('/EXPRESSCACHE_BOT/', $_SERVER['HTTP_USER_AGENT'])) {

                    if (in_array('HTTP_X_EC_COUNTRY', $_SERVER)) {
                        $id_country = $_SERVER['HTTP_X_EC_COUNTRY'];
                        $id_currency = $_SERVER['HTTP_X_EC_CURRENCY'];
                        $id_currency = $_SERVER['HTTP_X_EC_LANGUAGE'];
                    } else {
                        //for backward compatibility. will be removed
                        $id_currency = Configuration::get('EXPRESSCACHE_PRECAHE_CURRENCY');
                        $id_country = Configuration::get('EXPRESSCACHE_PRECAHE_COUNTRY');
                        $id_country = Configuration::get('EXPRESSCACHE_PRECACHE_LANGUAGE');
                    }
                } else {
                    // echo "no bot";
                    $id_currency = (int) $this->context->currency->id;
                    $id_country = (int) $this->context->country->id;
                    $id_langauge = (int) $this->context->language->id;
                }

                $id_shop = (int) $this->context->shop->id;
                $entity_type = $page_name;

                $entity_type_ids = array('id_product', 'id_category', 'id_manufacturer', 'id_cms', 'id_supplier');
                $id_entity = 0;
                $POST = $_POST;
                foreach ($entity_type_ids as $entity_type_id) {
                    if (Tools::getValue($entity_type_id, 0)) {
                        $id_entity = Tools::getValue($entity_type_id);
                        // break;
                    }
                    if (array_key_exists($entity_type_id, $POST)) {
                        unset($POST[$entity_type_id]);
                    }
                }
                

                if (count($POST) > 0) {
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute('
                            UPDATE ' ._DB_PREFIX_."express_cache set cache='NULL'
                            WHERE page_id = '" .pSQL($page_id)."' and id_language = ".(int)$id_langauge.' and id_currency = '.(int)$id_currency.' and id_country = '.(int)$id_country.' and id_shop = '.(int)$id_shop);
                } else {
                    if (Configuration::get('EXPRESSCACHE_STORAGE_LIMIT') > 0) {
                        $cache_db = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
                                            SELECT sum(cache_size) as cache_size
                                            FROM ' ._DB_PREFIX_.'express_cache');
                        if ($cache_db['cache_size'] == null) {
                            $cache_db['cache_size'] = 0;
                        }

                        $cache_size = $cache_db['cache_size'];

                        if ($cache_size > 0) {
                            $cache_size = round($cache_db['cache_size'] / (1024 * 1024), 2);
                            if ($cache_size > Configuration::get('EXPRESSCACHE_STORAGE_LIMIT')) {
                                $last_cache = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT page_id, last_updated, cache FROM 
                                            ' ._DB_PREFIX_."express_cache WHERE cache != 'NULL' ORDER BY last_updated");

                                $del_page_id = $last_cache['page_id'];
                                $del_last_updated = $last_cache['last_updated'];
                                $file_to_delete = $last_cache['cache'];

                                Db::getInstance(_PS_USE_SQL_SLAVE_)->execute(
                                    'UPDATE '._DB_PREFIX_."express_cache
                                    set cache='NULL', cache_size = 0 
                                    where page_id = '".pSQL($del_page_id)."' and last_updated = '".pSQL($del_last_updated)."'"
                                );

                                $cache_dir = _PS_MODULE_DIR_.'expresscache/cache/';

                                if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE')) {
                                    $cache_dir .= $id_shop.'/';
                                }
                                $dir_name = Tools::substr($file_to_delete, 0, 2);
                                $dir_name = Tools::substr($dir_name, 0, 1).'/'.Tools::substr($dir_name, 1, 2).'/';
                                unlink($cache_dir.$dir_name.$file_to_delete);
                            }
                        }
                    }

                    $cache = $template;

                    $filename = $page_id.$id_currency.$id_langauge.$id_country.$id_shop.$isMobile;
                    $cache_dir = _PS_MODULE_DIR_.'expresscache/cache/';
                    if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE')) {
                        $cache_dir .= $id_shop.'/';
                    }
                    $dir_name = Tools::substr($filename, 0, 2);
                    $dir_name = Tools::substr($dir_name, 0, 1).'/'.Tools::substr($dir_name, 1, 2).'/';
                    if (!file_exists($cache_dir.$dir_name)) {
                        mkdir($cache_dir.$dir_name, 0777, true);
                    }

                    if (Configuration::get('EXPRESSCACHE_GZIP')) {
                        $cache = gzdeflate($cache);
                    }

                    $cache_size = Tools::strlen($cache);

                    file_put_contents($cache_dir.$dir_name.$filename, $cache, LOCK_EX);
                    $cache = $filename;

                    $now = microtime(true);
                    $cache_time = round($now - ($this->express_start_time), 3);
                    $cache_processed = true;

                    Db::getInstance()->execute(
                        'INSERT INTO '._DB_PREFIX_."express_cache 
                        (page_id, page_url, id_currency, id_language, id_country, id_shop, 
                        cache, cache_size, hits, miss, hit_time,
                        miss_time, entity_type, id_entity, is_mobile, last_updated, customer_groups)
                        VALUES ('".pSQL($page_id)."', '".pSQL($page_url)."', ".(int)$id_currency.", 
                        ".(int)$id_langauge.", ".(int)$id_country.", ".(int)$id_shop.",'".pSQL($cache)."', ".(int)$cache_size.",
                        0, 1, 0, ".(int)$cache_time.", '".pSQL($entity_type)."', ".(int)$id_entity.", ".(int)$isMobile.",'".gmdate('Y-m-d H:i:s')."','".pSQL($customer_groups)."')
                        ON DUPLICATE KEY UPDATE miss = miss + 1,
                        last_updated ='" .gmdate('Y-m-d H:i:s')."',
                        cache = '$cache', cache_size = $cache_size,
                        miss_time = ((miss_time*miss) + $cache_time) / (miss + 1)"
                    );

                    // echo  Db::getInstance()->getMsgError();exit;
                }
            }
        }

        $html = $template;
        $content = $html;

        $ec_cookie = new Cookie('ec_cookie');

        if ((Configuration::get('ADVCACHEMGMT') == 2 || $ec_cookie->advcachemgmt) && !Tools::getValue('live_edit', 0)) {
            if (!$cache_time) {
                $now = microtime(true);
                //echo $this->express_start_time;
                $cache_time = round($now - ($this->express_start_time), 3);
            }
            $height = '30px';
            $background_color = 'black';
            if (version_compare(_PS_VERSION_, '1.6.0', '>=')) {
                $height = '45px';
                $background_color = 'rgb(52, 52, 52)';
            }
            

            $this->context->smarty->assign(array(
                    'background_color' => $background_color,
                    'height' => $height,
                    'cache_time' => $cache_time,
                    'cache_processed' => $cache_processed,
                    'url' => $url,
                    'is_hit' => 0
                ));


            $content .= $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'expresscache/views/templates/front/live_cache_editor.tpl');
        }

        $this->context->cookie->write();

        echo $content;
    }
}
