<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <div class="custom-form">
        <h1><?php echo __('Contact', 'custom-contact-form'); ?></h1>
        <div id="form-response"></div>
        <form action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post">
            <input type="hidden" name="action" value="my_custom_form_submit">
            <label for="first_name"><?php echo esc_html__('First Name:', 'custom-contact-form'); ?></label>
            <input type="text" id="first_name" name="firstname"
                value="<?php echo esc_attr(isset($_POST['firstname']) ? $form_data['firstname'] : ''); ?>">
            <br>
            <label for="last_name"><?php echo esc_html__('Last Name:', 'custom-contact-form'); ?></label>
            <input type="text" id="last_name" name="lastname"
                value="<?php echo esc_attr(isset($_POST['lastname']) ? $form_data['lastname'] : ''); ?>">
            <br>
            <label for="email"><?php echo esc_html__('E-mail:', 'custom-contact-form'); ?></label>
            <input type="text" id="email" name="email"
                value="<?php echo esc_attr(isset($_POST['email']) ? $form_data['email'] : ''); ?>">
            <br>
            <label for="subject"><?php echo esc_html__('Subject:', 'custom-contact-form'); ?></label>
            <input type="text" id="subject" name="subject"
                value="<?php echo esc_attr(isset($_POST['subject']) ? $form_data['subject'] : ''); ?>">
            <br>
            <label for="message"><?php echo esc_html__('Message:', 'custom-contact-form'); ?></label>
            <textarea id="message"
                name="message"><?php echo esc_textarea(isset($_POST['message']) ? $form_data['message'] : ''); ?></textarea>
            <br>
            <button type="submit" name="submit"><?php echo esc_html__('Submit', 'custom-contact-form'); ?></button>
        </form>
    </div>
</body>

</html>