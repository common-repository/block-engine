<?php

function block_engine_render_table_of_contents_block($attributes){
    extract($attributes);
    $linkArray = json_decode($links, true);

    $filteredHeaders = array_values(array_filter($linkArray, function ($header) use ($allowedHeaders){
        return $allowedHeaders[$header['level'] - 1];
    }));

    $sortedHeaders = [];

    foreach($filteredHeaders as $elem){
        $elem['content'] = trim(preg_replace('/(<.+?>)/', '', $elem['content']));
        $last = count($sortedHeaders) - 1;
        if (count($sortedHeaders) == 0 || $sortedHeaders[$last][0]['level'] < $elem['level']) {
            array_push($sortedHeaders, [$elem]);
        }
        else if ($sortedHeaders[$last][0]['level'] == $elem['level']){
            array_push($sortedHeaders[$last], $elem);
        }
        else{
            while($sortedHeaders[$last][0]['level'] > $elem['level'] && count($sortedHeaders) > 1){
                array_push($sortedHeaders[count($sortedHeaders) - 2], array_pop($sortedHeaders));
                $last = count($sortedHeaders) - 1;
            }
            if($sortedHeaders[$last][0]['level'] == $elem['level']){
                array_push($sortedHeaders[$last], $elem);
            }
        }
    }

    if(count($sortedHeaders) > 0){
        while(count($sortedHeaders) > 1 &&
            $sortedHeaders[count($sortedHeaders) - 1][0]['level'] > $sortedHeaders[count($sortedHeaders) - 2][0]['level']){
            array_push($sortedHeaders[count($sortedHeaders) - 2], array_pop($sortedHeaders));
        }
        $sortedHeaders = $sortedHeaders[0];
    }

    $listItems = '';

    if (!function_exists('block_engine_makeListItem')) {
        function block_engine_makeListItem($num, $item, $listStyle, $blockID, $gaps){
            static $outputString = '';
            if($num == 0 && $outputString != ''){
                $outputString = '';
            }
            if (array_key_exists("level", $item)){
                $anchor = '#' . $item["anchor"];

                if(count($gaps) && get_query_var('page') != $gaps[$num]){
                    $baseURL = get_permalink();
                    $anchor = $baseURL . ($gaps[$num] > 1 ? (get_post_status(get_the_ID()) == 'publish' ? '' : '&page=')
                            . $gaps[$num] : '') . $anchor;
                }

                $content = $item["content"];
                $outputString .= '<li><a href='. $anchor.'>'. $content .'</a></li>';
            }
            else{
                $openingTag = $listStyle == 'numbered' ? '<ol>' :
                    '<ul'.($listStyle == 'plain' && $blockID == '' ? ' style="list-style: none;"' : '').'>';

                $outputString = substr_replace($outputString, $openingTag,
                    strrpos($outputString, '</li>'), strlen('</li>'));

                forEach($item as $key => $subItem){
                    block_engine_makeListItem($key+1, $subItem, $listStyle, $blockID, $gaps);
                }
                $outputString .= ($listStyle == 'numbered' ? '</ol>' : '</ul>') . '</li>';
            }
            return $outputString;
        }
    }

    if(count($sortedHeaders) > 0){
        foreach($sortedHeaders as $key => $item){
            $listItems = block_engine_makeListItem($key, $item, $listStyle, $blockID, $gaps);
        }
    }
    
    return '<div class="block_engine_table-of-contents'.(isset($className) ? ' ' . esc_attr($className) : '')
                .(!$showList && strlen($title) > 0 ? ' block_engine_table-of-contents-collapsed' : '' ).
                '" data-showtext="'.__('show').'" data-hidetext="'.__('hide')
                .'"'.($blockID==''?'':' id="block_engine_table-of-contents-'.$blockID.'"').'>'.
                (strlen($title) > 0 ? ('<div class="block_engine_table-of-contents-header">
                    <div class="block_engine_table-of-contents-title">'.
                        $title .'</div>'. 
                    ($allowToCHiding ?
                    '<div id="block_engine_table-of-contents-header-toggle">
                        <div id="block_engine_table-of-contents-toggle">
                        &nbsp;[<a class="block_engine_table-of-contents-toggle-link" href="#">'.
                            __($showList ? 'hide' : 'show')
                            .'</a>]</div></div>' :'')
                .'</div>') : '')
                .'<div class="block_engine_table-of-contents-container block_engine_table-of-contents-' .
                    $numColumns. '-column ' . ($showList || strlen($title) == 0 ||
                    (strlen($title) == 1 && $title[0] == '') ? '' : 'block-engine-hide').'">'.
                ($listStyle == 'numbered' ? '<ol>' :  '<ul'.($listStyle == 'plain' && $blockID == '' ? ' style="list-style: none;"' : '').'>')
                . $listItems .
                ($listStyle == 'numbered' ? '</ol>' : '</ul>')
                .'</div></div>';
}

function block_engine_register_table_of_contents_block() {
	if( function_exists( 'register_block_type' ) ) {
        require dirname(dirname(__DIR__)) . '/defaults.php';
		register_block_type( 'block-engine/table-of-contents', array(
            'attributes' => $defaultValues['block-engine/table-of-contents']['attributes'],
            'render_callback' => 'block_engine_render_table_of_contents_block'));
    }
}

function block_engine_table_of_contents_add_frontend_assets() {
    require_once dirname(dirname(__DIR__)) . '/common.php';

    $presentBlocks = block_engine_get_present_blocks();

    foreach( $presentBlocks as $block ){
        if($block['blockName'] == 'block-engine/table-of-contents' || $block['blockName'] == 'block-engine/table-of-contents-block'){
            wp_enqueue_script(
                'block_engine-table-of-contents-front-script',
                BLOCK_ENGINE_PLUGIN_URI. '/includes/guten/blocks/table-of-contents/js/front.build.js',
                array( ),
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
        }
    }
}

add_action('init', 'block_engine_register_table_of_contents_block');
add_action( 'wp_enqueue_scripts', 'block_engine_table_of_contents_add_frontend_assets' );