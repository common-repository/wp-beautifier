=== WP-Beautifier ===
Contributors: tillkruess
Donate link: http://tillkruess.com/donations/
Tags: clean, tidy, strip, remove, indent, indentation, format, formatting, source, code, output, html, xhtml, xml, feed
Requires at least: 3.0
Tested up to: 3.1
Stable tag: 1.3.1

WP-Beautifier provides several options to clean up WP's messy (X)HTML and XML-Feed source code output, such as code indentation.

== Description ==

WP-Beautifier provides the following options to clean up WP's messy (X)HTML and XML-Feed source code output:

* Strip unnecessary whitespace, like empty lines and trailing whitespace
* Code indentation, formatting, cleanup
* Remove (X)HTML comments
* Convert single-quoted tag attribute values into double-quoted attribute values
* Compress inline JavaScript code
* Remove *anything* you want from the output by using custom regular expressions

Detailed information about all features can be found in the Contextual Help section of this plugin (_Settings_->_Beautification_: top right corner).

If something is not working, please read the [FAQ](http://wordpress.org/extend/plugins/wp-beautifier/faq/) or open a topic in the [support forum](http://wordpress.org/tags/wp-beautifier).


== Installation ==

1. Upload the `/wp-beautifier/` directory and its contents to `/wp-content/plugins/`.
2. Login to your WordPress installation and activate the plugin through the _Plugins_ menu.
3. If desired, refine the plugin's options in the _Settings_ menu under _Beautification_.


== Frequently Asked Questions ==

= Excuse me, what exactly is this plugin doing? =

This plugin provides a bunch of options to change what WordPress is sending to the browser. The text sent to the browser is called HTML or XHTML and you can usually see it by doing a right-click on the page and select something like "View Page Source" or "Show Sourcecode". This plugin provides the ability to indent all lines correctly for easier reading, to remove unnecessary whitespace characters and a few other options, like inline javascript compression and regular expressions.

= This plugin leaves my (X)HTML untouched! =

The reason for that is most likely a invalid or missing Doctype declarations (DTD). This plugin only takes action, if a valid (X)HTML DTD is found in the first 100 characters of the page. If you don't know what a Doctype declarations is, read this [article about Doctypes](http://www.w3.org/QA/Tips/Doctype) or see this [list of valid doctypes for (X)HTML documents](http://www.w3.org/QA/2002/04/valid-dtd-list.html).

= My HTML source code isn't indented correct! =

Most likly the reason for that is the actual source code output of your WordPress theme is using [HTML](http://www.w3.org/TR/html401/) notation. This plugin only supports valid [XHTML](http://www.w3.org/TR/xhtml1/) notation, which can, by the way, be used in HTML5 documents.

= When I activate the plugin, my theme breaks! =

To narrow down the cause, start disabling every single option (under *Settings*->*Beautification*) one by one and see if the problem persists. If it persists, deactivate the plugin and copy-paste the working generated (X)HTML source code into a txt-file; afterwards activate the plugin again and copy-paste the _broken_ (X)HTML output into a second txt-file. Send these two files to [the plugin author](http://tillkruess.com/contact/) with a short description of your problem.

= I see a white/blank page where my website used to be! =

Try the _Reset Options_ button in the _Settings_ menu under _Beautification_. If that doesn't help, deactivate the plugin and contact [the plugin author](http://tillkruess.com/contact/). To resolve your problem faster, attach your server's PHP error-log-file to the email.


== Screenshots ==

1. Cleaned and nicely formatted code.


== Changelog ==

= 1.3.1 =

* Renamed Settings menu link and added plugin meta row support links
* `beautifier_add_indention()` is now PHP4 compatible
* Moved `beautifier_uninstall()` to plugin options file

= 1.3 =

* Added basic RSS and Atom-Feed output cleaning
* Moved admin related code into `options.php` file
* Made most of the language strings easier to understand
* Added settings link to plugin actions in plugin listing
* `beautifier_clean_whitespace()` is now also removing tabs between tags

= 1.2 =

* Prevented permanent `E_NOTICE` caused by strict `ob_start()`
* Added toggle for *custom regular expressions*
* Added contextual help to administration menu
* Added German translation 
* `beautifier_uninstall()` is now working again
* Updated language strings and old comments
* *Custom regular expressions* are now applied before the code cleaning to enhance their potential
* Moved the *single quotes conversion* before the tag preservation
* Cleaning only if `WP_USE_THEMES` is defined and a valid DTD is found in the beginning of the output buffer

= 1.1.1 =

* Improved tag preservation to keep `<script>` tags intact
* Conditional Comments are not removed anymore
* Custom regexps are now called after comment removal and quotes conversion
* Fixed mis-labeled input field id attribute

= 1.1 =

* Renewed many functions and made the descriptions more clear.
* Added the ability to convert single quotes into double quotes in html tags
* Added the ability to clean the output with custom regular expressions
* Prevented an infinite loop, when a html comment is not closed

= 1.0 =

* Initial release


== Upgrade Notice ==

= 1.3.1 =

This version makes the plugin PHP4 compatible and contains minor improvements. 

= 1.3 =

This version contains speed improvements and new feed cleaning support. 

= 1.2 =

This version prevents an on-going E_NOTICE and extends, enhances and fixes many sections of the plugin. 

= 1.1.1 =

This version leaves conditional comments and `<script>` tags untouched.

= 1.1 =

This version prevents an infinite loop and provides additional cleaning functionality.