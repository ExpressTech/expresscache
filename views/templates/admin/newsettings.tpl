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

<script src="//cdnjs.cloudflare.com/ajax/libs/riot/3.6.0/riot+compiler.min.js"></script>
{if $ps17}
<script src="../modules/expresscache/views/js/ps17.js"></script>
{/if}
<link href="../modules/expresscache/views/css/style.css" rel="stylesheet" type="text/css" media="all" />
{if $ps15}
<link href="../modules/expresscache/views/css/style_15.css" rel="stylesheet" type="text/css" media="all" />
{/if}

{if $multishop}
<p style='text-align:center'>{l s='Showing cache stats for shop' mod='expresscache'} : {$shopname}</p>
{/if}

<div class="panelStats">
<ps-panel icon="icon-list" img="../img/t/AdminStats.gif" header="{l s='Cache Entries' mod='expresscache'}">


	<h1>{$cache_entries|escape:'htmlall':'UTF-8'}</h1>

</ps-panel>

<ps-panel icon="icon-bell" img="../img/t/AdminFeatures.gif" header="{l s='Cache Hits' mod='expresscache'}">


	<h1>{$showHitPercentage|escape:'htmlall':'UTF-8'}</h1>

</ps-panel>

<ps-panel icon="icon-clock-o" img="../img/t/AdminStatuses.gif" header="{l s='Time Saved' mod='expresscache' }">


	<h1>{$showTimeSaved|escape:'htmlall':'UTF-8'}</h1>


</ps-panel>

<ps-panel icon="icon-hdd-o" img="../img/t/AdminBackup.gif" header="{l s='Space Used' mod='expresscache' }">


	<h1>{$showSpaceUsed|escape:'htmlall':'UTF-8'}</h1>


</ps-panel>
<br>
<form action="" method="post">
<input type="submit" onclick="return confirmCache();" name="clearCache" value="{l s='Clear Cache' mod='expresscache' }" class="button btn btn-small btn-primary" />
</form>
<br>
</div>
    
    <script type="riot/tag">
        <form-basic>
            {$forms['form_basic']}
        </form-basic>

        <form-advanced>
            {$forms['form_advanced']}
        </form-advanced>

        <form-precaching>
            {$forms['form_precaching']}
        </form-precaching>

        <form-triggers>
            {$forms['form_triggers']}
        </form-triggers>
    </script>

	<ps-tabs position="top">

	<ps-tab label="{l s='Basic' mod='expresscache' }" active="true" id="tab-basic" img="../img/t/AdminBackup.gif" >

     <!-- icon="icon-AdminParentModules" fa="cogs" -->

        <form-basic></form-basic>
		
	</ps-tab>

	<ps-tab label="{l s='Advanced' mod='expresscache' }" id="tab-advanced" img="../img/t/AdminBackup.gif" >
        <form-advanced></form-advanced>

	</ps-tab>

    {if $ps17}
	<ps-tab label="{l s='Dynamic Modules & Widgets' mod='expresscache' }" id="tab-dynamic">
    {else}
    <ps-tab label="{l s='Dynamic Modules' mod='expresscache' }" id="tab-dynamic">
    {/if}

        <form action="" method="POST">
        
		<div class="warn alert alert-warning">{l s='Dynamic Hooks are executed even if the page is served from cache. 
            This allows to show dynamic elements in a cached page.' mod='expresscache' }</div>

        <div class="warn alert alert-info">{l s='CLEAR CACHE after you have changed any configuration below' mod='expresscache' }</div>
        <label class="control-label" for="field1">Dynamic Hooks</label>
        <table class="table">
        <tr><th>{l s='Module Name' mod='expresscache'}</th><th>{l s='Hooks' mod='expresscache'}</th>
        {foreach from=$mod_hook_array key=name item=hooks}
        <tr style="line-height:30px; ">
        {assign var=name_split value="|"|explode:$name}
        <td><img style="width: 16px; height: 16px" src="{$mod_logos[$name_split[0]]|escape:'htmlall':'UTF-8'}" /> {$name_split[1]|escape:'htmlall':'UTF-8'}</td>
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

        
        {if $ps17}
        <div class="container">
            <div class="row">
                <div class="control-group" id="fields">
                    <p>&nbsp;</p>
                    <label class="control-label" for="field1">Dynamic Widgets</label>
                    <p>Type in module_name,hook_name to call make a module and hook combination as dynamic. (Note - hook_name is optional). Example - "ps_contactinfo,displayLeftColumn" or just "contactform".
                    <div class="controls"> 
                        <div class="form">
                            
                            {foreach from=$active_widgets_arr item=active_widget}
                            <div class="entry input-group col-xs-3">
                                <input class="form-control" name="activewidgets[]" type="text" placeholder="module_name[,hook_name]" value="{$active_widget}" />
                                
                                <span class="input-group-btn">
                                    <button class="btn btn-danger btn-remove" type="button">
                                        <span class="icon-minus"></span>
                                    </button>
                                </span>
                                <!-- <br/>Dynamic Block?<input class="form-control" name="activewidget_blocks[]" type="checkbox"  value="" /> -->
                            </div>
                            {/foreach}
                            <div class="entry input-group col-xs-3">
                                <input class="form-control" name="activewidgets[]" type="text" placeholder="module_name[,hook_name]" />
                                 <!-- <input class="form-control" name="activewidget_blocks[]" type="checkbox"  value="" /> -->
                                <span class="input-group-btn">
                                    <button class="btn btn-success btn-add" type="button">
                                        <span class="icon-plus"></span>
                                    </button>
                                </span>
                            </div>
                        </div>
                    <br>
                    </div>
                </div>
            </div>
        </div>
        {/if}
        

        <div class="panel-footer">                                              
        <button type="submit" value="{l s='Save' mod='expresscache'}" id="module_form_submit_btn_dyn" name="submitExpresscache" class="btn btn-default pull-right">
        <i class="process-icon-save"></i> {l s='Save' mod='expresscache'}  
        </button>                                                                                                                   
        </div>
        </form>

	</ps-tab>

	<ps-tab label="{l s='Triggers' mod='expresscache' }" id="tab-triggers">
		<!-- <raw id="form_triggers"></raw> -->
        <form-triggers></form-triggers>

	</ps-tab>

	<ps-tab label="{l s='Stats' mod='expresscache' }" id="tab-stats">
        <form action="" method="POST">
		<input type="submit" name="clearStats" value="{l s='Clear Stats' mod='expresscache' }" class="button btn btn-small btn-warning" />


        <input onclick="return refreshTheStats(); " type="submit" id="refreshStats" name="refreshStats" value="{l s='Refresh Stats' mod='expresscache' }" class="button btn btn-small btn-default" />
        </form>
                        <br><br>
        
        <table id="statsTable" class="table" style="width:100%;">
            <thead>
            <tr><th>{l s='URL' mod='expresscache' }</th><th>{l s='Hits' mod='expresscache' }</th><th>{l s='Misses' mod='expresscache' }</th></tr>
            </thead>
            <tbody>
                
            </tbody>
        
        </table>

        <div id="statsError" style="display: none"></div>
        
	</ps-tab>

	<ps-tab label="{l s='Pre Caching Cron' mod='expresscache' }" id="tab-precache">
        <div class="warn alert alert-info">{l s='Use the following settings to generate Cron URLs, you can use any combination to generate multiple Cron URLs.' mod='expresscache' }</div>
		<!-- <raw id="form_precaching"></raw> -->
        <form-precaching></form-precaching>
	</ps-tab>

	</ps-tabs>


<script>



//populate the stats table
function populateStats(stats_rows) {
    $('#statsTable tbody').html('');
    $(stats_rows).each(function() {
        var stat = this;
        // console.log(stat['is_mobile']);
        var is_mobile = stat['is_mobile'] == 0 ? '' : ' (Mobile)';
        $('#statsTable tbody').append('<tr><td>' + stat['page_url'] + is_mobile + '</td><td>' + stat['hits'] + '</td><td>' + stat['miss'] + '</td></tr>');
    })

}



</script>

<script>


//for custom animation
jQuery.fn.highlight = function() {
   $(this).each(function() {
        var el = $(this);
        el.before("<div/>")
        el.prev()
            .width(el.width())
            .height(el.height())
            .css({
                "position": "absolute",
                "background-color": "#ffff99",
                "opacity": ".8",
                "height": "100%",
                "width" : "100%"
            }).fadeOut(1500);
    });
}


var selected_tab = '';

function showTab(tab_id) {
    console.log(tab_id);
    // console.log('ul.tabs li[data-tab=\"' + tab_id + '\"]');
    var tab = $( 'ul.nav.nav-tabs li a[href=\"' + tab_id + '\"]' )[0];
    console.log(tab);
    $(tab).trigger( 'click' );
}

function confirmCache() {
    return confirm("{l s='Do you want to clear all cache entries?' mod='expresscache'}");
}

function genPreCacheURL() {
    var url = $('textarea[name="EXPRESSCACHE_PRECACHE_CRONURL"]').val();
    var IDLANG = $('select[name="EXPRESSCACHE_PRECACHE_LANGUAGE"]').val();
    var IDCOUNTRY = $('select[name="EXPRESSCACHE_PRECACHE_COUNTRY"]').val();
    var IDCURRENCY = $('select[name="EXPRESSCACHE_PRECACHE_CURRENCY"]').val();
    var NUMPRODUCTS = $('input[name="EXPRESSCACHE_PRECACHE_LIMIT"]').val();
    // if(typeof IDLANG == 'undefined') {
    //     return;
    // }
    url = url.replace(/id_lang=.*?&/, 'id_lang=' + IDLANG + '&');
    url = url.replace(/id_country=.*?&/, 'id_country=' + IDCOUNTRY + '&');
    url = url.replace(/id_currency=.*?&/, 'id_currency=' + IDCURRENCY + '&');
    url = url.replace(/num_products=.*?&/, 'num_products=' + NUMPRODUCTS + '&');
    $('textarea[name="EXPRESSCACHE_PRECACHE_CRONURL"]').val(url);
    if(!{$ps15}) {
        //ignoring for PS 1.5 since jQuery 1.7.2 has a bug with highlight()
        $('textarea[name="EXPRESSCACHE_PRECACHE_CRONURL"]').highlight();
    } 
    
}

$(document).ready(function(){


    //populate all forms dynamically

    {foreach from=$forms key=form_name item=form_content}
        
    {/foreach}
    
    $('ul.nav.nav-tabs').click(function(e){
        selected_tab = $(e.target).attr('href');
    })


    // populateStats(stats_rows);

    //init refresh for the first time
    refreshTheStats(); 

    //hook before any form submit to insert selected_tab field. 
    $('form').submit(function() {
        $(this).append('<input type="hidden" name="selected_tab" value="' + selected_tab + '">');
    });

    //precaching URL generator
    $('select[name*="EXPRESSCACHE_PRECACHE_"]').change(function() {
        genPreCacheURL();
    });

    $('input[name*="EXPRESSCACHE_PRECACHE_"]').change(function() {
        genPreCacheURL();
    });

    genPreCacheURL();

    if({$ps15}) {
        $('input[type="submit"]').addClass('button');
        $('button[type="submit"]').addClass('button');
    }
    
    
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


