# BSF Docs #
**Contributors:** brainstormforce, aniljbsfio  
**Tags:** docs, wpdocs, documentation, wpdocs, documentation  
**Requires at least:** 3.0  
**Tested up to:** 4.9.0  
**Stable tag:** 1.0.1

## Description ##

BSF Docs allows you to create documentation website within a minute with ajax search. Organize your product documentation in your site, beautifully!

#### Documentation With a User-Friendly Structure For Your Visitors ####

* The main “Docs Page” will list all the categories. Clicking on a category will take you to the docs only for that category.

* For a single doc will have a predefined template with dedicated sidebar.

* BSF Docs works on MultiSite WordPress installations, as well as regular WordPress sites.


## Features ##

* Beautifully designed boxed categories styles
* Shortcode to add the search bar anywhere in your website
* Shortcode for display doc's category list     
* Drag and drop reorder docs categories 
* Well designed page templates
* A dedicated sidebar for single docs
* BSF Recent docs widget
* BSF Category widget ( Category Hierarchy, Count, Dropdown option )
* Support YOAST SEO breadcrumb 
* Docs H2-H5 ScrollView anchor link option

#### WIDGETS ####

The plugin provides 2 widgets that are used to display docs categories on the sidebar with various styles.

#### BSF Resent Docs Widget: ####

* Use to display recent document categories.

#### BSF Docs Category Widget: ####

BSF Docs Category Widget provides below-listed options.

* Display as drop-down
* Show post counts
* Show hierarchy

#### SHORTCODES ####

The following shortcodes are available:

[documentation_documents] Lists documents category.

[doc_wp_live_search placeholder="Have a question?"] Renders a dynamic search form.

## Frequently Asked Questions ##

### I’m getting a 404 error after visiting docs. ###
Please go to Settings > Permalinks and resave your permalink structure.

### How to change category orders? ###
You can change category orders simply by drag and drop from the category orders setting option. Docs->Category Orders

### How to show breadcrumb for my documentation? ###
BSF Docs fully support Yoast SEO breadcrumbs, So you can just need to enable breadcrumb from the Yoast SEO settings -> Search Appearance -> Breadcrumbs settings.

### How to display docs menu on my website? ###
BSF Docs plugin add *docs* custom post type on your website, So if you would like to add a menu on your site simple add Custom Links from WordPress menus and put a custom link like wwww.yourdomain.com/docs

### How to add a sidebar for single docs? ###
BSF Docs will automatically add a right sidebar for your single docs page, If you don't like the sidebar to your docs page then you can simply disable plugin single template file from the plugin settings menu. If you unchecked this option single docs post will display from active theme single templates.

### How to enable comments for my docs? ###
You can enable comments for your documentation post just like a normal WordPress blog post, But make sure to enabled Turn Off Doc's Comments option setting from the BSF docs plugin. 

### Can import/export Docs Knowledgebase data? ###
Yes! You can import/export data using the built-in WordPress function via Tools. It may not import any images in use (although it will import the file paths) so you will need to copy across any images from your old site to the new site uploads folder via FTP. If images still appear broken or missing then you might need to run a search and replace tool to correct the image file paths for your new site.

## Installation ##

1. Unzip the downloaded zip file
2. Upload the included folder to '/wp-content/plugins' directory of your WordPress installation 
3. Activate the plugin via the WordPress Plugins page 

## Changelog ##

Version 1.0.2 - 17-Sep-2018
* Improvement: Added category sort option
* Improvement: Added H2-H5 auto anchor link option
* Fix: Hide empty category issue

Version 1.0.1 - 04-Apr-2018
* Fix: Category undefined issue fixed for WP-4.9 
* Fix: Translation ready

Version 1.0.0
* Initial release
