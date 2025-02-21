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
    private $api_key;
    private const ALLOWED_FIELDS = ['first_name', 'last_name', 'company_name', 'email', 'phone_number'];

    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'init_settings']);
        add_shortcode('kanban_ease_contact_form', [$this, 'render_contact_form']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);

        $this->register_rest_route(); // Register the REST route

        $this->api_key = get_option('kanban_ease_contacts_form_api_key');
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
        register_setting(
            'kanban_ease_settings',
            'kanban_ease_contacts_form_api_key',
            'sanitize_text_field',
        );

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
        $value = get_option('kanban_ease_contacts_form_api_key', '');
?>
        <input type='text'
            name='kanban_ease_contacts_form_api_key'
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

            <?php settings_errors(); ?>

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

                <h3><?php esc_html_e('Use a specific website form', 'kanban-ease-contacts-form'); ?></h3>
                <pre style="background: #f5f5f5; padding: 15px; border-radius: 4px;">[kanban_ease_contact_form form="1"]</pre>
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
                            <td><code>company_name</code></td>
                            <td><?php esc_html_e('Company name of the contact', 'kanban-ease-contacts-form'); ?></td>
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

    public function register_rest_route() {
        add_action('rest_api_init', function () {
            register_rest_route('kanban-ease/v1', '/submit-form', [
                'methods' => 'POST',
                'callback' => [$this, 'handle_form_submission'],
                'permission_callback' => '__return_true',
            ]);
        });
    }

    // 2. Handle the form submission and make secure API calls
    public function handle_form_submission($request) {
        // Get parameters from request
        $params = $request->get_params();

        // Check agreement
        if (!filter_var($params['approved'], FILTER_VALIDATE_BOOLEAN)) {
            return new WP_Error('agreement_required', 'You must agree to the data processing', ['status' => 400]);
        }

        // Prepare data for Kanban Ease API
        $form_data = [
            'form_id' => isset($params['form_id']) ? sanitize_text_field($params['form_id']) : null,
            'first_name' => isset($params['first_name']) ? sanitize_text_field($params['first_name']) : null,
            'last_name' => isset($params['last_name']) ? sanitize_text_field($params['last_name']) : null,
            'company_name' => isset($params['company_name']) ? sanitize_text_field($params['company_name']) : null,
            'email' => isset($params['email']) ? sanitize_email($params['email']) : null,
            'phone_number' => isset($params['phone_number']) ? sanitize_text_field($params['phone_number']) : '',
            'approved' => true,
        ];

        // Make secure API call to Kanban Ease
        $response = wp_remote_post('https://my.kanbanease.com/api/v1/contacts', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->api_key,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'body' => json_encode($form_data),
            'timeout' => 30,
        ]);

        // Handle the response
        if (is_wp_error($response)) {
            return new WP_Error('api_error', $response->get_error_message(), ['status' => 500]);
        }

        $status_code = wp_remote_retrieve_response_code($response);
        $body = json_decode(wp_remote_retrieve_body($response), true);

        if ($status_code >= 400) {
            $error_message = isset($body['message']) ? $body['message'] : 'An error occurred with the API request';
            return new WP_Error('api_error', $error_message, ['status' => $status_code]);
        }

        // Success response
        return [
            'success' => true,
            'message' => 'Contact information submitted successfully',
        ];
    }

    public function render_contact_form($atts) {
        if (empty($this->api_key)) {
            return esc_html__('Please configure the Kanban Ease API token in the WordPress admin settings.', 'kanban-ease-contacts-form');
        }

        $atts = shortcode_atts([
            'show_fields' => implode(',', self::ALLOWED_FIELDS),
            'form' => null,
        ], $atts);

        $show_fields = array_intersect(
            explode(',', str_replace(' ', '', $atts['show_fields'])),
            self::ALLOWED_FIELDS
        );

        ob_start();
    ?>
        <form id="kanban-ease-form" class="kanban-ease-form">
            <?php if (isset($atts['form'])): ?>
                <input type="hidden" name="form" value="<?php echo esc_attr($atts['form']); ?>">
            <?php endif; ?>
            <?php if (in_array('first_name', $show_fields) && in_array('last_name', $show_fields)): ?>
                <div class="form-group">
                    <input type="text" name="first_name" id="first_name" class="form-control" placeholder="" required>
                    <label for="first_name"><?php esc_html_e('Your first name', 'kanban-ease-contacts-form'); ?></label>
                </div>
                <div class="form-group">
                    <input type="text" name="last_name" id="last_name" class="form-control" placeholder="" required>
                    <label for="last_name"><?php esc_html_e('Your last name', 'kanban-ease-contacts-form'); ?></label>
                </div>
            <?php else: ?>
                <?php if (in_array('first_name', $show_fields)): ?>
                    <div class="form-group">
                        <input type="text" name="first_name" id="first_name" class="form-control" placeholder="" required>
                        <label for="first_name"><?php esc_html_e('Your name', 'kanban-ease-contacts-form'); ?></label>
                    </div>
                <?php endif; ?>

                <?php if (in_array('last_name', $show_fields)): ?>
                    <div class="form-group">
                        <input type="text" name="last_name" id="last_name" class="form-control" placeholder="" required>
                        <label for="last_name"><?php esc_html_e('Your name', 'kanban-ease-contacts-form'); ?></label>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (in_array('company_name', $show_fields)): ?>
                <div class="form-group">
                    <input type="text" name="company_name" id="company_name" class="form-control" placeholder="" required>
                    <label for="company_name"><?php esc_html_e('Your company name', 'kanban-ease-contacts-form'); ?></label>
                </div>
            <?php endif; ?>

            <?php if (in_array('email', $show_fields)): ?>
                <div class="form-group">
                    <input type="email" name="email" id="email" class="form-control" placeholder="" required>
                    <label for="email"><?php esc_html_e('Your email address', 'kanban-ease-contacts-form'); ?></label>
                </div>
            <?php endif; ?>

            <?php if (in_array('phone_number', $show_fields)): ?>
                <div class="form-group">
                    <input type="tel" name="phone_number" id="phone_number" class="form-control" placeholder="">
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
                ['jquery', 'wp-api'],
                '1.0.0',
                true
            );

            // Only pass required frontend data - No API token! 
            wp_localize_script('kanban-ease-form', 'kanbanEase', array(
                'nonce' => wp_create_nonce('kanban_ease_nonce'),
                'i18n' => [
                    'agree_required' => esc_html__('Please agree to the data processing.', 'kanban-ease-contacts-form'),
                    'sending' => esc_html__('Sending...', 'kanban-ease-contacts-form'),
                    'submit' => esc_html__('Submit', 'kanban-ease-contacts-form'),
                    'success' => esc_html__('Thank you! Your information has been submitted successfully.', 'kanban-ease-contacts-form'),
                    'error' => esc_html__('An error occurred. Please try again later.', 'kanban-ease-contacts-form')
                ]
            ));

            // Add the WordPress API settings for REST API authentication
            wp_localize_script('kanban-ease-form', 'wpApiSettings', array(
                'root' => esc_url_raw(rest_url()),
                'nonce' => wp_create_nonce('wp_rest')
            ));
        }
    }
}
