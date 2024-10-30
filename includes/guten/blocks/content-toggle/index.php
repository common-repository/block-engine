<?php

/**
 * Enqueue frontend script for content toggle block
 *
 * @return void
 */
function block_engine_content_toggle_add_frontend_assets() {
    require_once dirname(dirname(__DIR__)) . '/common.php';

    $presentBlocks = block_engine_get_present_blocks();

    foreach( $presentBlocks as $block ){
        if($block['blockName'] == 'block-engine/content-toggle' || $block['blockName'] == 'block-engine/content-toggle-panel'
            || $block['blockName'] == 'block-engine/content-accordion' || $block['blockName'] == 'block-engine/content-accordion-panel'){
                wp_enqueue_script(
                    'block_engine-content-toggle-front-script',
                    BLOCK_ENGINE_PLUGIN_URI. '/includes/guten/blocks/content-toggle/js/front.build.js',
                    array(  ),
                    BLOCK_ENGINE_VERSION,
                    true
                );
                //Enable Dashicon for logged-out users
                if( !wp_style_is('dashicons', 'enqueued')){
                    wp_enqueue_style( 'dashicons' );
                }
                break;
            }
        }
}

if ( !class_exists( 'simple_html_dom_node' ) ) {
    require dirname( dirname( __DIR__ ) ) . '/simple_html_dom.php';
}

function block_engine_render_content_toggle_block($attributes, $content){
    extract($attributes);

    return '<div class="wp-block-block-engine-content-toggle'.(isset($className) ? ' ' . esc_attr($className) : '')
                .'" '. ($blockID == '' ? '' : 'id="block-engine-content-toggle-'.$blockID.'"') .
                 ($preventCollapse ? ' data-preventcollapse="true"' : '') . '>'
                . $content.'</div>';
}

function block_engine_render_content_toggle_panel_block($attributes, $content){
    $classNamePrefix = 'wp-block-block-engine-content-toggle';
    extract($attributes);
    $border_class = $border ? " ":"no-border ";
    $icon_class = $toggleIcon;

    return '<div class="' . $border_class . $classNamePrefix.'-accordion'.(isset($className) ? ' ' . esc_attr($className) : '').'"'
                .($parentID == '' ? ' style="border-color: '.$theme.';"' : '').'>
                <div class="'.$classNamePrefix.'-accordion-title-wrap"'
                    .($parentID == '' ? ' style="background-color: '.$theme.';"' : '').'>
                    <'.$titleTag.' class="'.$classNamePrefix.'-accordion-title"'
                    .($parentID == '' ? ' style="color:'.$titleColor.';"' : '').'>'.$panelTitle.'</'.$titleTag.'>
                    <div class="' . $classNamePrefix. '-accordion-toggle-wrap ' . esc_attr($toggleLocation) . '" style="color:'. esc_attr($toggleColor) .'">
                    <span class="' . $classNamePrefix .
           '-accordion-state-indicator '. $icon_class  .
           ( $collapsed ? '' : ' open' ) . '"></span>
                    </div>
                </div><div class="'.$classNamePrefix.'-accordion-content-wrap'.
                        ($collapsed?' block-engine-hide':'').'">'. $content
                .'</div></div>' ;
}

function block_engine_register_content_toggle_panel_block() {
	if ( function_exists( 'register_block_type' ) ) {
        require dirname(dirname(__DIR__)) . '/defaults.php';
		register_block_type( 'block-engine/content-accordion-panel', array(
            'attributes' => $defaultValues['block-engine/content-accordion-panel']['attributes'],
			'render_callback' => 'block_engine_render_content_toggle_panel_block'));
	}
}

function block_engine_register_content_toggle_block() {
	if ( function_exists( 'register_block_type' ) ) {
        require dirname(dirname(__DIR__)) . '/defaults.php';
        register_block_type( 'block-engine/content-accordion',
            array('attributes' => $defaultValues['block-engine/content-accordion']['attributes'],
             'render_callback' => 'block_engine_render_content_toggle_block'));
	}
}

add_action('init', 'block_engine_register_content_toggle_block');

add_action('init', 'block_engine_register_content_toggle_panel_block');

add_action( 'wp_enqueue_scripts', 'block_engine_content_toggle_add_frontend_assets' );

add_filter( 'render_block', 'block_engine_content_toggle_filter', 10, 3);

function block_engine_faq_questions($qna = ''){
    static $parsed_qna;

    if(!isset($qna)){
        $parsed_qna = '';
    }

    if(empty($qna)){
        return $parsed_qna;
    }
    else{
        if($parsed_qna != ''){
            $parsed_qna .= ',' . PHP_EOL;
        }
        $parsed_qna .= $qna;
        return true;
    }
}

function block_engine_content_toggle_filter( $block_content, $block ) {

    if( "block-engine/content-accordion" != $block['blockName'] ) {
        return $block_content;
    }

    $output = $block_content;

    if(isset($block['attrs']['hasFAQSchema'])){
        $parsedBlockContent = str_get_html(preg_replace('/^<div class="wp-block-block-engine-content-toggle(?: [^>]*)?" id="block-engine-content-toggle-.*?">/',
        '<div class="toggleroot">', $block_content));

        $panel = $parsedBlockContent->find('.toggleroot>.wp-block-block-engine-content-toggle-accordion>.wp-block-block-engine-content-toggle-accordion-content-wrap');

        foreach($panel as $elem){
            //look for possible nested content toggles and remove existing ones
            foreach($elem->find('.wp-block-block-engine-content-toggle') as $nestedToggle){
                $nestedToggle->outertext='';
            }
            foreach($elem->find('script[type="application/ld+json"]') as $nestedToggle){
                $nestedToggle->outertext='';
            }
        }

        $panel = array_map(function($elem){
            return $elem->innertext;
        }, $panel);

        $questions = "";

        foreach($block['innerBlocks'] as $key => $togglePanel){
            if(array_key_exists($key, $panel)){
                $answer = preg_replace_callback('/<([a-z1-6]+)[^>]*?>[^<]*?<\/(\1)>/i', function($matches){
                    return (in_array($matches[1], ['script', 'svg', 'iframe', 'applet', 'map',
                        'audio', 'button', 'table', 'datalist', 'form', 'frameset',
                        'select', 'optgroup', 'picture', 'style', 'video']) ? '' : $matches[0]);
                }, $panel[$key]);

                $answer = preg_replace_callback('/<\/?([a-z1-6]+).*?\/?>/i', function($matches){
                    if(in_array($matches[1], ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'a', 'br', 'ol',
                                        'ul', 'li', 'p', 'div', 'b', 'strong', 'i', 'em', 'u', 'del'])){
                        return $matches[0];
                    }
                    else{
                        $replacement = '';
                        if ($matches[1] == 'ins'){
                            $replacement = 'u';
                        }
                        elseif ($matches[1] == 'big') {
                            $replacement = 'strong';
                        }
                        elseif ($matches[1] == 'q'){
                            $replacement = 'p';
                        }
                        elseif ($matches[1] == 'dir'){
                            $replacement = 'ul';
                        }
                        elseif ($matches[1] == 'address' || $matches[1] == 'cite'){
                            $replacement = 'em';
                        }
                        elseif (in_array($matches[1], ['article', 'aside', 'blockquote', 'details', 'dialog', 'figure',
                                                'figcaption', 'footer', 'header', 'nav', 'pre', 'section', 'textarea'])){
                            $replacement = 'div';
                        }

                        return ($replacement == '' ? '' : str_replace($matches[1], $replacement, $matches[0]));
                    }
                }, $answer);

                while(preg_match_all('/<([a-z1-6]+)[^>]*?><\/(\1)>/i', $answer) > 0){ //remove empty tags and tags that only contain empty tags
                    $answer = preg_replace('/<([a-z1-6]+)[^>]*?><\/(\1)>/i', '', $answer);
                }

                //check all attributes

                $answer = preg_replace_callback('/<[a-z1-6]+( (?:(?:aria|data)-[^\t\n\f \/>"\'=]+|[a-z]+)=[\'"][\s\S]+?[\'"])>/i',
                    function($matches){
                        $attributeList = preg_replace_callback('/ ([\S]+)=([\'"])([\s\S]*?)(\2)/', function($matches){
                            return $matches[1] == 'href' ? (" href='" . $matches[3] . "'"): '';
                        }, $matches[1]);
                        return str_replace($matches[1], $attributeList, $matches[0]);
                }, $answer);

                if($answer != "" && $togglePanel['attrs']['panelTitle'] != ''){ //blank answers and questions are invalid
                    if($questions != ""){
                        $questions .= ',' . PHP_EOL;
                    }
                    $questions .= '{
                        "@type": "Question",
                        "name": "'.$togglePanel['attrs']['panelTitle'].'",
                        "acceptedAnswer": {
                            "@type": "Answer",
                            "text": "'.trim(str_replace('"', '\"', $answer)).'"
                        }
                    }';
                }


            }

        }
        block_engine_faq_questions($questions);
    }

  return $output;
}

function block_engine_merge_faqpages(){
    ?><?php echo '<script type="application/ld+json">{
            "@context":"http://schema.org/",
            "@type":"FAQPage",
            "mainEntity": [' . block_engine_faq_questions() . ']}</script>';  ?>
<?php
}

add_action('wp_footer', 'block_engine_merge_faqpages', 20);
