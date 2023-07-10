<?php

namespace HexCoupon\App\Core;

use HexCoupon\App\Controllers\AdminMenuController;
use HexCoupon\App\Controllers\WooCommerce\Admin\CouponColumTabController;
use HexCoupon\App\Core\Lib\SingleTon;
use HexCoupon\App\Core\WooCommerce\CartPage;
use HexCoupon\App\Core\WooCommerce\CouponCategory;
use HexCoupon\App\Core\WooCommerce\CouponSingleCustomTab;
use HexCoupon\App\Core\WooCommerce\MyAccount;
use HexCoupon\App\Services\ActivationService;
use HexCoupon\App\Services\DeactivationService;
use Kathamo\Framework\Lib\BootManager;

final class Core extends BootManager
{
	use SingleTon;

	protected function registerList()
	{
		/**
		 * need to resgiter controller
		 * */
		$this->registerList = [
			ActivationService::class,
			DeactivationService::class,
			AssetsManager::class,
			AdminMenuController::class,
			AdminNoticeManager::class,
			CartPage::class,
			MyAccount::class,
			CouponCategory::class,
			CouponSingleCustomTab::class,
			CouponColumTabController::class
		];
	}
}
