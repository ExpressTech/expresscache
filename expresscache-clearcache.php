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

include dirname(__FILE__).'/../../config/config.inc.php';
include dirname(__FILE__).'/expresscache.php';

if (Tools::substr(Tools::encrypt('expresscache/index'), 0, 10) != Tools::getValue('token') || !Module::isInstalled('expresscache')) {
    die('Bad token');
}

function rrmdir($dir)
{
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != '.' && $object != '..') {
                if (filetype($dir.'/'.$object) == 'dir') {
                    rrmdir($dir.'/'.$object);
                } else {
                    unlink($dir.'/'.$object);
                }
            }
        }
        reset($objects);
        rmdir($dir);
    }
}

$context = Context::getContext();
$id_shop = (int) $context->shop->id;

//delete cached db rows

Db::getInstance()->execute('UPDATE '._DB_PREFIX_."express_cache set cache='NULL', cache_size=0 where id_shop = $id_shop");

//delete cached files from the filesystem as well
$cache_dir = _PS_MODULE_DIR_.'expresscache/cache/';
$files = glob($cache_dir.'*');

// get all file names

foreach ($files as $file) {
    // echo $file;
    if (is_file($file) && strpos($file, 'index.php') === false) {
        unlink($file);
    } elseif (is_dir($file)) {
        rrmdir($file);
    }
}

echo 'Cache cleared';
