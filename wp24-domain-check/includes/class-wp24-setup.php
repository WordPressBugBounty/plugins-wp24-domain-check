<?php

/**
 * Class with setup functions.
 */
class WP24_Domain_Check_Setup {

	/**
	 * Check database version and update if necessary.
	 * 
	 * @return void
	 */
	public static function update_database() {
		$options = get_option( 'wp24_domaincheck' );

		if ( ! is_array( $options ) )
			$options = array();

		if ( ! isset( $options['database_version'] ) || 
			version_compare( $options['database_version'], WP24_DOMAIN_CHECK_DATABASE_VERSION ) == -1 ) {
			global $wpdb;

			$charset_collate = $wpdb->get_charset_collate();
			
			$table_name = $wpdb->prefix . 'wp24_whois_queries';
			$sql[] = "CREATE TABLE $table_name (
				id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				limit_group varchar(25) NOT NULL,
				query_time datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
				query_count smallint(5) DEFAULT 1 NOT NULL,
				PRIMARY KEY  (id)
			) $charset_collate;";

			$table_name = $wpdb->prefix . 'wp24_tld_prices_links';
			$sql[] = "CREATE TABLE $table_name (
				tld varchar(25) NOT NULL,
				price varchar(25),
				link text,
				price_transfer varchar(25),
				link_transfer text,
				PRIMARY KEY  (tld)
			) $charset_collate;";

			$table_name = $wpdb->prefix . 'wp24_tld_woocommerce';
			$sql[] = "CREATE TABLE $table_name (
				tld varchar(25) NOT NULL,
				product_id_purchase bigint(20),
				product_id_transfer bigint(20),
				PRIMARY KEY  (tld)
			) $charset_collate;";

			$table_name = $wpdb->prefix . 'wp24_whois_servers';
			$sql[] = "CREATE TABLE $table_name (
				tld varchar(25) NOT NULL,
				host varchar(100),
				status_free varchar(200),
				PRIMARY KEY  (tld)
			) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );

			$options['database_version'] = WP24_DOMAIN_CHECK_DATABASE_VERSION;
			update_option( 'wp24_domaincheck', $options );
		}
	}

	/**
	 * Uninstall plugin.
	 * 
	 * @return void
	 */
	public static function uninstall() {
		global $wpdb;

		// drop tables
		$table_name = $wpdb->prefix . 'wp24_whois_queries';
		$sql = "DROP TABLE IF EXISTS $table_name";
		$wpdb->query( $sql );
		$table_name = $wpdb->prefix . 'wp24_tld_prices_links';
		$sql = "DROP TABLE IF EXISTS $table_name";
		$wpdb->query( $sql );
		$table_name = $wpdb->prefix . 'wp24_tld_woocommerce';
		$sql = "DROP TABLE IF EXISTS $table_name";
		$wpdb->query( $sql );
		$table_name = $wpdb->prefix . 'wp24_whois_servers';
		$sql = "DROP TABLE IF EXISTS $table_name";
		$wpdb->query( $sql );
		
		// delete all settings
		delete_option( 'wp24_domaincheck' );
	}

}
