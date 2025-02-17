<?php

/**
 * Plugin Name: Kanban Ease Contacts Form
 * Plugin URI: https://my.kanbanease.com
 * Description: Integration with Kanban Ease to collect contact information through customizable forms
 * Version: 1.0.0
 * Requires at least: 5.2
 * Requires PHP: 7.4
 * Author: Kanban Ease
 * Author URI: https://kanbanease.com
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: kanban-ease-contacts-form
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('KANBAN_EASE_CONTACTS_FORM_VERSION', '1.0.0');
define('KANBAN_EASE_CONTACTS_FORM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('KANBAN_EASE_CONTACTS_FORM_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include the main plugin class
require_once KANBAN_EASE_CONTACTS_FORM_PLUGIN_DIR . 'includes/class-kanban-ease.php';

// Activation hook
function activate_kanban_ease() {
    // Future activation code if needed
}

// Deactivation hook
function deactivate_kanban_ease() {
    // Future deactivation code if needed
}

add_action('plugins_loaded', function () {
    load_plugin_textdomain('kanban-ease-contacts-form', false, dirname(plugin_basename(__FILE__)) . '/languages');
});

register_activation_hook(__FILE__, 'activate_kanban_ease');
register_deactivation_hook(__FILE__, 'deactivate_kanban_ease');

// Initialize the plugin
function run_kanban_ease() {
    $plugin = new KanbanEaseContactsForm();
}
run_kanban_ease();
