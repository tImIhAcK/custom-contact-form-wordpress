<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Custom Contact Form</title>
</head>

<body>
    <div class="custom-form">
        <h1><?php echo __('Contact', 'custom-contact-form'); ?></h1>
        <div id="form-response"></div>
        <form id="custom-contact-form" method="post">
            <fieldset>
                <label for="first_name"><?php echo esc_html__('First Name:', 'custom-contact-form'); ?></label>
                <input type="text" id="first_name" name="firstname" value="<?php echo esc_attr(isset($_POST['firstname']) ? $form_data['firstname'] : ''); ?>">

                <label for="last_name"><?php echo esc_html__('Last Name:', 'custom-contact-form'); ?></label>
                <input type="text" id="last_name" name="lastname" value="<?php echo esc_attr(isset($_POST['lastname']) ? $form_data['lastname'] : ''); ?>">

                <label for="email"><?php echo esc_html__('E-mail:', 'custom-contact-form'); ?></label>
                <input type="text" id="email" name="email" value="<?php echo esc_attr(isset($_POST['email']) ? $form_data['email'] : ''); ?>">
            </fieldset>

            <fieldset>
                <label for="subject"><?php echo esc_html__('Subject:', 'custom-contact-form'); ?></label>
                <input type="text" id="subject" name="subject" value="<?php echo esc_attr(isset($_POST['subject']) ? $form_data['subject'] : ''); ?>">

                <label for="message"><?php echo esc_html__('Message:', 'custom-contact-form'); ?></label>
                <textarea id="message" name="message"><?php echo esc_textarea(isset($_POST['message']) ? $form_data['message'] : ''); ?></textarea>

            </fieldset>

            <input type="hidden" name="action" value="custom_contact_form">
            <?php wp_nonce_field('custom_contact_form_nonce', 'security'); ?>
            <button type="submit" name="submit"><?php echo esc_html__('Submit', 'custom-contact-form'); ?></button>
        </form>
    </div>
</body>

</html>