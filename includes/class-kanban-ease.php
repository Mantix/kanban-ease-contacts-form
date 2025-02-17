<?php

/**
 * Plugin Name: Kanban Ease Contacts Form
 * Description: Adds contacts or tickets to your Kanban Ease companies via customizable forms
 * Version: 1.0
 * Author: Kanban Ease BV (https://kanbanease.com)
 */

// Prevent direct access
defined('ABSPATH') || exit;

class KanbanEaseContactsForm {
    private $options;

    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'init_settings']);
        add_shortcode('kanban_ease_contact_form', [$this, 'render_contact_form']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);

        $this->options = get_option('kanban_ease_settings');
    }

    public function add_admin_menu() {
        add_options_page(
            esc_html__('Kanban Ease Settings', 'kanban-ease-contacts-form'),
            esc_html__('Kanban Ease', 'kanban-ease-contacts-form'),
            'manage_options',
            'kanban-ease-contacts-form',
            [$this, 'render_admin_page']
        );
    }

    public function init_settings() {
        register_setting('kanban_ease_settings', 'kanban_ease_settings');

        add_settings_section(
            'kanban_ease_settings_section',
            esc_html__('API Configuration', 'kanban-ease-contacts-form'),
            null,
            'kanban-ease-contacts-form'
        );

        add_settings_field(
            'api_token',
            esc_html__('API Token', 'kanban-ease-contacts-form'),
            [$this, 'render_api_token_field'],
            'kanban-ease-contacts-form',
            'kanban_ease_settings_section'
        );
    }

    public function render_api_token_field() {
        $value = isset($this->options['api_token']) ? $this->options['api_token'] : '';
?>
        <input type='text'
            name='kanban_ease_settings[api_token]'
            value='<?php echo esc_attr($value); ?>'
            class='regular-text'>
        <p class="description">
            <?php esc_html_e('Enter your Kanban Ease API Token. You can generate this in your Kanban Ease dashboard. Click "Change your company" and open the tab "API tokens".', 'kanban-ease-contacts-form'); ?>
        </p>
    <?php
    }

    public function render_admin_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
    ?>
        <div class="wrap">
            <h1>
                <?php echo esc_html(get_admin_page_title()); ?>
            </h1>

            <div class="notice notice-info">
                <p>
                    <?php echo wp_kses_post(__('Need help? Visit our <a href="https://docs.kanbanease.com" target="_blank">documentation</a> or <a href="https://kanbanease.com/contact" target="_blank">contact support</a>.', 'kanban-ease-contacts-form')); ?>
                </p>
            </div>

            <form method="post" action="options.php">
                <?php
                settings_fields('kanban_ease_settings');
                do_settings_sections('kanban-ease-contacts-form');
                submit_button();
                ?>
            </form>

            <hr>

            <h2><?php esc_html_e('Shortcode Usage', 'kanban-ease-contacts-form'); ?></h2>

            <p><?php esc_html_e('Add the contacts form to any page or post using the shortcode. You can customize which fields are displayed.', 'kanban-ease-contacts-form'); ?></p>

            <div class="card" style="max-width: 800px; margin-top: 20px;">
                <h3><?php esc_html_e('Basic Usage', 'kanban-ease-contacts-form'); ?></h3>
                <pre style="background: #f5f5f5; padding: 15px; border-radius: 4px;">[kanban_ease_contact_form]</pre>

                <h3><?php esc_html_e('Customize Fields', 'kanban-ease-contacts-form'); ?></h3>
                <pre style="background: #f5f5f5; padding: 15px; border-radius: 4px;">[kanban_ease_contact_form show_fields="first_name,last_name,email,phone_number"]</pre>
            </div>

            <div class="card" style="max-width: 800px; margin-top: 20px;">
                <h3><?php esc_html_e('Available Fields', 'kanban-ease-contacts-form'); ?></h3>
                <table class="widefat striped">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Field', 'kanban-ease-contacts-form'); ?></th>
                            <th><?php esc_html_e('Description', 'kanban-ease-contacts-form'); ?></th>
                            <th><?php esc_html_e('Required', 'kanban-ease-contacts-form'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>first_name</code></td>
                            <td><?php esc_html_e('First name of the contact', 'kanban-ease-contacts-form'); ?></td>
                            <td><?php esc_html_e('Optional', 'kanban-ease-contacts-form'); ?></td>
                        </tr>
                        <tr>
                            <td><code>last_name</code></td>
                            <td><?php esc_html_e('Last name of the contact', 'kanban-ease-contacts-form'); ?></td>
                            <td><?php esc_html_e('Optional', 'kanban-ease-contacts-form'); ?></td>
                        </tr>
                        <tr>
                            <td><code>email</code></td>
                            <td><?php esc_html_e('Email address', 'kanban-ease-contacts-form'); ?></td>
                            <td><?php esc_html_e('Optional', 'kanban-ease-contacts-form'); ?></td>
                        </tr>
                        <tr>
                            <td><code>phone_number</code></td>
                            <td><?php esc_html_e('Phone number', 'kanban-ease-contacts-form'); ?></td>
                            <td><?php esc_html_e('Optional', 'kanban-ease-contacts-form'); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    <?php
    }

    public function render_contact_form($atts) {
        $atts = shortcode_atts([
            'show_fields' => 'first_name,last_name,email,phone_number',
        ], $atts);

        $show_fields = explode(',', str_replace(' ', '', $atts['show_fields']));

        ob_start();
    ?>
        <form id="kanban-ease-form" class="kanban-ease-form">
            <?php if (in_array('first_name', $show_fields) && in_array('last_name', $show_fields)): ?>
                <div class="form-group">
                    <input type="text" name="first_name" id="first_name" class="form-control" placeholder=" " required>
                    <label for="first_name"><?php esc_html_e('Your first name', 'kanban-ease-contacts-form'); ?></label>
                </div>
                <div class="form-group">
                    <input type="text" name="last_name" id="last_name" class="form-control" placeholder=" " required>
                    <label for="last_name"><?php esc_html_e('Your last name', 'kanban-ease-contacts-form'); ?></label>
                </div>
            <?php else: ?>
                <?php if (in_array('first_name', $show_fields)): ?>
                    <div class="form-group">
                        <input type="text" name="first_name" id="first_name" class="form-control" placeholder=" " required>
                        <label for="first_name"><?php esc_html_e('Your name', 'kanban-ease-contacts-form'); ?></label>
                    </div>
                <?php endif; ?>

                <?php if (in_array('last_name', $show_fields)): ?>
                    <div class="form-group">
                        <input type="text" name="last_name" id="last_name" class="form-control" placeholder=" " required>
                        <label for="last_name"><?php esc_html_e('Your name', 'kanban-ease-contacts-form'); ?></label>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (in_array('email', $show_fields)): ?>
                <div class="form-group">
                    <input type="email" name="email" id="email" class="form-control" placeholder=" " required>
                    <label for="email"><?php esc_html_e('Your email address', 'kanban-ease-contacts-form'); ?></label>
                </div>
            <?php endif; ?>

            <?php if (in_array('phone_number', $show_fields)): ?>
                <div class="form-group">
                    <input type="tel" name="phone_number" id="phone_number" class="form-control" placeholder=" ">
                    <label for="phone_number"><?php esc_html_e('Your phone number', 'kanban-ease-contacts-form'); ?></label>
                </div>
            <?php endif; ?>

            <div class="form-check">
                <input type="checkbox" name="agreement" id="agreement" required>
                <label for="agreement">
                    <?php esc_html_e('I agree that my data will be processed by', 'kanban-ease-contacts-form'); ?>
                    <?php echo esc_html(get_bloginfo('name')); ?>
                </label>
            </div>

            <div class="form-submit">
                <button type="submit"><?php esc_html_e('Submit', 'kanban-ease-contacts-form'); ?></button>
            </div>

            <div id="form-messages"></div>
        </form>
<?php
        return ob_get_clean();
    }

    public function enqueue_scripts() {
        if (!wp_script_is('kanban-ease-form', 'enqueued')) {
            wp_enqueue_style(
                'kanban-ease-form',
                plugins_url('../css/style.css', __FILE__),
                [],
                '1.0.0'
            );

            wp_enqueue_script(
                'kanban-ease-form',
                plugins_url('../js/script.js', __FILE__),
                ['jquery'],
                '1.0.0',
                true
            );

            wp_localize_script('kanban-ease-form', 'kanbanEase', [
                'api_url' => 'https://my.kanbanease.com/api/v1',
                'api_token' => $this->options['api_token'] ?? '',
                'nonce' => wp_create_nonce('kanban_ease_nonce'),
                'i18n' => [
                    'agree_required' => esc_html__('Please agree to the data processing.', 'kanban-ease-contacts-form'),
                    'sending' => esc_html__('Sending...', 'kanban-ease-contacts-form'),
                    'submit' => esc_html__('Submit', 'kanban-ease-contacts-form'),
                    'success' => esc_html__('Thank you! Your information has been submitted successfully.', 'kanban-ease-contacts-form'),
                    'error' => esc_html__('An error occurred. Please try again later.', 'kanban-ease-contacts-form')
                ]
            ]);
        }
    }
}
