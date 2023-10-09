<?php

namespace HexCoupon\App\Services;

use HexCoupon\App\Core\Lib\SingleTon;
use HexCoupon\Database\Migrations\CacheMigration;

class ActivationService
{
	use SingleTon;

	public function register()
	{
		// activation event handler
		\register_activation_hook(
			HEXCOUPON_FILE,
			[ __CLASS__, 'activate' ]
		);
	}

	public static function activate()
	{
		CacheMigration::getInstance();
	}
}
