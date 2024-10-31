=== Notes PostWidgets ===
Contributors: feedmeastraycat
Tags: posts, custom posts, widgets, wysiwyg
Requires at least: 3.0.0
Tested up to: 3.8
Stable tag: 1.0.8

	Notes PostWidgets is a plugin which adds a custom post type that you can use 
to create text widgets with.

== Description ==

**Please note!** Developement of this plugin has been suspended. I would recommend my newer plugin
[WP Editor Widget](http://wordpress.org/plugins/wp-editor-widget/) instead. WP Editor Widget adds
a WYSIWYG widget using the built in WP Editor without adding a custom post type.

Notes PostWidgets is a plugin which adds a custom post type *("Post Widgets")* 
that you can use to create text widgets with. This way you can use the built in
WYSIWYG *(What You See Is What You Get)* editor to set the mark up on your widgets.
You can set a custom CSS class name for each widget for more specific mark up.

The plugin probably works fine with 2.9 but has not been tested. It uses custom 
posts which was introduced in 2.9 so lower versions will not work.

== Installation ==

1. Extract the ZIP file and move the folder "notes-postwidgets", with it contents, 
   to `/wp-content/plugins/` in your WordPress installation
2. Activate the pluing under 'Plugins' in the WordPress admin area
3. Create a Post Widget *(in the Post Widgets menu)*
4. Add a Notes PostWidget in Widgets section and select your Post Widget 
   *(make sure it has been published)*. Don't forget to save it!

== Frequently Asked Questions ==

= How can I style my widgets? =
The widgets are styled by the themes just like regular widgets. For additional styling you can sett the "Container class" to a class name of your choice.
This class name will be used on the DIV tag that contains everything inside the widgets.

Example:
<pre>&lt;style&gt;
.my-class-name { background-color: black; color: white; }
.my-class-name h2.widgettitle { color: pink; }
&lt;/style&gt;</pre>

== Files ==

* /notes-postwidgets/notes-postwidgets.php
* /notes-postwidgets/README.txt
* /notes-postwidgets/languages/ *[DIR]*
* /notes-postwidgets/languages/Notes-PostWidgets.pot
* /notes-postwidgets/languages/Notes-PostWidgets-sv_SE.mo
* /notes-postwidgets/languages/Notes-PostWidgets-sv_SE.po

== Screenshots ==

*No screenshots at the moment.*

== Changelog ==

* 1.0.7
* Minor translation fixex
* Tested in WP 3.5, no changes required
* 1.0.6
* PHP "undefined index" error fix. Thanks Mark (http://www.plebeian.se/) :)
* Tested for WP 3.3.1
* 1.0.5
* Tested for WP 3.3
* Swedish translation fix
* 1.0.4
* Fix for the Custom Post Type update which might might fail in a error loop when there is nothing to update.
* 1.0.3
* Tested for WP 3.2.1
* 1.0.2
* A change to Custom Post Type name (somewhere) in WordPress 3.1.x caused a bug which removed the edit functionality on the PostWidgets for some users. This version includes a upgrade to fix this issue.
* 1.0.1 (never set as stable)
* Removes widget posts (the custom post type) from search
* 1.0.0
* Added search function. When creating the widget you don't have to choose from all, you can now search.
* Bug: The drop down select (when creating the widget) only showed 10 latest. Now show all in order by title 
* 0.2.2
* Removed usage of short open tag in PHP.
* 0.2.1
* Checked the wrong version option name in a couple of places. Had no affect. Fixed anyway.
* 0.2.0
* Updated for localization. Swedish language added.
* 0.1.0
* First stable release

== Upgrade Notice ==

*No upgrade notices at the moment.*