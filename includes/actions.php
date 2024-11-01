<?php

namespace Ted\Includes;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

if (!class_exists('\Ted\Includes\Actions')) {
    class Actions
    {
        public function __construct()
        {
            error_log("Ted Actions");
            add_action('admin_bar_menu', array($this, 'action_admin_bar_menu'), 999);

            add_action('admin_footer', [$this, 'print_admin_js_template']);
            add_action('media_buttons', [$this, 'add_editor_buttons']);

            /*  Enqueing Script Action hook */
            add_action('wp_enqueue_scripts', array($this, 'frontend_enqueue_scripts'));

        }

        public function add_editor_buttons()
        {
            $html = '';
            $html .= '<span class="ted-switch-mode">';
            $html .= $this->get_editor_button();
            $html .= ' </span>';
            echo $html;
        }

        public function print_admin_js_template()
        {
            $html = "";

            $html .= '<script id="ted-gutenberg-button-switch-mode" type="text/html">';
            $html .= '<div class="ted-switch-mode">';
            $html .= $this->get_editor_button();
            $html .= ' </div>';
            $html .= ' </script>';

            echo $html;
        }

        public function get_editor_button($args = array())
        {

            $label = __('Edit with Ted', 'tedwp');
            $mobile_label = __('Ted', 'tedwp');
            $links = apply_filters('ted/get_editor_links', []);

            $button_html = '<a class="button button-primary button-large ted-editor__button" href="' . $links['edit_link'] . '">';
            $button_html .= '<span class="ted-editor__button-wrapper">';
            $button_html .= '<span class="dashicons dashicons-editor-paste-text ted-editor__button--icon"></span>';
            $button_html .= '<span class="ted-editor__button--label ted-editor__button--edit-label">' . $label . '</span>';
            $button_html .= '<span class="ted-editor__button--label ted-editor__button--mobile-label">' . $mobile_label . '</span>';
            $button_html .= '</span>';
            $button_html .= '</a>';
            return $button_html;
        }

        public function action_admin_bar_menu($wp_admin_bar)
        {
            $links = apply_filters('ted/get_editor_links', []);
            $create_link_title_label = __('Create New Post with Ted', 'tedwp');
            $edit_link_title_label = __('Edit Post with Ted', 'tedwp');

            $icon_tag = '<span style="margin-top:2px !important;" class="ab-item dashicons dashicons-editor-paste-text ted-editor__icon"></span>';

            $args = array();
            $args[0] = array(
                'id' => 'ted-posts-create',
                'title' => $icon_tag . $create_link_title_label,
                'href' => $links['create_link'],
                'meta' => false,
            );

            /** Don't need to show edit links if they saw the non-post edit pages. */
            if (!empty($links['edit_link'])) {
                $args[1] = array(
                    'id' => 'ted-posts-edit',
                    'title' => $icon_tag . $edit_link_title_label,
                    'href' => $links['edit_link'],
                    'meta' => false,
                );
            }

            foreach ($args as $admin_bar_args) {
                $wp_admin_bar->add_node($admin_bar_args);
            }

        }

        public function frontend_enqueue_scripts()
        {

            $nonce = wp_create_nonce('ted_nonce');
            wp_enqueue_style(TED_DOMAIN . '-bundle', TED_URL . 'assets/bundles/public.bundle.css', [], TED_VERSION, 'all');
            wp_enqueue_script(TED_DOMAIN . '-bundle', TED_URL . 'assets/bundles/public.bundle.js', ['jquery'], TED_VERSION, false);

            /* Load Post Data to Editor */
            wp_localize_script(TED_DOMAIN . '-admin-bundle', 'ted_ajax_object', array(
                'nonce' => $nonce,
                'ajax_url' => admin_url('admin-ajax.php'),
            ));

        }
    }
}