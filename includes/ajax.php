<?php
add_action('wp_ajax_my_ajax_form','plugin_ajax_action');
function plugin_ajax_action(){
    if(isset($_POST['action']) && isset($_POST['chatbot_id'])){
        update_option('chatbot_id',sanitize_text_field($_POST['chatbot_id']));
    }else{
        echo "failed";
    }
    wp_die();
}