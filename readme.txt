=== XML Sitemap Feed ===
Contributors: RavanH
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=ravanhagen%40gmail%2ecom&item_name=XML%20Sitemap&item_number=2%2e6%2e2%2e9&no_shipping=0&tax=0&bn=PP%2dDonationsBF&charset=UTF%2d8
Tags: xml sitemap, sitemap, google sitemap, yahoo sitemap, msn sitemap, ask sitemap, search engine, feed
Requires at least: 2.5
Tested up to: 2.8
Stable tag: 1.0

Creates a feed that complies to the XML Sitemap protocol ready be submitted to Google, Yahoo, MSN, Ask.com and others.

== Description ==

This plugin dynamicaly creates an XML feed that complies to the XML Sitemap protocol. The main advantages of this approach are simplicity (no need to change file or folder permissions or move files) and the fact that this method works out-of-the-box on shared codebase / multi-blog setups like [WordPress MU](http://mu.wordpress.org/), [WP_OneInstall](http://wordpress.org/extend/plugins/wp-oneinstall/), [WP Hive](http://wordpress.org/extend/plugins/wp-hive/) and others. 

It requires no options to be set and instantly creates the feed of all posts on http://yourblogurl.tld/sitemap.xml and http://yourblogurl.tld/feed/sitemap. Ready to be submitted to search engines like Google, Yahoo, MSN, Ask.com and others. An entry `Sitemap: http://yourblogurl.tld/sitemap.xml` is added to the (by WordPress dynamicly created) robots.txt on http://yourblogurl.tld/robots.txt that will tell search engines visiting your blog where to find the XML Sitemap. 

It was based on the plugin Standard XML Sitemap Generator by Patrick Chia but requires no modifications to file locations. Some small improvements and a bugfix were done in the first version.

Current version limits:
* The feed contains the front page and all posts (normal pages are treated as posts) but excludes category, tag and other dynamic archive pages.
* The number of posts listed in the sitemap is limited to 1000. Please let me know if you need more than that :)

== Installation ==

= Wordpress =

Just use that slick installation and auto update feature on your Pugins page - OR - follow these simple steps:

1. Download archive and unpack.

2. Upload the unpacked folder and its content to the /plugins/ folder. 

3. Activate the plugin on the Plug-ins page.

Done! Check your sparkling new XML Sitemap by adapting the url http://yourblogurl.tld/sitemap.xml to your blog and visiting it with a browser or online XML Sitemap validator. You might also want to see if the sitemap is listed in your http://yourblogurl.tld/robots.txt file.

= Wordpress MU =

The plugin also works from the /mu-plugins/ folder where it works quietly in the background without bothering any blog owner with new options or the need for extra knowledge of XML Sitemap submission. Just upload the cpmplete package content and move the file xml-sitemap.php from /mu-plugins/xml-sitemap-feed/ to /mu-plugins/.

== Frequently Asked Questions ==

= Can I change the sitemap name/URL? =

No. The sitemap url that you manually submit too Google (if you are impatient) should be http://yourblogurl.tld/sitemap.xml but is also available via http://yourblogurl.tld/feed/sitemap

= I found no sitemap file in my blog! =

The sitemap is dynamically generated just like a feed. There is no actual file created.

= Where can I customize the xml output? =

You may edit the XML output in template.php but be carefull not to break Sitemap protocol comliance. Read more on [Sitemaps XML format](http://www.sitemaps.org/protocol.php).

= Do I need change my robots.txt? =

No. Your sitemap url will be automatically added to your dynamic robots.txt when plugin actived. Unless you have a static file robots.txt in your root. In that case you might want to open it in a text editor and add a line like `Sitemap: http://yourblogurl.tld/sitemap.xml` to it.

== Changelog ==

= 1.0 =
* changed feed template location to avoid the need to relocate files outside the plugins folder
* bugfix: get_post_modified_time instead of get_post_time
* bugfix: rewrite rules causing unlimited amount of sitemap feeds on any url ending with sitemap.xml instead of just one in the root

= 0.1 =
* rework from Patrick Chia's [Standard XML Sitemaps](http://wordpress.org/extend/plugins/standard-xml-sitemap/)
* increased post urls limit from 100 to 1000 (of max. 50,000 allowed by the Sitemap protocol)

