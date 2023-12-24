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
    add_action('init', array('Custom Form', 'init'));
}

// Enqueue CSS function
function custom_contact_form_enqueue_styles()
{
    wp_enqueue_style('custom-contact-form-styles', plugins_url('style.css', __FILE__));
}


// Settings page
function custom_contact_form_settings()
{

    // Output admin page HTML
    custom_contact_form_shortcode();
}

// ...

function custom_contact_form($firstname, $lastname, $email, $subject, $message)
{
    echo '
    
    <div class="custom-form">
         <h1>' . __('Contact', 'custom-contact-form') . '</h1>
        <form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
            <input type="hidden" name="action" value="my_custom_form_submit">
            <label for="first_name">' . esc_html__('First Name:', 'custom-contact-form') . '</label>
            <input type="text" id="first_name" name="firstname" value="' . esc_attr(isset($_POST['firstname']) ? $firstname : null) . '">
            <br>
            <label for="last_name">' . esc_html__('Last Name:', 'custom-contact-form') . '</label>
            <input type="text" id="last_name" name="lastname" value="' . esc_attr(isset($_POST['lastname']) ? $lastname : null) . '">
            <br>
            <label for="email">' . esc_html__('E-mail:', 'custom-contact-form') . '</label>
            <input type="text" id="email" name="email" value="' . esc_attr(isset($_POST['email']) ? $email : null) . '">
            <br>
            <label for="subject">' . esc_html__('Subject:', 'custom-contact-form') . '</label>
            <input type="text" id="subject" name="subject" value="' . esc_attr(isset($_POST['subject']) ? $subject : null) . '">
            <br>
            <label for="message">' . esc_html__('Message:', 'custom-contact-form') . '</label>
            <textarea id="message" name="message">' . esc_textarea(isset($_POST['message']) ? $message : null) . '</textarea>
            <br>
           <button type="submit" name="submit">' . esc_html__('Submit', 'custom-contact-form') . '</button>
        </form>
    </div>';
}


// INput validation function 
function custom_contact_form_validation($firstname, $lastname, $email, $subject, $message)
{
    $form_errors = new WP_Error;

    if (empty($firstname) || empty($lastname) || empty($email) || empty($subject) || empty($message)) {
        $form_errors->add('field', __('All fields are required', 'custom-contact-form'));
    }
    if (!is_email($email)) {
        $form_errors->add('email', __('Email is not valid', 'custom-contact-form'));
    }

    if (is_wp_error($form_errors)) {
        foreach ($form_errors->get_error_messages() as $error) {
            echo '<div style="background-color: #f44336; color: white; padding: 15px; margin: 10px 0;">';
            echo '<strong>' . $error . '</strong>';
            echo '</div>';
        }
    }

    return $form_errors;
}


function handle_contact_form($firstname, $lastname, $email, $subject, $message)
{
    global $config;

    $hubspot = new HubSpotIntegration($config);
    $responseData = $hubspot->createContact($firstname, $lastname, $email, $message);

    // Check response
    if (isset($responseData['id'])) {
        echo '<div style="background-color: #4CAF50; color: white; padding: 15px; margin: 10px 0;">';
        echo '<strong>Contact Created Successfully!</strong>';
        echo '</div>';


        echo '<div style="background-color: #e6c400; color: white; padding: 15px; margin: 10px 0;">';
        echo '<strong>Sending mail!</strong>';
        echo '</div>';
        $mail = new EmailSender($config);
        $mail_result = $mail->send($email, $subject, $message);
        echo $mail_result;


        if ($mail_result == 1) {
            // Add record to log messages if mail is sent
            $log_message = "Email sent to $email. Subject: $subject, Message: $message";
            error_log($log_message);
            echo '<div style="background-color: #4CAF50; color: white; padding: 15px; margin: 10px 0;">';
            echo '<strong>Mail sent Successfully!</strong>';
            echo '</div>';
        } else {
            $log_message = "Email not sent to $email. Subject: $subject, Message: $message";
            error_log($log_message);
            echo '<div style="background-color: #f44336; color: white; padding: 15px; margin: 10px 0;">';
            echo '<strong>Error sending mail' . $mail_result . '!</strong>';
            echo '</div>';
        }
    } else if (isset($responseData['status']) && $responseData['status'] === 'error') {
        $message = explode('.', $responseData['message']);
        $message = $message[0];

        echo '<div style="background-color: #f44336; color: white; padding: 15px; margin: 10px 0;">';
        echo "<strong>Error creating contact: " . trim($message) . "</strong>";
        echo '</div>';
    } else {
        // Unknown error
        echo '<div style="background-color: #f44336; color: white; padding: 15px; margin: 10px 0;">';
        echo "Error creating HubSpot contact";
        echo '</div>';
    }
}


function custom_contact_form_function()
{
    if (isset($_POST['submit'])) {
        $firstname = sanitize_text_field($_POST['firstname']);
        $lastname = sanitize_text_field($_POST['lastname']);
        $email = sanitize_email($_POST['email']);
        $subject = sanitize_text_field($_POST['subject']);
        $message = sanitize_textarea_field($_POST['message']);


        $form_errors = custom_contact_form_validation($firstname, $lastname, $email, $subject, $message);
        if (!$form_errors->get_error_message()) {
            // Create Contact at hubspot.com (implement HubSpot API integration here)
            handle_contact_form($firstname, $lastname, $email, $subject, $message);
        }
    }
    custom_contact_form($firstname, $lastname, $email, $subject, $message);
}


// Register a new shortcode: [cr_custom_contact_form]
add_shortcode('cr_custom_contact_form', 'custom_contact_form_shortcode');

function custom_contact_form_shortcode()
{
    custom_contact_form_function();
}
