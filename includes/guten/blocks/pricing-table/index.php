<?php

/**
 * Enqueue frontend script for button block
 *
 * @return void
 */


function block_engine_render_pricing_table_block($attributes)
{
    extract($attributes);

    require dirname(dirname(__DIR__)) . '/common.php';

    $iconSize = array('small' => 25, 'medium' => 30, 'large' => 35, 'larger' => 40);

    return '<div class="block-engine-button-container align-button-' . $align . (isset($className) ? ' ' . esc_attr($className) : '') . '"' . (!isset($blockID) || $blockID == '' ? ' ' : ' id="block-engine-button-' . $blockID . '"') . '>
                <a href="' . esc_url($url) . '" target="' . ($openInNewTab ? '_blank' : '_self') . '"
                rel="noopener noreferrer' . ($addNofollow ? ' nofollow' : '') . '"
                class="block-engine-button-block-main block-engine-button-' . $size .
        ($buttonWidth == 'full' ? ' block-engine-button-full-width' :
            ($buttonWidth == 'flex' ? ' block-engine-button-flex-' . $size : '')) . '"' .
        (isset($blockID) && $blockID != '' ? '' : 'data-defaultColor="' . $buttonColor . '"
                data-defaultTextColor="' . $buttonTextColor . '"
                data-hoverColor="' . $buttonHoverColor . '"
                data-hoverTextColor="' . $buttonTextHoverColor . '"
                data-buttonIsTransparent="' . json_encode($buttonIsTransparent) . '"
                style="background-color: ' . ($buttonIsTransparent ? 'transparent' : $buttonColor) . ';' .
            'color: ' . ($buttonIsTransparent ? $buttonColor : $buttonTextColor) . ';' .
            'border-radius: ' . ($buttonRounded ? '60' : '0') . 'px;' .
            'border: ' . ($buttonIsTransparent ? ('3px solid ' . $buttonColor) : 'none') . ';"') . '>
                <div class="block-engine-button-content-holder">' .
        ($chosenIcon != '' ? '<span class="block-engine-button-icon-holder"><svg xmlns="http://www.w3.org/2000/svg"
                    height="' . $iconSize[$size] . '", width="' . $iconSize[$size] . '"
                    viewBox="0, 0, ' . $block_engine_font_awesome_icon[$chosenIcon][0] . ', ' . $block_engine_font_awesome_icon[$chosenIcon][1]
            . '"><path fill="currentColor" d="' . $block_engine_font_awesome_icon[$chosenIcon][2] . '"></svg></span>    ' : '')
        . '<span class="block-engine-button-block-btn">' . $buttonText . '</span>
                </div>
            </a>
        </div>';
}

function block_engine_pricing_add_frontend_assets()
{
    require_once dirname(dirname(__DIR__)) . '/common.php';

    $presentBlocks = block_engine_get_present_blocks();

    foreach ($presentBlocks as $block) {
        if (($block['blockName'] == 'block-engine/button' && !isset($block['attrs']['blockID'])) || $block['blockName'] == 'block-engine/button-block') {
            wp_enqueue_script(
                'block_engine-button-front-script',
                BLOCK_ENGINE_PLUGIN_URI . '/includes/guten/blocks/button/js/front.build.js',
                array(),
                BLOCK_ENGINE_VERSION,
                true
            );
            break;
        }
    }
}

function block_engine_register_pricing_table_block()
{
    if (function_exists('table_register_block_type')) {
        require dirname(dirname(__DIR__)) . '/defaults.php';
        register_block_type('block-engine/pricing-table', array(
                // 'attributes' => $defaultValues['block-engine/pricing']['attributes'],
                //'render_callback' => 'block_engine_render_button_block'
            )
        );
    }
}

add_action('init', 'block_engine_register_pricing_table_block');

//add_action( 'wp_enqueue_scripts', 'block_engine_button_add_frontend_assets' );