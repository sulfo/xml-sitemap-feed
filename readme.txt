=== XML Sitemap Feed ===
Contributors: RavanH
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=ravanhagen%40gmail%2ecom&item_name=XML%20Sitemap&item_number=2%2e6%2e2%2e9&no_shipping=0&tax=0&bn=PP%2dDonationsBF&charset=UTF%2d8
Tags: sitemap, xml sitemap, google, yahoo, feed, wpmu
Requires at least: 2.5
Tested up to: 3.0
Stable tag: 3.4

Creates a feed that complies with the XML Sitemap protocol ready for indexing by Google, Yahoo, MSN, Ask.com and others.

== Description ==

This plugin dynamically creates an XML feed that complies with the XML Sitemap protocol. There are no options to be set and the feed becomes instantly available after activation on yourblogurl.tld/sitemap.xml (or yourblogurl.tld/index.php?feed=sitemap.xml if you do not use a fancy permalink structure) ready for indexing by search engines like Google, Yahoo, MSN, Ask.com and others. 

An entry `Sitemap: http://yourblogurl.tld/sitemap.xml` is added to the (by WordPress dynamically created) robots.txt on yourblogurl.tld/robots.txt to tell search engines where to find your XML Sitemap. If you do not use fancy URL's in WordPress, you will have to create your own robots.txt file. See FAQ's.

= Advantages =

* The main advantage of this plugin over other XML Sitemap plugins is **simplicity**. No need to change file or folder permissions, move files or spend time on a difficult plugin options page. In fact, there are no options at all!
* Works out-of-the-box on **multi-site / shared codebase / multi-blog setups** like [WordPress MU](http://mu.wordpress.org/), WP 3.0 in MS mode and others. 
* Automatic post URL priority calculation based on post age and comment/trackback activity.
* Works both when **Network Activated** or placed in **/mu-plugins/** on WP 3.0 in Mutli site mode and WPMU.

= Limitations =

* The feed contains the front page and all posts and pages but _excludes_ category, tag and other dynamic archive pages. This should not be a problem and by some it is even advised. There are SEO plugins around that even make these archive pages non-indexable by search engines.
* Except by _resaving_ older posts from time to time (keeping the lastmod date fairly recent to ensure automatic high priority calculation for those urls) there is no way to manually set the priority of individual posts/pages in the sitemap. See the Faq's for more.
* This plugin does not ping any search engines. But then, WordPress does this by default already via the Ping-o-Matic service so why bother?
* The number of posts listed in the sitemap is limited to 1000. This should satisfy most blogs while limiting the sitemap size on bigger blogs by stripping of the oldest posts. Please let me know if you need more than your most recent 1000 posts listed in your sitemap.xml :)

= Translations =

There is nothing to translate. The sitemap protocol is international, there is no options page nor any front-end / widget output. Nothing to see here, please move along ;)  

= Credits =
XML Sitemap Feed was originally based on the (discontinued?) plugin Standard XML Sitemap Generator by Patrick Chia. Many thanks!


== Installation ==

= Wordpress =

Just use that slick installation and auto update feature on your Pugins page

&hellip; OR &hellip;

follow these simple steps:

1. Download archive.

2. Upload the zip file via the Plugins > Add New > Upload page &hellip; OR &hellip; unpack and upload with your favourite FTP client to the /plugins/ folder.

3. Activate the plugin on the Plug-ins page.

Done! Check your sparkling new XML Sitemap by visiting yourblogurl.tld/sitemap.xml (adapted to your domain name ofcourse) with a browser or any online XML Sitemap validator. You might also want to check if the sitemap is listed in your yourblogurl.tld/robots.txt file.

= Wordpress MU =

The plugin works best from the **/mu-plugins/** folder where it runs quietly in the background without bothering any blog owner with new options or the need for special knowledge of XML Sitemap submission. Just upload the complete package content to /mu-plugins/ and move the file xml-sitemap.php from the new /mu-plugins/xml-sitemap-feed/ to /mu-plugins/.

Installed in /mu-plugins/ alongside [WordPress MU Sitewide Tags Pages](http://wordpress.org/extend/plugins/wordpress-mu-sitewide-tags/), XML Sitemap Feed will **not** create a sitemap.xml nor change robots.txt for any tag blogs. They would be full of links outside the tags blogs own domain and subsequently ignored (or worse: penalized) by Google.

== Frequently Asked Questions ==

Questions devided in **Sitemap / Robots** with most q&a about sitemap.xml and robots.txt, **WordPress** with q&a about your WordPress configuration and **Advanced / Troubleshooting** with more advanced q&a and troubleshooting issues.

= Sitemap / Robots =

**Q: How are the values for priority and changefreq calculated?**

**A:** The front page has a fixed priority of 100% (1.0), pages are always 50% (0.5) and new posts have a default priority of 70% (0.7) but can vary between 10% (0.1) and 90% (0.9) depending on comments and post age. The cangefreq of the frontpage is set to daily, monthly for pages and either monthly or weekly for posts depending on comments. 

Dynamic pages like category pages, tag pages and archive pages are not listed in the XML Sitemap.

**Q: Can I manipulate values for priority and changefreq?**

**A:** Yes and No. Since this plugin has no options page there is no way (yet) to manually set the priority of urls in the sitemap. The priority of the frontpage is fixed to 100% (1.0), all other pages are fixed to 50% (0.5) and new posts are always 70% (0.7). Since version 2.0 there is automatic post priority calculation based on post age and comment activity, that can either make post priority go to 90% (0.9) for fairly recent posts with many comments or 10% (0.1) for very old posts with no comments. 

This feature can be used to your advantage: by resaving your most important older posts from time to time, keeping the lastmod date fairly recent, you can ensure a priority of at least 70% (0.7) for those urls. And if you have comments on on those pages, the priority will even go up to 90% (0.9).

If you cannot live with these rules, edit the values `$post_priority`, `$minpost_priority`, `$maxpost_priority`, `$page_priority`, `$frontpage_priority` in xml-sitemap-feed/template-xml.php

**Q: Do I need to submit the sitemap to search engines?**

**A:** No. In normal circumstances, your site will be indexed by the major search engines before you know it. The search engines will be looking for a robots.txt file and (with this plugin activated) find a pointer in it to the XML Sitemap on your blog. The search engines will return on a regular basis to see if your site has updates.

Read more about _Ping-O-Matic_ under **Does this plugin ping search engines** (below) to make sure your site is under _normal circumstances_ ;) 

**Q: Does this plugin ping search engines?**

**A:** No. While other XML Sitemap plugins provide pinging to some search engines upon each post edit or publication, this plugin does not. There are two reasons for that:

1. WordPress has a built-in pinging feature. Go in your WP Admin section to Settings > Writing and make sure that the text area under **Update services** contains at least 
`
http://rpc.pingomatic.com
` 
Read more on [Ping-O-Matic](http://pingomatic.com) about what excellent service you are actually getting _for free with every WordPress blog_ installation!

2. For the average website, in my experience, pinging Google or others after each little change does not benefit anything except a theoretical smaller delay in re-indexation of your website. This is only theoretical because if your site is popular and active, major search engines will likely be crawling your site on a very regular basis anyway. And if, on the other hand, your site is not high on the agenda of the major search engines, they will likely give no priority to your pings at all.

You can always take a [Google Webmaster Tools account](https://www.google.com/webmasters/tools/) which will tell you many interesting things about your website, search terms and your visitors. Try it!

**Q: I see no sitemap.xml file in my server space!**

**A:** The sitemap is dynamically generated just like a feed. There is no actual file created.

**Q: Do I need to change my robots.txt?**

**A:** That depends. If you have no physical robots.txt file in your site root, the new sitemap url will be automatically added to your dynamic robots.txt when plugin actived. But if you use a static robots.txt file in your website root, you will need to open it in a text editor and add a line like `Sitemap: http://yourblogurl.tld/sitemap.xml` (adapt to your site url) to make search engines find your XML Sitemap.

= WordPress =

**Q: My WordPress powered blog is installed in a subdirectory. Does that change anything?**

**A:** That depends on where the index.php and .htaccess of your installation resides. If it is in the root, meaning WP is installed in a subdir but the blog is accessible from your domain root, you do not have to do anything. It should work out of the box. However, if the index.php is (e.g. still with your wp-config.php and all other WP files) in a subdir, meaning your blog is only accessible via that subdir, you need to manage your own robots.txt file in your domain root. It _has_ to be in the root (!) and needs a line starting with `Sitemap:` followed by the full URL to the sitemap feed provided by XML Sitemap Feed plugin. Like:
`
Sitemap: http://yourblogurl.tld/subdir/sitemap.xml
` 

If you already have a robots.txt file with a line like that, you might want to read more about creating an XML Sitemap Index on [sitemaps.org](http://www.sitemaps.org/protocol.php#index).

**Q: Do I need to use a fancy Permalink structure?**

**A:** No. While I would advise you to use any one of the nicer Permalink structures, you might not be able to (or don't want to) do that. If so, you can still use this plugin: 

Check to see if the URL yourblogurl.tld/?feed=sitemap.xml (notice the **?feed=**!) does produce a feed. Now manually upload your own robots.txt file to your website root containing: 
`
Sitemap: http://yourblogurl.tld/?feed=sitemap.xml

User-agent: *
Allow: /
`
You can also choose to notify major search engines of your new XML sitemap manually. Start with getting a [Google Webmaster Tools account](https://www.google.com/webmasters/tools/).

= Advanced / Troubleshooting =

**Q: Can I change the sitemap name/URL?**

**A:** No. If you have fancy URL's turned ON in WordPress (Permalinks), the sitemap url that you manually submit to Google (if you are impatient) should be `yourblogurl.tld/sitemap.xml` but if you have the Default option set the feed is only available via `yourblogurl.tld/?feed=sitemap.xml` (notice the *?feed=*).

**Q: Where can I customize the xml output?**

**A:** You may edit the XML output in `xml-sitemap-feed/feed-xml.php` but be carefull not to break Sitemap protocol comliance.  Read more on [Sitemaps XML format](http://www.sitemaps.org/protocol.php).

The stylesheet (to make the sitemap human readable) can be edited in `xml-sitemap-feed/feed-xsl.php`.

**Q: I get a 404 page instead of both sitemap.xml and robots.txt!**

**A:** There are plugins like Event Calendar (at least v.3.2.beta2) known to mess with rewrite rules, causing problems with WordPress internal feeds and robots.txt generation and thus conflict with the XML Sitemap Feed plugin. Deactivate all plugins and see if you get a basic robots.txt file showing: 
`
User-agent: *
Disallow:
`
Reactivate your plugins one by one to find out which one is causing the problem. Then report the bug to the plugin developer. 

**Q: I get a 404 page instead of robots.txt while sitemap.xml works fine!**

**A:** There is a know issue with WordPress (at least up to 2.8) not generating a robots.txt when there are _no posts_ with _published_ status. If you use WordPress as a CMS with only _pages_, this will affect you. 

To get around this, you might either at least write one post and give it _Private_ status or alternatively create your own robots.txt file containing:
`
Sitemap: http://yourblogurl.tld/sitemap.xml

User-agent: *
Allow: /
`
and upload it to your web root...

== Changelog ==

= 3.4 =
* bugfix: use home instead of siteurl for blog url for sitemap reference in robots.txt
* code streamline and cleanup

= 3.3 =
* automatic exclusion of tags blog in wpmu

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

= 0.1 =
* rework from Patrick Chia's [Standard XML Sitemaps](http://wordpress.org/extend/plugins/standard-xml-sitemap/)
* increased post urls limit from 100 to 1000 (of max. 50,000 allowed by the Sitemap protocol)

