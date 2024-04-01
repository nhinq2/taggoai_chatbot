<?php
/*
 * Plugin Name:       TaggoAI Chatbot
 * Plugin URI:        https://wordpress.org/plugins/taggoai-chatbot/
 * Description:       Chatbot for your WordPress Website. AI Chatbot for Customer Support!
 * Version:           1.0.3
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            TaggoAI Team
 * Author URI:        https://taggoai.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       taggoai_chatbot
 */
if (!defined('ABSPATH')) {
    header("Location: /wordpress");
    die("");
}
define('PLUGIN_PATH', plugin_dir_path(__FILE__));
include PLUGIN_PATH . "includes/ajax.php";
add_action("wp_enqueue_scripts", "my_js_script");
add_action("admin_enqueue_scripts", "my_admin_js_script");
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'add_action_links' );

function add_action_links ( $actions ) {
    $mylinks = array(
    '<a href="' . admin_url( 'options-general.php?page=taggoai_chatbot' ) . '">Settings</a>',
    );
    $actions = array_merge( $actions, $mylinks );
    return $actions;
}
function my_js_script()
{
    wp_enqueue_script("jquery");
    wp_enqueue_style("test_css", plugin_dir_url(__FILE__) . "assets/css/style.css");
    wp_enqueue_script("test_js", plugin_dir_url(__FILE__) . "assets/js/custom.js", array(), '1.0.1', false);
    wp_localize_script(
        'test_js',
        'getOption',
        array(
            'chatbot_id' => get_option("chatbot_id")
        )
    );
}
;
function my_admin_js_script()
{
    wp_enqueue_script("test_js", plugin_dir_url(__FILE__) . "assets/js/custom.js", array(), '1.1.8', false);
    wp_enqueue_style("test_css", plugin_dir_url(__FILE__) . "assets/css/style.css", array(), '1.2.5', false);
    wp_localize_script(
        'test_js',
        'getOption',
        array(
            'chatbot_id' => get_option("chatbot_id")
        )
    );
}
function add_html_script_to_admin()
{
    echo '<div data-taggo-botid="' . esc_js(get_option("chatbot_id")) . '"></div>
    <script async src="https://widget.taggo.chat/script.js"></script>
    ';
}

add_action('admin_enqueue_scripts', 'add_html_script_to_admin');
add_action("wp_enqueue_scripts", "add_html_script_to_admin");
function my_openai_update_notice()
{
    global $pagenow;
    if (!esc_html(get_option("chatbot_id")) && $pagenow === 'plugins.php') {
        ?>
        <div class="add-apiKey">
            <h2 class="title">Please setup your chatbot</h2>
            <div>
                <a class="add-api-btn" href="options-general.php?page=taggoai_chatbot">Setup now</a>
            </div>
        </div>
        <?php
    }
}
add_action('admin_notices', 'my_openai_update_notice');

add_action('admin_menu', 'plugin_menu');
add_action('admin_menu', 'plugin_menu_process');
function plugin_menu()
{

    add_submenu_page('options-general.php', 'TaggoAI Chatbot', 'TaggoAI Chatbot', 'manage_options', 'taggoai_chatbot', 'plugin_menu_option_func');
}
;

function plugin_menu_process()
{
    register_setting('plugin_option_group', 'plugin_option_name');
    if (isset($_POST['action']) && current_user_can('manage_options')) {
        update_option('chatbot_id', sanitize_text_field($_POST['chatbot_id']));
    }
    ;
}
;

function plugin_menu_option_func()
{ ?>
    <?php settings_errors(); ?>
    <div class="outer">
        <div class="animated-background"></div>
        <div class="container">
            <h2 class="heading">TaggoAI Chatbot Settings</h2>
            <form id="ajax_form" action="options.php" method="post">
                <?php settings_fields('plugin_option_group'); ?>
                <div class="input-group">
                    <label for="chatbot_id" class="label">Enter your Chatbot ID:</label>
                    <input type="text" id="chatbot_id" class="input-text" placeholder="Chatbot ID" name="chatbot_id" value= "<?php echo esc_html(get_option("chatbot_id")) ?>">
                </div>
                <input type="submit" name="submit" id="submit" class="chatbotbtn" value="Save Changes">
            </form>
            <br />
            <div style="display: flex; justify-content: space-between;">
				<a href='https://taggoai.com/en/help/chatbot' target="_blank" class="help">How to setup</a>
			</div>
          
        </div>
    </div>
    <?php
}

function my_plugin_activation()
{
    if (!esc_html(get_option("chatbot_id"))) {
        delete_option('chatbot_id');
    }
    add_option('chatbot_id');
}

register_activation_hook(__FILE__, 'my_plugin_activation');

function my_plugin_deactivation()
{
    delete_option('chatbot_id');
}

register_deactivation_hook(__FILE__, 'my_plugin_deactivation');