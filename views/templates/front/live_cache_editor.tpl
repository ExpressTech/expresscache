{*
* 2007-2016 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if $is_hit}

<div id="expresscache_liveeditor" style="background-color:000; background-color: rgba(0,0,0, 0.7); border-bottom: 1px solid #000; width:100%;height:{$height}; padding:5px 10px; position:fixed;top:0;left:0;z-index:9999;">

    <span style="color:lightgreen; float:left; padding: 8px; background-color: {$background_color}">Cached Page</span>
    <span style="color:white; float:left; padding: 8px; background-color: {$background_color}; margin-left:10px">Load Time: {$cache_time} s</span>
    <span style="color:white; float:left; padding: 8px; background-color: {$background_color}; margin-left:10px">Updated: {$last_updated} mins ago</span>
    <span style="color:white; float:left; padding: 8px; background-color: {$background_color}; margin-left:10px">Hits: {$hits}</span>
    <span style="color:white; float:left; padding: 8px; background-color: {$background_color}; margin-left:10px">Misses: {$miss}</span>
    <form method='GET'><input type='submit' name = 'refresh_cache' value='Refresh Cache' id='refreshCache' class='button' style='background: #333 none; color:#fff; border:1px solid #000; float:right; margin-right:20px;'>
        <input type="submit" value="View Live Page" name="no_cache" id="viewLivePage" class="exclusive" style="color:black;float:right; text-shadow: 0 -1px 0 #157402; margin-right:10px;">
    
    {foreach from=$hidden_fields key=key item=value}
    <input type="hidden" value="{$value}" name="{$key}">
    {/foreach}
    </form>
</div>
{else}
    <div id="expresscache_liveeditor" style="background-color:000; background-color: rgba(0,0,0, 0.7); border-bottom: 1px solid #000; width:100%;height:{$height}; padding:5px 10px; position:fixed;top:0;left:0;z-index:9999;">
        <span style="color:red; float:left; padding: 8px; background-color: {$background_color}">Non-Cached Page</span>
        <span style="color:white; float:left; padding: 8px; background-color: '.$background_color.'; margin-left:10px">Load Time: {$cache_time} s</span>

            {if $cache_processed}
                <input onclick="window.location.href='{$url}'" type="submit" value="View Cached Page" name="no_cache" id="viewLivePage" class="exclusive" style="color:black;float:right; text-shadow: 0 -1px 0 #157402; margin-right:10px;">
            {else}
                <span style="color:white;float:right; padding: 8px; background-color: '.$background_color.'; margin-right:10px;">This page was not processed for caching</span>
            {/if}

     </div>
{/if}