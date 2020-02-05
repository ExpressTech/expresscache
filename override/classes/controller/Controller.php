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

abstract class Controller extends ControllerCore
{
    public $express_start_time;

    public function run()
    {
        $then = $this->express_start_time = microtime(true);

        if (!Module::isInstalled('expresscache') || !Module::isEnabled('expresscache')) {
            return parent::run();
        }

        //maintainance mode disables express cache.
        if (!(int)Configuration::get('PS_SHOP_ENABLE')) {
            if (!in_array(Tools::getRemoteAddr(), explode(',', Configuration::get('PS_MAINTENANCE_IP')))) {
                return parent::run();
            }
        }

        //skip cache hit altogether for precache bot activity.
        if (preg_match('/EXPRESSCACHE_BOT/', $_SERVER['HTTP_USER_AGENT'])) {
            return parent::run();
        }
        
        $cache = false;
        $page_name = Dispatcher::getInstance()->getController();
        $c_controllers = explode(',', Configuration::get('EXPRESSCACHE_CONTROLLERS'));
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && Tools::strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        $isLogged = $this->context->customer ? $this->context->customer->isLogged() && Configuration::get('EXPRESSCACHE_LOGGEDIN_SKIP') : false;



        // echo $isLogged;exit;

        $isMobile = !Configuration::get('EXPRESSCACHE_MOBILE') && $this->context->getMobileDevice() ? 1 : 0;

        $cache_row = false;

        if (in_array($page_name, $c_controllers) && !Tools::isSubmit('refresh_cache') && !Tools::isSubmit('live_edit') && !Tools::isSubmit('live_configurator_token') && !Tools::isSubmit('no_cache') && count($_POST) == 0 && !$isAjax && !$isLogged) {
            if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
                $proto = 'https'.':'.'/'.'/';
            } else {
                $proto = 'http'.':'.'/'.'/';
            }
            //remove ignored url vars
            list($urlpart, $qspart) = array_pad(explode('?', $_SERVER['REQUEST_URI']), 2, '');
            parse_str($qspart, $query);
            $ignore_params = Configuration::get('EXPRESSCACHE_URLVARS');
            $ignore_params = explode(',', $ignore_params);
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

            $page_id = md5($url);

            $context = Context::getContext();

            $id_langauge = (int) $this->context->language->id;

            //FrontController.php runs this code afterwards. 
            //can also use Tools::getCountry()
            if (Configuration::get('PS_GEOLOCATION_ENABLED')) {
                if (($new_default = $this->geolocationManagement($this->context->country)) && Validate::isLoadedObject($new_default)) {
                    $this->context->country = $new_default;
                }
            }


            $id_country = (int) $this->context->country->id;
            $id_shop = (int) $this->context->shop->id;
            $currency = Tools::setCurrency($this->context->cookie);
            $id_currency = $currency->id;


            $country_check = Configuration::get('EXPRESSCACHE_UNIQUECOUNTRY');
            $country_sql = '';
            if ($country_check) {
                $country_sql = ' and id_country = '.(int)$id_country;
            }

            $customer_groups_sql = '';


            $customer_groups_check = Configuration::get('EXPRESSCACHE_ENABLE_CUSTGROUP');
            $customer_groups = '';
            if ($customer_groups_check) {
                $customer_groups = pSQL(implode('|', $this->getCurrentCustomerGroups()));
                $customer_groups_sql = " and customer_groups = '$customer_groups'";
            }

            $cache_row = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                        SELECT id_express_cache, cache, hits, miss, last_updated
                        FROM ' ._DB_PREFIX_."express_cache 
                        WHERE page_id = '" .pSQL($page_id)."' and id_language = ".(int)$id_langauge.' and id_currency = '.(int)$id_currency.$country_sql.' and id_shop = '.(int)$id_shop.' and is_mobile = '.(int)$isMobile.' '.$customer_groups_sql);

            if ($cache_row != false) {
                if ($cache_row[0]['cache'] != 'NULL') {
                    $filename = $cache_row[0]['cache'];
                    $cache_dir = _PS_MODULE_DIR_.'expresscache/cache/';
                    if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE')) {
                        $cache_dir .= $id_shop.'/';
                    }
                    $dir_name = Tools::substr($filename, 0, 2);
                    $dir_name = Tools::substr($dir_name, 0, 1).'/'.Tools::substr($dir_name, 1, 2).'/';
                    $cache_file = $cache_dir.$dir_name.$filename;
                    if (file_exists($cache_file)) {
                        $content = Tools::file_get_contents($cache_file);

                        $last_updated = strtotime($cache_row[0]['last_updated']);
                        $now = strtotime(gmdate('Y-m-d H:i:s'));
                        $is_bot = false;
                        if (Configuration::get('EXPRESSCACHE_SEOEXP') && array_key_exists('HTTP_USER_AGENT', $_SERVER)
                        && preg_match('/BotLink|ahoy|AlkalineBOT|anthill|appie|arale|araneo|AraybOt|ariadne|arks|ATN_Worldwide|Atomz|bbot|Bjaaland|Ukonline|borg\-bot\/0\.9|boxseabot|bspider|calif|christcrawler|CMC\/0\.01|combine|confuzzledbot|CoolBot|cosmos|Internet Cruiser Robot|cusco|cyberspyder|cydralspider|desertrealm, desert realm|digger|DIIbot|grabber|downloadexpress|DragonBot|dwcp|ecollector|ebiness|elfinbot|esculapio|esther|fastcrawler|FDSE|FELIX IDE|ESI|fido|H�m�h�kki|KIT\-Fireball|fouineur|Freecrawl|gammaSpider|gazz|gcreep|golem|googlebot|griffon|Gromit|gulliver|gulper|hambot|havIndex|hotwired|htdig|iajabot|INGRID\/0\.1|Informant|InfoSpiders|inspectorwww|irobot|Iron33|JBot|jcrawler|Teoma|Jeeves|jobo|image\.kapsi\.net|KDD\-Explorer|ko_yappo_robot|label\-grabber|larbin|legs|Linkidator|linkwalker|Lockon|logo_gif_crawler|marvin|mattie|mediafox|MerzScope|NEC\-MeshExplorer|MindCrawler|udmsearch|moget|Motor|msnbot|muncher|muninn|MuscatFerret|MwdSearch|sharp\-info\-agent|WebMechanic|NetScoop|newscan\-online|ObjectsSearch|Occam|Orbsearch\/1\.0|packrat|pageboy|ParaSite|patric|pegasus|perlcrawler|phpdig|piltdownman|Pimptrain|pjspider|PlumtreeWebAccessor|PortalBSpider|psbot|Getterrobo\-Plus|Raven|RHCS|RixBot|roadrunner|Robbie|robi|RoboCrawl|robofox|Scooter|Search\-AU|searchprocess|Senrigan|Shagseeker|sift|SimBot|Site Valet|skymob|SLCrawler\/2\.0|slurp|ESI|snooper|solbot|speedy|spider_monkey|SpiderBot\/1\.0|spiderline|nil|suke|http:\/\/www\.sygol\.com|tach_bw|TechBOT|templeton|titin|topiclink|UdmSearch|urlck|Valkyrie libwww\-perl|verticrawl|Victoria|void\-bot|Voyager|VWbot_K|crawlpaper|wapspider|WebBandit\/1\.0|webcatcher|T\-H\-U\-N\-D\-E\-R\-S\-T\-O\-N\-E|WebMoose|webquest|webreaper|webs|webspider|WebWalker|wget|winona|whowhere|wlm|WOLP|WWWC|none|XGET|Nederland\.zoek|AISearchBot|woriobot|NetSeer|Nutch/i', $_SERVER['HTTP_USER_AGENT'])) {
                            $is_bot = true;
                            // echo "VIKAS";
                        }
                        if (!$is_bot && round(abs($now - $last_updated) / 60, 2) > Configuration::get('EXPRESSCACHE_TIMEOUT')) {
                            $cache_row = false;
                        }
                    } else {
                        $cache_row = false;
                    }
                } else {
                    $cache_row = false;
                }
            }
        }

        if ($cache_row) {
            $this->init();

            $this->context->cookie->write();

            if (Configuration::get('EXPRESSCACHE_GZIP') || !strstr($content, 'body')) {
                $content = gzinflate($content);
            }

            $activehooks = unserialize(Configuration::get('EXPRESSCACHE_ACTIVEHOOKS'));

            /* Added on 3 Dec 2016 (3.1) - ExpressCache 3 got Hooks! **/
            $hook_args = array();

            $hook_file_exists = 0;

            if (is_array($activehooks)) {
                foreach ($activehooks as $id_module => $hook_array) {
                    if (is_array($hook_array)) {
                        foreach ($hook_array as $hook_name) {
                            if ($hook_file_exists || file_exists(_PS_MODULE_DIR_.'/expresscache/et_hooks/before_hook_exec.php')) {
                                include _PS_MODULE_DIR_.'/expresscache/et_hooks/before_hook_exec.php';
                                $hook_file_exists = 1;
                            }

                            $hook_content = Hook::exec($hook_name, $hook_args, $id_module, false, true, false, null);

                            $pattern = "/<!--\[hook $hook_name\] $id_module-->(.*?)<!--\[hook $hook_name\] $id_module-->/s";

                            //fix for $ in the content.
                            //http://php.net/manual/en/function.preg-replace.php#103985

                            $hook_content = preg_replace('/\$(\d)/', '\\\$$1', $hook_content);

                            $count = 0;

                            $p_content = preg_replace($pattern, $hook_content, $content, 1, $count);

                            if (preg_last_error() === PREG_NO_ERROR && $count > 0) {
                                $content = $p_content;
                            }
                        }
                    }
                }
            }

            $is_warehouse_theme = $this->context->shop->theme_name == 'warehouse' ? 1 : 0; 

            if($is_warehouse_theme) {
                
                include_once(_PS_MODULE_DIR_.'/expresscache/includes/ps17_warehouse_theme.php');
            
            }

            //widget support for PS 1.7.x
            if(version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
             /* Added on 25 Oct 2017 (3.5.0) - ExpressCache 3 got Widgets! **/

                $activewidgets = unserialize(Configuration::get('EXPRESSCACHE_ACTIVE_WIDGETS'));
               
                $hook_args = array();

                if (is_array($activewidgets)) {
                    foreach ($activewidgets as $widget) {

                        $expl_widget = explode(',', $widget);

                        $name_module = $expl_widget[0];

                        if(count($expl_widget) == 2) {
                            $hook_name = $expl_widget[1];
                        } else {
                            $hook_name = 'null';
                        }

                        $moduleInstance = Module::getInstanceByName($name_module);
                        
                        $hook_content = Hook::coreRenderWidget($moduleInstance, $hook_name, $hook_args);


                        $pattern = "/<!--\[widget $hook_name\] $name_module-->(.*?)<!--\[widget $hook_name\] $name_module-->/s";
                        //fix for $ in the content.
                        //http://php.net/manual/en/function.preg-replace.php#103985

                        $hook_content = preg_replace('/\$(\d)/', '\\\$$1', $hook_content);

                        $count = 0;

                        $p_content = preg_replace($pattern, $hook_content, $content, 1, $count);

                        if (preg_last_error() === PREG_NO_ERROR && $count > 0) {
                            $content = $p_content;
                        }
                            // }
                        // }
                    }
                }

            }


            $id_express_cache = $cache_row[0]['id_express_cache'];
            $now = microtime(true);
            $cache_time = round($now - $then, 3);
            Db::getInstance(_PS_USE_SQL_SLAVE_)->execute(
                'UPDATE '._DB_PREFIX_."express_cache set hits = hits + 1, 
                hit_time = ((hit_time*hits) + $cache_time) / (hits + 1)
                WHERE id_express_cache = '".(int)$id_express_cache."'"
            );

            $ec_cookie = new Cookie('ec_cookie');

            if ((Configuration::get('ADVCACHEMGMT') == 2 || $ec_cookie->advcachemgmt)) {
                $hits = $cache_row[0]['hits'] + 1;
                $miss = $cache_row[0]['miss'];
                $last_updated = $cache_row[0]['last_updated'];
                $last_updated = round((time() - date('Z') - strtotime($last_updated)) / 60, 2);
                $height = '30px';
                $background_color = 'black';
                if (version_compare(_PS_VERSION_, '1.6.0', '>=')) {
                    $height = '45px';
                    $background_color = 'rgb(52, 52, 52)';
                }
                
                $hidden_fields = array();

                $parts = parse_url($_SERVER['REQUEST_URI']);
                if (array_key_exists('query', $parts)) {
                    parse_str($parts['query'], $query);

                    foreach ($query as $key => $val) {
                        $hidden_fields[$key] = Tools::getValue($key);
                        //<input type="hidden" value="'.Tools::getValue($key).'" name="'.$key.'">
                    }
                }

                $this->context->smarty->assign(array(
                    'background_color' => $background_color,
                    'height' => $height,
                    'cache_time' => $cache_time,
                    'last_updated' => $last_updated,
                    'hits' => $hits,
                    'miss' => $miss,
                    'hidden_fields' => $hidden_fields,
                    'is_hit' => 1
                ));


                $content .= $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'expresscache/views/templates/front/live_cache_editor.tpl');

                // echo $content;exit;
            }

            if (Configuration::get('PS_TOKEN_ENABLE')) {
                //if the Front Office security is set to On. Overwrite the links
                $new_token = Tools::getToken(false);

                //search for - var token = '49978e0404ec69e57dc664c7d6c5c2c2';
                if (preg_match("/static_token[ ]?=[ ]?'([a-f0-9]{32})'/", $content, $matches)) {
                    //found the token
                    if (count($matches) > 1 && $matches[1] != '') {
                        $old_token = $matches[1];
                        $content = preg_replace("/$old_token/", $new_token, $content);
                    }
                } else {
                    //Not optimal method. Keeping for future reference.
                    //HTML Tokens
                    $content = preg_replace('/name="token" value="[a-f0-9]{32}/', 'name="token" value="'.$new_token, $content);
                    $content = preg_replace('/token=[a-f0-9]{32}"/', 'token='.$new_token.'"', $content);
                    //JS Token
                    $content = preg_replace('/static_token[ ]?=[ ]?\'[a-f0-9]{32}/', 'static_token = \''.$new_token, $content);
                }
            }

            echo $content;
        } else {
            $this->express_start_time = microtime(true);

            $display = parent::run();
        }
    }
}
