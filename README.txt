=== Kanban Ease Contacts Form ===
Contributors: mantix
Tags: contacts form, crm, contact, contacts, kanban
Requires at least: 5.2
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add contacts to your Kanban Ease companies directly from your WordPress website using customizable contacts forms.

== Description ==

The Kanban Ease Contacts Form plugin allows you to seamlessly integrate your WordPress website with your Kanban Ease account. Create customizable contacts forms that add contacts directly to your Kanban Ease companies.

= Features =
* Easy setup with API token
* Customizable form fields
* GDPR-compliant with required consent
* Responsive design
* Error handling and success messages
* Multiple forms possible with different company IDs

= Usage =

Basic usage:
`[kanban_ease_contact_form]`

Show specific fields:
`[kanban_ease_contact_form show_fields="first_name,email,phone_number"]`

Use a specific website form from your Kanban Ease environment:
`[kanban_ease_contact_form form="1"]

= Available Fields =
* first_name
* last_name
* company_name
* email
* phone_number

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/kanban-ease-contacts-form` directory, or install the plugin through the WordPress plugins screen.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Navigate to 'Kanban Ease' in your admin menu.
4. Add your API token from your Kanban Ease dashboard.
5. Use the shortcode on any page or post.

== Frequently Asked Questions ==

= Where do I find my API token? =

You can generate an API token in your Kanban Ease dashboard. Click "Change your company" and open the tab "API tokens".

= Can I have multiple forms on one page? =

Yes, you can use the shortcode multiple times with different fields.

= Are the forms GDPR compliant? =

Yes, the forms include a required consent checkbox. However, you should ensure your privacy policy is up to date.

== Screenshots ==

1. Contacts form on the frontend
2. Admin settings page
3. Shortcode usage example

== Changelog ==

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.0.0 =
Initial release of the Kanban Ease Contacts Form plugin.

== Support ==

For support, please visit https://kanbanease.com or send us an email at support@kanbanease.com