=== Quick Bulk Term Taxonomy Creator ===
Contributors: tyxla
Tags: quick, taxonomies, create, creator, bulk, batch, insert, taxonomy, fast, swift, generator, hierarchy, developer, term, terms
Requires at least: 3.0.1
Tested up to: 4.5
Stable tag: 1.0.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A handy tool for batch creation of taxonomy terms in your preferred hierarchy. 

== Description ==

A handy WordPress plugin for batch creation of taxonomy terms in your preferred hierarchy. 
Indispensable tool for WordPress developers.

== Installation ==

1. Install Quick Bulk Term Taxonomy Creator either via the WordPress.org plugin directory, or by uploading the files to your server.
1. Activate the plugin.
1. That's it. You're ready to go! Please, refer to the Usage and Examples sections for examples and usage information.

== Getting Started ==
After installing and activating the plugin, go to Tools -> Quick Term Creator. Please, refer to the Usage section below for usage information and examples.

== Usage ==

When you go to Tools -> Quick Term Creator, you will find a form that allows you to bulk insert taxonomy terms. The form contains the following fields:

**Hierarchy Indent Character**

Specifies the character (or set of characters) that are used to specify the hierarchy indentation. You can use those characters in your Terms text, prepending one or more terms with one or more of these characters. You can read more about how this field is used in the "Terms" field description below.

**Taxonomy**

Specifies the taxonomy of the terms that you want to bulk create.

**Terms**

Allows you to insert as many titles of terms as you wish. Each term should be on a separate line. You can additionally prepend each term with one or more hierarchy indent characters. For example, if your character is an asterisk - `*`, you can use one asterisk at the beginning of an term to specify that it is a child of the last item without any asterisks. You can use 2 asterisks at the beginning of an term to specify that it is a child of the last item with 1 asterisk, and so on. There is no limit of how deep you can go with your hierarchy. Also, there is no limit of the number of terms that you might want to bulk create. 

== Examples ==

**Example 1**

The following example will create 5 category terms with the corresponding titles.

* Hierarchy Indent Character: `*`
* Term Type: `Category`
* Terms: 

`Term 1
Term 2
Term 3
Term 4
Term 5`

**Example 2**

The following example will create 5 category terms with the corresponding titles and the specified hierarchy (X1, X2 as a child of X, X1a as a child of X1 and X2, X and Y as parents).

* Hierarchy Indent Character: `-`
* Term Type: `Category`
* Terms: 

`Term X
- Term X1
-- Term X1a
- Term X2
Term Y`

== Ideas and bug reports ==

If you have an idea for a new feature, or you want to report a bug, feel free to do it here in the Support tab, or you can do it at the Github repository of the project: 

https://github.com/tyxla/quick-bulk-taxonomy-term-creator

== Changelog ==

= 1.0.4 =
Tested with WordPress 4.5.

= 1.0.3 =
Tested with WordPress 4.4.

= 1.0.2 =
Tested with WordPress 4.3.

= 1.0.1 =
Fixing minor whitespace glitch.

= 1.0 =
Initial version.