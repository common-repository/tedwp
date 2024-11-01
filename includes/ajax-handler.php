<?php

namespace Ted\Includes;

if (!class_exists('\Ted\Includes\Ajax_Handler')) {
    class Ajax_Handler
    {
        public function __construct()
        {
            // error_log('ajax_handler : ');
            add_action('wp_ajax_save_post', array($this, 'save_post'));
            add_action('wp_ajax_nopriv_save_post', array($this, 'save_post'));
        }


        public function save_post()
        {
            // error_log('save_post...');
            $props = [
                'post_title' => '',
                'post_content' => '',
                'post_status' => 'publish'
            ];
            if (isset($_REQUEST['post_title']) && !empty($_REQUEST['post_title'])) {
                $props['post_title'] = sanitize_text_field($_REQUEST['post_title']);
            }

            if (isset($_REQUEST['post_id']) && !empty($_REQUEST['post_id'])) {
                $props['ID'] = sanitize_text_field(wp_unslash($_REQUEST['post_id']));
            }

            if (isset($_REQUEST['post_content']) && !empty($_REQUEST['post_content'])) {
                $props['post_content'] = wp_filter_post_kses(wp_kses_post($_REQUEST['post_content']));
            }

            if (isset($_REQUEST['action_type']) && !empty($_REQUEST['post_content'])) {
                $props['action_type'] = sanitize_text_field(wp_unslash($_REQUEST['action_type']));
            }

            if ($_REQUEST['post_type'] == 'create' && isset($_REQUEST['post_type'])) {
                $props['post_type'] = sanitize_text_field(wp_unslash($_REQUEST['post_type']));
            }

            $saved_post_id = wp_update_post($props);

            $reponse = array(
                'saved_post_id' => $saved_post_id,
                'type' => 'UPDATED',
                'edit_page_url' => $this->get_ted_edit_url($saved_post_id),
            );

            // error_log('$props : ' . print_r($props, true));
            // error_log('$saved_post_id : ' . $saved_post_id);

            wp_send_json($reponse);
            wp_die();
        }

        public function get_ted_edit_url($post_id)
        {
            $url = admin_url('/admin.php?page=ted_editor_page&post_id=' . $post_id . '');
            return $url;
        }
    }
}
