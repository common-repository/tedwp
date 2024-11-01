<?php

/*
Plugin Name: TedWP
Plugin URI: http://tedwp.com/
Description: Awesome WordPress Editor plugin
Author: Pauple
Version: 0.0.5
Author URI: http://pauple.com
Network: True
Text Domain: tedwp
Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (function_exists('ted_fs')) {
    ted_fs()->set_basename(true, __FILE__);
} else {
    if (!class_exists('Ted_Plugin')) {
        class Ted_Plugin
        {
            private static $instance;
            public static function get_instance()
            {
                if (!isset(self::$instance) && !self::$instance instanceof Ted_Plugin) {
                    self::$instance = new Ted_Plugin();
                }
                return self::$instance;
            }

            private function __construct()
            {
                $this->setup_constants();
                $this->ted_activation();
                // require_once plugin_dir_path(__FILE__) . "/includes/lib/freemius-integrator.php";
            }

            public function setup_constants()
            {
                $constants = [
                    'TED_VERSION' => '0.0.5',
                    'TED_DOMAIN' => 'tedwp',
                    'TED_CPT' => 'ted_cpt',
                    'TED__FILE__' => __FILE__,
                    'TED_PLUGIN_BASE' => plugin_basename(__FILE__),
                    'TED_PATH' => plugin_dir_path(__FILE__),
                    'TED_URL' => plugins_url('/', __FILE__),

                    /** Storing Settings Options in Database tables feilds using CS_Framework*/
                    'TED_OPTIONS' => 'ted_options',
                    'TED_CUSTOMIZE_OPTIONS' => 'ted_customize_options',
                ];

                foreach ($constants as $constant => $value) {

                    if (!defined($constant)) {
                        define($constant, $value);
                    }
                }
            }

            public function ted_activation()
            {
                if (!version_compare(PHP_VERSION, '5.4', '>=')) {
                    add_action('admin_notices', [$this, 'ted_fail_php_version']);
                } elseif (!version_compare(get_bloginfo('version'), '4.5', '>=')) {
                    add_action('admin_notices', [$this, 'ted_fail_wp_version']);
                } else {
                    require plugin_dir_path(__FILE__) . 'includes/plugin.php';
                }
            }

            /**
             * Show in WP Dashboard notice about the plugin is not activated (PHP version).
             * @since 1.0.0
             * @return void
             */
            public function ted_fail_php_version()
            {
                /* translators: %s: PHP version */
                $message = sprintf(esc_html__('TED requires PHP version %s+, plugin is currently NOT ACTIVE.', 'tedwp'), '5.4');
                $html_message = sprintf('<div class="error">%s</div>', wpautop($message));
                echo wp_kses_post($html_message);
            }

            /**
             * Show in WP Dashboard notice about the plugin is not activated (WP version).
             * @since 1.5.0
             * @return void
             */
            public function ted_fail_wp_version()
            {
                /* translators: %s: WP version */
                $message = sprintf(esc_html__('TED requires WordPress version %s+. Because you are using an earlier version, the plugin is currently NOT ACTIVE.', 'tedwp'), '4.5');
                $html_message = sprintf('<div class="error">%s</div>', wpautop($message));
                echo wp_kses_post($html_message);
            }
        }
    }

    Ted_Plugin::get_instance();
}