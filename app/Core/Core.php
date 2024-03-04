<?php

namespace HexCoupon\App\Core;

use HexCoupon\App\Controllers\AdminMenuController;
use HexCoupon\App\Controllers\AjaxApiController;
use HexCoupon\App\Controllers\WooCommerce\Admin\CouponGeneralTabController;
use HexCoupon\App\Controllers\WooCommerce\Admin\CouponStartingDateController;
use HexCoupon\App\Controllers\WooCommerce\Admin\PaymentAndShippingTabController;
use HexCoupon\App\Controllers\WooCommerce\Admin\CouponSharableUrlTabController;
use HexCoupon\App\Controllers\WooCommerce\Admin\CouponUsageRestrictionTabController;
use HexCoupon\App\Controllers\WooCommerce\Admin\CouponUsageLimitsTabController;
use HexCoupon\App\Controllers\WooCommerce\Admin\CouponGeographicRestrictionTabController;
use HexCoupon\App\Controllers\WooCommerce\Admin\Bogo\HexcouponBogoController;
use HexCoupon\App\Controllers\WooCommerce\Admin\Bogo\GetSpecificProductForSpecificProduct;
use HexCoupon\App\Controllers\WooCommerce\Admin\Bogo\GetSameProductForSpecificProduct;
use HexCoupon\App\Controllers\WooCommerce\Admin\Bogo\GetCombinationOfProductForSpecificProduct;
use HexCoupon\App\Controllers\WooCommerce\Admin\Bogo\GetProductFromListForSpecificProduct;
use HexCoupon\App\Controllers\WooCommerce\Admin\Bogo\GetSpecificProductForCombinationOfProduct;
use HexCoupon\App\Controllers\WooCommerce\Admin\Bogo\GetCombinationOfProductForCombinationOfProduct;
use HexCoupon\App\Controllers\WooCommerce\Admin\Bogo\GetProductFromListForCombinationOfProduct;
use HexCoupon\App\Controllers\WooCommerce\Admin\Bogo\GetSpecificProductForAnyListedProduct;
use HexCoupon\App\Controllers\WooCommerce\Admin\Bogo\GetCombinationOfProductForAnyListedProduct;
use HexCoupon\App\Controllers\WooCommerce\Admin\Bogo\GetAnyListedProductForAnyListedProduct;
use HexCoupon\App\Controllers\WooCommerce\Admin\Bogo\GetSpecificProductAndCombinationOfProductForProductCategory;
use HexCoupon\App\Controllers\WooCommerce\Admin\Bogo\GetAnyProductFromListForProductCategory;
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
			PaymentAndShippingTabController::class,
			CouponSingleGeneralTab::class,
			CouponGeneralTabController::class,
//			CouponStartingDateController::class,
			CouponSingleGeographicRestrictions::class,
			CouponGeographicRestrictionTabController::class,
			CouponSingleUsageRestriction::class,
			CouponUsageRestrictionTabController::class,
			CouponSingleUsageLimits::class,
			CouponUsageLimitsTabController::class,
			CouponSingleSharableUrl::class,
			CouponSharableUrlTabController::class,
			AjaxApiController::class,
			HexcouponBogoController::class,
			GetSpecificProductForSpecificProduct::class,
			GetSameProductForSpecificProduct::class,
			GetCombinationOfProductForSpecificProduct::class,
			GetProductFromListForSpecificProduct::class,
			GetSpecificProductForCombinationOfProduct::class,
			GetCombinationOfProductForCombinationOfProduct::class,
			GetProductFromListForCombinationOfProduct::class,
			GetSpecificProductForAnyListedProduct::class,
			GetCombinationOfProductForAnyListedProduct::class,
			GetAnyListedProductForAnyListedProduct::class,
			GetSpecificProductAndCombinationOfProductForProductCategory::class,
			GetAnyProductFromListForProductCategory::class,
		];
	}
}
