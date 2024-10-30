<?php

/**
 * Enqueue frontend script for content toggle block
 *
 * @return void
 */

function block_engine_render_image_slider_block($attributes)
{
    extract($attributes);

    $imageArray = count($pics) > 0 ? $pics : json_decode($images, true);
    $captionArray = count($descriptions) > 0 ? $descriptions : json_decode($captions, true);
    $gallery = '';
    $isDraggable = isset($isDraggable) ? (boolean)$isDraggable : false;
    $showPageDots = isset($showPageDots) ? (boolean)$showPageDots : false;
    $wrapsAround = isset($wrapsAround) ? (boolean)$wrapsAround : false;
    $autoplays = isset($autoplays) ? (boolean)$autoplays : false;

    foreach ($imageArray as $key => $image) {
        $gallery .= '<div>
        <img src="' . $image['url'] . '"' . ($blockID == '' ? ' style="height: ' . $sliderHeight . 'px;"' : '') . '>' .
            ($captionArray[$key]['link'] == '' ? '<span' : '<a href="' . esc_url($captionArray[$key]['link']) . '"')
            . ' class="block_engine_image_slider_image_caption">' . $captionArray[$key]['text']
            . ($captionArray[$key]['link'] == '' ? '</span>' : '</a>') . ' </div>';
    }

    return '<div class="block_engine_image_slider' . (isset($className) ? ' ' . esc_attr($className) : '') .
        '" ' . ($blockID == '' ? 'style="min-height: ' . (25 + (count($imageArray) > 0) ? $sliderHeight : 200) . 'px;"'
            : 'id="block_engine_image_slider_' . $blockID . '"') . '>
                <!---->' .
        ($gallery == '' ? '' : '<div data-flickity=' . json_encode(array('draggable' => $isDraggable, 'pageDots' => $showPageDots,
                'wrapAround' => $wrapsAround, 'autoPlay' => ($autoplays ? $autoplayDuration * 1000 : $autoplays),
                'adaptiveHeight' => true)) . '>' . $gallery
            . '</div>') . '</div>';
}

function block_engine_register_image_slider_block()
{
    if (function_exists('register_block_type')) {
        require dirname(dirname(__DIR__)) . '/defaults.php';
        $args = array(
            'render_callback' => 'block_engine_render_image_slider_block'
        );
        register_block_type('block-engine/image-slider', $args);
    }
}

function block_engine_image_slider_add_frontend_assets()
{
    if (has_block('block-engine/image-slider')) {
        wp_enqueue_script(
            'block_engine-image-slider-init-script',
            BLOCK_ENGINE_PLUGIN_URI . '/includes/guten/blocks/image-slider/js/front.build.js',
            array(),
            BLOCK_ENGINE_VERSION,
            true
        );
        wp_enqueue_script(
            'block_engine-image-slider-front-script',
            BLOCK_ENGINE_PLUGIN_URI . '/includes/guten/blocks/image-slider/js/flickity.pkgd.js',
            array(),
            BLOCK_ENGINE_VERSION
        );
    }
}

add_action('init', 'block_engine_register_image_slider_block');
add_action('wp_enqueue_scripts', 'block_engine_image_slider_add_frontend_assets');