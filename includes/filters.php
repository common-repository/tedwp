<?php

namespace Ted\Includes;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

if (!class_exists('\Ted\Includes\Filters')) {
    class Filters
    {
        public function __construct()
        {
            error_log("Ted Filters");
            add_filter('ted/get_editor_links', [$this, 'get_editor_links']);

            /** Add the create post link to the archive page title bottom right */
            add_filter('get_the_archive_title', [$this, 'modify_the_archive_title']);

            /** Default post types */
            $post_types = array('post', 'page');
            foreach ($post_types as $post_type) {
                add_filter("{$post_type}_row_actions", [$this, 'modify_row_actions'], 10, 2);
            }
        }

        public function modify_row_actions($actions, $post)
        {
            if (!isset($post) || empty($post)) {
                return $actions;
            }

            if ($post->post_status == 'trash') {
                return $actions;
            }

            $url = admin_url('admin.php?page=ted_editor_page');
            $url = $url . '&post_id=' . $post->ID . '&action=edit&post_type=' . $post->post_type;

            $label = __('Edit with Ted', 'ted');
            $link = '<a href="' . $url . '" aria-label="' . $label . '">' . $label . '</a>';
            $actions['ted-edit'] = $link;
            return $actions;
        }

        public function modify_the_archive_title($title)
        {
            $user_can_create_post = (current_user_can('editor') || current_user_can('administrator'));

            if (!$user_can_create_post) {
                return $title;
            }

            $links = apply_filters("ted/get_editor_links", []);

            if (isset($links['create_link']) && empty($links['create_link'])) {
                return $title;
            }

            $label = __('Create New Post', 'ted');

            $html = '<div class="tedwp-archive">';
            $html .= '<a class="tedwp-archive__title button button-primary" href="' . $links['create_link'] . '" >';
            $html .= '<span class="dashicons dashicons-editor-paste-text tedwp-archive__title--icon"></span>';
            $html .= $label;
            $html .= '</a>';
            $html .= '</div>';

            return $title . $html;
        }

        public function get_editor_links($args = array())
        {
            global $post;

            $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : 'create';
            $post_type = isset($_GET['post_type']) ? sanitize_text_field($_GET['post_type']) : '';

            $post_id = isset($_GET['post_id']) ? $_GET['post_id'] : 0;
            $post_id = (empty($post_id) && isset($post) && !empty($post->ID)) ? $post->ID : $post_id;

            $edit_link = null;

            $post = get_post($post_id);
            if (empty($post_type) && !empty($post)) {
                $post_type = $post->post_type;
            }

            $default_post_id = $this->get_default_post_id();
            $url = admin_url('admin.php?page=ted_editor_page');

            $create_link = $url . '&post_id=' . $default_post_id . '&action=create&post_type=post';

            /** Dont need to show the edit link if we were in non-post pages */
            if (!empty($post)) {
                $edit_link = $url . '&post_id=' . $post_id . '&action=' . $action . '&post_type=' . $post_type;
            }
            return [
                'create_link' => $create_link,
                'edit_link' => $edit_link,
            ];
        }

        public function get_default_post_id()
        {
            global $wpdb;
            $table_name = $wpdb->prefix . 'posts';

            /** Query for get the last record from wp_posts DB table */
            $query = "select * from $table_name order by ID desc limit 1 ";
            $last_record = $wpdb->get_row($query);

            $is_auto_draft_status = (isset($last_record) && $last_record->post_status == 'auto-draft');
            $is_auto_draft_title = (isset($last_record) && $last_record->post_title == 'Auto Draft');

            $should_create_default_post = (!$is_auto_draft_status && !$is_auto_draft_title);

            /**
             *  return the last record post ID if the status is auto-draft and the title is Auto Draft
             *   else create a new post with auto-draft status.
             *
             * */

            if (!$should_create_default_post) {
                return $last_record->ID;
            }

            $default_post_args = array(
                'post_title' => 'Auto Draft',
                'post_type' => 'post',
                'post_content' => '',
                'post_status' => 'auto-draft',
            );

            return wp_insert_post($default_post_args);
        }
    }
}