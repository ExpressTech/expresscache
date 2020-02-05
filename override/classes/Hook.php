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

class Hook extends HookCore
{

    /**
     * Return hook ID from name.
     */
    
    public static function getNameById($hook_id)
    {
        $cache_id = 'hook_namebyid_'.$hook_id;
        if (!Cache::isStored($cache_id)) {
            Cache::store($cache_id, Db::getInstance()->getValue('
				SELECT `name`
				FROM `' ._DB_PREFIX_.'hook`
				WHERE `id_hook` = ' .(int) $hook_id));
        }
        return Cache::retrieve($cache_id);
    }

    
    public static function exec($hook_name, $hook_args = array(), $id_module = null, $array_return = false, $check_exceptions = true, $use_push = false, $id_shop = null, $chain = false)
    {
        if (!Module::isInstalled('expresscache') || !Module::isEnabled('expresscache')) {
            return parent::exec($hook_name, $hook_args, $id_module, $array_return, $check_exceptions, $use_push, $id_shop, $chain);
        }
        $activehooks = unserialize(Configuration::get('EXPRESSCACHE_ACTIVEHOOKS'));
        $found = false;
        if (is_array($activehooks)) {
            foreach ($activehooks as $hook_arr) {
                if (is_array($hook_arr) && in_array($hook_name, $hook_arr)) {
                    $found = true;
                    break;
                }
            }
        }
        
        if (!$found) {
            return parent::exec($hook_name, $hook_args, $id_module, $array_return, $check_exceptions, $use_push, $id_shop);
        }
        if (!$module_list = self::getHookModuleExecList($hook_name)) {
            return '';
        }
        if ($array_return) {
            $return = array();
        } else {
            $return = '';
        }
        if (!$id_module) {
            foreach ($module_list as $m) {
                $data = parent::exec($hook_name, $hook_args, $m['id_module'], $array_return, $check_exceptions, $use_push, $id_shop);
                if (isset($data)) {
                    if (is_array($data)) {
                        $data = array_shift($data);
                    }
                    if (is_array($data)) {
                        $return[$m['module']] = $data;
                    } else {
                        $data_wrapped = '<!--[hook '.$hook_name.'] '.$m['id_module'].'-->'.$data.'<!--[hook '.$hook_name.'] '.$m['id_module'].'-->';
                        if ($array_return) {
                            $return[$m['module']] = $data_wrapped;
                        } else {
                            $return .= $data_wrapped;
                        }
                    }
                }
            }
        } else {
            $return = parent::exec($hook_name, $hook_args, $id_module, $array_return, $check_exceptions, $use_push, $id_shop, $chain);
        }
        return $return;
    }

    
}
