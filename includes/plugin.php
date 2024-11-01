<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Register a custom menu page.
 */
function wpdocs_register_my_custom_menu_page()
{
    add_submenu_page(
        null,
        __('Ted - Text Editor', 'textdomain'),
        'Ted Editor',
        'manage_options',
        'ted_editor_page',
        'ted_editor_page'
        //plugins_url('myplugin/images/icon.png'),
    );

    add_menu_page(
        __('Ted - Text Editor', 'textdomain'),
        'Ted Editor',
        'manage_options',
        'ted_editor_settings_page',
        'ted_editor_settings_page'
        //plugins_url('myplugin/images/icon.png'),
    );
}

function ted_editor_settings_page()
{
    $post_id = isset($_GET['post_id']) ? sanitize_text_field($_GET['post_id']) : 0;
    $html = "To use Ted Editor, go to post / page -> Add / New -> Click 'Ted Editor' button on the top";
    echo $html;
}

/**
 * Display a custom menu page
 */
function ted_editor_page()
{
    $post_id = isset($_GET['post_id']) ? sanitize_text_field($_GET['post_id']) : 0;
    $editor = new \Ted\Includes\Editor();
    echo $editor->get_view($post_id);
}

if (!class_exists('\Ted')) {
    class Ted
    {
        public function __construct()
        {
            // error_log('class Ted');
            $this->setup_autoload();
            $this->admin_init_hook();
            add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
            /*  Tablesome Init Hook */
            add_action('init', array($this, 'init_hook'));
            // $this->register_cpt_and_taxonomy();
            // $this->load_update_handler();
            $this->load_actions();
            $this->load_filters();
            // $this->load_shorcodes();
            require_once TED_PATH . "/includes/integrations/freemius-integrator.php";
        }

        protected function setup_autoload()
        {
            require_once TED_PATH . '/includes/autoloader.php';
            \Ted\Autoloader::run();
        }

        // Belows are callback functions of adding Actions order wise
        public function init_hook()
        {
            /*  Tablesome Table-Actions Ajax Hooks */
            new \Ted\Includes\Ajax_Handler();
        }

        public function admin_init_hook()
        {
            add_action('admin_menu', 'wpdocs_register_my_custom_menu_page');
        }

        public function admin_enqueue_scripts()
        {
            $nonce = wp_create_nonce('ted_nonce');
            wp_enqueue_style(TED_DOMAIN . '-admin-bundle', TED_URL . 'assets/bundles/admin.bundle.css', [], TED_VERSION, 'all');
            wp_enqueue_script(TED_DOMAIN . '-admin-bundle', TED_URL . 'assets/bundles/admin.bundle.js', ['jquery'], TED_VERSION, false);

            /* Load Post Data to Editor */
            wp_localize_script(TED_DOMAIN . '-admin-bundle', 'ted_ajax_object', array(
                'nonce' => $nonce,
                'ajax_url' => admin_url('admin-ajax.php'),
            ));

            $js_vars = [
                'endpoint' => esc_url_raw(rest_url('/wp/v2/media/')),
                'nonce' => wp_create_nonce('wp_rest'),
                'pluginURL' => TED_URL,
                'dirURL' => TED_PATH,
            ];
            wp_localize_script(TED_DOMAIN . '-admin-bundle', 'RestVars', $js_vars);

            $post_id = isset($_GET['post_id']) ? sanitize_text_field($_GET['post_id']) : 0;
            $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : 'create';
            $post_type = isset($_GET['post_type']) ? sanitize_text_field($_GET['post_type']) : 'post';

            $post = get_post($post_id, ARRAY_A); // Get post as array instead of object

            wp_localize_script(TED_DOMAIN . '-admin-bundle', 'ted_post', [
                'post' => $post,
                'post_id' => $post_id,
                'post_type' => $post_type,
                'action' => $action,

            ]);
        }

        /*  Register Ted Post types and its Taxonomies */
        public function register_cpt_and_taxonomy()
        {
            $cpt = new \Ted\Includes\Cpt();
            $cpt->register();
        }

        public function load_actions()
        {
            new \Ted\Includes\Actions();
        }

        public function load_filters()
        {
            new \Ted\Includes\Filters();
        }

        public function load_update_handler()
        {
            $upgrade = new \Ted\Includes\Update\Upgrade();
            $upgrade::init();
        }

        /*  Ted Shortcode */
        public function load_shorcodes()
        {
            new \Ted\Includes\Shortcodes();
        }
    }

    new Ted();
}