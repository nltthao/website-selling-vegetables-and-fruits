<?php

if (!defined("ABSPATH")) {
    exit; // Exit if accessed directly.
}
    class WpdiscuzElementorIntegration extends Elementor\Widget_Base
    {

        public function get_name()
        {
            return "wpDiscuz";
        }

        public function get_title() {
            return esc_html__( "Comments – wpDiscuz", "wpdiscuz" );
        }

        public function get_icon() {
            return 'eicon-comments';
        }

        public function get_custom_help_url() {
            return 'https://wpdiscuz.com/docs/';
        }

        public function get_categories() {
            return [ 'general' ];
        }

        public function get_keywords() {
            return [ 'comment', 'comments', 'wpdiscuz' ];
        }

        protected function render() {
            global $post;
            $form = wpDiscuz()->wpdiscuzForm->getForm($post->ID);
            $form->initFormFields();
            if (apply_filters("is_load_wpdiscuz", $form->getFormID() && ( comments_open($post) || $post->comment_count ) && is_singular() && post_type_supports($post->post_type, "comments"), $post)) {
                    include ABSPATH . "wp-content/plugins/wpdiscuz/themes/default/comment-form.php";
            }else{
                add_filter('deprecated_file_trigger_error', '__return_false');
                comments_template();
                remove_filter('deprecated_file_trigger_error', '__return_false');
            }
        }
    }
