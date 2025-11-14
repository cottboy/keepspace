=== KeepSpace ===
Contributors: cottboy
Tags: space, whitespace, writing, indent, formatting
Requires at least: 5.0
Tested up to: 6.8
Stable tag: 1.0.5
Requires PHP: 7.4
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Automatically convert regular spaces into special character spaces to prevent them from being omitted.

== Description ==

WordPress by default omits consecutive spaces and leading/trailing spaces. KeepSpace can automatically convert regular spaces to special character spaces to prevent them from being omitted.

**Main functions:**

* Automatically convert regular spaces into special-character spaces
* Supports four independent toggles for title, excerpt, content, and comments
* Provides three types of special space characters to choose from

**Three types of special space characters:**

1. **Non-breaking space (\u00A0)** - Recommended
   * Same visual effect as &nbsp;
   * Counts as only 1 character and does not affect excerpt truncation

2. **Chinese full-width space (\u3000)**
   * In a Chinese environment, it's very natural
   * The width is exactly one Chinese character

3. **HTML Entity Space (&nbsp;)**
   * Best compatibility, supported by all browsers
   * Counting 5 characters will affect the summary extraction

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins` directory
2. Activate the plugin in the "Plugins" menu of the WordPress admin dashboard
3. Go to "Settings" > "KeepSpace" to configure the plugin options

== Frequently Asked Questions ==

= Will existing content be automatically updated after changing the space type? =

No. The special space type change only applies to newly saved content; existing special spaces in saved content will not be automatically updated.

== Screenshots ==

1. Settings interface

== Changelog ==

= 1.0.5（2025-10-13） =
* Optimize the space-replacement logic to reduce the chance of mistakenly intercepting other forms

= 1.0.4（2025-6-20） =
* Launch on wordpress.org

== Upgrade Notice ==

= 1.0.5 =
* Optimize the space-replacement logic to reduce the chance of mistakenly intercepting other forms
