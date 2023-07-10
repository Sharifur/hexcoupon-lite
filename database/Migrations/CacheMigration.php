<?php

namespace HexCoupon\Database\Migrations;

use CodesVault\Howdyqb\DB;
use HexCoupon\App\Core\Lib\SingleTon;

class CacheMigration
{
	use SingleTon;

	public function __construct()
	{
		DB::create('hexcoupon')
			->column('ID')->bigInt()->unsigned()->autoIncrement()->primary()->required()
			->column('name')->string(255)->required()
			->column('email')->string(255)->default('NULL')
			->index(['ID'])
			->execute();
	}
}
