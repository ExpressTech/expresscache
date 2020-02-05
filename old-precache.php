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

die('deprecated, use new URL');

ini_set('max_execution_time', 0);

ob_start();

include dirname(__FILE__).'/../../config/config.inc.php';
include dirname(__FILE__).'/expresscache.php';

if (Tools::substr(Tools::encrypt('expresscache/index'), 0, 10) != Tools::getValue('token') || !Module::isInstalled('expresscache')) {
    die('Bad token');
}

function is_cache_exist($url)
{
    $page_id = md5($url);

    $cache_row = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                SELECT id_express_cache, cache, hits, miss, last_updated
                FROM ' ._DB_PREFIX_."express_cache 
                WHERE page_id = '" .pSQL($page_id)."'");
    if ($cache_row) {
        $last_updated = strtotime($cache_row[0]['last_updated']);

        $now = strtotime(gmdate('Y-m-d H:i:s'));

        if (round(abs($now - $last_updated) / 60, 2) > Configuration::get('EXPRESSCACHE_TIMEOUT')) {
            $cache_row = false;
        }
    }
}

function get_web_page($url, $agent = "desktop")
{
    // return;
    if ($agent == "desktop") {
        $user_agent = 'Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0 EXPRESSCACHE_BOT';
    } else {
        $user_agent = 'Mozilla/5.0 (Linux; U; Android 4.0.3; ko-kr; LG-L160L Build/IML74K) AppleWebkit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30 EXPRESSCACHE_BOT';
    }

    $options = array(

        CURLOPT_CUSTOMREQUEST => 'GET',        //set request type post or get
        CURLOPT_POST => false,        //set to GET
        CURLOPT_USERAGENT => $user_agent, //set user agent
        CURLOPT_RETURNTRANSFER => true,     // return web page
        CURLOPT_HEADER => false,    // don't return headers
        CURLOPT_FOLLOWLOCATION => true,     // follow redirects
        CURLOPT_ENCODING => '',       // handle all encodings
        CURLOPT_AUTOREFERER => true,     // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
        CURLOPT_TIMEOUT => 120,      // timeout on response
        CURLOPT_MAXREDIRS => 5,       // stop after 10 redirects
    );

    $url .= (parse_url($url, PHP_URL_QUERY) ? '&' : '?');

    $ch = curl_init($url);
    curl_setopt_array($ch, $options);
    $content = curl_exec($ch);
    $err = curl_errno($ch);
    $errmsg = curl_error($ch);
    $header = curl_getinfo($ch);
    curl_close($ch);

    $header['errno'] = $err;
    $header['errmsg'] = $errmsg;
    $header['content'] = $content;
    // var_dump($header);
    return $header;
}

function display($txt)
{
    if (php_sapi_name() === 'cli') {
        echo $txt;
    } else {
        echo $txt.'<br>';
    }
    ob_flush();
}

//generating URLs
//TODO: if we can use the URLs from Express Cache table itself?
//if not try to generate by yourself.

$context = Context::getContext();
$id_shop = (int) $context->shop->id;

//Getting settings from the URL
$id_lang = (int) $context->language->id;

//fetch index page
$index_page = $context->shop->getBaseURL(true);
display($index_page);
get_web_page($index_page);
get_web_page($index_page, "mobile");

//fetch bestsales,pricesdrop,newproducts
$link = $context->link->getPageLink('bestsales');
display($link);
get_web_page($link);
get_web_page($link, "mobile");

$link = $context->link->getPageLink('pricesdrop');
display($link);
get_web_page($link);
get_web_page($link, "mobile");

$link = $context->link->getPageLink('newproducts');
display($link);
get_web_page($link);
get_web_page($link, "mobile");

// exit;
//fetch categories pages
$categories = Category::getSimpleCategories($id_lang, true);

foreach ($categories as $category) {
    $cat = new Category($category['id_category']);
    // var_dump($cat);
    if ($cat->active == '1') {
        $link = $cat->getLink();
        display($link);
        get_web_page($link, "desktop");
        get_web_page($link, "mobile");
    }
}

//fetch product pages
$products = Product::getSimpleProducts($id_lang, $context);

//fetch cached products
$cached_products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                SELECT id_entity as id_product
                FROM ' ._DB_PREFIX_."express_cache where cache !='NULL' and entity_type = 'product' order by last_updated desc");

//convert to one dimensional array for easier search
$cached_products = array_map('current', $cached_products);

//build the list of pages to be pre-cached by filtering thru these two lists
// Note - could have done this with simple SQL, but don't want to mess with PS tables
$product_to_cache = array();
foreach ($products as $product) {
    $id_product = $product['id_product'];
    // echo $id_product;
    if (!in_array($id_product, $cached_products)) {
        $product_to_cache[] = $id_product;
    }
}

//merge the not cache products with cached ones (but in reverse direction)
// $product_to_cache = $product_to_cache + array_reverse($cached_products);
$product_to_cache = array_merge($product_to_cache, array_reverse($cached_products));

$limit = Configuration::get('EXPRESSCACHE_PRECACHE_LIMIT');
$i = 1;
foreach ($product_to_cache as $id_product) {
    // display($product['id_product']);
    $prod = new Product($id_product);
    // var_dump($cat);
    if ($prod->active == '1') {
        $link = $prod->getLink($context);
        display($link);
        get_web_page($link);
        get_web_page($link, "mobile");
    }

    if ($i >= (int) $limit) {
        break;
    }

    ++$i;
}

//fetch cms pages
$cmses = CMS::getLinks($id_lang);

foreach ($cmses as $cms) {
    $link = $cms['link'];
    display($link);
    get_web_page($link);
    get_web_page($link, "mobile");
    // var_dump($cms);
}

//fetch manufacturer pages
$manufacturers = Manufacturer::getManufacturers();
// var_dump($manufacturers);

foreach ($manufacturers as $manufacturer) {
    // display($product['id_product']);
    $man = new Manufacturer($manufacturer['id_manufacturer']);
    // var_dump($cat);
    if ($man->active == '1') {
        // echo "1";
        $link = $context->link->getManufacturerLink($man, $man->link_rewrite, $id_lang);
        display($link);
        get_web_page($link);
        get_web_page($link, "mobile");
    }
}
