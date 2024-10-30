<?php

function block_engine_render_call_to_action_block($attributes)
{
    extract($attributes);
    return '<div class="block_engine_call_to_action' . (isset($className) ? ' ' . esc_attr($className) : '') .
        '"' . ($blockID != '' ? ' id="block_engine_call_to_action_' . $blockID . '"' :
            'style="background-color: ' . $ctaBackgroundColor . '; border-width: ' . $ctaBorderSize . 'px; border-color: ' . $ctaBorderColor . '"') . '>
                <div class="block_engine_call_to_action_headline">
                    <' . ($useHeadingTag ? $selectedHeadingTag : 'p') . ' class="block_engine_call_to_action_headline_text"' . ($blockID == '' ?
            ' style="font-size: ' . $headFontSize . 'px; color: ' . $headColor . '; text-align: ' . $headAlign . ';"' : '') . '>' .
        $block_engine_call_to_action_headline_text . '</' . ($useHeadingTag ? $selectedHeadingTag : 'p') . '></div>
                <div class="block_engine_call_to_action_content">
                    <p class="block_engine_cta_content_text"' . ($blockID == '' ?
            ' style="font-size: ' . $contentFontSize . 'px; color: ' . $contentColor . '; text-align: ' . $contentAlign . ';"' : '') . '>' . $block_engine_cta_content_text . '</p></div>
                <div class="block_engine_call_to_action_button">
                    <a href="' . esc_url($url) . '" target="_' . ($openInNewTab ? 'blank' : 'self')
        . '" rel="' . ($addNofollow ? 'nofollow ' : '') . 'noopener noreferrer"
                        class="block_engine_cta_button"' . ($blockID == '' ? ' style="background-color: ' . $buttonColor . '; width: ' . $buttonWidth . 'px;"' : '') . '>
                        <p class="block_engine_cta_button_text"' . ($blockID == '' ? ' style="color: ' .
            $buttonTextColor . '; font-size: ' . $buttonFontSize . 'px;"' : '') . '>' .
        $block_engine_cta_button_text . '</p></a></div></div>';
}

function block_engine_register_call_to_action_block()
{
    if (function_exists('register_block_type')) {
        require dirname(dirname(__DIR__)) . '/defaults.php';
        register_block_type('block-engine/call-to-action', array(
            'attributes' => $defaultValues['block-engine/call-to-action']['attributes'],
            'render_callback' => 'block_engine_render_call_to_action_block'));
    }
}

add_action('init', 'block_engine_register_call_to_action_block');