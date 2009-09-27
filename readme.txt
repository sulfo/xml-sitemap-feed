=== XML Sitemap Feed ===
Contributors: RavanH
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=ravanhagen%40gmail%2ecom&item_name=XML%20Sitemap&item_number=2%2e6%2e2%2e9&no_shipping=0&tax=0&bn=PP%2dDonationsBF&charset=UTF%2d8
Tags: xml sitemap, sitemap, google sitemap, yahoo sitemap, msn sitemap, ask sitemap, search engine, feed
Requires at least: 2.5
Tested up to: 2.8
Stable tag: 2.0

Creates a feed that complies to the XML Sitemap protocol ready to be submitted to Google, Yahoo, MSN, Ask.com and others.

== Description ==

This plugin dynamically creates an XML feed that complies to the XML Sitemap protocol. There are no options to be set and the feed becomes instantly available after activation on yourblogurl.tld/sitemap.xml and yourblogurl.tld/feed/sitemap ready to be submitted to search engines like Google, Yahoo, MSN, Ask.com and others. An entry `Sitemap: http:// yourblogurl.tld/sitemap.xml` is added to the (by WordPress dynamically created) robots.txt on yourblogurl.tld/robots.txt to tell search engines where to find your XML Sitemap. 

The main advantages of this approach are **simplicity** (no need to change file or folder permissions or move files) and the fact that this method works out-of-the-box on **shared codebase / multi-blog setups** like [WordPress MU](http://mu.wordpress.org/), [WP_OneInstall](http://wordpress.org/extend/plugins/wp-oneinstall/), [WP Hive](http://wordpress.org/extend/plugins/wp-hive/) and others. 

It was based on the plugin Standard XML Sitemap Generator by Patrick Chia but requires no modifications to file locations. Some other small improvements and a bugfix were done in the first version.

= Limitations =

* The feed contains the front page and all posts and pages but (still) excludes category, tag and other dynamic archive pages.
* There is no way (yet) to manually change the priority of posts/pages in the sitemap but since version 2.0 there is some basic automatic priority calculation (based on post age and comment activity) doing the work for you. 
* The number of posts listed in the sitemap is limited to 1000. This should satisfy most blogs while limiting the sitemap size on bigger blogs by stripping of the oldest posts. Please let me know if you need more than your most recent 1000 posts listed in your sitemap.xml :)

= Translations =

There is nothing to translate. The sitemap protocol is international, there is no options page nor any front-end output. Nothing to see here, please move along ;)  

== Installation ==

= Wordpress =

Just use that slick installation and auto update feature on your Pugins page
- OR - 
follow these simple steps:

1. Download archive and unpack.

2. Upload the unpacked folder and its content to the /plugins/ folder. 

3. Activate the plugin on the Plug-ins page.

Done! Check your sparkling new XML Sitemap by adapting the url yourblogurl.tld/sitemap.xml to your blog and visiting it with a browser or online XML Sitemap validator. You might also want to see if the sitemap is listed in your yourblogurl.tld/robots.txt file.

= Wordpress MU =

The plugin also works from the /mu-plugins/ folder where it runs quietly in the background without bothering any blog owner with new options or the need for extra knowledge of XML Sitemap submission. Just upload the complete package content and move the file xml-sitemap.php from /mu-plugins/xml-sitemap-feed/ to /mu-plugins/.

== Frequently Asked Questions ==

= How are the values for priority and changefreq calculated? =

The front page has a priority of 1.0, pages are always 0.6 and posts will have a priority between 0.8 and 0.3 depending on comments and its age. the cangefreq of the frontpage is set to daily, monthly for pages and either monthly or weekly for posts depending on comments. 

Dynamic pages like category pages, tag pages and archive pages are not listed in this version yet.

= Do I need to submit the sitemap to search engines? =

No. In normal circumstances, your site will be indexed by the major search engines before you know it. The search engines will be looking for a robots.txt file and (with this plugin activated) find a pointer in it to the XML Sitemap on your blog. The search engines will return on a regular basis to see if your site has updates.

= Does this plugin ping search engines? =

No. While other XML Sitemap plugins provide pinging to some search engines upon each post edit or publication, this plugin does not. For the average website, in my experience, pinging Google or others after each little change does not benefit anything except a theoretical smaller delay in re-indexation of your website. This is only theoretical because if your site is popular and active, major search engines will likely be crawling your site on a very regular basis anyway. And if, on the other hand, your site is not high in the agenda with the major search engines, they will likely give no priority to your pings anyway. 

If you **really** feel the need to get your latest post indexed ASAP, you can let major search engines know about updates manually. For most search engines you need to have some account for that. Like a [Google Webmaster Tools account](https://www.google.com/webmasters/tools/) which will tell you much interesting things about your website and your readers. Try it!

= Can I change the sitemap name/URL? =

No. The sitemap url that you manually submit to Google (if you are impatient) should be yourblogurl.tld/sitemap.xml 
The feed is also available via yourblogurl.tld/feed/sitemap

= I see no sitemap.xml file in my server space! =

The sitemap is dynamically generated just like a feed. There is no actual file created.

= Where can I customize the xml output? =

You may edit the XML output in xml-sitemap-feed/template.php but be carefull not to break Sitemap protocol comliance. Read more on [Sitemaps XML format](http://www.sitemaps.org/protocol.php).

= Do I need to change my robots.txt? =

No. Your sitemap url will be automatically added to your dynamic robots.txt when plugin actived. Unless you use a static robots.txt file in your website root. In that case you areadvised to open it in a text editor and add a line like `Sitemap: http:// yourblogurl.tld/sitemap.xml` (adapt to your site url).

== Changelog ==

= 2.0 =
* priority calculation based on comments and age
* changefreq based on comments 

= 1.0 =
* changed feed template location to avoid the need to relocate files outside the plugins folder
* bugfix: get_post_modified_time instead of get_post_time
* bugfix: rewrite rules causing unlimited amount of sitemap feeds on any url ending with sitemap.xml instead of just one in the root

= 0.1 =
* rework from Patrick Chia's [Standard XML Sitemaps](http://wordpress.org/extend/plugins/standard-xml-sitemap/)
* increased post urls limit from 100 to 1000 (of max. 50,000 allowed by the Sitemap protocol)

