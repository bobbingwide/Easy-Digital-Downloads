<?php

class EDD_DB_Customer_Meta extends EDD_DB {

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   2.6
	*/
	public function __construct() {
		global $wpdb;

		$this->table_name  = $wpdb->prefix . 'edd_customermeta';
		$this->primary_key = 'meta_id';
		$this->version     = '1.0';

		add_action( 'plugins_loaded', array( $this, 'register_table' ), 11 );

	}

	/**
	 * Get table columns and data types
	 *
	 * @access  public
	 * @since   1.7.18
	*/
	public function get_columns() {
		return array(
			'meta_id'     => '%d',
			'customer_id' => '%d',
			'meta_key'    => '%s',
			'meta_value'  => '%s',
		);
	}

	/**
	 * Register the table with $wpdb so the metadata api can find it
	 *
	 * @access  public
	 * @since   2.6
	*/
	public function register_table() {
		global $wpdb;
		$wpdb->customermeta = $this->table_name;
	}

	/**
	 * Retrieve customer meta field for a customer.
	 *
	 * @param   int    $customer_id   Customer ID.
	 * @param   string $meta_key      The meta key to retrieve.
	 * @param   bool   $single        Whether to return a single value.
	 * @return  mixed                 Will be an array if $single is false. Will be value of meta data field if $single is true.
	 *
	 * @access  public
	 * @since   2.6
	 */
	public function get_meta( $customer_id = 0, $meta_key = '', $single = false ) {
		return get_metadata( 'customer', $customer_id, $meta_key, $single );
	}

	/**
	 * Add meta data field to a customer.
	 *
	 * @param   int    $customer_id   Customer ID.
	 * @param   string $meta_key      Metadata name.
	 * @param   mixed  $meta_value    Metadata value.
	 * @param   bool   $unique        Optional, default is false. Whether the same key should not be added.
	 * @return  bool                  False for failure. True for success.
	 *
	 * @access  public
	 * @since   2.6
	 */
	public function add_meta( $customer_id = 0, $meta_key = '', $meta_value, $unique = false ) {
		return add_metadata( 'customer', $customer_id, $meta_key, $meta_value, $unique );
	}

	/**
	 * Update customer meta field based on Customer ID.
	 *
	 * Use the $prev_value parameter to differentiate between meta fields with the
	 * same key and Customer ID.
	 *
	 * If the meta field for the customer does not exist, it will be added.
	 *
	 * @param   int    $customer_id   Customer ID.
	 * @param   string $meta_key      Metadata key.
	 * @param   mixed  $meta_value    Metadata value.
	 * @param   mixed  $prev_value    Optional. Previous value to check before removing.
	 * @return  bool                  False on failure, true if success.
	 *
	 * @access  public
	 * @since   2.6
	 */
	public function update_meta( $customer_id = 0, $meta_key = '', $meta_value, $prev_value = '' ) {
		return update_metadata( 'customer', $customer_id, $meta_key, $meta_value, $prev_value );
	}

	/**
	 * Remove metadata matching criteria from a customer.
	 *
	 * You can match based on the key, or key and value. Removing based on key and
	 * value, will keep from removing duplicate metadata with the same key. It also
	 * allows removing all metadata matching key, if needed.
	 *
	 * @param   int    $customer_id   Customer ID.
	 * @param   string $meta_key      Metadata name.
	 * @param   mixed  $meta_value    Optional. Metadata value.
	 * @return  bool                  False for failure. True for success.
	 *
	 * @access  public
	 * @since   2.6
	 */
	public function delete_meta( $customer_id = 0, $meta_key = '', $meta_value = '' ) {
		return delete_metadata( 'customer', $customer_id, $meta_key, $meta_value );
	}

	/**
	 * Create the table
	 *
	 * @access  public
	 * @since   2.6
	*/
	public function create_table() {

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$sql = "CREATE TABLE {$this->table_name} (
			meta_id bigint(20) NOT NULL AUTO_INCREMENT,
			customer_id bigint(20) NOT NULL DEFAULT '0',
			meta_key varchar(255) DEFAULT NULL,
			meta_value longtext,
			PRIMARY KEY  (meta_id),
			KEY customer_id (customer_id),
			KEY meta_key (meta_key)
			) CHARACTER SET utf8 COLLATE utf8_general_ci;";

		dbDelta( $sql );

		update_option( $this->table_name . '_db_version', $this->version );
	}

}