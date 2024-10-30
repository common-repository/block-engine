<?php
/**
 * Blocks Initializer
 *
 * Enqueue CSS/JS of all the blocks.
 *
 * @since    1.0.0
 * @package CGB
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('get_current_screen')) {
    require_once(ABSPATH . 'wp-admin/includes/screen.php');
}

/**
 * Check if the current page is the Gutenberg block editor.
 * @return bool
 */
function block_engine_check_is_gutenberg_page()
{

    // The Gutenberg plugin is on.
    if (function_exists('is_gutenberg_page') && is_gutenberg_page()) {
        return true;
    }

    // Gutenberg page on WordPress 5+.
    $current_screen = get_current_screen();
    if (method_exists($current_screen, 'is_block_editor') && $current_screen->is_block_editor()) {
        return true;
    }

    return false;

}

/**
 * Enqueue Gutenberg block assets for both frontend + backend.
 *
 * `wp-blocks`: includes block type registration and related functions.
 *
 * @since 1.0.0
 */

function block_engine_update_css_version($updated)
{
    static $frontendStyleUpdated = false;
    static $editorStyleUpdated = false;
    if ($updated == 'frontend') {
        $frontendStyleUpdated = true;
    } else if ($updated == 'editor') {
        $editorStyleUpdated = true;
    }

    if ($frontendStyleUpdated && $editorStyleUpdated) {
        update_option('block_engine_css_version', BLOCK_ENGINE_VERSION);
        $frontendStyleUpdated = false;
        $editorStyleUpdated = false;
    }
}

function block_engine_load_assets()
{
    wp_enqueue_style(
        'block_engine-cgb-style-css', // Handle.
        BLOCK_ENGINE_PLUGIN_URI . '/assets/dist/blocks.style.build.css', // Block style CSS.
        array(), // Dependency to include the CSS after it.
        BLOCK_ENGINE_VERSION  // Version: latest version number.
    );
}

function block_engine_cgb_block_assets()
{
    // Styles.
    if (is_singular() and has_blocks()) {
        require_once plugin_dir_path(__FILE__) . 'common.php';

        $presentBlocks = block_engine_get_present_blocks();

        foreach ($presentBlocks as $block) {
            if (strpos($block['blockName'], 'block-engine/') === 0) {
                block_engine_load_assets();
                break;
            }
        }
    } elseif (block_engine_check_is_gutenberg_page()) {
        block_engine_load_assets();
    }
} // End function block_engine_cgb_block_assets().

// Hook: Frontend assets.
add_action('enqueue_block_assets', 'block_engine_cgb_block_assets');

function block_engine_include_block_attribute_css()
{
    require plugin_dir_path(__FILE__) . 'defaults.php';
    require_once plugin_dir_path(__FILE__) . 'common.php';

    $presentBlocks = array_unique(block_engine_get_present_blocks(), SORT_REGULAR);
    $blockStylesheets = "";

    $hasNoSmoothScroll = true;

    foreach ($presentBlocks as $block) {
        if (array_key_exists($block['blockName'], $defaultValues)) {
            $attributes = array_merge(array_map(function ($attribute) {
                return $attribute['default'];
            }, $defaultValues[$block['blockName']]['attributes']), $block['attrs']);
        }

        if (isset($attributes) && array_key_exists('blockID', $attributes) && $attributes['blockID'] != '') {
            switch ($block['blockName']) {
                default:
                    //nothing could be done
                    break;
                case 'block-engine/button':
                    $prefix = '#block-engine-button-' . $attributes['blockID'];
                    $blockStylesheets .= $prefix . ' a{' . PHP_EOL;
                    if ($attributes['buttonIsTransparent']) {
                        $blockStylesheets .= 'background-color: transparent;' . PHP_EOL .
                            'color: ' . $attributes['buttonColor'] . ';' . PHP_EOL .
                            'border: 3px solid ' . $attributes['buttonColor'] . ';';
                    } else {
                        $blockStylesheets .= 'background-color: ' . $attributes['buttonColor'] . ';' . PHP_EOL .
                            'color: ' . $attributes['buttonTextColor'] . ';' . PHP_EOL .
                            'border: none;';
                    }
                    $blockStylesheets .= 'border-radius: ' . ($attributes['buttonRounded'] ? '60' : '0') . 'px;' . PHP_EOL .
                        '}' . PHP_EOL .

                        $prefix . ' a:hover{' . PHP_EOL;
                    if ($attributes['buttonIsTransparent']) {
                        $blockStylesheets .= 'color: ' . $attributes['buttonHoverColor'] . ';' . PHP_EOL .
                            'border: 3px solid ' . $attributes['buttonHoverColor'] . ';';
                    } else {
                        $blockStylesheets .= 'background-color: ' . $attributes['buttonHoverColor'] . ';' . PHP_EOL .
                            'color: ' . $attributes['buttonTextHoverColor'] . ';' . PHP_EOL .
                            'border: none;';
                    }
                    $blockStylesheets .= '}' . PHP_EOL .
                        $prefix . ' block-engine-button-content-holder{' . PHP_EOL .
                        'flex-direction: ' . ($attributes['iconPosition'] == 'left' ? 'row' : 'row-reverse') . ';' . PHP_EOL .
                        '}' . PHP_EOL;
                    break;
                case 'block-engine/call-to-action-block':
                    $prefix = '#block_engine_call_to_action_' . $attributes['blockID'];
                    $blockStylesheets .= $prefix . '{' . PHP_EOL .
                        'background-color: ' . $attributes['ctaBackgroundColor'] . ';' . PHP_EOL .
                        'border-width: ' . $attributes['ctaBorderSize'] . 'px;' . PHP_EOL .
                        'border-color: ' . $attributes['ctaBorderColor'] . ';' . PHP_EOL .
                        '}' . PHP_EOL .
                        $prefix . ' .block_engine_call_to_action_headline_text{' . PHP_EOL .
                        'font-size: ' . $attributes['headFontSize'] . 'px;' . PHP_EOL .
                        'color: ' . $attributes['headColor'] . ';' . PHP_EOL .
                        'text-align: ' . $attributes['headAlign'] . ';' . PHP_EOL .
                        '}' . PHP_EOL .
                        $prefix . ' .block_engine_cta_content_text{' . PHP_EOL .
                        'font-size: ' . $attributes['contentFontSize'] . 'px;' . PHP_EOL .
                        'color: ' . $attributes['contentColor'] . ';' . PHP_EOL .
                        'text-align: ' . $attributes['contentAlign'] . ';' . PHP_EOL .
                        '}' . PHP_EOL .
                        $prefix . ' .block_engine_cta_button{' . PHP_EOL .
                        'background-color: ' . $attributes['buttonColor'] . ';' . PHP_EOL .
                        'width: ' . $attributes['buttonWidth'] . 'px;' . PHP_EOL .
                        '}' . PHP_EOL .
                        $prefix . ' .block_engine_cta_button_text{' . PHP_EOL .
                        'color: ' . $attributes['buttonTextColor'] . ';' . PHP_EOL .
                        'font-size: ' . $attributes['buttonFontSize'] . 'px;' . PHP_EOL .
                        '}' . PHP_EOL;
                    break;
                case 'block-engine/click-to-tweet':
                    $prefix = '#block_engine_click_to_tweet_' . $attributes['blockID'];
                    $blockStylesheets .= $prefix . '{' . PHP_EOL .
                        'border-color: ' . $attributes['borderColor'] . ';' . PHP_EOL .
                        '}' . PHP_EOL .
                        $prefix . ' .block_engine_tweet{' . PHP_EOL .
                        'color: ' . $attributes['tweetColor'] . ';' . PHP_EOL .
                        'font-size: ' . $attributes['tweetFontSize'] . 'px;' . PHP_EOL .
                        '}' . PHP_EOL;
                    break;

                case 'block-engine/content-accordion':
                    $attributes = array_merge($attributes,
                        array_map(function ($attribute) {
                            return $attribute['default'];
                        }, $defaultValues['block-engine/content-accordion-panel']['attributes']),
                        $block['innerBlocks'][0]['attrs']);
                    $prefix = '#block-engine-content-toggle-' . $attributes['blockID'];
                    $blockStylesheets .= $prefix . ' .wp-block-block-engine-content-toggle-accordion{' . PHP_EOL .
                        'border-color: ' . $attributes['theme'] . ';' . PHP_EOL .
                        '}' . PHP_EOL .
                        $prefix . ' .wp-block-block-engine-content-toggle-accordion-title-wrap{' . PHP_EOL .
                        'background-color: ' . $attributes['theme'] . ';' . PHP_EOL .
                        '}' . PHP_EOL .
                        $prefix . ' .wp-block-block-engine-content-toggle-accordion-title{' . PHP_EOL .
                        'color: ' . $attributes['titleColor'] . ';' . PHP_EOL .
                        '}' . PHP_EOL;
                    break;
                case 'block-engine/countdown':
                    $blockStylesheets .= '#block_engine_countdown_' . $attributes['blockID'] . '{' . PHP_EOL .
                        'text-align: ' . $attributes['messageAlign'] . PHP_EOL .
                        '}';
                    break;
                case 'block-engine/divider':
                    $blockStylesheets .= '#block_engine_divider_' . $attributes['blockID'] . '{' . PHP_EOL .
                        'border-top: ' . $attributes['borderSize'] . 'px ' . $attributes['borderStyle'] . ' ' . $attributes['borderColor'] . ';' . PHP_EOL .
                        'margin-top: ' . $attributes['borderHeight'] . 'px;' . PHP_EOL .
                        'margin-bottom: ' . $attributes['borderHeight'] . 'px;' . PHP_EOL .
                        '}' . PHP_EOL;
                    break;
                case 'block-engine/expand':
                    $blockStylesheets .= '#block-engine-expand-' . $attributes['blockID'] . ' .block-engine-expand-toggle-button{' . PHP_EOL .
                        'text-align: ' . $attributes['toggleAlign'] . ';' . PHP_EOL .
                        '}' . PHP_EOL;
                    break;
                case 'block-engine/feature-box':
                    $prefix = '#block_engine_feature_box_' . $attributes['blockID'];
                    $blockStylesheets .= $prefix . ' .block_engine_feature_one_title{' . PHP_EOL .
                        'text-align: ' . $attributes['title1Align'] . ';' . PHP_EOL .
                        '}' . PHP_EOL .
                        $prefix . ' .block_engine_feature_two_title{' . PHP_EOL .
                        'text-align: ' . $attributes['title2Align'] . ';' . PHP_EOL .
                        '}' . PHP_EOL .
                        $prefix . ' .block_engine_feature_three_title{' . PHP_EOL .
                        'text-align: ' . $attributes['title3Align'] . ';' . PHP_EOL .
                        '}' . PHP_EOL .
                        $prefix . ' .block_engine_feature_one_body{' . PHP_EOL .
                        'text-align: ' . $attributes['body1Align'] . ';' . PHP_EOL .
                        '}' . PHP_EOL .
                        $prefix . ' .block_engine_feature_two_body{' . PHP_EOL .
                        'text-align: ' . $attributes['body2Align'] . ';' . PHP_EOL .
                        '}' . PHP_EOL .
                        $prefix . ' .block_engine_feature_three_body{' . PHP_EOL .
                        'text-align: ' . $attributes['body3Align'] . ';' . PHP_EOL .
                        '}';
                    break;
                case 'block-engine/how-to':
                    $prefix = '#block_engine_howto_' . $attributes['blockID'];
                    if ($attributes['sectionListStyle'] == 'none') {
                        $blockStylesheets .= $prefix . ' .block_engine_howto-section-display{' . PHP_EOL .
                            'list-style: none;' . PHP_EOL .
                            '}' . PHP_EOL .
                            $prefix . ' .block_engine_howto-step-display{' . PHP_EOL .
                            'list-style: none;' . PHP_EOL .
                            '}' . PHP_EOL;
                    }
                    if ($attributes['suppliesListStyle'] == 'none') {
                        $blockStylesheets .= $prefix . ' .block_engine_howto-supplies-list{' . PHP_EOL .
                            'list-style: none;' . PHP_EOL .
                            '}' . PHP_EOL;
                    }
                    if ($attributes['toolsListStyle'] == 'none') {
                        $blockStylesheets .= $prefix . ' .block_engine_howto-tools-list{' . PHP_EOL .
                            'list-style: none;' . PHP_EOL .
                            '}' . PHP_EOL;
                    }
                    break;
                case 'block-engine/image-slider':
                    $prefix = '#block_engine_image_slider_' . $attributes['blockID'];
                    $blockStylesheets .= $prefix . ' .flickity-slider img{' . PHP_EOL .
                        'max-height: ' . $attributes['sliderHeight'] . 'px;' . PHP_EOL .
                        '}' . PHP_EOL;
                    break;
                case 'block-engine/notification-box':
                    $blockStylesheets .= '#block-engine-notification-box-' . $attributes['blockID'] . ' .block_engine_notify_text{' . PHP_EOL .
                        'text-align: ' . $attributes['align'] . ';' . PHP_EOL .
                        '}' . PHP_EOL;
                    break;
                case 'block-engine/number-box-block':
                    $prefix = '#block-engine-number-box-' . $attributes['blockID'];
                    $blockStylesheets .= $prefix . ' .block_engine_number_one_title{' . PHP_EOL .
                        'text-align: ' . $attributes['title1Align'] . ';' . PHP_EOL .
                        '}' . PHP_EOL .
                        $prefix . ' .block_engine_number_two_title{' . PHP_EOL .
                        'text-align: ' . $attributes['title2Align'] . ';' . PHP_EOL .
                        '}' . PHP_EOL .
                        $prefix . ' .block_engine_number_three_title{' . PHP_EOL .
                        'text-align: ' . $attributes['title3Align'] . ';' . PHP_EOL .
                        '}' . PHP_EOL .
                        $prefix . ' .block_engine_number_one_body{' . PHP_EOL .
                        'text-align: ' . $attributes['body1Align'] . ';' . PHP_EOL .
                        '}' . PHP_EOL .
                        $prefix . ' .block_engine_number_two_body{' . PHP_EOL .
                        'text-align: ' . $attributes['body2Align'] . ';' . PHP_EOL .
                        '}' . PHP_EOL .
                        $prefix . ' .block_engine_number_three_body{' . PHP_EOL .
                        'text-align: ' . $attributes['body3Align'] . ';' . PHP_EOL .
                        '}' . PHP_EOL .
                        $prefix . ' .block_engine_number_column{' . PHP_EOL .
                        'text-align: ' . $attributes['borderColor'] . ';' . PHP_EOL .
                        '}' . PHP_EOL .
                        $prefix . ' .block_engine_number_box_number{' . PHP_EOL .
                        'background-color: ' . $attributes['numberBackground'] . ';' . PHP_EOL .
                        '}' . PHP_EOL .
                        $prefix . ' .block_engine_number_box_number>p{' . PHP_EOL .
                        'color: ' . $attributes['numberColor'] . ';' . PHP_EOL .
                        '}';
                    break;
                case 'block-engine/progress-bar':
                    $prefix = '#block-engine-progress-bar-' . $attributes['blockID'];
                    $blockStylesheets .= $prefix . ' .block_engine_progress-bar-text p{' . PHP_EOL .
                        'text-align: ' . $attributes['detailAlign'] . ';' . PHP_EOL .
                        '}' . PHP_EOL .
                        $prefix . ' .block_engine_progress-bar-text p{' . PHP_EOL .
                        'text-align: ' . $attributes['detailAlign'] . ';' . PHP_EOL .
                        '}' . PHP_EOL;

                    if ($attributes['barType'] == 'linear') {
                        $blockStylesheets .= $prefix . ' .block_engine_progress-bar-line-path{' . PHP_EOL .
                            'stroke-dashoffset: 100px;' . PHP_EOL .
                            '}' . PHP_EOL .
                            $prefix . ' .block_engine_progress-bar-label{' . PHP_EOL .
                            'width: ' . $attributes['percentage'] . '%;' . PHP_EOL;
                    } else {
                        $circleRadius = 50 - ($attributes['barThickness'] + 3) / 2;
                        $circlePathLength = $circleRadius * M_PI * 2;
                        $blockStylesheets .= '#block-engine-progress-bar-' . $attributes['blockID'] . ' .block_engine_progress-bar-container{' . PHP_EOL .
                            'height: 150px;' . PHP_EOL . 'width: 150px;' . PHP_EOL .
                            '}' . PHP_EOL .
                            $prefix . ' .block_engine_progress-bar-circle-trail{' . PHP_EOL .
                            'stroke-dasharray: ' . $circlePathLength . 'px,' . $circlePathLength . 'px' . PHP_EOL .
                            '}' . PHP_EOL .
                            $prefix . ' .block_engine_progress-bar-circle-path{' . PHP_EOL .
                            'stroke-dasharray: 0px, ' . $circlePathLength . 'px' . PHP_EOL .
                            '}' . PHP_EOL .
                            $prefix . ' .block_engine_progress-bar-label{' . PHP_EOL;
                    }
                    $blockStylesheets .= 'visibility: hidden;' . PHP_EOL .
                        '}' . PHP_EOL .
                        $prefix . '.block_engine_progress-bar-filled .block_engine_progress-bar-label{' . PHP_EOL .
                        'visibility: visible;' . PHP_EOL .
                        '}' . PHP_EOL;
                    if ($attributes['barType'] == 'linear') {
                        $blockStylesheets .= $prefix . '.block_engine_progress-bar-filled .block_engine_progress-bar-line-path{' . PHP_EOL .
                            'stroke-dashoffset: ' . (100 - $attributes['percentage']) . 'px';
                    } else {
                        $strokeArcLength = $circlePathLength * $attributes['percentage'] / 100;
                        $blockStylesheets .= $prefix . '.block_engine_progress-bar-filled .block_engine_progress-bar-circle-path{' . PHP_EOL .
                            'stroke-linecap: round;' . PHP_EOL .
                            'stroke-dasharray: ' . $strokeArcLength . 'px, ' . $circlePathLength . 'px;' . PHP_EOL;
                    }
                    $blockStylesheets .= '}';
                    break;
                case 'block-engine/review':
                    $prefix = '#block_engine_review_' . $attributes['blockID'];
                    $blockStylesheets .= $prefix . ' .block_engine_review_item_name{' . PHP_EOL .
                        'text-align: ' . $attributes['titleAlign'] . ';' . PHP_EOL .
                        '}' . PHP_EOL .
                        $prefix . ' .block_engine_review_author_name{' . PHP_EOL .
                        'text-align: ' . $attributes['authorAlign'] . ';' . PHP_EOL .
                        '}' . PHP_EOL .
                        $prefix . ' .block_engine_review_description{' . PHP_EOL .
                        'text-align: ' . $attributes['descriptionAlign'] . ';' . PHP_EOL .
                        '}' . PHP_EOL .
                        $prefix . ' .block_engine_review_cta_main>a{' . PHP_EOL .
                        'color: ' . $attributes['callToActionForeColor'] . ';' . PHP_EOL .
                        '}' . PHP_EOL .
                        $prefix . ' .block_engine_review_cta_btn{' . PHP_EOL .
                        'color: ' . $attributes['callToActionForeColor'] . ';' . PHP_EOL .
                        'border-color: ' . $attributes['callToActionForeColor'] . ';' . PHP_EOL .
                        'background-color: ' . $attributes['callToActionBackColor'] . ';' . PHP_EOL .
                        '}' . PHP_EOL .
                        $prefix . ' .block_engine_review_image{' . PHP_EOL .
                        'max-height: ' . $attributes['imageSize'] . 'px;' . PHP_EOL .
                        'max-width: ' . $attributes['imageSize'] . 'px;' . PHP_EOL .
                        '}' . PHP_EOL;
                    break;
                case 'block-engine/social-share':
                    $icon_sizes = array(
                        'normal' => 20,
                        'medium' => 30,
                        'large' => 40,
                    );
                    $icon_size = $icon_sizes[$attributes['iconSize']];
                    $blockStylesheets .= '#block-engine-social-share-' . $attributes['blockID'] . ' .social-share-icon{' . PHP_EOL .
                        'width:' . ($icon_size * 1.5) . 'px;' . PHP_EOL .
                        'height:' . ($icon_size * 1.5) . 'px;' . PHP_EOL .
                        '}' . PHP_EOL;
                    break;
                case 'block-engine/star-rating-block':
                    $prefix = '#block-engine-star-rating-' . $attributes['blockID'];
                    $blockStylesheets .= $prefix . ' .block-engine-star-outer-container{' . PHP_EOL .
                        'justify-content: ' . ($attributes['starAlign'] == 'center' ? 'center' :
                            ('flex-' . $attributes['starAlign'] == 'left' ? 'start' : 'end')) . ';' . PHP_EOL .
                        '}' . PHP_EOL .
                        $prefix . ' .block-engine-review-text{' . PHP_EOL .
                        'text-align: ' . $attributes['reviewTextAlign'] . ';' . PHP_EOL .
                        '}' . PHP_EOL .
                        $prefix . ' svg{' . PHP_EOL .
                        'fill: ' . $attributes['starColor'] . ';' . PHP_EOL .
                        '}' . PHP_EOL;
                    break;
                case 'block-engine/styled-box':
                    $prefix = '#block-engine-styled-box-' . $attributes['blockID'];
                    if ($attributes['mode'] == 'notification') {
                        $blockStylesheets .= $prefix . ' .block-engine-notification-text{' . PHP_EOL .
                            'background-color: ' . $attributes['backColor'] . ';' . PHP_EOL .
                            'color: ' . $attributes['foreColor'] . ';' . PHP_EOL .
                            'border-left-color: ' . $attributes['outlineColor'] . ';' . PHP_EOL .
                            'text-align: ' . $attributes['textAlign'][0] . ';' . PHP_EOL .
                            '}' . PHP_EOL;
                    } else if ($attributes['mode'] == 'feature') {
                        foreach (range(1, count($attributes['text'])) as $i) {
                            $blockStylesheets .= $prefix . ' .block-engine-feature:nth-child(' . $i . ') .block-engine-feature-title{' . PHP_EOL .
                                'text-align: ' . $attributes['titleAlign'][$i - 1] . ';' . PHP_EOL .
                                '}' . PHP_EOL .
                                $prefix . ' .block-engine-feature:nth-child(' . $i . ') .block-engine-feature-body{' . PHP_EOL .
                                'text-align: ' . $attributes['textAlign'][$i - 1] . ';' . PHP_EOL .
                                '}' . PHP_EOL;
                        }
                    } else if ($attributes['mode'] == 'number') {
                        $blockStylesheets .= $prefix . ' .block-engine-number-panel{' . PHP_EOL .
                            'border-color: ' . $attributes['outlineColor'] . ';' . PHP_EOL .
                            '}' . PHP_EOL .
                            $prefix . ' .block-engine-number-container{' . PHP_EOL .
                            'background-color: ' . $attributes['backColor'] . ';' . PHP_EOL .
                            '}' . PHP_EOL .
                            $prefix . ' .block-engine-number-display{' . PHP_EOL .
                            'color: ' . $attributes['foreColor'] . ';' . PHP_EOL .
                            '}' . PHP_EOL;
                        foreach (range(1, count($attributes['text'])) as $i) {
                            $blockStylesheets .= $prefix . ' .block-engine-number-panel:nth-child(' . $i . ') .block-engine-number-box-title{' . PHP_EOL .
                                'text-align: ' . $attributes['titleAlign'][$i - 1] . ';' . PHP_EOL .
                                '}' . PHP_EOL .
                                $prefix . ' .block-engine-number-panel:nth-child(' . $i . ') .block-engine-number-box-body{' . PHP_EOL .
                                'text-align: ' . $attributes['textAlign'][$i - 1] . ';' . PHP_EOL .
                                '}' . PHP_EOL;
                        }
                    }
                    break;
                case 'block-engine/styled-list':
                    $prefix = '#block_engine_styled_list-' . $attributes['blockID'];
                    if ($attributes['iconSize'] < 3) {
                        $blockStylesheets .= $prefix . ' .fa-li{' . PHP_EOL .
                            'top: -0.1em;' . PHP_EOL .
                            '}' . PHP_EOL;
                    } elseif ($attributes['iconSize'] >= 5) {
                        $blockStylesheets .= $prefix . ' .fa-li{' . PHP_EOL .
                            'top: 3px;' . PHP_EOL .
                            '}' . PHP_EOL;
                    }
                    $blockStylesheets .= $prefix . '{' . PHP_EOL .
                        'justify-content: ' . ($attributes['alignment'] == 'center' ? 'center' :
                            'flex-' . ($attributes['alignment'] == 'left' ? 'start' : 'end')) . ';' . PHP_EOL .
                        '}' . PHP_EOL;
                    break;
                case 'block-engine/tabbed-content-block':
                    $prefix = '#block-engine-tabbed-content-' . $attributes['blockID'];
                    $blockStylesheets .= $prefix . ' .wp-block-block-engine-tabbed-content-tab-title-wrap{' . PHP_EOL .
                        'background-color: initial;' . PHP_EOL .
                        'border-color: lightgrey;' . PHP_EOL .
                        'color: #000000;' . PHP_EOL .
                        '}' . PHP_EOL .
                        $prefix . ' .wp-block-block-engine-tabbed-content-tab-title-wrap.active, ' .
                        $prefix . ' .wp-block-block-engine-tabbed-content-tab-title-vertical-wrap.active,' .
                        $prefix . ' .wp-block-block-engine-tabbed-content-accordion-toggle.active{' . PHP_EOL .
                        'background-color: ' . $attributes['theme'] . ';' . PHP_EOL .
                        'border-color: ' . $attributes['theme'] . ';' . PHP_EOL .
                        'color: ' . $attributes['titleColor'] . ';' . PHP_EOL .
                        '}' .
                        $prefix . ' .wp-block-block-engine-tabbed-content-tabs-title{' . PHP_EOL .
                        'justify-content: ' . ($attributes['tabsAlignment'] == 'center' ? 'center' :
                            'flex-' . ($attributes['tabsAlignment'] == 'left' ? 'start' : 'end')) . ';' . PHP_EOL .
                        '}' . PHP_EOL;
                    foreach ($attributes['tabsTitleAlignment'] as $key => $titleAlign) {
                        $blockStylesheets .= $prefix . ' .wp-block-block-engine-tabbed-content-tab-title-wrap:nth-child(' . ($key + 1) . '){' . PHP_EOL .
                            'text-align: ' . $titleAlign . ';' . PHP_EOL .
                            '}' . PHP_EOL;
                    }
                    break;
                case 'block-engine/table-of-contents-block':
                    $prefix = '#block_engine_table-of-contents-' . $attributes['blockID'];
                    if ($attributes['listStyle'] == 'plain') {
                        $blockStylesheets .= $prefix . ' ul{' . PHP_EOL .
                            'list-style: none;' . PHP_EOL .
                            '}' . PHP_EOL;
                    }
                    if ($attributes['enableSmoothScroll'] && $hasNoSmoothScroll) {
                        $blockStylesheets .= 'html {' . PHP_EOL .
                            'scroll-behavior: smooth;' . PHP_EOL .
                            '}' . PHP_EOL;
                        $hasNoSmoothScroll = false;
                    }
                    $blockStylesheets .= $prefix . ' .block_engine_table-of-contents-header{' . PHP_EOL .
                        'justify-self: ' . ($attributes['titleAlignment'] == 'center' ? 'center' :
                            'flex-' . ($attributes['titleAlignment'] == 'left' ? 'start' : 'end')) . ';' . PHP_EOL .
                        '}' . PHP_EOL;
                    break;
                case 'block-engine/testimonial':
                    $prefix = '#block_engine_testimonial_' . $attributes['blockID'];
                    $blockStylesheets .= $prefix . '{' . PHP_EOL .
                        'background-color: ' . $attributes['backgroundColor'] . ';' . PHP_EOL .
                        'color: ' . $attributes['textColor'] . ';' . PHP_EOL .
                        '}' . PHP_EOL .
                        $prefix . ' .block_engine_testimonial_text{' . PHP_EOL .
                        'font-size: ' . $attributes['textSize'] . ';' . PHP_EOL .
                        'text-align: ' . $attributes['textAlign'] . ';' . PHP_EOL .
                        '}' . PHP_EOL .
                        $prefix . ' .block_engine_testimonial_author{' . PHP_EOL .
                        'text-align: ' . $attributes['authorAlign'] . ';' . PHP_EOL .
                        '}' . PHP_EOL .
                        $prefix . ' .block_engine_testimonial_author_role{' . PHP_EOL .
                        'text-align: ' . $attributes['authorRoleAlign'] . ';' . PHP_EOL .
                        '}' . PHP_EOL;
                    break;
                case 'block-engine/post-grid':
                    $prefix = '#block_engine_post-grid-block_' . $attributes['blockID'];
                    break;
            }
        }
    }
    $blockStylesheets = preg_replace('/\s+/', ' ', $blockStylesheets);
    ob_start(); ?>

    <style><?php echo($blockStylesheets); ?></style>

    <?php
    ob_end_flush();
}

add_action('wp_head', 'block_engine_include_block_attribute_css');

/**
 * Enqueue Gutenberg block assets for backend editor.
 *
 * `wp-blocks`: includes block type registration and related functions.
 * `wp-element`: includes the WordPress Element abstraction for describing the structure of your blocks.
 * `wp-i18n`: To internationalize the block's text.
 *
 * @since 1.0.0
 */
function block_engine_cgb_editor_assets()
{
    // Scripts.
    wp_enqueue_script(
        'block_engine-cgb-block-js', // Handle.
        BLOCK_ENGINE_PLUGIN_URI . '/assets/dist/blocks.build.js', // Block.build.js: We register the block here. Built with Webpack.
        array('wp-blocks', 'wp-element', 'wp-components', 'wp-editor', 'wp-i18n'), // Dependencies, defined above.
        BLOCK_ENGINE_VERSION, true  // Version: latest version number.
    );
    wp_localize_script('block_engine-cgb-block-js', 'BlockEngineJSObj', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'baseUrl' => BLOCK_ENGINE_PLUGIN_URI,
        'apiUrl' => Block_Engine_Api_Helper::$api_url,
        'elementorPro' => false,
        'key' => "",
        'host' => $_SERVER['HTTP_HOST'],
        'nonce' => "BlockEngineJSObj",
        'pxStatus' => false,
        'pxKey' => "54c67446-89e5250d-3808bb85-942e870d",
        'pxUrl' => "pixabay/image/"
    ));

    wp_enqueue_script(
        'block_engine-cgb-deactivator-js', // Handle.
        BLOCK_ENGINE_PLUGIN_URI . '/assets/dist/deactivator.build.js', // Block.build.js: We register the block here. Built with Webpack.
        array('wp-editor', 'wp-blocks', 'wp-i18n', 'wp-element'), // Dependencies, defined above.
        BLOCK_ENGINE_VERSION, // Version: latest version number.
        true
    );

    wp_enqueue_style(
        'block_engine-cgb-block-editor-css', // Handle.
        BLOCK_ENGINE_PLUGIN_URI . '/assets/dist/blocks.editor.build.css', // Block editor CSS.
        array('wp-edit-blocks'), // Dependency to include the CSS after it.
        BLOCK_ENGINE_VERSION // Version: latest version number
    );
} // End function block_engine_cgb_editor_assets().

// Hook: Editor assets.
add_action('enqueue_block_editor_assets', 'block_engine_cgb_editor_assets');


/**
 * Rank Math ToC Plugins List.
 */
add_filter('rank_math/researches/toc_plugins', function ($toc_plugins) {
    $toc_plugins['block-engine/block-engine.php'] = 'Block Engine';
    return $toc_plugins;
});

// Click to Tweet Block.
require_once BLOCK_ENGINE_ABSPATH . 'includes/guten/blocks/click-to-tweet/index.php';

// Social Share Block.
require_once BLOCK_ENGINE_ABSPATH . 'includes/guten/blocks/social-share/index.php';

// Content toggle Block.
require_once BLOCK_ENGINE_ABSPATH . 'includes/guten/blocks/content-toggle/index.php';

// Tabbed Content Block.
require_once BLOCK_ENGINE_ABSPATH . 'includes/guten/blocks/tabbed-content/index.php';

// Progress Bar Block.
require_once BLOCK_ENGINE_ABSPATH . 'includes/guten/blocks/progress-bar/index.php';

// Countdown Block
require_once BLOCK_ENGINE_ABSPATH . 'includes/guten/blocks/countdown/index.php';

// Image Slider Block
require_once BLOCK_ENGINE_ABSPATH . 'includes/guten/blocks/image-slider/index.php';

// Table of Contents Block
require_once BLOCK_ENGINE_ABSPATH . 'includes/guten/blocks/table-of-contents/index.php';

// Button Block
require_once BLOCK_ENGINE_ABSPATH . 'includes/guten/blocks/button/index.php';

// Call to Action Block
require_once BLOCK_ENGINE_ABSPATH . 'includes/guten/blocks/call-to-action/index.php';

// Feature Box
require_once BLOCK_ENGINE_ABSPATH . 'includes/guten/blocks/feature-box/index.php';

// Notification Box
require_once BLOCK_ENGINE_ABSPATH . 'includes/guten/blocks/notification-box/index.php';

// Number Box
require_once BLOCK_ENGINE_ABSPATH . 'includes/guten/blocks/number-box/index.php';

// Star Rating
require_once BLOCK_ENGINE_ABSPATH . 'includes/guten/blocks/star-rating/index.php';

// Testimonial
require_once BLOCK_ENGINE_ABSPATH . 'includes/guten/blocks/testimonial/index.php';

// Review
require_once BLOCK_ENGINE_ABSPATH . 'includes/guten/blocks/review/index.php';

// Divider
require_once BLOCK_ENGINE_ABSPATH . 'includes/guten/blocks/divider/index.php';

//Post-Grid
require_once BLOCK_ENGINE_ABSPATH . 'includes/guten/blocks/post-grid/index.php';

//Styled Box
require_once BLOCK_ENGINE_ABSPATH . 'includes/guten/blocks/styled-box/index.php';

//Expand
require_once BLOCK_ENGINE_ABSPATH . 'includes/guten/blocks/expand/index.php';

// Styled List
require_once BLOCK_ENGINE_ABSPATH . 'includes/guten/blocks/styled-list/index.php';

// How To
require_once BLOCK_ENGINE_ABSPATH . 'includes/guten/blocks/how-to/index.php';

// Pricing
require_once BLOCK_ENGINE_ABSPATH . 'includes/guten/blocks/pricing-table/index.php';

