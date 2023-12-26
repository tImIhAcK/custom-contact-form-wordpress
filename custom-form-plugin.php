<?php
/*
Plugin Name: Custom Contact Form
Description: This plugin allows you to create a Contact on Hubspot and Send a Mail.
Version: 1.0
Author: Adeniran John
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
    add_action('wp_enqueue_scripts', 'custom_contact_form_enqueue_scripts'); // Enqueue scripts in the front-end
    add_action('init', array('Custom Form', 'init'));
}

// Enqueue CSS function
function custom_contact_form_enqueue_styles()
{
    wp_enqueue_style('custom-contact-form-styles', plugins_url('style.css', __FILE__));
}

function custom_contact_form_enqueue_scripts()
{
    wp_enqueue_script('jquery');
    wp_enqueue_script('custom-contact-form-scripts', plugins_url('scripts.js', __FILE__), array('jquery'), null, true);
    wp_localize_script('custom-contact-form-scripts', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}

// Settings page
function custom_contact_form_settings()
{
    include_once __DIR__ . "/views/custom-form.php";
    custom_contact_form_enqueue_scripts();
    custom_contact_form_shortcode();
}

// Input validation function
function custom_contact_form_validation($form_data)
{
    $form_errors = new WP_Error;

    if (empty($form_data['firstname']) || empty($form_data['lastname']) || empty($form_data['email']) || empty($form_data['subject']) || empty($form_data['message'])) {
        $form_errors->add('field', __('All fields are required', 'custom-contact-form'));
    }
    if (!is_email($form_data['email'])) {
        $form_errors->add('email', __('Email is not valid', 'custom-contact-form'));
    }

    return $form_errors->get_error_messages();
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
        $mail = new EmailSender($config);
        $mail_result = $mail->send(
            $form_data['email'],
            $form_data['subject'],
            $form_data['message']
        );

        if ($mail_result === true) {
            error_log("Email sent to {$form_data['email']}. Subject: {$form_data['subject']}, Message: {$form_data['message']}");
            return array("data" => array(
                "status" => true,
                "class" => "success",
                "message" => "Contact created successfully and email sent!"
            ));
        } else {
            error_log("Email not sent to {$form_data['email']}. Subject: {$form_data['subject']}, Message: {$form_data['message']}");
            return array("data" => array(
                "status" => false,
                "class" => "info",
                "message" => "Contact created successfully, but email failed to send."
            ));
        }
    } elseif (isset($responseData['status']) && $responseData['status'] === 'error') {
        $message = explode('.', $responseData['message']);
        $message = $message[0];

        return array("data" => array(
            "status" => false,
            "class" => "danger",
            "message" => "Failed to create contact in HubSpot." . trim($message)
        ));
    } else {
        return array("data" => array(
            "status" => false,
            "class" => "danger",
            "message" => "Error creating contact in HubSpot."
        ));
    }
}

function custom_contact_form_function()
{

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $form_data = [
            'firstname' => sanitize_text_field($_POST['firstname']),
            'lastname' => sanitize_text_field($_POST['lastname']),
            'email' => sanitize_email($_POST['email']),
            'subject' => sanitize_text_field($_POST['subject']),
            'message' => sanitize_text_field($_POST['message']),
        ];

        $form_errors = custom_contact_form_validation($form_data);

        if (!$form_errors) {
            header('Content-Type: application/json');
            $response = handle_contact_form($form_data);
            echo json_encode($response);
            exit;
        } else {
            header('Content-Type: application/json');
            echo json_encode(array("data" => array("status" => false, "class" => "danger", "message" => $form_errors)));
            exit;
        }
    }
}


add_action('wp_ajax_custom_contact_form', 'custom_contact_form_function');
add_action('wp_ajax_nopriv_custom_contact_form', 'custom_contact_form_function');



// Register a new shortcode: [cr_custom_contact_form]
add_shortcode('cr_custom_contact_form', 'custom_contact_form_shortcode');

function custom_contact_form_shortcode()
{
    custom_contact_form_function();
}
