<?php

namespace HexCoupon\App\Services;

use HexCoupon\App\Core\Lib\SingleTon;

class DeactivationService
{
	use SingleTon;

	public function register()
	{
		// deactivation event handler
		\register_deactivation_hook(
			HXC_FILE,
			[ __CLASS__, 'deactivate' ]
		);
	}

	public static function deactivate()
	{
		?>
		<div class="notice notice-error is-dismissible">
			<p><?php echo esc_html__( 'Plugin is deactivated', 'hexcoupon'); ?></p>
		</div>
		<?php
	}
}
