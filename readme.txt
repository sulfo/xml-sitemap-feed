=== XML Sitemap Feed ===
Contributors: RavanH
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=ravanhagen%40gmail%2ecom&item_name=XML%20Sitemap&item_number=2%2e6%2e2%2e9&no_shipping=0&tax=0&bn=PP%2dDonationsBF&charset=UTF%2d8
Tags: xml sitemap, sitemap, google sitemap, yahoo sitemap, msn sitemap, ask sitemap, search engine, feed
Requires at least: 2.5
Tested up to: 2.8
Stable tag: 3.2

Creates a feed that complies with the XML Sitemap protocol ready to be submitted to Google, Yahoo, MSN, Ask.com and others.

== Description ==

This plugin dynamically creates an XML feed that complies with the XML Sitemap protocol. There are no options to be set and the feed becomes instantly available after activation on yourblogurl.tld/sitemap.xml (or yourblogurl.tld/index.php?feed=sitemap.xml if you do not use a fancy permalink structure) ready to be submitted to search engines like Google, Yahoo, MSN, Ask.com and others. 

An entry `Sitemap: http://yourblogurl.tld/sitemap.xml` is added to the (by WordPress dynamically created) robots.txt on yourblogurl.tld/robots.txt to tell search engines where to find your XML Sitemap. If you do not use fancy URL's in WordPress, you will have to create your own robots.txt file. See FAQ's.

= Advantages =

* The main advantage of this plugin over other XML Sitemap plugins is **simplicity**. No need to change file or folder permissions or move files. In fact, no options at all!
* Works out-of-the-box on **shared codebase / multi-blog setups** like [WordPress MU](http://mu.wordpress.org/), [WP_OneInstall](http://wordpress.org/extend/plugins/wp-oneinstall/), [WP Hive](http://wordpress.org/extend/plugins/wp-hive/) and others. 

= Limitations =

* The feed contains the front page and all posts and pages but (still) excludes category, tag and other dynamic archive pages.
* There is no way (yet) to manually set the priority of posts/pages in the sitemap but since version 2.0 there is some basic automatic priority calculation (based on post age and comment activity) doing the work for you. However, by resaving older posts/pages from time to time, keeping the lastmod date fairly recent, you can ensure high priority for those urls.
* The number of posts listed in the sitemap is limited to 1000. This should satisfy most blogs while limiting the sitemap size on bigger blogs by stripping of the oldest posts. Please let me know if you need more than your most recent 1000 posts listed in your sitemap.xml :)

= Translations =

There is nothing to translate. The sitemap protocol is international, there is no options page nor any front-end output. Nothing to see here, please move along ;)  

= Credits =
XML Sitemap Feed is based on the plugin Standard XML Sitemap Generator (discontinued?) by Patrick Chia. Many thanks!


== Installation ==

= Wordpress =

Just use that slick installation and auto update feature on your Pugins page

&hellip; OR &hellip;

follow these simple steps:

1. Download archive and unpack.

2. Upload the unpacked folder and its content to the /plugins/ folder. 

3. Activate the plugin on the Plug-ins page.

Done! Check your sparkling new XML Sitemap by adapting the url yourblogurl.tld/sitemap.xml to your blog and visiting it with a browser or online XML Sitemap validator. You might also want to see if the sitemap is listed in your yourblogurl.tld/robots.txt file.

= Wordpress MU =

The plugin also works from the /mu-plugins/ folder where it runs quietly in the background without bothering any blog owner with new options or the need for extra knowledge of XML Sitemap submission. Just upload the complete package content and move the file xml-sitemap.php from /mu-plugins/xml-sitemap-feed/ to /mu-plugins/.

== Frequently Asked Questions ==

= My WordPress powered blog is installed in a subdirectory. Does that change anything? =

That depends on where the index.php of your installation resides. If it is in the root, meaning WP is installed in a subdir but the blog is accessible from your domain root, you do not have to do anything. It should work out of the box. However, if the index.php is (e.g. still with your wp-config.php and all other WP files) in a subdir, meaning your blog is only accessible via that subdir, you need to manage your own robots.txt file in your domain root. It _has_ to be in the root (!) and needs a line starting with `Sitemap:` followed by the full URL to the sitemap feed provided by XML Sitemap Feed plugin. Like:
`
Sitemap: http://yourblogurl.tld/subdir/sitemap.xml
` 

= Can I manipulate values for priority and changefreq? =

Yes and No. Since this plugin has no options page there is no way (yet) to manually set the priority of urls in the sitemap. Since version 2.0 there is some basic automatic priority calculation (based on post age and comment activity) doing the work for you. 

This feature can be used to your advantage: by resaving older posts from time to time, keeping the lastmod date fairly recent, you can ensure a priority of 70% (0.7) for those urls. And if you have comments on on those pages, the priority can even go up to 90% (0.9).

If you cannot live with these rules, edit the values `$post_priority`, `$minpost_priority`, `$maxpost_priority`, `$page_priority`, `$frontpage_priority` in xml-sitemap-feed/template-xml.php

= How are the values for priority and changefreq calculated? =

The front page has a fixed priority of 100% (1.0), pages are always 60% (0.6) and posts have a default priority of 70% (0.7) but can vary between 30% (0.3) and 90% (0.9) depending on comments and post age. the cangefreq of the frontpage is set to daily, monthly for pages and either monthly or weekly for posts depending on comments. 

Dynamic pages like category pages, tag pages and archive pages are not listed in this version yet.

= Do I need to submit the sitemap to search engines? =

No. In normal circumstances, your WordPress site will be indexed by the major search engines before you know it. The search engines will be looking for a robots.txt file and (with this plugin activated) find a pointer in it to the XML Sitemap on your blog. The search engines will return on a regular basis to see if your site has updates.

= Does this plugin ping search engines? =

No. While other XML Sitemap plugins provide pinging to some search engines upon each post edit or publication, this plugin does not. For the average website, in my experience, pinging Google or others after each little change does not benefit anything except a theoretical smaller delay in re-indexation of your website. This is only theoretical because if your site is popular and active, major search engines will likely be crawling your site on a very regular basis anyway. And if, on the other hand, your site is not high in the agenda with the major search engines, they will likely give no priority to your pings at all. 

Besides, WordPress has a built-in pinging feature. Go in your WP Admin section to Settings > Writing and make sure that the field under **Update services** contains at least 
`
http://rpc.pingomatic.com
` 
Read more on [Ping-O-Matic](http://pingomatic.com) about what excellent service you are actually getting for free with your WordPress installation!

You can also take a [Google Webmaster Tools account](https://www.google.com/webmasters/tools/) which will tell you many interesting things about your website and your readers. Try it!

= Do I need to make use of a fancy Permalink structure? =

No. While I would advise you to use any one of the nicer Permalink structures, you might not be able to (or don't want to) do that. If so, you can still use this plugin: 

Check to see if the URL yourblogurl.tld/?feed=sitemap.xml (notice the **?feed=**!) does produce a feed. Now manually upload your own robots.txt file to your website root containing: 
`
Sitemap: http://yourblogurl.tld/?feed=sitemap.xml

User-agent: *
Disallow:
`
You can also choose to notify major search engines of your new XML sitemap manually.

= Can I change the sitemap name/URL? =

No. If you have fancy URL's turned ON in WordPress (Permalinks), the sitemap url that you manually submit to Google (if you are impatient) should be `yourblogurl.tld/sitemap.xml` but if you have the Default option set the feed is only available via `yourblogurl.tld/?feed=sitemap.xml` (notice the *?feed=*).

= I see no sitemap.xml file in my server space! =

The sitemap is dynamically generated just like a feed. There is no actual file created.

= Where can I customize the xml output? =

You may edit the XML output in `xml-sitemap-feed/feed-xml.php` but be carefull not to break Sitemap protocol comliance.  Read more on [Sitemaps XML format](http://www.sitemaps.org/protocol.php).

The stylesheet (to make the sitemap human readable) can be edited in `xml-sitemap-feed/feed-xsl.php`.

= Do I need to change my robots.txt? =

No. Your sitemap url will be automatically added to your dynamic robots.txt when plugin actived. Unless you use a static robots.txt file in your website root. In that case you are advised to open it in a text editor and add a line like `Sitemap: http://yourblogurl.tld/sitemap.xml` (adapt to your site url).

= I get a 404 page instead of sitemap.xml and robots.txt! =

There are plugins like Event Calendar (at least v.3.2.beta2) known to mess with rewrite rules, causing problems with WordPress internal feeds and robots.txt generation and thus conflict with the XML Sitemap Feed plugin. Deactivate all plugins and see if you get a basic robots.txt file showing: 
`
User-agent: *
Disallow:
`
Reactivate your plugins one by one to find out which one is causing the problem. Then report the bug to the plugin developer. 

== Changelog ==

= 3.2 =
* rewrite and add_feed calls improvements
* bugfix: double entry when static page is frontpage

= 3.0 =
* added styling to the xml feed to make it human readable

= 2.1 =
* bugfix: lastmod timezone offset displayed wrong (extra space and missing double-colon)

= 2.0 =
* priority calculation based on comments and age
* changefreq based on comments 

= 1.0 =
* changed feed template location to avoid the need to relocate files outside the plugins folder
* bugfix: `get_post_modified_time` instead of `get_post_time`
* bugfix: rewrite rules causing unlimited amount of sitemap feeds on any url ending with sitemap.xml instead of just one in the root

= 0.1 =
* rework from Patrick Chia's [Standard XML Sitemaps](http://wordpress.org/extend/plugins/standard-xml-sitemap/)
* increased post urls limit from 100 to 1000 (of max. 50,000 allowed by the Sitemap protocol)

