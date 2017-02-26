=== Simple Linked Variations for WooCommerce ===
Contributors: dingo_bastard
Tags: shop, attributes, variations, variable product, woocommerce
Requires at least: 4.1
Tested up to: 4.7.2
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

An add-on plugin for WooCommerce which allows variations to be linked together, and will then toggle drop downs on the front end based on the links made

== Description ==

If you want to hide some variation drop downs from the front end in your shop this is the plugin for you.

Create a variable product, set your variations and link them logically and see how the variation select appears or disappears on the front end.
This plugin works with WooCommerce, so be sure to have WooCoommerce installed before you install this plugin.
After that create variable product with variations, save it and then set your links in the 'Linked attributes' tab in the 'Product Data' meta box.

** Created using the best coding practices and latest standards **

== Installation ==

= Install & Activate =

Installing the plugin is easy. Just follow these steps:

**Installing from WordPress repository:**

Be sure you have WooCommerce plugin installed first, otherwise you'll get an error on the plugin activation.

1. From the dashboard of your site, navigate to Plugins --> Add New.
2. In the Search type Simple Linked Variations for WooCommerce
3. Click Install Now
4. When it's finished, activate the plugin via the prompt. A message will show confirming activation was successful.

**Uploading the .zip file:**

1. From the dashboard of your site, navigate to Plugins --> Add New.
2. Select the Upload option and hit "Choose File."
3. When the popup appears select the simple-linked-variations-for-woocommerce.x.x.zip file from your desktop. (The 'x.x' will change depending on the current version number).
4. Follow the on-screen instructions and wait as the upload completes.
5. When it's finished, activate the plugin via the prompt. A message will show confirming activation was successful.

That's it!

= Requirements =

* PHP 5.4 or greater (recommended: PHP 7 or greater)
* WordPress 4.1 or above
* jQuery 1.11.x

== Screenshots ==

1. First set your product as a variable one. You won't have any attributes so nothing will appear in the 'Linked attributes' tab. Save your product.
2. Then set up some variations. Notice that if you want to hide a certain attribute dropdown, that you'll have to add a 'none' value as a default.
3. After setting some variations we are ready to link them. Again, be sure to set the value for the attribute you don't want to appear to none, because WooCommerce won't let you have a non existing value for a variation.
4. Now you can link attributes to the values you want them to appear on. In this case we are linking 'Frame-color' attribute to the 'Frame-dimensions' values that have frames.
5. After saving your product check on the front end if you have hidden a select field. Notice that we don't have a frame color select drop down.
6. We can now select the option without the frame.
7. Or we can select the option with the frame - notice that the frame color is now here. Just like we needed.

== Changelog ==

= 1.0.0 =
* Initial Release
