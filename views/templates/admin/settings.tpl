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

<link href="../modules/expresscache/views/css/style.css" rel="stylesheet" type="text/css" media="all" />
<form action="" method="post">
<div style="margin: 0 auto; width:550px; text-align: center">
            <div style="width: 130px; float:left">
                <div class="feedback backspace">
                    <div class="feedback_content">
                        <div class="text_part">
                            <h3>{$cache_entries|escape:'htmlall':'UTF-8'}</h3>
                            <p>{l s='Cache Entries' mod='expresscache'}</p>
                        </div>
                    </div>
                    
                    <div class="view_detail view_backspace">
                        <input type="submit" onclick="return confirmCache();" name="clearCache" value="{l s='Clear Cache' mod='expresscache' }" class="button" />                            
                    </div>
                </div>  
            </div>
<div style="width: 130px; float:left; margin-left:10px">
                <div class="feedback backspace">
                    <div class="feedback_content">
                        <div class="text_part">
                            <h3>{$showHitPercentage|escape:'htmlall':'UTF-8'}</h3>
                            <p>{l s='Cache Hits' mod='expresscache'}</p>
                        </div>
                    </div>
                    
                    <div class="view_detail view_backspace">
                       <p class="small view"><a href="javascript:void(0)" onclick="showTab(\'tab-stats\')">{l s='More' mod='expresscache' } &raquo;</a></p>
                    </div>
                </div>  
            </div>
<div style="width: 130px; float:left; margin-left:10px">
                <div class="feedback backspace">
                    <div class="feedback_content">
                        <div class="text_part">
                            <h3>{$showTimeSaved|escape:'htmlall':'UTF-8'}</h3>
                            <p>{l s='Time Saved' mod='expresscache' }</p>
                        </div>
                    </div>
                    
                    <div class="view_detail view_backspace">
                       <p class="small view"><a href="javascript:void(0)" onclick="showTab(\'tab-activehooks\')">{l s='More' mod='expresscache' } &raquo;</a></p>
                    </div>
                </div>  
            </div>

<div style="width: 130px; float:left; margin-left:10px">
                <div class="feedback backspace">
                    <div class="feedback_content">
                        <div class="text_part">
                            <h3>{$showSpaceUsed|escape:'htmlall':'UTF-8'}</h3>
                            <p>{l s='Space Used' mod='expresscache' }</p>
                        </div>
                    </div>
                    
                    <div class="view_detail view_backspace">
                       <p class="small view"><a href="javascript:void(0)" onclick="showTab(\'tab-storage\')">{l s='More' mod='expresscache' } &raquo;</a></p>
                    </div>
                </div>  
            </div></div>

<div style="clear:both; height: 10px; text-align: center"><i class="process-icon-help" style="display: inline;font-size: 16px;"></i> <a target="_blank" href="http://docs.expresstech.io/article/5-express-cache">Documentation</a></div>


<br>
<form action="" method="post">

<div class="container">
 
    <ul class="tabs">
        <li class="tab-link current" data-tab="tab-basic">{l s='Basic' mod='expresscache' }</li>
        <li class="tab-link" data-tab="tab-caching">{l s='Caching' mod='expresscache' }<sup> new</sup></li>
        <li class="tab-link" data-tab="tab-activehooks">{l s='Dynamic Modules' mod='expresscache' }</li>
        <li class="tab-link" data-tab="tab-triggers">{l s='Triggers' mod='expresscache' }</li>
        <li class="tab-link" data-tab="tab-storage">{l s='Storage' mod='expresscache' }</li>
        <li class="tab-link" data-tab="tab-stats">{l s='Stats' mod='expresscache' }</li>
        <li class="tab-link" data-tab="tab-cron">{l s='Cron' mod='expresscache' }</li>
    </ul>

    <div id="tab-basic" class="tab-content current">
             
        
            <p><label for="output">{l s='Cache timeout' mod='expresscache' }</label>
            <input style="width: 40px; text-align: center" type="text" name="EXPRESSCACHE_timeout" value="{$EXPRESSCACHE_TIMEOUT|escape:'htmlall':'UTF-8'}"> minutes
            </p>
            <label>{l s='Live Cache Editor' mod='expresscache' }</label>
            <div class="margin-form">
                <input type="radio" name="ADVCACHEMGMT" id="ADVCACHEMGMT_on" value="2" {$ADVCACHEMGMT_on|escape:'htmlall':'UTF-8'} />
                <label class="t" for="ADVCACHEMGMT_on">Visible to All</label>
                <input type="radio" name="ADVCACHEMGMT" id="ADVCACHEMGMT_emp" value="1" ' {$ADVCACHEMGMT_emp|escape:'htmlall':'UTF-8'}/>
                <label class="t" for="ADVCACHEMGMT_emp">Visible to Current Employee</label>
                <input type="radio" name="ADVCACHEMGMT" id="ADVCACHEMGMT_off" value="0" {$ADVCACHEMGMT_off|escape:'htmlall':'UTF-8'}/>
                <label class="t" for="ADVCACHEMGMT_off">Disabled</label>
                <p class="clear">{l s='Set visibility of live cache editor. You will be able to see and manage live cache status from front office' mod='expresscache' }</p>
            </div>


           

            <label>{l s='Skip caching for logged in users' mod='expresscache' }</label>
            <div class="margin-form">
                <input type="radio" name="EXPRESSCACHE_LOGGEDIN_SKIP" id="EXPRESSCACHE_LOGGEDIN_SKIP_on" value="1" {$EXPRESSCACHE_LOGGEDIN_SKIP_on|escape:'htmlall':'UTF-8'}/>
                <label class="t" for="EXPRESSCACHE_LOGGEDIN_SKIP_on"> <img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='expresscache' }" title="{l s='Enabled' mod='expresscache'}" /></label>
                <input type="radio" name="EXPRESSCACHE_LOGGEDIN_SKIP" id="EXPRESSCACHE_LOGGEDIN_SKIP_off" value="0" {$EXPRESSCACHE_LOGGEDIN_SKIP_off|escape:'htmlall':'UTF-8'}/>
                <label class="t" for="EXPRESSCACHE_LOGGEDIN_SKIP_off"> <img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='expresscache' }" title="{l s='Disabled' mod='expresscache'}" /></label>
                <p class="clear">{l s='You can disable caching for logged in users by enabling this. Useful when \'blockuserinfo\' module is not configured with  dynamic hook.' mod='expresscache'}</p>
            </div>


            <label>{l s='Disable caching on mobile devices' mod='expresscache' }</label>
            <div class="margin-form">
                <input type="radio" name="EXPRESSCACHE_MOBILE" id="EXPRESSCACHE_MOBILE_on" value="1" {$EXPRESSCACHE_MOBILE_on|escape:'htmlall':'UTF-8'} />
                <label class="t" for="EXPRESSCACHE_MOBILE_on"> <img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='expresscache'}" title="{l s='Enabled' mod='expresscache'}" /></label>
                <input type="radio" name="EXPRESSCACHE_MOBILE" id="EXPRESSCACHE_MOBILE_off" value="0" {$EXPRESSCACHE_MOBILE_off|escape:'htmlall':'UTF-8'}/>
                <label class="t" for="EXPRESSCACHE_MOBILE_off"> <img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='expresscache'}" title="{l s='Disabled' mod='expresscache'}" /></label>
                <p class="clear">{l s='If caching for mobile pages is not working use this option.' mod='expresscache'}</p>
            </div>

            <label>{l s='Never expire cache for bots' mod='expresscache' }</label>
            <div class="margin-form">
                <input type="radio" name="EXPRESSCACHE_SEOEXP" id="EXPRESSCACHE_SEOEXP_on" value="1" {$EXPRESSCACHE_SEOEXP_on|escape:'htmlall':'UTF-8'}/>
                <label class="t" for="EXPRESSCACHE_SEOEXP_on"> <img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='expresscache'}" title="{l s='Enabled' mod='expresscache'}" /></label>
                <input type="radio" name="EXPRESSCACHE_SEOEXP" id="EXPRESSCACHE_SEOEXP_off" value="0" {$EXPRESSCACHE_SEOEXP_off|escape:'htmlall':'UTF-8'}/>
                <label class="t" for="EXPRESSCACHE_SEOEXP_off"> <img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='expresscache'}" title="{l s='Disabled' mod='expresscache'}" /></label>
                <p class="clear">{l s='If a cache entry exist (even if timeout is over), serve the page to a search engine bot. Improves SEO.' mod='expresscache'}</p>
            </div>


            <hr>
           
            <p><label for="output">{l s='What to cache?' mod='expresscache' }</label>
                <textarea rows="5" cols="70" name="EXPRESSCACHE_CONTROLLERS">{$EXPRESSCACHE_CONTROLLERS|escape:'htmlall':'UTF-8'}</textarea>
            </p>

            <p><label for="output">{l s='Ignore URL variables' mod='expresscache' }</label>
                <textarea rows="5" cols="70" name="EXPRESSCACHE_URLVARS">{$EXPRESSCACHE_URLVARS|escape:'htmlall':'UTF-8'}</textarea>
            </p>

           

            <div class="margin-form">
            <input type="submit" name="submitModule" value="{l s='Update settings' mod='expresscache'}" class="button" />
            </div>
    </div>
    <div id="tab-caching" class="tab-content">
             

            <label>{l s='Unique Cache per Country' mod='expresscache' }</label>
            <div class="margin-form">
                <input type="radio" name="EXPRESSCACHE_UNIQUECOUNTRY" id="EXPRESSCACHE_UNIQUECOUNTRY_on" value="1" {$EXPRESSCACHE_UNIQUECOUNTRY_on|escape:'htmlall':'UTF-8'} />
                <label class="t" for="EXPRESSCACHE_UNIQUECOUNTRY_on"> <img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='expresscache'}" title="{l s='Enabled' mod='expresscache'}" /></label>
                <input type="radio" name="EXPRESSCACHE_UNIQUECOUNTRY" id="EXPRESSCACHE_UNIQUECOUNTRY_off" value="0" {$EXPRESSCACHE_UNIQUECOUNTRY_off|escape:'htmlall':'UTF-8'}/>
                <label class="t" for="EXPRESSCACHE_UNIQUECOUNTRY_off"> <img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='expresscache'}" title="{l s='Disabled' mod='expresscache'}" /></label>
                <p class="clear">{l s='Enable this if your shop shows different content based on the country a user is visiting from.' mod='expresscache'}</p>
            </div>


            <label for="output">{l s='Pre-caching Limit' mod='expresscache' }</label>
            <div class="margin-form">
                <input style="width: 50px; text-align: center" type="text" name="EXPRESSCACHE_PRECACHE_LIMIT" value="{$EXPRESSCACHE_PRECACHE_LIMIT|escape:'htmlall':'UTF-8'}"> products
                <p class="clear">{l s='Number of products to pre-cache on each execution of pre-caching CRON url.' mod='expresscache' }</p>
            </div>

          

            <label>{l s='Pre-caching Bot Simulation' mod='expresscache' }</label>
            <div class="margin-form">
                <select name="EXPRESSCACHE_PRECAHE_COUNTRY">
                {foreach from=$EXPRESSCACHE_OPTION_COUNTRIES key=id_country item=country_details}
                <option value="{$id_country|escape:'htmlall':'UTF-8'}" {$country_details['selected']|escape:'htmlall':'UTF-8'}>{$country_details['country_name']|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
                </select>

                <select name="EXPRESSCACHE_PRECAHE_CURRENCY">
                {foreach from=$EXPRESSCACHE_OPTION_CURRENCIES key=currency_id item=currency_details}
                <option value="{$currency_id|escape:'htmlall':'UTF-8'}" {$currency_details['selected']|escape:'htmlall':'UTF-8'} >{$currency_details['iso_code']|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
                </select>
                <p class="clear">{l s='Settings used by the pre-caching cron job to simulate a visitor.' mod='expresscache' }</p>
            </div>

            <div class="margin-form">
            <input type="submit" name="submitModule" value="{l s='Update settings' mod='expresscache' }" class="button" />
            </div>
    </div>
    <div id="tab-activehooks" class="tab-content bootstrap">
             
            <div class="warn alert alert-warning">{l s='Dynamic Hooks are executed even if the page is served from cache. 
            This allows to show dynamic elements in a cached page.' mod='expresscache' }</div>

            <div class="warn alert alert-info">{l s='CLEAR CACHE after you have changed any configuration below' mod='expresscache' }</div>
            
            <table class="table">
            <tr><th>{l s='Module Name' mod='expresscache'}</th><th>{l s='Hooks' mod='expresscache'}</th>
            {foreach from=$mod_hook_array key=name item=hooks}
            <tr style="line-height:30px; ">
            {assign var=name_split value="|"|explode:$name}
            <td>{$name_split[1]|escape:'htmlall':'UTF-8'}</td>
            <td>
                {foreach from=$hooks item=hook}
                <span class="hook-name">
                    <input name="chk_activehook[]" type="checkbox" 
                    value="{$hook['id_module']}_{$hook['hook_name']}" {$hook['checked']}>
                    {$hook['hook_name']}
                </span>
                {/foreach}
            </td>
            </tr>
            {/foreach}
            </table>
            <div class="margin-form">
            <input type="submit" name="submitModule" value="{l s='Update settings' mod='expresscache'}" class="button" />
            </div>

            

                        
            
    </div>
    
    <div id="tab-triggers" class="tab-content">

        <label>{l s='Refresh Cache on Product Updation' mod='expresscache' }</label>
        <div class="margin-form">
            <input type="radio" name="EXPRESSCACHE_TRIGGER_PRODUCT" id="EXPRESSCACHE_TRIGGER_PRODUCT_on" value="1" {$EXPRESSCACHE_TRIGGER_PRODUCT_on|escape:'htmlall':'UTF-8'} />
            <label class="t" for="EXPRESSCACHE_TRIGGER_PRODUCT_on"> <img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='expresscache'}" title="{l s='Enabled' mod='expresscache'}" /></label>
            <input type="radio" name="EXPRESSCACHE_TRIGGER_PRODUCT" id="EXPRESSCACHE_TRIGGER_PRODUCT_off" value="0" {$EXPRESSCACHE_TRIGGER_PRODUCT_off|escape:'htmlall':'UTF-8'}/>
            <label class="t" for="EXPRESSCACHE_TRIGGER_PRODUCT_off"> <img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='expresscache'}" title="{l s='Disabled' mod='expresscache'}" /></label>
            <p class="clear">{l s='Refreshes the corresponding cache if a product is updated or deleted' mod='expresscache'}</p>
        </div>
        <div style="margin-left: 50px">
        

        <label>{l s='Refresh Cache for Linked Categories' mod='expresscache' }</label>
        <div class="margin-form">
            <input type="radio" name="EXPRESSCACHE_TRIGGER_LINKED" id="EXPRESSCACHE_TRIGGER_LINKED_on" value="1" {$EXPRESSCACHE_TRIGGER_LINKED_on|escape:'htmlall':'UTF-8'}/>
            <label class="t" for="EXPRESSCACHE_TRIGGER_LINKED_on"> <img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='expresscache'}" title="{l s='Enabled' mod='expresscache'}" /></label>
            <input type="radio" name="EXPRESSCACHE_TRIGGER_LINKED" id="EXPRESSCACHE_TRIGGER_LINKED_off" value="0" {$EXPRESSCACHE_TRIGGER_LINKED_off|escape:'htmlall':'UTF-8'}/>
            <label class="t" for="EXPRESSCACHE_TRIGGER_LINKED_off"> <img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='expresscache'}" title="{l s='Disabled' mod='expresscache'}" /></label>
            <p class="clear">{l s='Refreshes the categories cache when product is updated or deleted' mod='expresscache'}</p>
        </div>
        </div>

        <label>{l s='Refresh Cache on Category Updation' mod='expresscache' }</label>
        <div class="margin-form">
            <input type="radio" name="EXPRESSCACHE_TRIGGER_CATEGORY" id="EXPRESSCACHE_TRIGGER_CATEGORY_on" value="1" {$EXPRESSCACHE_TRIGGER_CATEGORY_on|escape:'htmlall':'UTF-8'}/>
            <label class="t" for="EXPRESSCACHE_TRIGGER_CATEGORY_on"> <img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='expresscache'}" title="{l s='Enabled' mod='expresscache'}" /></label>
            <input type="radio" name="EXPRESSCACHE_TRIGGER_CATEGORY" id="EXPRESSCACHE_TRIGGER_CATEGORY_off" value="0" {$EXPRESSCACHE_TRIGGER_CATEGORY_off|escape:'htmlall':'UTF-8'}/>
            <label class="t" for="EXPRESSCACHE_TRIGGER_CATEGORY_off"> <img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='expresscache'}" title="{l s='Disabled' mod='expresscache'}" /></label>
            <p class="clear">{l s='Refreshes the corresponding cache if a category is updated or deleted' mod='expresscache'}</p>
        </div>

        <label>{l s='Refresh Cache on Order Confirmation' mod='expresscache' }</label>
        <div class="margin-form">
            <input type="radio" name="EXPRESSCACHE_TRIGGER_ORDER" id="EXPRESSCACHE_TRIGGER_ORDER_on" value="1" {$EXPRESSCACHE_TRIGGER_ORDER_on|escape:'htmlall':'UTF-8'}/>
            <label class="t" for="EXPRESSCACHE_TRIGGER_ORDER_on"> <img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='expresscache'}" title="{l s='Enabled' mod='expresscache'}" /></label>
            <input type="radio" name="EXPRESSCACHE_TRIGGER_ORDER" id="EXPRESSCACHE_TRIGGER_ORDER_off" value="0" {$EXPRESSCACHE_TRIGGER_ORDER_off|escape:'htmlall':'UTF-8'}/>
            <label class="t" for="EXPRESSCACHE_TRIGGER_ORDER_off"> <img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='expresscache'}" title="{l s='Disabled' mod='expresscache'}" /></label>
            <p class="clear">{l s='Refreshes the corresponding products cache when a order is confirmed' mod='expresscache'}</p>
        </div>

        <label>{l s='Refresh Home Page Cache' mod='expresscache' }</label>
        <div class="margin-form">
            <input type="radio" name="EXPRESSCACHE_TRIGGER_HOME" id="EXPRESSCACHE_TRIGGER_HOME_on" value="1" {$EXPRESSCACHE_TRIGGER_HOME_on|escape:'htmlall':'UTF-8'}/>
            <label class="t" for="EXPRESSCACHE_TRIGGER_HOME_on"> <img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='expresscache'}" title="{l s='Enabled' mod='expresscache'}" /></label>
            <input type="radio" name="EXPRESSCACHE_TRIGGER_HOME" id="EXPRESSCACHE_TRIGGER_HOME_off" value="0" {$EXPRESSCACHE_TRIGGER_HOME_off|escape:'htmlall':'UTF-8'}/>
            <label class="t" for="EXPRESSCACHE_TRIGGER_HOME_off"> <img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='expresscache'}" title="{l s='Disabled' mod='expresscache'}" /></label>
            <p class="clear">{l s='Refreshes the home (index) page cache when a product / category / order / cms is updated or deleted' mod='expresscache'}</p>
        </div>

        <label>{l s='Refresh CMS Cache' mod='expresscache' }</label>
        <div class="margin-form">
            <input type="radio" name="EXPRESSCACHE_TRIGGER_CMS" id="EXPRESSCACHE_TRIGGER_CMS_on" value="1" {$EXPRESSCACHE_TRIGGER_CMS_on|escape:'htmlall':'UTF-8'}/>
            <label class="t" for="EXPRESSCACHE_TRIGGER_CMS_on"> <img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='expresscache'}" title="{l s='Enabled' mod='expresscache'}" /></label>
            <input type="radio" name="EXPRESSCACHE_TRIGGER_CMS" id="EXPRESSCACHE_TRIGGER_CMS_off" value="0" {$EXPRESSCACHE_TRIGGER_CMS_off|escape:'htmlall':'UTF-8'}/>
            <label class="t" for="EXPRESSCACHE_TRIGGER_CMS_off"> <img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='expresscache'}" title="{l s='Disabled' mod='expresscache'}" /></label>
            <p class="clear">{l s='Refreshes the CMS page cache when a cms page is added, updated or deleted' mod='expresscache'}</p>
        </div>
        
        
        


        <div class="margin-form">
            <input type="submit" name="submitModule" value="{l s='Update settings' mod='expresscache' }" class="button" />
        </div>
    </div>

    <div id="tab-storage" class="tab-content">

        <label>{l s='Compress Cache' mod='expresscache' }</label>
        <div class="margin-form">
            <input type="radio" name="EXPRESSCACHE_GZIP" id="EXPRESSCACHE_GZIP_on" value="1" {$EXPRESSCACHE_GZIP_on|escape:'htmlall':'UTF-8'}/>
            <label class="t" for="EXPRESSCACHE_GZIP_on"> <img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='expresscache'}" title="{l s='Enabled' mod='expresscache'}" /></label>
            <input type="radio" name="EXPRESSCACHE_GZIP" id="EXPRESSCACHE_GZIP_off" value="0" {$EXPRESSCACHE_GZIP_off|escape:'htmlall':'UTF-8'}/>
            <label class="t" for="EXPRESSCACHE_GZIP_off"> <img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='expresscache'}" title="{l s='Disabled' mod='expresscache'}" /></label>
            <p class="clear">{l s='Store cache files in compressed format (gzip)' mod='expresscache' }</p>
        </div>

        <label for="output">{l s='Cache storage limit' mod='expresscache' }</label> 
        <div class="margin-form">
            
            <input style="width: 40px; text-align: center" type="text" name="EXPRESSCACHE_STORAGE_LIMIT" value="{$EXPRESSCACHE_STORAGE_LIMIT|escape:'htmlall':'UTF-8'}"> MB
            <p class="clear">{l s='0 = No limit' mod='expresscache' }</p>

        </div>
        <div class="margin-form">
            <input type="submit" name="submitModule" value="{l s='Update settings' mod='expresscache' }" class="button" />
        </div>
        
    </div>

    <div id="tab-stats" class="tab-content">
        
        <input type="submit" name="clearStats" value="{l s='Clear Stats' mod='expresscache' }" class="button" />

        <input onclick="return refreshTheStats(); " type="submit" id="refreshStats" name="refreshStats" value="{l s='Refresh Stats' mod='expresscache' }" class="button" />
                        <br><br>
        
        <table id="statsTable" class="table" style="width:90%;">
            <thead>
            <tr><th>{l s='URL' mod='expresscache' }</th><th>{l s='Hits' mod='expresscache' }</th><th>{l s='Misses' mod='expresscache' }</th></tr>
            </thead>
            <tbody>
                
            </tbody>
        
        </table>

        <div id="statsError" style="display: none"></div>
        
    </div>

    <div id="tab-cron" class="tab-content">
        
        {l s='Clear all cache entries' mod='expresscache' } :<br>
        <a href="{$cron_url|escape:'htmlall':'UTF-8'}">{$cron_url|escape:'htmlall':'UTF-8'}</a><br><br>
        

         {l s='Pre-cache entries' mod='expresscache' } :<br>
        <a href="{$precache_cron_url|escape:'htmlall':'UTF-8'}">{$precache_cron_url|escape:'htmlall':'UTF-8'}</a><br><br>
        
    </div>

</div><!-- container -->
<input type="hidden" name="selected_tab" id="selected_tab">
</form>

<script>

//populate the stats table
function populateStats(stats_rows) {
    $('#statsTable tbody').html('');
    $(stats_rows).each(function() {
        var stat = this;
        console.log(stat['is_mobile']);
        var is_mobile = stat['is_mobile'] == 0 ? '' : ' (Mobile)';
        $('#statsTable tbody').append('<tr><td>' + stat['page_url'] + is_mobile + '</td><td>' + stat['hits'] + '</td><td>' + stat['miss'] + '</td></tr>');
    })

}



</script>

<script>
function showTab(tab_id) {
    console.log(tab_id);
    // console.log('ul.tabs li[data-tab=\"' + tab_id + '\"]');
    var tab = $( 'ul.tabs li[data-tab=\"' + tab_id + '\"]' )[0];
    // console.log(tab);
    $(tab).trigger( 'click' );
}

function confirmCache() {
    return confirm("{l s='Do you want to clear all cache entries?' mod='expresscache'}");
}
$(document).ready(function(){
    
    $('ul.tabs li').click(function(e){
        var tab_id = $(this).attr('data-tab');

        $('ul.tabs li').removeClass('current');
        $('.tab-content').removeClass('current');

        $(this).addClass('current');
        $('#'+tab_id).addClass('current');
        $('#selected_tab').val($(e.target).attr('data-tab'));
    })

    // populateStats(stats_rows);

    //init refresh for the first time
    refreshTheStats();

})
</script>

<script>
function refreshTheStats() {
    //alert(1);
    $('#refreshStats').val("{l s='Refreshing...' mod='expresscache' }");
    urlStats = addParamToURL('getStats', '1');
    $.get(urlStats, function(data){
        //alert(data);
        try {
            populateStats(JSON.parse(data));
            $('#statsTable').show();
            $('#statsError').hide();
        } catch(e) {
            $('#statsTable').hide();
            $('#statsError').show();
            $('#statsError').html(data);
        }
        
        $('#refreshStats').val("{l s='Refresh Stats' mod='expresscache' }");
    });
    return false;
}

function addParamToURL(paramName, paramValue)
{
    var url = window.location.href;
    if (url.indexOf(paramName + '=') >= 0)
    {
        var prefix = url.substring(0, url.indexOf(paramName));
        var suffix = url.substring(url.indexOf(paramName));
        suffix = suffix.substring(suffix.indexOf('=') + 1);
        suffix = (suffix.indexOf('&') >= 0) ? suffix.substring(suffix.indexOf('&')) : '';
        url = prefix + paramName + '=' + paramValue + suffix;
    }
    else
    {
    if (url.indexOf('?') < 0)
        url += '?' + paramName + '=' + paramValue;
    else
        url += '&' + paramName + '=' + paramValue;
    }
    return url;
}


</script>
{if $selected_tab} 
<script>
$(document).ready(function() { 
    
    showTab('{$selected_tab|escape:'htmlall':'UTF-8'}') 
})
</script>
{/if}
