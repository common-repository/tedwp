<?php

if ( !function_exists( 'ted_fs' ) ) {
    // Create a helper function for easy SDK access.
    function ted_fs()
    {
        global  $ted_fs ;
        
        if ( !isset( $ted_fs ) ) {
            // Include Freemius SDK.
            $freemius_wordpress_sdk = TED_PATH . "vendor/freemius/wordpress-sdk/start.php";
            if ( !file_exists( $freemius_wordpress_sdk ) ) {
                wp_die( "composer package \"freemius/wordpress-sdk\" was not installed, Do run \"composer update.\"" );
            }
            require_once $freemius_wordpress_sdk;
            // require_once dirname(__FILE__) . '/freemius/start.php';
            $ted_fs = fs_dynamic_init( array(
                'id'             => '8321',
                'slug'           => 'tedwp',
                'type'           => 'plugin',
                'public_key'     => 'pk_7b6f76f540b2b8c616d186b4929fd',
                'premium_suffix' => 'Pro',
                'is_premium'     => false,
                'has_addons'     => false,
                'has_paid_plans' => false,
                'menu'           => array(
                'slug'       => 'ted_editor_settings_page',
                'first-path' => 'admin.php?page=ted_editor_settings_page',
                'support'    => false,
            ),
                'is_live'        => true,
            ) );
        }
        
        return $ted_fs;
    }
    
    // Init Freemius.
    ted_fs();
    // Signal that SDK was initiated.
    do_action( 'ted_fs_loaded' );
}
