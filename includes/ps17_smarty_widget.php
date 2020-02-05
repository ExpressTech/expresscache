<?php

$smarty->unregisterPlugin('function', 'widget');

smartyRegisterFunction($smarty, 'function', 'widget', 'smartyWidgetOverride');

$smarty->unregisterPlugin('block', 'widget_block');

smartyRegisterFunction($smarty, 'block', 'widget_block', 'smartyWidgetBlockOverride');

function ecWidgeWrapper($widget_data, $params, $widget_type = 'widget') {

    if(isset($params['name'])) {

        $module_name = $params['name'];
        $hook_name = isset($params['hook']) ? $params['hook'] : false;

        $activewidgets = unserialize(Configuration::get('EXPRESSCACHE_ACTIVE_WIDGETS')); 

        $is_warehouse_theme = Context::getContext()->shop->theme_name == 'warehouse' ? 1 : 0; 
        if($is_warehouse_theme) {
            $activewidgets = array_merge($activewidgets, array('ps_shoppingcart', 'ps_customersignin'));
        }
        
        $found = false;

        if (is_array($activewidgets)) {
        
            foreach ($activewidgets as $widget) {
                $expl_widget = explode(',', $widget);

                $active_module_name = $expl_widget[0];

                if(count($expl_widget) == 2) {
                    $active_hook_name = $expl_widget[1];
                } else {
                    $active_hook_name = 'null';
                }

                if ($active_module_name == $module_name) { //) {
                    
                    if($hook_name == false || $hook_name == $active_hook_name) {
                        $found = true;
                        break;
                    }

                }

            }
        
        }

        if($found && trim($widget_data) != '') {

            //$hook_name = $hook_name == null ? 'null' : $hook_name;
            
            $data_wrapped = '<!--['.$widget_type.' '.$active_hook_name.'] '.$active_module_name.'-->'.$widget_data.'<!--['.$widget_type.' '.$active_hook_name.'] '.$active_module_name.'-->';

            return $data_wrapped;
        }
    }

    return $widget_data;
}

function smartyWidgetOverride($params, &$smarty) {
    

    error_log('widget' . var_export($params, true));

    $widget_data = smartyWidget($params, $smarty);

    if (!Module::isInstalled('expresscache') || !Module::isEnabled('expresscache')) {
        return $widget_data;
    }


    return ecWidgeWrapper($widget_data, $params, 'widget');
        
}


function smartyWidgetBlockOverride($params, $content, &$smarty) {
    
    // error_log('block' . var_export($params, true));

    $widget_data = smartyWidgetBlock($params, $content, $smarty);

    if (!Module::isInstalled('expresscache') || !Module::isEnabled('expresscache')) {
        return $widget_data;
    }

    return ecWidgeWrapper($widget_data, $params, 'widgetblock');
}