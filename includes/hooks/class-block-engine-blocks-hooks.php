<?php
/**
 * Block_Engine_Blocks_Hooks setup
 *
 * @package Block_Engine_Blocks_Hooks
 * @since   1.0.0
 */

/**
 * Main Block_Engine_Blocks_Hooks Class.
 *
 * @class Block_Engine_Blocks_Hooks
 */
class Block_Engine_Blocks_Hooks
{


    public function __construct()
    {
        add_filter('block_categories', array($this, 'block_categories'));

    }

    public function block_categories($categories)
    {

        return array_merge(
            $categories,
            array(
                array(
                    'slug' => 'blockengine',
                    'title' => __('Block Engine', 'block-engine'),
                ),
            )
        );
    }


}

new Block_Engine_Blocks_Hooks();