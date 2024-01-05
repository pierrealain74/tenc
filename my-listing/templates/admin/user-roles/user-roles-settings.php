<?php
/**
 * MyListing user-roles settings screen.
 *
 * @since 2.5
 */
if ( ! defined('ABSPATH') ) {
	exit;
} ?>

<?php if ( ! empty( $_GET['saved'] ) ): ?>
	<div class="updated"><p>Settings have been saved.</p></div>
<?php endif ?>

<div class="wrap mylisting-options" id="mylisting-roles" v-cloak>
	<div class="mb40" style="max-width: 900px;">
		<h1 class="m-heading mb30">Accounts & Roles</h1>

		<div class="form-group mb30 inline-option">
			<h4 class="m-heading mb5">Enable user registration</h4>
			<p class="mt0 mb10">Allow customers to create an account on the "My account" page</p>
			<label class="form-switch">
				<input type="checkbox" v-model="config.settings.enable_registration">
				<span class="switch-slider"></span>
			</label>
		</div>

		<div class="form-group mb30 inline-option">
			<h4 class="m-heading mb5">Generate username automatically</h4>
			<p class="mt0 mb10">
				When creating an account, automatically generate an account username
				for the customer based on their name, surname or email
			</p>
			<label class="form-switch">
				<input type="checkbox" v-model="config.settings.generate_username">
				<span class="switch-slider"></span>
			</label>
		</div>

		<div class="form-group mb30 inline-option">
			<h4 class="m-heading mb5">Generate password automatically</h4>
			<p class="mt0 mb10">When creating an account, automatically generate an account password</p>
			<label class="form-switch">
				<input type="checkbox" v-model="config.settings.generate_password">
				<span class="switch-slider"></span>
			</label>
		</div>

		<div class="form-group mb30 inline-option">
			<h4 class="m-heading mb5">Default account type</h4>
			<p class="mt0 mb10">This will be the preselected option in the registration form.</p>
			<div class="select-wrapper">
				<select v-model="config.roles.default_form">
					<option value="primary">{{config.roles.primary.label}}</option>
					<option value="secondary" v-if="config.roles.secondary.enabled">
						{{config.roles.secondary.label}}
					</option>
				</select>
			</div>
		</div>

		<div class="form-group mb30 inline-option">
			<h4 class="m-heading mb5">Enable Google reCAPTCHA in login form</h4>
			<p class="mt0 mb10">
				<a href="<?php echo admin_url('edit.php?post_type=job_listing&page=mylisting-settings#captcha-config') ?>">
					Configure reCAPTCHA
				</a>
			</p>
			<label class="form-switch">
				<input type="checkbox" v-model="config.roles.login_captcha">
				<span class="switch-slider"></span>
			</label>
		</div>

		<div class="form-group mb30 inline-option">
			<h4 class="m-heading mb5">Enable Google reCAPTCHA in register form</h4>
			<p class="mt0 mb10">
				<a href="<?php echo admin_url('edit.php?post_type=job_listing&page=mylisting-settings#captcha-config') ?>">
					Configure reCAPTCHA
				</a>
			</p>
			<label class="form-switch">
				<input type="checkbox" v-model="config.roles.register_captcha">
				<span class="switch-slider"></span>
			</label>
		</div>
	</div>

	<h3 class="m-heading mb20">User Roles & Permissions</h3>
	<div v-for="role, roleKey in {primary: roles.primary, secondary: roles.secondary}" class="role-settings">
		<h2>
			{{ role.label }}
			<span>{{ roleKey === 'secondary' ? 'Alternate Account Type' : 'Main Account Type' }}</span>
		</h2>

		<div class="settings-wrapper">
			<div v-if="roleKey === 'secondary'" class="form-group mt20 mb20 enable-role">
				<h4 class="m-heading mb5">Enable alternate account type</h4>
				<p class="mt0 mb10">
					If enabled, users will be able to choose their role during registration.
					You can configure different permissions and registration fields for this
					type of users.<br><br>
					This allows for separate user types, e.g. Businesses and Private Users,
					Employers and Job Seekers, etc.
				</p>
				<label class="form-switch">
					<input type="checkbox" v-model="role.enabled">
					<span class="switch-slider"></span>
				</label>
			</div>

			<div v-if="roleKey === 'primary' || (roleKey === 'secondary' && role.enabled)">
				<div class="form-group mb30">
					<h4 class="m-heading mb5">Role name</h4>
					<p class="mt0 mb10">
						This label will be used in the user registration form, when filtering users by role, etc.
					</p>
					<input type="text" v-model="role.label" class="m-input"
						:placeholder="roleKey === 'primary' ? 'e.g. &quot;Business User&quot;' : 'e.g. &quot;Private User&quot;'">
				</div>

				<div class="form-group mb30">
					<h4 class="m-heading mb5">Can add listings</h4>
					<p class="mt0 mb10">
						Allow users with this role to submit new listings through the Add Listing form.
					</p>
					<label class="form-switch">
						<input type="checkbox" v-model="role.can_add_listings">
						<span class="switch-slider"></span>
					</label>
				</div>

				<div class="form-group mb30">
					<h4 class="m-heading mb5">Can switch role</h4>
					<p v-if="roleKey === 'primary'" class="mt0 mb10">
						If enabled, users with this role will have the option to switch
						to the alternate account type through their user dashboard.
					</p>
					<p v-else class="mt0 mb10">
						If enabled, users with this role will have the option to switch
						to the main account type through their user dashboard.
					</p>
					<label class="form-switch">
						<input type="checkbox" v-model="role.can_switch_role">
						<span class="switch-slider"></span>
					</label>
				</div>

				<div class="form-group">
					<h4 class="m-heading mb5">Registration form fields</h4>
					<p class="mt0 mb10">
						Set what fields to show in the registration form for this role.
					</p>
					<div class="register-fields tabs-content">
						<draggable v-model="role.fields" :options="{group: 'fields-'+roleKey, handle: '.row-head'}">
							<div v-for="field in role.fields" class="row-item" :class="field === activeField ? 'open' : ''">
								<div @click="activeField = (field !== activeField) ? field : null" class="row-head">
									<div class="row-head-toggle"><i class="mi chevron_right"></i></div>
									<div class="row-head-label">
										<h4>{{ field.label }}</h4>
										<div class="details">
											<div class="detail">{{ field.slug }}</div>
										</div>
									</div>
									<div class="row-head-actions">
										<span title="Remove" v-if="!isFieldRequired(field)" @click.stop="deleteField(field, roleKey)" class="action red">
											<i class="mi delete"></i>
										</span>
									</div>
								</div>
								<div class="row-edit" v-if="activeField === field">
									<div class="field-settings-wrapper">
										<div v-if="field.slug === 'username' && config.settings.generate_username" class="form-group">
											<p class="mb0 mt0">
												Username is currently configured to be generated automatically, so this
												field won't be shown in the registration form.
											</p>
										</div>

										<div v-if="field.slug === 'password' && config.settings.generate_password" class="form-group">
											<p class="mb0 mt0">
												Password is currently configured to be generated automatically, so this
												field won't be shown in the registration form.
											</p>
										</div>

										<?php foreach ( \MyListing\Src\User_Roles\get_field_types() as $field_type ): ?>
											<?php echo $field_type->print_editor_options() ?>
										<?php endforeach ?>
									</div>

									<div class="text-right">
										<div class="btn btn-xs" @click="activeField = null">Done</div>
									</div>
								</div>
							</div>
						</draggable>

						<div class="mt20" v-if="hasAvailableFields(roleKey)">
							<p class="mt0 mb10">Available fields</p>
							<div
								v-for="field, key in config.presets"
								class="btn btn-secondary btn-xs"
								style="margin: 0 6px 8px 0;"
								v-if="!hasField(key, roleKey)"
								@click="addField(key, roleKey)"
							>{{field.label}}</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ) ?>" method="POST">
		<input type="hidden" name="roles_config" :value="rolesJson">
		<input type="hidden" name="general_settings" :value="settingsJson">
		<input type="hidden" name="action" value="mylisting_role_settings">
		<input type="hidden" name="_wpnonce"
			value="<?php echo esc_attr( wp_create_nonce( 'mylisting_role_settings' ) ) ?>">
		<button type="submit" class="btn btn-primary-alt btn-xs">Save settings</button>
	</form>

	<!-- <pre>{{$data}}</pre> -->
</div>