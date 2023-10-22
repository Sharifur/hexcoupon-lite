<?php

namespace HexCoupon\App\Core;

use HexCoupon\App\Controllers\AdminMenuController;
use HexCoupon\App\Controllers\AjaxApiController;
use HexCoupon\App\Controllers\WooCommerce\Admin\CouponColumTabController;
use HexCoupon\App\Controllers\WooCommerce\Admin\CouponGeneralTabController;
use HexCoupon\App\Controllers\WooCommerce\Admin\CouponSharableUrlTabController;
use HexCoupon\App\Controllers\WooCommerce\Admin\CouponUsageRestrictionTabController;
use HexCoupon\App\Controllers\WooCommerce\Admin\CouponUsageLimitsTabController;
use HexCoupon\App\Controllers\WooCommerce\Admin\CouponGeographicRestrictionTabController;
use HexCoupon\App\Core\Lib\SingleTon;
use HexCoupon\App\Core\WooCommerce\CouponCategory;
use HexCoupon\App\Core\WooCommerce\CouponEmailSMS;
use HexCoupon\App\Core\WooCommerce\CouponPaymentandShipping;
use HexCoupon\App\Core\WooCommerce\CouponSingleGeneralTab;
use HexCoupon\App\Core\WooCommerce\CouponSingleGeographicRestrictions;
use HexCoupon\App\Core\WooCommerce\CouponSingleSharableUrl;
use HexCoupon\App\Core\WooCommerce\CouponSingleUsageRestriction;
use HexCoupon\App\Core\WooCommerce\CouponSingleUsageLimits;
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
			CouponShortcode::class,
			CouponPaymentandShipping::class,
			CouponColumTabController::class,
			CouponSingleGeneralTab::class,
			CouponGeneralTabController::class,
			CouponSingleUsageRestriction::class,
			CouponUsageRestrictionTabController::class,
			CouponSingleUsageLimits::class,
			CouponUsageLimitsTabController::class,
			CouponSingleGeographicRestrictions::class,
			CouponGeographicRestrictionTabController::class,
			CouponSingleSharableUrl::class,
			CouponSharableUrlTabController::class,
			CouponEmailSMS::class,
			AjaxApiController::class
		];
	}
}
