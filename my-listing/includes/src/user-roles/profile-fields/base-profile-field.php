<?php
/**
 * Base field class which can be extended to construct field types
 * for the user profile fields.
 *
 * @since 2.5
 */

namespace MyListing\Src\User_Roles\Profile_Fields;

if ( ! defined('ABSPATH') ) {
	exit;
}

abstract class Base_Profile_Field implements \JsonSerializable {

	/**
	 * Slugified string used to identify a field. Alias of `$this->props['slug']`
	 *
	 * @since 2.5
	 */
	public $key;

	/**
	 * User object which this field belongs to.
	 *
	 * @since 2.5
	 */
	public $user;

	/**
	 * The current form this field is being used at.
	 * Possible values: static::FORM_REGISTER, static::FORM_ACCOUNT_DETAILS
	 *
	 * @since 2.5
	 */
	public $form;

	const FORM_REGISTER = 'register';
	const FORM_ACCOUNT_DETAILS = 'account-details';

	/**
	 * Stores the field value retrieved from the current request.
	 *
	 * @since 2.5
	 */
	private $posted_value;

	/**
	 * List of field properties/configuration. Values below are available for
	 * all field types, but there can be additional props for specific field types.
	 *
	 * @since 2.5
	 */
	public $props = [
		'type' => 'text',
		'slug' => 'custom-field',
		'label' => 'Custom Field',
		'description' => '',
		'required' => false,
		'show_in_register_form' => true,
		'show_in_account_details' => true,
	];

	public function __construct( $props = [] ) {
		$this->field_props();

		// override props if any provided as a parameter
		foreach ( $props as $key => $value ) {
			if ( isset( $this->props[ $key ] ) ) {
				$this->props[ $key ] = $value;
			}
		}

		$this->key = $this->props['slug'];
	}

	/**
	 * Get and sanitize the posted field value in user registration form and
	 * account details form.
	 *
	 * @since 2.5
	 * @return sanitized field value
	 */
	abstract protected function get_posted_value();

	/**
	 * Memoizes `get_posted_value`.
	 *
	 * @since 2.5
	 */
	public function the_posted_value() {
		if ( ! is_null( $this->posted_value ) ) {
			return $this->posted_value;
		}

		$this->posted_value = $this->get_posted_value();
		return $this->posted_value;
	}

	/**
	 * Validate the field value after submission in user registration form and
	 * account details form. If no excpetions are thrown, validation is successful.
	 *
	 * @since 2.5
	 * @return void
	 */
	abstract protected function validate();

	/**
	 * After the field value has been validated successfully, update user with the
	 * field value. By default, it will store the value in `wp_usermeta` table.
	 *
	 * @since 2.5
	 */
	public function update() {
		// storing these fields in the registration form is handled by woocommerce
		if ( $this->form === static::FORM_REGISTER && in_array( $this->key, ['username', 'password', 'email'], true ) ) {
			return;
		}

		// handle display_name in register form
		if ( $this->form === static::FORM_REGISTER && $this->key === 'display_name' ) {
			wp_update_user( [
				'ID' => $this->user->ID,
				'display_name' => $this->the_posted_value(),
			] );
			return;
		}

		// storing these fields in the account details form is handled by woocommerce
		if ( $this->form === static::FORM_ACCOUNT_DETAILS && in_array( $this->key, [
			'first_name',
			'last_name',
			'display_name',
			'email',
			'password',
			'username', // username cannot be edited in account details
		], true ) ) {
			return;
		}

		$value = $this->the_posted_value();
		update_user_meta( $this->user->ID, $this->key, $value );
	}

	/**
	 * Get the field value from database. By default, it will be retrieved from `wp_usermeta`
	 * table. If the `update` method has been overriden to save in a different format, then
	 * this method must be overridden as well to get the value from there.
	 *
	 * @since 2.5
	 */
	public function get_value() {
		if ( ! $this->user ) {
			return;
		}

		if ( $this->key === 'username' ) {
			return $this->user->user_login;
		} elseif ( $this->key === 'email' ) {
			return $this->user->user_email;
		} elseif ( $this->key === 'first_name' ) {
			return $this->user->first_name;
		} elseif ( $this->key === 'last_name' ) {
			return $this->user->last_name;
		} elseif ( $this->key === 'display_name' ) {
			return $this->user->display_name;
		} else {
			return get_user_meta( $this->user->ID, $this->key, true );
		}
	}

	/**
	 * Used to override the default props or set new ones. `$this->props['type']` must
	 * be set for every field type that extends this class.
	 *
	 * @since 2.5
	 */
	abstract protected function field_props();

	/**
	 * Get the markup for field settings to be shown in the user role editor.
	 *
	 * @since 2.5
	 */
	abstract protected function get_editor_options();

	/**
	 * Get the markup for rendering field in register/account details forms.
	 *
	 * @since 2.5
	 */
	abstract public function get_form_markup();

	/**
	 * Print the editor settings markup.
	 *
	 * @since 2.5
	 */
	final public function print_editor_options() {
		ob_start(); ?>
		<div class="field-settings-wrapper" v-if="field.type === '<?php echo esc_attr( $this->get_type() ) ?>'">
			<?php $this->get_editor_options(); ?>
		</div>
		<?php return ob_get_clean();
	}

	/**
	 * When an object of this type is serialized, simply output its props.
	 *
	 * @since 2.5
	 */
	public function jsonSerialize() {
		return $this->props;
	}

	/**
	 * Validate common rules among all field types,
	 * then run the unique validations for each field.
	 *
	 * @since 2.1
	 */
	public function check_validity() {
		$value = $this->the_posted_value();

		// required field check
		// 0, '0', and 0.0 need special handling since they're valid, but PHP considers them falsy values.
		if ( $this->is_required() && ( empty( $value ) && ! in_array( $value, [ 0, '0', 0.0 ], true ) ) ) {
			// translators: Placeholder %s is the label for the required field.
			throw new \Exception( sprintf(
				_x( '%s is a required field.', 'User details', 'my-listing' ),
				$this->get_label()
			) );
		}

		// if field isn't required, then no validation is needed for empty values
		if ( empty( $value ) ) {
			return;
		}

		// otherwise, run validations
		$this->validate();
	}

	/**
	 * Set the user for this field if available.
	 *
	 * @since 2.5
	 */
	public function set_user( $user ) {
		$this->user = $user;
	}

	public function get_type() {
		return $this->props['type'];
	}

	public function get_key() {
		return $this->key;
	}

	public function get_form_key() {
		$form_key = $this->get_key();

		// set the field key expected by woocommerce in the account details form for default fields
		if ( $this->form === static::FORM_ACCOUNT_DETAILS ) {
			if ( in_array( $form_key, ['first_name', 'last_name', 'display_name', 'email'], true ) ) {
				$form_key = 'account_'.$form_key;
			}
		}

		return $form_key;
	}

	public function get_label() {
		return $this->props['label'];
	}

	public function get_description() {
		return $this->props['description'];
	}

	public function is_required() {
		return (bool) $this->props['required'];
	}

	public function get_prop( $prop ) {
		if ( ! isset( $this->props[ $prop ] ) ) {
			return false;
		}

		return $this->props[ $prop ];
	}

	public function get_props() {
		return $this->props;
	}

	/**
	 * Validation helpers
	 */
	protected function validate_minlength( $strip_tags = false ) {
		$value = $this->the_posted_value();
		if ( $strip_tags ) {
			$value = wp_strip_all_tags( $value );
		}

		if ( is_numeric( $this->props['minlength'] ) && mb_strlen( $value ) < $this->props['minlength'] ) {
			// translators: %1$s is the field label; %2%s is the minimum characters allowed.
			throw new \Exception( sprintf(
				_x( '%1$s can\'t be shorter than %2$s characters.', 'User details', 'my-listing' ),
				$this->props['label'],
				absint( $this->props['minlength'] )
			) );
		}
	}

	protected function validate_maxlength( $strip_tags = false ) {
		$value = $this->the_posted_value();
		if ( $strip_tags ) {
			$value = wp_strip_all_tags( $value );
		}

		if ( is_numeric( $this->props['maxlength'] ) && mb_strlen( $value ) > $this->props['maxlength'] ) {
			// translators: %1$s is the field label; %2%s is the maximum characters allowed.
			throw new \Exception( sprintf(
				_x( '%1$s can\'t be longer than %2$s characters.', 'User details', 'my-listing' ),
				$this->props['label'],
				absint( $this->props['maxlength'] )
			) );
		}
	}

	protected function validate_email() {
		$value = $this->the_posted_value();
		if ( ! filter_var( $value, FILTER_VALIDATE_EMAIL ) ) {
			// translators: Placeholder %s is the label for the required field.
			throw new \Exception( sprintf(
				_x( '%s must be a valid email address.', 'User details', 'my-listing' ),
				$this->props['label']
			) );
		}
	}

	/**
	 * Editor settings markup helpers
	 */

	protected function get_label_option() { ?>
		<div class="form-group">
			<label>Label</label>
			<input type="text" v-model="field.label">
		</div>
	<?php }

	protected function get_description_option() { ?>
		<div class="form-group">
			<label>Description</label>
			<input type="text" v-model="field.description">
		</div>
	<?php }

	protected function get_minlength_option() { ?>
		<div class="form-group">
			<label>Min length (characters)</label>
			<input type="number" v-model="field.minlength">
		</div>
	<?php }

	protected function get_maxlength_option() { ?>
		<div class="form-group">
			<label>Max length (characters)</label>
			<input type="number" v-model="field.maxlength">
		</div>
	<?php }

	protected function get_required_option() { ?>
		<div class="form-group" v-if="!isFieldRequired(field)">
			<label>Required field</label>
			<label class="form-switch mb0">
				<input type="checkbox" v-model="field.required">
				<span class="switch-slider"></span>
			</label>
		</div>
	<?php }

	protected function get_show_in_register_option() { ?>
		<div class="form-group" v-if="!isFieldRequired(field)">
			<label>Show in user registration form</label>
			<label class="form-switch mb0">
				<input type="checkbox" v-model="field.show_in_register_form">
				<span class="switch-slider"></span>
			</label>
		</div>
	<?php }

	protected function get_show_in_account_details_option() { ?>
		<div class="form-group" v-if="!isFieldRequired(field)">
			<label>Show in user account details form</label>
			<label class="form-switch mb0">
				<input type="checkbox" v-model="field.show_in_account_details">
				<span class="switch-slider"></span>
			</label>
		</div>
	<?php }
}
