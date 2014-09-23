=== Recurring Timer Widget ===
Contributors: jrfoell
Tags: widget, timer
Tested up to: 3.6
Stable tag: 1.3

Displays a countdown timer in a widget for a recurring event.

== Description ==

Displays a countdown timer in a widget for a recurring event.

It uses string-to-time (strtotime) formats which can simple or very
complex (and confusing). 

The widget has many CSS classes and IDs to style it with, but it's
been left purposely unstyled so you can format it in whatever way you
want.  The included recurring-style.css-example file shows how you can
move the time and time labels around for a custom look.  You can
customize recurring-style.css to your liking without worry that it will
be overwritten when you upgrade (only recurring-style.css-example
would updated).

Known issues:

* It requires a browser refresh before the "next next" event.  i.e.  If
you're viewing the widget and the timer is set for a daily event, the
timer will countdown to today's event, then countdown to tomorrow's
event.  But it will not countdown to the day after tomorrow's event
unless the browser is refreshed.
* Events that occur more than once in a 24-hour period have not been
tested and may not work.


== Installation ==

1. Unzip and upload the plugin to your 'wp-content/plugins' directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Add the widget to your site through the 'Widgets' menu in WordPress
(under 'Appearance').
1. Optionally copy recurring-timer.css-example to recurring-timer.css
to add a little style.  Instead you may also style the widget by
customizing the CSS in your theme.

== Frequently Asked Questions ==

= Where do I find information about the time formats? =

* http://php.net/strtotime
* http://www.php.net/manual/en/datetime.formats.relative.php
* http://www.gnu.org/software/tar/manual/html_node/Date-input-formats.html

Please note this comment on php.net:
http://us.php.net/manual/en/datetime.formats.relative.php#98989
In my screenshot example I used "fourth thursday of this month" for
the Event Day.  It seems this only works in PHP 5.3+.  However,
removing the 'of' and using "fourth thursday this month" works in
earlier versions of PHP.  Your mileage may vary, so you'll want to
experiment if your timer is displaying negative time and counting up.

== Screenshots ==

1. Widget menu in WP-Admin with an example event.
2. An un-styled version of the widget (not very pretty).
3. The widget using the example CSS file for style.

== Changelog ==

= 1.3 =
* Refactored javascript to allow widget to be placed multiple times on a
single page

= 1.2 =
* Fixed misspelled variable affecting next event start

= 1.1 =
* Changed dates to use GMT -- let the user's browser determine the
timezone
* Added readme.txt and screenshots

= 1.0 =
* Initial release
