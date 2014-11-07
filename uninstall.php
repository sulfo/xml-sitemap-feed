<?php

//if uninstall not called from WordPress exit
if (!defined('WP_UNINSTALL_PLUGIN'))
    exit();

/*
 * XML Sitemap Feed uninstallation
 *
 * @since 4.4
 */
class XMLSitemapFeed_Uninstall {

	/*
	 * constructor: manages uninstall for multisite
	 *
	 * @since 4.4
	 */
	function __construct() 
	{
		global $wpdb;

		// check if it is a multisite uninstall - if so, run the uninstall function for each blog id
		if (is_multisite()) {
			error_log('Clearing XML Sitemap Feeds settings from each site brefore uninstall:');
			foreach ($wpdb->get_col("SELECT blog_id FROM $wpdb->blogs") as $blog_id) {
				switch_to_blog($blog_id);
				$this->uninstall($blog_id);
			}
			restore_current_blog();
			error_log('Done.');
		}
		else
			$this->uninstall();
	}

	/*
	 * remove plugin data
	 *
	 * @since 4.4
	 */
	function uninstall($blog_id = false) 
	{
		// remove plugin settings
		delete_option('xmlsf_version');
		delete_option('xmlsf_sitemaps');
		delete_option('xmlsf_post_types');
		delete_option('xmlsf_taxonomies');
		delete_option('xmlsf_news_sitemap');
		delete_option('xmlsf_ping');
		delete_option('xmlsf_robots');
		delete_option('xmlsf_urls');
		delete_option('xmlsf_custom_sitemaps');
		delete_option('xmlsf_domains');
		delete_option('xmlsf_news_tags');
		
/*		TODO: find a way to delete tax terms without the plugin active and the tax being registered.
 * 		
		if(!term_exists('gn-genre') || !term_exists('gn-location-1') || !term_exists('gn-location-2') || !term_exists('gn-location-3'))
			$this->register_gn_taxonomies();

		$terms = get_terms('gn-genre',array('hide_empty' => false));
		foreach ( $terms as $term ) {
			wp_delete_term(	$term->term_id, 'gn-genre' );
		}
		$terms = get_terms('gn-location-1',array('hide_empty' => false));
		foreach ( $terms as $term ) {
			wp_delete_term(	$term->term_id, 'gn-genre' );
		}
		$terms = get_terms('gn-location-2',array('hide_empty' => false));
		foreach ( $terms as $term ) {
			wp_delete_term(	$term->term_id, 'gn-genre' );
		}
		$terms = get_terms('gn-location-3',array('hide_empty' => false));
		foreach ( $terms as $term ) {
			wp_delete_term(	$term->term_id, 'gn-genre' );
		} */

		delete_option('rewrite_rules');
		
		if ($blog_id)
			error_log('XML Sitemap Feeds settings cleared from site '.$blog_id.'.');
		else
			error_log('XML Sitemap Feeds settings cleared before uninstall.');
	}
}

new XMLSitemapFeed_Uninstall();
