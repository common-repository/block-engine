<?php

/**
 * Enqueue frontend script for content toggle block
 *
 * @return void
 */


function block_engine_render_tabbed_content_block($attributes, $contents){
    extract($attributes);
    $blockName = 'wp-block-block-engine-tabbed-content';

    $tabs = '';

    $contents = str_get_html('<div id="tabarray">' . $contents . '</div>')
                    ->find('#tabarray > .wp-block-block-engine-tabbed-content-tab-content-wrap');

    $tabContents = [];

    foreach ($contents as $key => $content) {
        if($useAnchors){
            if($tabsAnchor[$key] != ''){
                $content->{'data-tab-anchor'} = $tabsAnchor[$key];
            }
        }
        $tabContent = $content->outertext;
        if(preg_match('/^<div class="wp-block-block-engine-tabbed-content-tab-content-wrap active"/', $tabContent)){
            $accordionIsActive = true;
        }
        else{
            $accordionIsActive = false;
        }
        
        if($tabletTabDisplay == 'accordion' || $mobileTabDisplay == 'accordion'){
            $content = '<div class="' . $blockName . '-accordion-toggle'.
            ($accordionIsActive ? ' active' : '') .
            ($tabletTabDisplay == 'accordion' ? ' block-engine-tablet-display':'') .
            ($mobileTabDisplay == 'accordion' ? ' block-engine-mobile-display':'') .
            '">'.$tabsTitle[$key].'</div>' . $tabContent;
            array_push($tabContents, $content);
        }
        else{
            array_push($tabContents, $content->outertext);
        }
    }

    foreach($tabsTitle as $key=>$title){
        $tabs .= '<div class = "'.$blockName.'-tab-title-'.($tabVertical ? 'vertical-' : '').'wrap'
        . ($mobileTabDisplay == 'verticaltab' ? ' ' . $blockName . '-tab-title-mobile-vertical-wrap' : '')
        . ($tabletTabDisplay == 'verticaltab' ? ' ' . $blockName . '-tab-title-tablet-vertical-wrap' : '')
            .($activeTab == $key ? ' active' : '') . '"'.
            ($blockID == '' ?' style="background-color: '.($activeTab == $key ? $theme : 'initial')
            .'; border-color: '.($activeTab == $key ? $theme : 'lightgrey').
            '; color: '.($activeTab == $key ? $titleColor : '#000000').';"' :'').'>
            <div class="'.$blockName.'-tab-title">'.$title.'</div></div>';
    }

    $mobileTabStyle =  substr($mobileTabDisplay, 0, strlen($mobileTabDisplay) - 3);
    $tabletTabStyle = substr($tabletTabDisplay, 0, strlen($tabletTabDisplay) - 3);

    return '<div class="'.$blockName.' '.$blockName.'-holder '.($tabVertical ? 'vertical-holder' : '')
            . (isset($className) ? ' ' . esc_attr($className) : ''). (isset($align) ? 'align'.$align : '') 
            . ($mobileTabDisplay != 'accordion' ? ' ' . $blockName . '-'.$mobileTabStyle.'-holder-mobile' : '')
            . ($tabletTabDisplay != 'accordion' ? ' ' . $blockName . '-'.$tabletTabStyle.'-holder-tablet' : '')
            . '"' .($blockID == '' ? '' : ' id="block-engine-tabbed-content-' . $blockID . '"')
             . ($mobileTabDisplay == 'accordion' || $tabletTabDisplay == 'accordion' ? ' data-active-tabs="['.$activeTab.']"' : '') . '>
                <div class="' . $blockName . '-tab-holder ' . ($tabVertical ? 'vertical-tab-width' : '')
                . ($mobileTabDisplay != 'accordion' ? ' ' . $mobileTabStyle. '-tab-width-mobile' : '')
                . ($tabletTabDisplay != 'accordion' ? ' ' . $tabletTabStyle . '-tab-width-tablet' : '') .'">
                    <div class="' . $blockName.'-tabs-title' . ($tabVertical ? '-vertical-tab' : '')
                    . ($mobileTabDisplay == 'accordion' ? ' block-engine-mobile-hide' : ' ' . $blockName . '-tabs-title-mobile-'.$mobileTabStyle.'-tab' )
                    . ($tabletTabDisplay == 'accordion' ? ' block-engine-tablet-hide' : ' ' . $blockName . '-tabs-title-tablet-'.$tabletTabStyle.'-tab').'">'.
                    $tabs . '</div></div>
                <div class="' . $blockName . '-tabs-content ' . ($tabVertical ? 'vertical-content-width' : '')
                . ($mobileTabDisplay == 'verticaltab' ? ' vertical-content-width-mobile' : ($mobileTabDisplay == 'accordion' ? ' block-engine-tabbed-content-mobile-accordion' : ''))
                . ($tabletTabDisplay == 'verticaltab' ? ' vertical-content-width-tablet' : ($tabletTabDisplay == 'accordion' ? ' block-engine-tabbed-content-tablet-accordion' : ''))
                . '">' .
                implode($tabContents) . '</div>
            </div>';
}

function block_engine_register_tabbed_content_block(){
    if(function_exists('register_block_type')){
        require dirname(dirname(__DIR__)) . '/defaults.php';
        register_block_type('block-engine/tabbed-content', array(
            'attributes' => $defaultValues['block-engine/tabbed-content']['attributes'],
            'render_callback' =>  'block_engine_render_tabbed_content_block'));
    }
}

function block_engine_tabbed_content_add_frontend_assets() {
    require_once dirname(dirname(__DIR__)) . '/common.php';

    $presentBlocks = block_engine_get_present_blocks();

    foreach( $presentBlocks as $block ){
        if($block['blockName'] == 'block-engine/tabbed-content' || $block['blockName'] == 'block-engine/tabbed-content-block'){
            wp_enqueue_script(
                'block_engine-tabbed-content-front-script',
                BLOCK_ENGINE_PLUGIN_URI. '/includes/guten/blocks/tabbed-content/js/front.build.js',
                array(),
                BLOCK_ENGINE_VERSION,
                true
            );
            if(!wp_script_is('block_engine-scrollby-polyfill', 'queue')){
                wp_enqueue_script(
                    'block_engine-scrollby-polyfill',
                    BLOCK_ENGINE_PLUGIN_URI. '/includes/guten/commonjs/scrollby-polyfill.js',
                    array(),
                    BLOCK_ENGINE_VERSION,
                    true
                );
            }
            break;
        }
    }
}

add_action( 'wp_enqueue_scripts', 'block_engine_tabbed_content_add_frontend_assets' );
add_action('init', 'block_engine_register_tabbed_content_block');
