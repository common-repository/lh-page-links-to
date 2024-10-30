=== LH Page Links To ===
Contributors: shawfactor
Donate link: https://lhero.org/portfolio/lh-page-links-to/
Tags: page, redirect, link, external link, repoint  
Requires at least: 5.0
Tested up to: 6.5
Stable tag: 1.02
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Lets you make a WordPress post or page link to a URL of your choosing (on your site, or on another site), instead of its normal WordPress URL.

== Description ==

This plugin allows you to make a WordPress post or page link to a URL of your choosing, instead of its WordPress URL. It also will redirect people who go to the old (or "normal") URL to the new one you've chosen.

This started as a fork of Mark Jacquiths Page Links to plugin. I forked it, to simplify the code base, better support gutenberg, and properly support redirects on password protected pages.

**Common uses:**

* Set up navigational links to non-WordPress sections of your site or to off-site resources.
* Publish content on other blogs (or other services, like Medium) but have them show up in your WordPress posts stream. All you have to supply is a title and a URL. The post title will link to the content on the other site.
* For store operators, you can link to products on other retailer's sites (maybe with an affiliate code) but have them show up like they're products in your store.
* Create a "pretty URL" for something complicated. Say you have https://example.com/crazy-store-url.cgi?search=productId&sourceJunk=cruft ... just create a WordPress page called "My Store" and use Page Links To to point it to the ugly URL. Give people the new URL: https://example.com/my-store/ and it will redirect them!

**Like this plugin? Please consider [leaving a 5-star review](https://wordpress.org/support/view/plugin-reviews/lh-page-links-to/).**

**Love this plugin or want to help the LocalHero Project? Please consider [making a donation](https://lhero.org/portfolio/lh-page-links-to/).**

== Installation ==

1. Upload the `lh-page-links-to` folder to your `/wp-content/plugins/` directory.

2. Activate the "LH Page Links To" plugin.

**Existing Content Usage:**

1. Edit a post or page.

2. Below, find the Page Links To widget, select "A custom URL", and add a URL of your choosing.

3. Save the post or page.

4. Done! Now that content will point to the URL that you chose. Also, if anyone had the old WordPress URL for that content, they will be redirected to the custom URL if they visit.

**Creating New Page Links:**

1. Click Pages > Add New Page Link.

2. Provide a title and a destination URL.

3. Optionally provide a custom slug, which will be used in creating a local redirect URL.

4. Click Publish.

== Screenshots ==

1. The Page Links To meta box in action
2. The quick Add Page Link dialog.

== Frequently Asked Questions ==

= My links are sending me to http://mysite.com/site-i-wanted-to-link-to.com ... why? =

If you want to link to a full URL, you *must* include the `http://` portion.

= Can I link to relative URLs for URLs on the same domain? =

Yes. Linking to `/my-content.php` is a good idea, as it will still work if you move your site to a different domain.

= What if something does not work?  =

LH Page Links To, and all [https://lhero.org](LocalHero) plugins are made to WordPress standards. Therefore they should work with all well coded plugins and themes. However not all plugins and themes are well coded (and this includes many popular ones). 

If something does not work properly, firstly deactivate ALL other plugins and switch to one of the themes that come with core, e.g. twentyfifteen, twentysixteen etc.

If the problem persists please leave a post in the support forum: [https://wordpress.org/support/plugin/lh-page-links-to/](https://wordpress.org/support/plugin/lh-page-links-to/) . I look there regularly and resolve most queries.

= What if I want to enable or disable this functionality for a specific post type?  =

Look at the filter lh_page_links_to_get_applicable_post_types . It will enable you to modify the post type(s) this functionality is available with.

More bbroadly this plugin is about decisions not options so I won't be adding settings but there is likely a filter to modify most behaviour, and it there is not I am happy to add one! 

= What if I need a feature that is not in the plugin?  =

Please contact me for custom work and enhancements here: [https://shawfactor.com/contact/](https://shawfactor.com/contact/)

== Changelog ==

**1.00 December 05, 2019**  
Initial release.

**1.02 March 10, 2024**  
Many code improvements. Changed text domain to be wordpress compliant, added column for linked pages, added any filters