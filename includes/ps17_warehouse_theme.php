<?php
if($isMobile) {
    $header = 'mobile-header-'.Configuration::get('iqitthemeed_rm_header').'.tpl';
} else {
    $header = 'header-'.Configuration::get('iqitthemeed_h_layout').'.tpl';
}

$header_content = file_get_contents(_PS_THEME_DIR_.'templates/_partials/_variants/'.$header);

//find widgets
$widgets_ret = preg_match_all('/{widget name="(.*?)"( hook="(.*?)")?}/', $header_content, $widgets);
$allowed_widgets = array('ps_customersignin');

// var_dump($widgets);exit;

if(count($widgets) >= 1) {
    foreach ($widgets[1] as $index => $widget_name) {
        if(!in_array($widget_name, $allowed_widgets)) 
            continue;
        

        $hook_content = '';
        
        if($widget_name) {

            $name_module = $widget_name;
            $hook_name = $widgets[3][$index] == '' ? 'null' : $widgets[3][$index];

            $moduleInstance = Module::getInstanceByName($name_module);
            $hook_content = Hook::coreRenderWidget($moduleInstance, $hook_name, $hook_args);
            // echo $hook_content;exit;
            // echo $hook_name;exit;
        }

        if($hook_content) {
            $pattern = "/<!--\[widget $hook_name\] $name_module-->(.*?)<!--\[widget $hook_name\] $name_module-->/s";
            $hook_content = preg_replace('/\$(\d)/', '\\\$$1', $hook_content);
            $count = 0;
            $p_content = preg_replace($pattern, $hook_content, $content, 1, $count);
            if (preg_last_error() === PREG_NO_ERROR && $count > 0) {
                $content = $p_content;
            }
            error_log($name_module);
            // break;
        }

    }
}

//find widget blocks
$widget_blocks_ret = preg_match_all('/{widget_block name="(.*?)"}(.*?){\/widget_block}/s', $header_content, $widget_blocks);

$allowed_widget_blocks = array('ps_shoppingcart');

if(count($widget_blocks) >= 1) {
    foreach ($widget_blocks[1] as $index => $widget_block_name) {
        if(!in_array($widget_block_name, $allowed_widget_blocks)) 
            continue;

        $hook_content = '';
        $widget_tpl = trim($widget_blocks[2][$index]);
        if($widget_tpl) {

            $name_module = $widget_block_name;
            $hook_name = 'null';

            $moduleInstance = Module::getInstanceByName($name_module);

            $this->context->smarty->assign($moduleInstance->getWidgetVariables($hook_name, $hook_args));

            $hook_content = $this->context->smarty->fetch("string:$widget_tpl", null, $this->context->controller->getLayout());
            

            
        }

        if($hook_content) {
            $pattern = "/<!--\[widgetblock $hook_name\] $name_module-->(.*?)<!--\[widgetblock $hook_name\] $name_module-->/s";
            $hook_content = preg_replace('/\$(\d)/', '\\\$$1', $hook_content);
            $count = 0;
            $p_content = preg_replace($pattern, $hook_content, $content, 1, $count);
            if (preg_last_error() === PREG_NO_ERROR && $count > 0) {
                $content = $p_content;
            }
            error_log($name_module);
            // break;
        }

        
        
    }
}
