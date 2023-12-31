<?php

namespace HexCoupon\App\Core;

use HexCoupon\App\Controllers\AdminMenuController;
use HexCoupon\App\Controllers\WooCommerce\Admin\CouponColumTabController;
use HexCoupon\App\Controllers\WooCommerce\Admin\CouponGeneralTabController;
use HexCoupon\App\Controllers\WooCommerce\Admin\CouponSharableUrlTabController;
use HexCoupon\App\Controllers\WooCommerce\Admin\CouponUsageRestrictionTabController;
use HexCoupon\App\Controllers\WooCommerce\Admin\CouponUsageLimitsTabController;
use HexCoupon\App\Controllers\WooCommerce\Admin\CouponGeographicRestrictionTabController;
use HexCoupon\App\Core\Lib\SingleTon;
use HexCoupon\App\Core\WooCommerce\CouponCategory;
use HexCoupon\App\Core\WooCommerce\CouponPaymentandShipping;
use HexCoupon\App\Core\WooCommerce\CouponSingleGeneralTab;
use HexCoupon\App\Core\WooCommerce\CouponSingleGeographicRestrictions;
use HexCoupon\App\Core\WooCommerce\CouponSingleSharableUrl;
use HexCoupon\App\Core\WooCommerce\CouponSingleUsageRestriction;
use HexCoupon\App\Core\WooCommerce\CouponSingleUsageLimits;
use HexCoupon\App\Core\WooCommerce\CouponDuplicatePost;
use HexCoupon\App\Core\WooCommerce\CouponShortcode;
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
			MyAccount::class,
			CouponCategory::class,
			CouponDuplicatePost::class,
			CouponShortcode::class,
			CouponColumTabController::class,
			CouponGeneralTabController::class,
			CouponUsageRestrictionTabController::class,
			CouponUsageLimitsTabController::class,
			CouponGeographicRestrictionTabController::class,
			CouponPaymentandShipping::class,
			CouponSingleGeneralTab::class,
			CouponSingleUsageRestriction::class,
			CouponSingleUsageLimits::class,
			CouponSingleGeographicRestrictions::class,
			CouponSingleSharableUrl::class,
			CouponSharableUrlTabController::class,
		];
	}
}
