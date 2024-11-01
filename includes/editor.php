<?php

namespace Ted\Includes;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

if (!class_exists('\Ted\Includes\Editor')) {
    class Editor
    {
        public function get_view($post_id)
        {
            $html = "";
            $post = get_post($post_id); // Get post as array instead of object
            // error_log('$post : ' . print_r($post, true));

            $title = isset($post->post_title) ? $post->post_title : '';

            $placeholder = __('Add title', 'ted');

            $html .= '<div class="ted-editor__container">';
            $html .= '<div class="ted-editor__navbar">';
            $html .= '<div class="ted-editor__navbar__item">';
            $html .= '<div class="ted__spinner"><div class="ted__loader"></div></div>';
            // $html .= '<div class="ted-editor__navbar__item--icons ted-editor__navbar__item--draft">';
            // $html .= '<span class="dashicons dashicons-backup"></span>';
            // $html .= '</div>';
            $html .= '<div class="ted-editor__navbar__item--icons ted-editor__navbar__item--publish">';
            $html .= '<span class="dashicons dashicons-controls-play"></span>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '<input type="text" class="ted-title" value="' . $title . '"  placeholder="' . $placeholder . '"/>';
            $html .= '<div id="editorjs"></div>';
            // $html .= '<div class="svelte-editorjs" placeholder="Enter markdown here"></div>';
            $args = $this->get_params_from_url();
            $html .= $this->get_form_button_controls($post, $args);

            $html .= '</div>';

            return $html;
        }

        public function get_params_from_url()
        {
            $post_id = isset($_GET['post']) ? sanitize_text_field($_GET['post']) : 0;
            $post_action = empty($post_id) ? 'add' : 'edit';

            return [
                'post_id' => $post_id,
                'post_action' => $post_action,
            ];
        }

        public function get_form_button_controls($post, $args)
        {
            // $disabled = (!isset($post->post_title) || empty($post->post_title)) ? 'disabled="disabled"' : '';

            $button_label = 'Save Post';
            if ($args['post_action'] == 'edit') {
                $button_label = 'Update Table';
            }

            $html = '';
            $html .= '<div class="ted-editor__footer">';
            $html .= '<div class="ted-editor__footer__button">';
            $html .= '<input type="button" class="button button-primary button-large ted__button--submit" value="' . $button_label . '">';
            $html .= '</div>';
            $html .= '<div class="ted__spinner"><div class="ted__loader"></div></div>';
            $html .= '</div>';
            return $html;
        }
    }
}