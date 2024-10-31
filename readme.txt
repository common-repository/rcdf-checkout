=== RCDF Checkout page ===
Tags: checkout page, checkout, record data, abandoned cart
Requires at least: 6.6
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 1.6
Version: 1.6
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

RCDF Checkout Page Plugin records customer data from checkout page on event

== Description ==

This plugin records the buyer's data from the сheckout page on event. Even if the buyer has not clicked the confirm order button, the data will be saved. And the seller can contact the buyer and make an offer that cannot be refused.

== Getting Started ==

After installation, you need to go to the plugin settings (RCDF Checkout -> Settings). Then fill in all the fields with the necessary selectors.
This is a list of fields whose data must be saved. And also the event after which you need to save the data. By default, data recording occurs after the blur event on the "phone" field. You can view the recorded data on the main page (List of data) of the plugin in the corresponding table.
After filling out, do not forget to save the changes.
Test the plugin. Make a test purchase without clicking the "confirm order" button. 
If the data recording did not occur, then you need to go to the plugin settings (Settings tab) and check the selectors that are written there. Selectors should be from your site to the Checkout pages. Enter your selectors in the required fields.
If you can't do something, you can write to me in telegram. If possible, I will answer as soon as possible. Don't be shy about it. It is important for me for further work on the plugin.

== Dependencies ==

- Wordpress 6.6 
- Woocommerce 8.9

== Installing ==

- Install in the usual way as a WordPress plugin.
- That's all !

== Necessary skills ==

To customize the plugin for yourself, you only need to select the desired selectors. You can read more about selectors [Type, class, and ID selectors](https://developer.mozilla.org/en-US/docs/Learn/CSS/Building_blocks/Selectors/Type_Class_and_ID_Selectors)


= To customize the plugin, you need to do the following:: =

- In the admin menu, go to the plugin settings - RCDF Checkout -> Settings;
- In the "Selectors of elements for tracking" section, select the appropriate selectors;
- In the "Select the appropriate selector and event" section, select the element and event for which data will be recorded. There are three options: blur, focus, click;
- Don't forget to save your changes.

== Authors ==

Plugin made by Artem Litvinov, my telegram:
[@artem_litvinov_8](https://t.me/artem_litvinov_8)

[Github](https://github.com/nevredimiy/plugin-wp-abandoned)

== Version History ==
= 1.6 =
* Added unique name for options
= 1.5 =
* Added shot description section to readme file
* Security: Use  wp_unslash() function to input data
= 1.4 =
* Security: Sanitizie data to prevent accidentally sending trash data
* Changed the name of the plugin to be more unique
* Added styles for mobile version
= 1.3 =
* Security: Added data cleaning functions
= 1.2 =
* Added folder language. It has become possible to translate into any language
= 1.1 =
* Added the ability to select elements and triggers from the admin menu
= 1.0 =
* Initial Release