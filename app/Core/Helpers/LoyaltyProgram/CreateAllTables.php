<?php
namespace HexCoupon\App\Core\Helpers\LoyaltyProgram;
use HexCoupon\App\Core\Lib\SingleTon;

class CreateAllTables
{
	use SingleTon;

	private $wpdb;
	private $table_name;

	private $pointsForSignup;

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @since 1.0.0
	 * @method create_points_transactions_table
	 * @return void
	 * Creating table called 'hex_points_transactions' table
	 */
	public function create_points_transactions_table()
	{
		$table_name = $this->wpdb->prefix . 'hex_loyalty_program_points';
		$charset_collate = $this->wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL,
        points int(11) NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate ENGINE=InnoDB;";

		$this->wpdb->query( $sql );
	}

}
