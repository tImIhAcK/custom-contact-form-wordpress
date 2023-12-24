# Custom Contact Form WordPress Plugin

Custom Contact Form is a WordPress plugin that allows you to create a custom form and submit contact information. This plugin also integrates with HubSpot to create contacts when the form is submitted.

## Installation

1. Clone or download this repository.
2. Upload the entire `custom-contact-form` directory to the `wp-content/plugins/` directory of your WordPress installation.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Create a Contact in HubSpot Private App.

## Usage

To use the custom contact form, follow these steps:

1. Go to the WordPress admin dashboard.
2. Navigate to the 'Contact Form' menu.
3. Fill in the required details and submit the form.
4. Upon submission, a contact will be created in HubSpot.

## Configuration

1. Create a `config.ini` file in the plugin directory with the following content:

   ```config.ini
   [api_key]
   HUBSPOT_API_KEY=your_hubspot_api_key
   USERNAME=<youremail@gmail.com>
   PASSWORD=SMTP_PASSWORD
   HOST=<SMTP HOST>
   PORT=PORT
   ```

   Replace `your_hubspot_api_key` with your actual HubSpot API key.</s>
   Replace `<youremail@gmail.com>` with your actual Email.</s>
   Replace `SMTP_PASSWORD` with your password.</s>
   Replace `SMTP HOST` with your SMTP Host</s>
   Replace `PORT` with the port</s>
