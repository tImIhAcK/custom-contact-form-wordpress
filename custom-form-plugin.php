<?php
/*
Plugin Name: Custom Contact Form
Description: This plugin allows you to create a custom form.
Version: 1.0
Author Name: Adeniran John
Author Email: adeniranjohn2016@gmail.com
*/

// Make sure we don't expose any info if called directly
if (!function_exists('add_action')) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

$config = parse_ini_file(__DIR__ . "/config.ini", true);

require __DIR__ . '/email-sender.php';
require __DIR__ . '/hubspot-integration.php';


// Add admin menu page
add_action('admin_menu', 'custom_contact_form_menu');

function custom_contact_form_menu()
{
    add_menu_page(
        __('Contact Form', 'custom-contact-form'),
        __('Contact Form', 'custom-contact-form'),
        'manage_options',
        'custom-contact-form',
        'custom_contact_form_settings',
        'dashicons-forms'
    );

    add_action('admin_enqueue_scripts', 'custom_contact_form_enqueue_styles');
    // add_action('wp_enqueue_scripts', 'custom_contact_form_enqueue_scripts');
    add_action('init', array('Custom Form', 'init'));

    echo '<input type="hidden" id="custom_contact_form_nonce" name="custom_contact_form_nonce" value="' . wp_create_nonce('custom_contact_form_nonce') . '" />';
}

// Enqueue CSS function
function custom_contact_form_enqueue_styles()
{
    wp_enqueue_style('custom-contact-form-styles', plugins_url('style.css', __FILE__));
}

// function custom_contact_form_enqueue_scripts()
// {
//     wp_enqueue_script('jquery');
//     wp_enqueue_script('custom-contact-form-scripts', plugins_url('scripts.js', __FILE__), array('jquery'), null, true);
// }



// Settings page
function custom_contact_form_settings()
{
    custom_contact_form_shortcode();
}

// ...

function custom_contact_form($form_data)
{
    include_once __DIR__ . "/views/custom-form.php";
}

// INput validation function 
function custom_contact_form_validation($form_data)
{
    $form_errors = new WP_Error;

    if (empty($form_data['firstname']) || empty($form_data['lastname']) || empty($form_data['email']) || empty($form_data['subject']) || empty($form_data['message'])) {
        $form_errors->add('field', __('All fields are required', 'custom-contact-form'));
    }
    if (!is_email($form_data['email'])) {
        $form_errors->add('email', __('Email is not valid', 'custom-contact-form'));
    }

    if (is_wp_error($form_errors)) {
        foreach ($form_errors->get_error_messages() as $error) {
            echo '<div id="status-message" style="background-color: #f44336; color: white; padding: 15px; margin: 10px 0;">';
            echo '<strong>' . $error . '</strong>';
            echo '</div>';

            timeout_message();
        }
    }

    return $form_errors;
}


function handle_contact_form($form_data)
{
    global $config;

    $hubspot = new HubSpotIntegration($config);
    $responseData = $hubspot->createContact(
        $form_data['firstname'],
        $form_data['lastname'],
        $form_data['email'],
        $form_data['message']
    );


    // Check response
    if (isset($responseData['id'])) {
        echo '<div id="status-message" style="background-color: #4CAF50; color: white; padding: 15px; margin: 10px 0;">';
        echo '<strong>Contact Created Successfully!</strong>';
        echo '</div>';


        echo '<div id="status-message" style="background-color: #e6c400; color: white; padding: 15px; margin: 10px 0;">';
        echo '<strong>Sending mail!</strong>';
        echo '</div>';

        $mail = new EmailSender($config);
        $mail_result = $mail->send(
            $form_data['email'],
            $form_data['subject'],
            $form_data['message']
        );

        if ($mail_result === true) {
            // Add record to log messages if mail is sent
            $log_message = "Email sent to {$form_data['email']}. Subject: {$form_data['subject']}, Message: {$form_data['message']}";
            error_log($log_message);
            echo '<div id="status-message" style="background-color: #4CAF50; color: white; padding: 15px; margin: 10px 0;">';
            echo '<strong>Mail sent Successfully!</strong>';
            echo '</div>';

            timeout_message();
        } else {
            $log_message = "Email not sent to {$form_data['email']}. Subject: {$form_data['subject']}, Message: {$form_data['message']}";
            error_log($log_message);
            echo '<div id="status-message" style="background-color: #f44336; color: white; padding: 15px; margin: 10px 0;">';
            echo '<strong>' . $mail_result . '!</strong>';
            echo '</div>';

            timeout_message();
        }
    } else if (isset($responseData['status']) && $responseData['status'] === 'error') {
        $message = explode('.', $responseData['message']);
        $message = $message[0];

        echo '<div id="status-message" style="background-color: #f44336; color: white; padding: 15px; margin: 10px 0;">';
        echo "<strong>Error creating contact: " . trim($message) . "</strong>";
        echo '</div>';

        timeout_message();
    } else {
        // Unknown error
        echo '<div id="status-message" style="background-color: #f44336; color: white; padding: 15px; margin: 10px 0;">';
        echo "Error creating HubSpot contact";
        echo '</div>';
        timeout_message();
    }
}

function timeout_message()
{
    echo '<script>
    setTimeout(function(){
        elements = document.getElementById("status-message");
            elements.style.display = "none";

    }, 5000); // Adjust the delay in milliseconds (e.g., 5000ms for 5 seconds)
</script>';
}


function custom_contact_form_function()
{
    if (isset($_POST['submit'])) {
        $form_data = [
            'firstname' => sanitize_text_field($_POST['firstname']),
            'lastname' => sanitize_text_field($_POST['lastname']),
            'email' => sanitize_email($_POST['email']),
            'subject' => sanitize_text_field($_POST['subject']),
            'message' => sanitize_text_field($_POST['message']),
        ];
        $form_errors = custom_contact_form_validation($form_data);
        if (!$form_errors->get_error_message()) {
            handle_contact_form($form_data);
        }
    }

    custom_contact_form($form_data);
}


// Register a new shortcode: [cr_custom_contact_form]
add_shortcode('cr_custom_contact_form', 'custom_contact_form_shortcode');

function custom_contact_form_shortcode()
{
    custom_contact_form_function();
}
