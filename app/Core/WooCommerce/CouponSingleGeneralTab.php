<?php
namespace HexCoupon\App\Core\WooCommerce;

use HexCoupon\App\Core\Helpers\FormHelpers;
use HexCoupon\App\Core\Helpers\RenderHelpers;
use HexCoupon\App\Core\Lib\SingleTon;

class CouponSingleGeneralTab
{
	use singleton;

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method register
	 * @return mixed
	 * @since 1.0.0
	 * Registers all hooks that are needed.
	 */
	public function register()
	{
		add_action( 'woocommerce_coupon_options', [ $this, 'add_coupon_extra_fields' ] );
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method expiry_date_message_field
	 * @return mixed
	 * @since 1.0.0
	 * Add coupon expiry date message textarea field.
	 */
	private function expiry_date_message_field()
	{
		global $post;

		woocommerce_wp_textarea_input(
			[
				'id' => 'message_for_coupon_expiry_date',
				'label' => '',
				'desc_tip' => true,
				'description' => esc_html__( 'Set a message for customers about the coupon expiry date.', 'hexcoupon' ),
				'placeholder' => esc_html__( 'Message for customer e.g. This coupon has been expired.', 'hexcoupon' ),
				'value' => get_post_meta( $post->ID, 'message_for_coupon_expiry_date', true ),
			]
		);
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method starting_date_field
	 * @return mixed
	 * @since 1.0.0
	 * Add coupon starting date input field.
	 */
	private function starting_date_field()
	{
		global $post;

		woocommerce_wp_text_input(
			[
				'id' => 'coupon_starting_date',
				'label' => esc_html__( 'Coupon starting date', 'hexcoupon' ),
				'desc_tip' => true,
				'description' => esc_html__( 'Set the coupon starting date.', 'hexcoupon' ),
				'type' => 'text',
				'value' => get_post_meta( $post->ID, 'coupon_starting_date', true ),
				'class' => 'date-picker',
				'placeholder' => esc_html( 'YYYY-MM-DD' ),
				'custom_attributes' => [
					'pattern' => apply_filters( 'woocommerce_date_input_html_pattern', '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])' ),
				],
			]
		);
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method starting_date_message_filed
	 * @return mixed
	 * @since 1.0.0
	 * Add coupon starting date input field.
	 */
	private function starting_date_message_filed()
	{
		global $post;

		woocommerce_wp_textarea_input(
			[
				'id' => 'message_for_coupon_starting_date',
				'label' => '',
				'desc_tip' => true,
				'description' => esc_html__( 'Set a message for customers about the coupon starting date.', 'hexcoupon' ),
				'placeholder' => esc_html__( 'Message for customer e.g. This coupon has not been started yet.', 'hexcoupon' ),
				'value' => get_post_meta( $post->ID, 'message_for_coupon_starting_date', true ),
			]
		);
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method add_coupon_apply_date_hours_checkbox
	 * @return mixed
	 * @since 1.0.0
	 * Add coupon day and hours applicable checkbox field.
	 */
	private function add_coupon_apply_date_hours_checkbox()
	{
		global $post;

		$checked = get_post_meta( $post->ID, 'apply_days_hours_of_week', true );
		$checked = ! empty( $checked ) ? 'yes' : '';

		woocommerce_wp_checkbox(
			[
				'id' => 'apply_days_hours_of_week',
				'label' => esc_html__( 'Valid for days/hours', 'hexcoupon' ),
				'description' => esc_html__( 'Check this box to make coupon valid for specific days and hours of the week.', 'hexcoupon' ),
				'value' => $checked,
			]
		);
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method add_coupon_apply_on_saturday_fields
	 * @return mixed
	 * @since 1.0.0
	 * Add coupon apply on saturday all fields.
	 */
	private function add_coupon_apply_on_saturday_fields()
	{
		global $post;

		$sat_coupon_start_time = get_post_meta( $post->ID, 'sat_coupon_start_time', true );
		$sat_coupon_expiry_time = get_post_meta( $post->ID, 'sat_coupon_expiry_time', true );
		?>
		<div class="options_group day_time_hours_block">
			<div class="options_group__saturdayWrap">
				<div class="options_group__saturdayWrap__inner">
					<div class="time-hours-label-checkbox">
						<p class="form-field form-day-field">
                            <span class="day-title">
                                <?php esc_html_e( 'Saturday', 'hexcoupon' ); ?>
                            </span>
						</p>
						<p class="form-field">
                            <span>
                                <label id="coupon_apply_on_saturday_label" for="coupon_apply_on_saturday" class="switch">
                                    <input type="checkbox" name="coupon_apply_on_saturday" id="coupon_apply_on_saturday" value="1" <?php checked( get_post_meta( $post->ID, 'coupon_apply_on_saturday', true ), 1 ); ?>>
                                    <span class="slider round"></span>
                                </label>
                            </span>
						</p>
					</div>

					<div class="time-hours-start-expiry">
						<p class="form-field saturday">
                            <span class="first-input">
                                <input type="text" class="time-picker-saturday" name="sat_coupon_start_time" id="coupon_start_time" value="<?php echo esc_attr( $sat_coupon_start_time ); ?>" placeholder="HH:MM" /><span class="input_separator_saturday">-</span>
                                <input type="text" class="time-picker-saturday" name="sat_coupon_expiry_time" id="coupon_expiry_time" value="<?php echo esc_attr( $sat_coupon_expiry_time ); ?>" placeholder="HH:MM" />

                                <input type="hidden" id="total_hours_count_saturday" name="total_hours_count_saturday" value="<?php $total_hours_count_saturday = intval( get_post_meta( $post->ID, 'total_hours_count_saturday', true ) ); echo esc_attr( $total_hours_count_saturday ); ?>">
                            </span>

							<?php
							for ( $i = 1; $i <= $total_hours_count_saturday; $i++ ) {
								$start_time = get_post_meta( $post->ID, 'sat_coupon_start_time_' . $i, true );
								$expiry_time = get_post_meta( $post->ID, 'sat_coupon_expiry_time_' . $i, true );
								$appendedElement = "<span class='appededItem first-input'>
                                                      <input type='text' class='time-picker-saturday' name='sat_coupon_start_time_".$i."' id='coupon_start_time' value='".$start_time."' placeholder='HH:MM'>
                                                      <span class='input_separator_saturday'>-</span><input type='text' class='time-picker-saturday' name='sat_coupon_expiry_time_".$i."' id='coupon_expiry_time' value='".$expiry_time."' placeholder='HH:MM'>
                                                      <a href='javascript:void(0)' class='cross_hour_saturday cross-hour'>X</a>
                                                    </span>";
								echo $appendedElement;
							}
							?>
						</p>
						<p class="form-field">
							<a id="sat_add_more_hours" href="javascript:void(0)"><?php echo esc_html__( 'Add More Hours', 'hexcoupon' );?></a>
						</p>
					</div>

					<span id="sat_deactivated_text"><?php echo esc_html__( 'Coupon deactivated for Saturday', 'hexcoupon' ); ?></span>
				</div>
			</div>
			<span class="time-hours-border-bottom"></span>
		</div>
		<?php
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method add_coupon_apply_on_sunday_fields
	 * @return mixed
	 * @since 1.0.0
	 * Add coupon apply on sunday all fields.
	 */
	private function add_coupon_apply_on_sunday_fields()
	{
		global $post;

		$sun_coupon_start_time = get_post_meta( $post->ID, 'sun_coupon_start_time', true );
		$sun_coupon_expiry_time = get_post_meta( $post->ID, 'sun_coupon_expiry_time', true );
		?>
		<div class="options_group day_time_hours_block">
			<div class="options_group__saturdayWrap">
				<div class="options_group__saturdayWrap__inner">
					<div class="time-hours-label-checkbox">
						<p class="form-field form-day-field">
                            <span class="day-title">
                                <?php esc_html_e( 'Sunday', 'hexcoupon' ); ?>
                            </span>
						</p>
						<p class="form-field">
                        <span>
                           <label id="coupon_apply_on_sunday_label" for="coupon_apply_on_sunday" class="switch">
                                <input type="checkbox" name="coupon_apply_on_sunday" id="coupon_apply_on_sunday" value="1" <?php checked( get_post_meta( $post->ID, 'coupon_apply_on_sunday', true ), 1 ); ?>>
                                <span class="slider round"></span>
                            </label>
                        </span>
						</p>
					</div>
					<div class="time-hours-start-expiry">
						<p class="form-field sunday">
                            <span class="first-input">
                                <input type="text" class="time-picker-sunday" name="sun_coupon_start_time" id="coupon_start_time" value="<?php echo esc_attr( $sun_coupon_start_time ); ?>" placeholder="HH:MM" /><span class="input_separator_sunday">-</span>
                                <input type="text" class="time-picker-sunday" name="sun_coupon_expiry_time" id="coupon_expiry_time" value="<?php echo esc_attr( $sun_coupon_expiry_time ); ?>" placeholder="HH:MM" />

                                <input type="hidden" id="total_hours_count_sunday" name="total_hours_count_sunday" value="<?php $total_hours_count_sunday = intval( get_post_meta( $post->ID, 'total_hours_count_sunday', true ) ); echo esc_attr( $total_hours_count_sunday); ?>">
                            </span>

							<?php
							for ( $i = 1; $i <= $total_hours_count_sunday; $i++ ) {
								$start_time = get_post_meta( $post->ID, 'sun_coupon_start_time_' . $i, true );
								$expiry_time = get_post_meta( $post->ID, 'sun_coupon_expiry_time_' . $i, true );
								$appendedElement = "<span class='appededItem first-input'>
                                                      <input type='text' class='time-picker-sunday' name='sun_coupon_start_time_".$i."' id='coupon_start_time' value='".$start_time."' placeholder='HH:MM'>
                                                      <span class='input_separator_sunday'>-</span><input type='text' class='time-picker-sunday' name='sun_coupon_expiry_time_".$i."' id='coupon_expiry_time' value='".$expiry_time."' placeholder='HH:MM'>
                                                      <a href='javascript:void(0)' class='cross_hour_sunday cross-hour'>X</a>
                                                    </span>";
								echo $appendedElement;
							}
							?>
						</p>
						<p class="form-field">
							<a id="sun_add_more_hours" href="javascript:void(0)"><?php echo esc_html__( 'Add More Hours', 'hexcoupon' );?></a>
						</p>
					</div>
					<span id="sun_deactivated_text"><?php echo esc_html__( 'Coupon deactivated for Sunday', 'hexcoupon' ); ?></span>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method add_coupon_apply_on_monday_fields
	 * @return mixed
	 * @since 1.0.0
	 * Add coupon apply on monday all fields.
	 */
	private function add_coupon_apply_on_monday_fields()
	{
		global $post;

		$mon_coupon_start_time = get_post_meta( $post->ID, 'mon_coupon_start_time', true );
		$mon_coupon_expiry_time = get_post_meta( $post->ID, 'mon_coupon_expiry_time', true );
		?>
		<div class="options_group day_time_hours_block">
			<div class="options_group__saturdayWrap">
				<div class="options_group__saturdayWrap__inner">
					<div class="time-hours-label-checkbox">
						<p class="form-field form-day-field">
                            <span class="day-title">
                                <?php esc_html_e( 'Monday', 'hexcoupon' ); ?>
                            </span>
						</p>
						<p class="form-field">
                        <span>
                           <label id="coupon_apply_on_monday_label" for="coupon_apply_on_monday" class="switch">
                                <input type="checkbox" name="coupon_apply_on_monday" id="coupon_apply_on_monday" value="1" <?php checked( get_post_meta( $post->ID, 'coupon_apply_on_monday', true ), 1 ); ?>>
                                <span class="slider round"></span>
                            </label>
                        </span>
						</p>
					</div>
					<div class="time-hours-start-expiry">
						<p class="form-field monday">
                            <span class="first-input">
                                <input type="text" class="time-picker-monday" name="mon_coupon_start_time" id="coupon_start_time" value="<?php echo esc_attr( $mon_coupon_start_time ); ?>" placeholder="HH:MM" /><span class="input_separator_monday">-</span>
                                <input type="text" class="time-picker-monday" name="mon_coupon_expiry_time" id="coupon_expiry_time" value="<?php echo esc_attr( $mon_coupon_expiry_time ); ?>" placeholder="HH:MM" />

                                <input type="hidden" id="total_hours_count_monday" name="total_hours_count_monday" value="<?php $total_hours_count_monday = intval( get_post_meta( $post->ID, 'total_hours_count_monday', true ) ); echo esc_attr( $total_hours_count_monday ); ?>">
                            </span>

							<?php
							for ( $i = 1; $i <= $total_hours_count_monday; $i++ ) {
								$start_time = get_post_meta( $post->ID, 'mon_coupon_start_time_' . $i, true );
								$expiry_time = get_post_meta( $post->ID, 'mon_coupon_expiry_time_' . $i, true );
								$appendedElement = "<span class='appededItem first-input'>
                                                      <input type='text' class='time-picker-monday' name='mon_coupon_start_time_".$i."' id='coupon_start_time' value='".$start_time."' placeholder='HH:MM'>
                                                      <span class='input_separator_monday'>-</span><input type='text' class='time-picker-monday' name='mon_coupon_expiry_time_".$i."' id='coupon_expiry_time' value='".$expiry_time."' placeholder='HH:MM'>
                                                      <a href='javascript:void(0)' class='cross_hour_monday cross-hour'>X</a>
                                                    </span>";
								echo $appendedElement;
							}
							?>
						</p>
						<p class="form-field">
							<a id="mon_add_more_hours" href="javascript:void(0)"><?php echo esc_html__( 'Add More Hours', 'hexcoupon' );?></a>
						</p>
					</div>
					<span id="mon_deactivated_text"><?php echo esc_html__( 'Coupon deactivated for Monday', 'hexcoupon' ); ?></span>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method add_coupon_apply_on_tuesday_fields
	 * @return mixed
	 * @since 1.0.0
	 * Add coupon apply on tuesday all fields.
	 */
	private function add_coupon_apply_on_tuesday_fields()
	{
		global $post;

		$tue_coupon_start_time = get_post_meta( $post->ID, 'tue_coupon_start_time', true );
		$tue_coupon_expiry_time = get_post_meta( $post->ID, 'tue_coupon_expiry_time', true );
		?>
		<div class="options_group day_time_hours_block">
			<div class="options_group__saturdayWrap">
				<div class="options_group__saturdayWrap__inner">
					<div class="time-hours-label-checkbox">
						<p class="form-field form-day-field">
                            <span class="day-title">
                                <?php esc_html_e( 'Tuesday', 'hexcoupon' ); ?>
                            </span>
						</p>
						<p class="form-field">
                        <span>
                           <label id="coupon_apply_on_tuesday_label" for="coupon_apply_on_tuesday" class="switch">
                                <input type="checkbox" name="coupon_apply_on_tuesday" id="coupon_apply_on_tuesday" value="1" <?php checked( get_post_meta( $post->ID, 'coupon_apply_on_tuesday', true ), 1 ); ?>>
                                <span class="slider round"></span>
                            </label>
                        </span>
						</p>
					</div>
					<div class="time-hours-start-expiry">
						<p class="form-field tuesday">
                           <span class="first-input">
                                <input type="text" class="time-picker-tuesday" name="tue_coupon_start_time" id="coupon_start_time" value="<?php echo esc_attr( $tue_coupon_start_time ); ?>" placeholder="HH:MM" /><span class="input_separator_tuesday">-</span>
                                <input type="text" class="time-picker-tuesday" name="tue_coupon_expiry_time" id="coupon_expiry_time" value="<?php echo esc_attr( $tue_coupon_expiry_time ); ?>" placeholder="HH:MM" />

                                <input type="hidden" id="total_hours_count_tuesday" name="total_hours_count_tuesday" value="<?php $total_hours_count_tuesday = intval( get_post_meta( $post->ID, 'total_hours_count_tuesday', true ) ); echo esc_attr( $total_hours_count_tuesday ); ?>">
                           </span>

							<?php
							for ( $i = 1; $i <= $total_hours_count_tuesday; $i++ ) {
								$start_time = get_post_meta( $post->ID, 'tue_coupon_start_time_' . $i, true );
								$expiry_time = get_post_meta( $post->ID, 'tue_coupon_expiry_time_' . $i, true );
								$appendedElement = "<span class='appededItem first-input'>
                                                      <input type='text' class='time-picker-tuesday' name='tue_coupon_start_time_".$i."' id='coupon_start_time' value='".$start_time."' placeholder='HH:MM'>
                                                      <span class='input_separator_tuesday'>-</span><input type='text' class='time-picker-tuesday' name='tue_coupon_expiry_time_".$i."' id='coupon_expiry_time' value='".$expiry_time."' placeholder='HH:MM'>
                                                      <a href='javascript:void(0)' class='cross_hour_tuesday cross-hour'>X</a>
                                                    </span>";
								echo $appendedElement;
							}
							?>
						</p>
						<p class="form-field">
							<a id="tue_add_more_hours" href="javascript:void(0)"><?php echo esc_html__( 'Add More Hours', 'hexcoupon' );?></a>
						</p>
					</div>
					<span id="tue_deactivated_text"><?php echo esc_html__( 'Coupon deactivated for Tuesday', 'hexcoupon' ); ?></span>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method add_coupon_apply_on_wednesday_fields
	 * @return mixed
	 * @since 1.0.0
	 * Add coupon apply on wednesday all fields.
	 */
	private function add_coupon_apply_on_wednesday_fields()
	{
		global $post;

		$wed_coupon_start_time = get_post_meta( $post->ID, 'wed_coupon_start_time', true );
		$wed_coupon_expiry_time = get_post_meta( $post->ID, 'wed_coupon_expiry_time', true );
		?>
		<div class="options_group day_time_hours_block">
			<div class="options_group__saturdayWrap">
				<div class="options_group__saturdayWrap__inner">
					<div class="time-hours-label-checkbox">
						<p class="form-field form-day-field">
                            <span class="day-title">
                                <?php esc_html_e( 'Wednesday', 'hexcoupon' ); ?>
                            </span>
						</p>
						<p class="form-field">
                        <span>
                           <label id="coupon_apply_on_wednesday_label" for="coupon_apply_on_wednesday" class="switch">
                                <input type="checkbox" name="coupon_apply_on_wednesday" id="coupon_apply_on_wednesday" value="1" <?php checked( get_post_meta( $post->ID, 'coupon_apply_on_wednesday', true ), 1 ); ?>>
                                <span class="slider round"></span>
                            </label>
                        </span>
						</p>
					</div>
					<div class="time-hours-start-expiry">
						<p class="form-field wednesday">
                        <span class="first-input">
                            <input type="text" class="time-picker-wednesday" name="wed_coupon_start_time" id="coupon_start_time" value="<?php echo esc_attr( $wed_coupon_start_time ); ?>" placeholder="HH:MM" /><span class="input_separator_wednesday">-</span>
                            <input type="text" class="time-picker-wednesday" name="wed_coupon_expiry_time" id="coupon_expiry_time" value="<?php echo esc_attr( $wed_coupon_expiry_time ); ?>" placeholder="HH:MM" />

                            <input type="hidden" id="total_hours_count_wednesday" name="total_hours_count_wednesday" value="<?php $total_hours_count_wednesday = intval( get_post_meta( $post->ID, 'total_hours_count_wednesday', true ) ); echo esc_attr( $total_hours_count_wednesday ); ?>">
                        </span>

							<?php
							for ($i = 1; $i <= $total_hours_count_wednesday; $i++) {
								$start_time = get_post_meta( $post->ID, 'we_coupon_start_time_' . $i, true );
								$expiry_time = get_post_meta( $post->ID, 'wed_coupon_expiry_time_' . $i, true );
								$appendedElement = "<span class='appededItem first-input'>
                                                      <input type='text' class='time-picker-wednesday' name='wed_coupon_start_time_".$i."' id='coupon_start_time' value='".$start_time."' placeholder='HH:MM'>
                                                      <span class='input_separator_wednesday'>-</span><input type='text' class='time-picker-wednesday' name='wed_coupon_expiry_time_".$i."' id='coupon_expiry_time' value='".$expiry_time."' placeholder='HH:MM'>
                                                      <a href='javascript:void(0)' class='cross_hour_wednesday cross-hour'>X</a>
                                                    </span>";
								echo $appendedElement;
							}
							?>
						</p>
						<p class="form-field">
							<a id="wed_add_more_hours" href="javascript:void(0)"><?php echo esc_html__( 'Add More Hours', 'hexcoupon' );?></a>
						</p>
					</div>
					<span id="wed_deactivated_text"><?php echo esc_html__( 'Coupon deactivated for Wednesday', 'hexcoupon' ); ?></span>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method add_coupon_apply_on_thursday_fields
	 * @return mixed
	 * @since 1.0.0
	 * Add coupon apply on thursday all fields.
	 */
	private function add_coupon_apply_on_thursday_fields()
	{
		global $post;

		$thu_coupon_start_time = get_post_meta( $post->ID, 'thu_coupon_start_time', true );
		$thu_coupon_expiry_time = get_post_meta( $post->ID, 'thu_coupon_expiry_time', true );
		?>
		<div class="options_group day_time_hours_block">
			<div class="options_group__saturdayWrap">
				<div class="options_group__saturdayWrap__inner">
					<div class="time-hours-label-checkbox">
						<p class="form-field form-day-field">
                            <span class="day-title">
                                <?php esc_html_e( 'Thursday', 'hexcoupon' ); ?>
                            </span>
						</p>
						<p class="form-field">
                        <span>
                            <label id="coupon_apply_on_thursday_label" for="coupon_apply_on_thursday" class="switch">
                                <input type="checkbox" name="coupon_apply_on_thursday" id="coupon_apply_on_thursday" value="1" <?php checked( get_post_meta( $post->ID, 'coupon_apply_on_thursday', true ), 1 ); ?>>
                                <span class="slider round"></span>
                            </label>
                        </span>
						</p>
					</div>
					<div class="time-hours-start-expiry">
						<p class="form-field thursday">
                        <span class="first-input">
                                <input type="text" class="time-picker-thursday" name="thu_coupon_start_time" id="coupon_start_time" value="<?php echo esc_attr( $thu_coupon_start_time ); ?>" placeholder="HH:MM" /><span class="input_separator_thursday">-</span>
                                <input type="text" class="time-picker-thursday" name="thu_coupon_expiry_time" id="coupon_expiry_time" value="<?php echo esc_attr( $thu_coupon_expiry_time ); ?>" placeholder="HH:MM" />

                                <input type="hidden" id="total_hours_count_thursday" name="total_hours_count_thursday" value="<?php $total_hours_count_thursday = intval( get_post_meta( $post->ID, 'total_hours_count_thursday', true ) ); echo esc_attr( $total_hours_count_thursday ); ?>">
                        </span>

							<?php
							for ( $i = 1; $i <= $total_hours_count_thursday; $i++ ) {
								$start_time = get_post_meta( $post->ID, 'thu_coupon_start_time_' . $i, true );
								$expiry_time = get_post_meta( $post->ID, 'thu_coupon_expiry_time_' . $i, true );
								$appendedElement = "<span class='appededItem first-input'>
                                                      <input type='text' class='time-picker-thursday' name='thu_coupon_start_time_".$i."' id='coupon_start_time' value='".$start_time."' placeholder='HH:MM'>
                                                      <span class='input_separator_thursday'>-</span><input type='text' class='time-picker-thursday' name='thu_coupon_expiry_time_".$i."' id='coupon_expiry_time' value='".$expiry_time."' placeholder='HH:MM'>
                                                      <a href='javascript:void(0)' class='cross_hour_thursday cross-hour'>X</a>
                                                    </span>";
								echo $appendedElement;
							}
							?>
						</p>
						<p class="form-field">
							<a id="thu_add_more_hours" href="javascript:void(0)"><?php echo esc_html__( 'Add More Hours', 'hexcoupon' );?></a>
						</p>
					</div>
					<span id="thu_deactivated_text"><?php echo esc_html__( 'Coupon deactivated for Thursday', 'hexcoupon' ); ?></span>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method add_coupon_apply_on_friday_fields
	 * @return mixed
	 * @since 1.0.0
	 * Add coupon apply on friday all fields.
	 */
	private function add_coupon_apply_on_friday_fields()
	{
		global $post;

		$fri_coupon_start_time = get_post_meta( $post->ID, 'fri_coupon_start_time', true );
		$fri_coupon_expiry_time = get_post_meta( $post->ID, 'fri_coupon_expiry_time', true );
		?>
		<div class="options_group day_time_hours_block">
			<div class="options_group__saturdayWrap">
				<div class="options_group__saturdayWrap__inner">
					<div class="time-hours-label-checkbox">
						<p class="form-field form-day-field">
                            <span class="day-title">
                                <?php esc_html_e( 'Friday', 'hexcoupon' ); ?>
                            </span>
						</p>
						<p class="form-field">
                        <span>
                            <label id="coupon_apply_on_friday_label" for="coupon_apply_on_friday" class="switch">
                                <input type="checkbox" name="coupon_apply_on_friday" id="coupon_apply_on_friday" value="1" <?php checked( get_post_meta( $post->ID, 'coupon_apply_on_friday', true ), 1 ); ?>>
                                <span class="slider round"></span>
                            </label>
                        </span>
						</p>
					</div>
					<div class="time-hours-start-expiry">
						<p class="form-field friday">
                        <span class="first-input">
                            <input type="text" class="time-picker-friday" name="fri_coupon_start_time" id="coupon_start_time" value="<?php echo esc_attr( $fri_coupon_start_time ); ?>" placeholder="HH:MM" /><span class="input_separator_friday">-</span>
                            <input type="text" class="time-picker-friday" name="fri_coupon_expiry_time" id="coupon_expiry_time" value="<?php echo esc_attr( $fri_coupon_expiry_time ); ?>" placeholder="HH:MM" />

                            <input type="hidden" id="total_hours_count_friday" name="total_hours_count_friday" value="<?php $total_hours_count_friday = intval( get_post_meta( $post->ID, 'total_hours_count_friday', true ) ); echo esc_attr( $total_hours_count_friday ); ?>">
                        </span>

							<?php
							for ( $i = 1; $i <= $total_hours_count_friday; $i++ ) {
								$start_time = get_post_meta( $post->ID, 'fri_coupon_start_time_' . $i, true );
								$expiry_time = get_post_meta( $post->ID, 'fri_coupon_expiry_time_' . $i, true );
								$appendedElement = "<span class='appededItem first-input'>
                                                  <input type='text' class='time-picker-friday' name='fri_coupon_start_time_".$i."' id='coupon_start_time' value='".$start_time."' placeholder='HH:MM'>
                                                  <span class='input_separator_friday'>-</span><input type='text' class='time-picker-friday' name='fri_coupon_expiry_time_".$i."' id='coupon_expiry_time' value='".$expiry_time."' placeholder='HH:MM'>
                                                  <a href='javascript:void(0)' class='cross_hour_friday cross-hour'>X</a>
                                                </span>";
								echo $appendedElement;
							}
							?>
						</p>
						<p class="form-field">
							<a id="fri_add_more_hours" href="javascript:void(0)"><?php echo esc_html__( 'Add More Hours', 'hexcoupon' );?></a>
						</p>
					</div>
					<span id="fri_deactivated_text"><?php echo esc_html__( 'Coupon deactivated for Friday', 'hexcoupon' ); ?></span>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * @package hexcoupon
	 * @author WpHex
	 * @method add_coupon_extra_fields
	 * @return mixed
	 * @since 1.0.0
	 * Add coupon expiry date message textarea field.
	 */
	public function add_coupon_extra_fields()
	{
		// Textarea message field for coupon expiry date
		$this->expiry_date_message_field();

		// Coupon starting date input field
		$this->starting_date_field();

		// Textarea message field for coupon starting date
		$this->starting_date_message_filed();

		// Add coupon apply date and hours checkbox
		$this->add_coupon_apply_date_hours_checkbox();

		// Add apply on saturday fields
		$this->add_coupon_apply_on_saturday_fields();

		// Add apply on sunday fields
		$this->add_coupon_apply_on_sunday_fields();

		// Add apply on monday fields
		$this->add_coupon_apply_on_monday_fields();

		// Add apply on tuesday fields
		$this->add_coupon_apply_on_tuesday_fields();

		// Add apply on wednesday fields
		$this->add_coupon_apply_on_wednesday_fields();

		// Add apply on thursday fields
		$this->add_coupon_apply_on_thursday_fields();

		// Add apply on friday fields
		$this->add_coupon_apply_on_friday_fields();
	}
}
