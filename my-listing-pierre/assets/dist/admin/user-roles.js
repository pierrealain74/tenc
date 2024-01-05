new Vue( {
	el: '#mylisting-roles',
	data: {
		config: MyListing_User_Roles_Config,
		activeField: null,
		requiredFields: ['email', 'password', 'username'],
		state: {
            editingOptions: false 
        },
        allField: [],
	},

	methods: {
		isFieldActive( field ) {
			return field === this.activeField;
		},

		deleteField( field, role ) {
			this.roles[ role ].fields = this.roles[ role ].fields
				.filter( f => f !== field );
		},

		isFieldRequired( field ) {
			return this.requiredFields.includes( field.slug );
		},

		hasField( fieldKey, role ) {
			return this.roles[ role ].fields.find( f => f.slug === fieldKey ) !== undefined;
		},

		addField( fieldKey, role ) {
			if ( ! this.config.presets[ fieldKey ] ) {
				return;
			}

			this.roles[ role ].fields.push( jQuery.extend(
				true, {}, this.config.presets[ fieldKey ]
			) );
		},

		hasAvailableFields( role ) {
			var hasAvailable = false;
			Object.keys( this.config.presets ).forEach( key => {
				if ( ! this.hasField( key, role ) ) {
					hasAvailable = true;
				}
			} );

			return hasAvailable;
		},

		editFieldOptions: function(e, field) {
            field.options = {};
            jQuery(e.target).val().trim().split('\n').map(function(el, i) {
                if (el.length < 1) return false;

                var elData = el.split(':');

                if (elData.length === 1) {
                    return field.options[elData[0].trim()] = elData[0].trim();
                }

                if (elData.length === 2) {
                    return field.options[elData[0].trim()] = elData[1].trim();
                }

                return false;
            });
        },
	},

	computed: {
		roles() {
			return {
				primary: this.config.roles.primary,
				secondary: this.config.roles.secondary,
			};
		},

		rolesJson() {
			return JSON.stringify( this.config.roles );
		},

		settingsJson() {
			return JSON.stringify( this.config.settings );
		},
	}
} );
