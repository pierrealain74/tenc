Vue.component( 'nav-item', {
	props: [ 'label', 'tab', 'icon', 'color', 'subtab' ],
	template: `
		<li :class="[ { active: $root.currentTab === tab }, 'tab-'+tab, 'editor-nav-item' ]">
			<a @click.prevent="$root.setTab( tab, subtab )" href="#"><i :class="icon"></i><span>{{ label }}</span></a>
		</li>
	`,

	created: function() {
		var target = '.editor-nav-item.tab-'+this.tab;
		this.$root.addStyle( `
			${target} a i { color: ${this.color}; border-color: ${this.color}; }
			${target}.active a i { background: ${this.color}; }
		` );
	},
} );

Vue.component( 'nav-sub-item', {
	props: [ 'label', 'tab', 'subtab' ],
	template: `
		<div
			@click.prevent="$root.setTab( tab, subtab )"
			:class="$root.currentTab === tab && $root.currentSubTab === subtab ? 'active' : ''"
			class="sub-tab"
		>{{ label }}</div>
	`,
} );

Vue.component( 'atwho', {
    props: {
        value: String,
        placeholder: String,
        template: String,
    },

    data() {
        return {
            modifiers: this.$root.editor.modifiers,
            aliases: CASE27_TypeDesigner.fieldAliases,
            fields: this.$root.fields.used,
        };
    },

    template: `
        <div>
            <div class="atwho-wrapper">
                <textarea ref="textarea"
                    :value="value"
                    :placeholder="placeholder"
                    :style="template === 'input' ? 'height:38px;' : ''"
                ></textarea>
            </div>
            <p class="form-description">
                This form item supports the
                <a href="#" class="cts-show-tip" data-tip="bracket-syntax">field bracket syntax.</a>
                Type <code>@</code> or <code>[[</code> for list of available tags.
            </p>
        </div>
    `,

    mounted() {
        var textarea = jQuery( this.$refs.textarea );
    	var config = {
    		at: "[[",
    		data: Object.values(this.$root.atWhoItems),
			insertTpl: "[[${slug}]]",
			displayTpl: '<li class="${classes}">${label} <small>${slug}</small></li>',
			limit: 10e5,
			startWithSpace: false,
			searchKey: 'search',
    	};

    	textarea.atwho( config ).atwho( jQuery.extend( config, { at: '@' } ) );
    	textarea.on( 'input change', e => this.$emit( 'input', e.target.value ) );
    	textarea.on( 'blur', e => this.$emit( 'blur', e.target.value ) );
    },

    beforeDestroy() {
        jQuery( this.$refs.textarea ).atwho('destroy');
    },
} );

Vue.component( 'seo', {
	props: [ 'value' ],
	data: function() {
		return {
			markup: null,
			editor: null,
		};
	},

	created: function() {
		this.markup = this.$root.settings.seo.markup;
	},

    mounted: function() {
        this.$nextTick( function() {
            this.initEditor();
        }.bind(this) );
    },

	methods: {
		setDefaultMarkup: function() {
            if ( confirm( 'Are you sure?' ) ) {
                this.markup = this.$root.blueprints.structured_data;
                this.editor.set( this.markup )
            }
        },

        initEditor: function() {
            var self = this;

            if (
                ! this.markup ||
                typeof this.markup !== 'object' ||
                Object.keys( this.markup ).length < 1
            ) {
                this.markup = this.$root.blueprints.structured_data;
            }

            // Setup JSON Editor
            var editor = new JSONEditor( this.$el.querySelector('.lte-seo-markup'), {
                mode: 'tree',
                modes: ['tree', 'text'],
                search: false,
                onChange: function() {
                    self.markup = editor.get();
                },
                autocomplete: {
                    caseSensitive: false,
                    getOptions: function (text, path, input, editor) {
                        var fields = self.$root.allFields();

                        return Object.keys( fields ).map( function( key ) {
                            if ( CASE27_TypeDesigner.fieldAliases[ fields[key].slug ] ) {
                                return '[[' + CASE27_TypeDesigner.fieldAliases[ fields[key].slug ] + ']]';
                            }

                            return '[[' + fields[key].slug + ']]';
                        } );
                    }
                }
            } );

            // Set JSON object
            editor.set( this.markup );
            this.editor = editor;
        },
	},

    watch: {
        markup: function( value ) {
            this.$root.settings.seo.markup = value;
        }
    },
} );

Vue.component( 'packages', {
	data: function() {
		return {
			activePackage: null,
		}
	},
	methods: {
        add: function( pkg ) {
            if ( typeof this.$root.state.settings.packages[ pkg ] === 'undefined' ) {
                return;
            }

            if ( this.isUsed( pkg ) ) {
                return;
            }

            this.$root.settings.packages.used.push( {
                package: pkg,
                label: '',
                description: '',
                featured: false,
            } );
        },

        remove: function( item ) {
            this.$root.settings.packages.used = this.$root.settings.packages.used.filter( function( pkg ) {
                return item !== pkg;
            } );
        },

        isUsed: function( package_id ) {
            var isUsed = false;

            this.$root.settings.packages.used.forEach( function( pkg ) {
                if ( pkg.package === package_id ) {
                    isUsed = true;
                }
            });

            return isUsed;
        },

        isActive: function( pkg ) {
        	return this.activePackage === pkg;
        },
	},
} );

Vue.component( 'reviews', {
	data: function() {
		return {
			activeCategory: null,
		}
	},
	methods: {
        addCategory: function() {
            this.$root.settings.reviews.ratings.categories.push( {
                id: 'category-key',
                label: 'Category Name',
                label_l10n: {},
                is_new: true,
            } );
        },

        removeCategory: function( category ) {
            this.$root.settings.reviews.ratings.categories = this.$root.settings.reviews.ratings.categories.filter( function( ctg ) {
                return category !== ctg;
            } );
        },

        isActive: function( category ) {
            return this.activeCategory === category;
        },
	},
} );

Vue.component( 'reviews-profile', {
	data: function() {
		return {
			activeCategory: null,
		}
	},
	methods: {
        addCategory: function() {
            this.$root.settings.reviewsProfile.ratings.categories.push( {
                id: 'category-key',
                label: 'Category Name',
                label_l10n: {},
                is_new: true,
            } );
        },

        removeCategory: function( category ) {
            this.$root.settings.reviewsProfile.ratings.categories = this.$root.settings.reviewsProfile.ratings.categories.filter( function( ctg ) {
                return category !== ctg;
            } );
        },

        isActive: function( category ) {
            return this.activeCategory === category;
        },
	},
} );

Vue.component( 'expiry-rules', {
    data() {
        return {
            rules: this.$root.settings.expiry_rules,
        };
    },

	methods: {
        addRule( field_key ) {
            if ( field_key.trim().length && ! this.rules.includes(field_key) ) {
                this.rules.push(field_key);
            }
        },

        removeRule( field_key ) {
            if ( this.rules.indexOf(field_key) !== -1 ) {
                this.rules.splice( this.rules.indexOf(field_key), 1 );
            }
        },

        getRuleLabel( field_key ) {
            var field = this.$root.fields.used.find(f => f.slug === field_key);
            if ( field ) {
                if ( field.type === 'recurring-date' ) {
                    return ( field.allow_recurrence )
                        ? `When the repeat end date for "${field.label}" is reached`
                        : `When the last occurence of "${field.label}" is finished`
                }

                if ( field.type === 'date' ) {
                    return `When the date in "${field.label}" field is reached`;
                }
            }
        },
	},

    computed: {
        availableRules() {
            var available = [];

            this.$root.fields.used.forEach( field => {
                // field has already been used as an expiry rule
                if ( this.rules.includes(field.slug) ) {
                    return;
                }

                if ( field.type === 'recurring-date' ) {
                    available.push( {
                        value: field.slug,
                        label: ( field.allow_recurrence )
                            ? `When the repeat end date for "${field.label}" is reached`
                            : `When the last occurence of "${field.label}" is finished`,
                    } );
                }

                if ( field.type === 'date' ) {
                    available.push( {
                        value: field.slug,
                        label: `When the date in "${field.label}" field is reached`,
                    } );
                }
            } );

            return available;
        },
    },
} );

Vue.component( 'head-buttons', {
	data() {
		return {
			active: null,
		};
	},

	methods: {
		isActive( button ) {
			return this.active === button;
		},

		setActive( button ) {
			this.active = button;
		},

		toggleActive( button ) {
			return this.active = ( button === this.active ) ? null : button;
		},

		deleteItem( button ) {
			this.$root.result.buttons = this.$root.result.buttons.filter( btn => btn !== button );
		},

		addItem() {
			this.$root.result.buttons.push( { label: '' } );
		},
	},
} );

Vue.component( 'info-fields', {
	data() {
		return {
			active: null,
		};
	},

	methods: {
		isActive( infoField ) {
			return this.active === infoField;
		},

		setActive( infoField ) {
			this.active = infoField;
		},

		toggleActive( infoField ) {
			return this.active = ( infoField === this.active ) ? null : infoField;
		},

		deleteItem( infoField ) {
			this.$root.result.info_fields = this.$root.result.info_fields.filter(
				i => i !== infoField
			);
		},

		addItem() {
			this.$root.result.info_fields.push( { label: '', icon: '' } );
		},
	},
} );

Vue.component( 'footer-sections', {
	data() {
		return {
			active: null,
			activeDetail: null,
			sectionTypes: {
                categories: {
                    type: 'categories',
                    taxonomy: 'job_listing_category',
                    show_bookmark_button: '',
                    show_quick_view_button: '',
					show_compare_button: '',
                },

                host: {
                    type: 'host',
                    label: '[[title]]',
                    show_field: 'related_listing',
                    show_bookmark_button: '',
                    show_quick_view_button: '',
					show_compare_button: '',
                },

                author: {
                    type: 'author',
                    label: '[[:authname]]',
                    show_bookmark_button: '',
                    show_quick_view_button: '',
					show_compare_button: '',
                },

                details: {
                    type: 'details',
                    details: [],
                    show_bookmark_button: '',
                    show_quick_view_button: '',
					show_compare_button: '',
                },

                actions: {
                    type: 'actions',
                    show_bookmark_button: '',
                    show_quick_view_button: '',
					show_compare_button: '',
                },
			},
		};
	},

	methods: {
		isActive( section ) {
			return this.active === section;
		},

		setActive( section ) {
			this.active = section;
		},

		toggleActive( section ) {
			return this.active = ( section === this.active ) ? null : section;
		},

		deleteSection( section ) {
			this.$root.result.footer.sections = this.$root.result.footer.sections.filter(
				btn => btn !== section
			);
		},

		addSection( sectionType ) {
			this.$root.result.footer.sections.push(
				jQuery.extend( true, {}, this.sectionTypes[ sectionType ] )
			);
		},

		deleteDetail( detail, section ) {
			section.details = section.details.filter( d => d !== detail );
		},

		addDetail( section ) {
			section.details.push( {
				icon: '',
				label: '',
			} );
		},
	},
} );

Vue.component( 'form-filters', {
	data() {
		return {
			filterTypes: CASE27_TypeDesigner.blueprints.filters,
			activeFilter: null,
		};
	},

	methods: {
		isActive( filter ) {
			return this.activeFilter === filter;
		},

		setActive( filter ) {
			this.activeFilter = filter;
		},

		toggleActive( filter ) {
			return this.activeFilter = ( filter === this.activeFilter ) ? null : filter;
		},

		addFilter( filterType ) {
			this.$root.search[ this.activeFormKey ].facets.push( jQuery.extend( true, {},
                this.filterTypes[ filterType ]
            ) );
		},

		deleteFilter( filter ) {
            this.$root.search[ this.activeFormKey ].facets = this.$root.search[ this.activeFormKey ].facets.filter(
            	f => f !== filter
            );
		},

        canAddFilter( filter ) {
            var filters = this.$root.search[ this.activeFormKey ].facets;

            // filters that are specific to the basic or advanced form shouldn't
            // be shown on the other form
            if ( filter.form && filter.form !== this.activeFormKey ) {
                return false;
            }

            // exclude ui items from basic form
            if ( this.activeFormKey === 'basic' && filter.type.slice(-2) === 'ui' ) {
                return false;
            }

            // filters that can be added only once shouldn't be shown if they
            // have already been added
            if ( filter.show_field === undefined && filter.type.slice(-2) !== 'ui' ) {
                if ( filters.find( f => f.type === filter.type ) ) {
                    return false;
                }
            }

            return true;
        },

        recurringRangeIsUsed( key, filter ) {
            return filter.ranges.find( range => range.key === key );
        },

        setPrimaryFilter( filter ) {
            // if this is already primary, uncheck it
            if ( filter.is_primary ) {
                return filter.is_primary = false;
            }

            // only one filter can be primary, so uncheck all others
            this.$root.search.advanced.facets.forEach( f => f.is_primary = false );

            // and set the selected one
            return filter.is_primary = true;
        },

        getFieldType( fieldKey ) {
        	if ( ! fieldKey ) {
        		return false;
        	}

        	var field = this.$root.getField( fieldKey );
        	return field ? field.type : false;
        },
	},

	computed: {
        activeFormKey() {
            return this.$root.currentSubTab === 'basic' ? 'basic' : 'advanced';
        },

        activeForm() {
        	return this.$root.search[ this.activeFormKey ];
        }
    },
} );

// Listing Type Builder.
if ( document.getElementById('case27-listing-type-options') ) {
	MyListing.TypeEditor = new Vue({
	    el: "#case27-listing-options-inside",

	    data: {
	        postid: null,
	        currentTab: 'settings',
	        currentSubTab: 'general',
	        drag: false,
	        styles: false,
	        editor: window.Type_Editor_Data,

	        state: {
	            custom_field_category: 'all',
	            fields: {
	                editingOptions: false,
	                active: null,
	            },

	            // Used when editing a cover button or a menu page.
	            single: {
	                active_button: null,
	                active_block: null,
	                active_detail: null,
	                active_cover_action: null,
	                active_quick_action: null,
	            },

	            preview: {
	                active_footer_section: null,
	            },

	            search: {
	                active_form: 'advanced',
	                active_facet: null,
	                active_order: null,
	                active_explore_tab: null,
	            },

	            settings: {
	                packages: CASE27_TypeDesigner.listing_packages,
	            },
	        },

	        blueprints: {
	            menu_page: {
	                defaults: {
	                    page: 'main',
	                    label: 'New Page',
	                    label_l10n: {locale: 'en_US'},
	                    slug: '',
	                },
	                main: {page: 'main', layout: [], sidebar: [], template: 'masonry'},
	                comments: {page: 'comments'},
	                reviews_profile: {page: 'reviews_profile'},
	                related_listings: {page: 'related_listings', related_listing_field: 'related_listing'},
	                custom: {page: 'custom', layout: [], sidebar: [], template: 'masonry'},
	                store: {page: 'store', field: '', hide_if_empty: false},
	                bookings: {page: 'bookings', field: '', provider: [], contact_form_id: 0},
	            },

	            facet: {
	                type: 'text',
	                label: 'New Facet',
	                placeholder: '',
	                search_field: '',
	            },

	            layout_blocks: window.Type_Editor_Data.content_blocks,
	            quick_actions: CASE27_TypeDesigner.blueprints.quick_actions,
	            structured_data: CASE27_TypeDesigner.blueprints.structured_data,
	            explore_tabs: CASE27_TypeDesigner.blueprints.explore_tabs,

	            map_skins: {},

	            preview: {
	                sections: {
	                    categories: {
	                        type: 'categories',
	                        title: 'Terms',
	                        taxonomy: 'job_listing_category',
	                        show_bookmark_button: '',
	                        show_quick_view_button: '',
	                        show_compare_button: '',
	                    },

	                    host: {
	                        type: 'host',
	                        title: 'Related Listing',
	                        label: '[[title]]',
	                        show_field: 'related_listing',
	                        show_bookmark_button: '',
	                        show_quick_view_button: '',
	                        show_compare_button: '',
	                    },

	                    author: {
	                        type: 'author',
	                        title: 'Author',
	                        label: '[[author]]',
	                        show_bookmark_button: '',
	                        show_quick_view_button: '',
	                        show_compare_button: '',
	                    },

	                    details: {
	                        type: 'details',
	                        title: 'Details',
	                        details: [],
	                        show_bookmark_button: '',
	                        show_quick_view_button: '',
	                        show_compare_button: '',
	                    },

	                    actions: {
	                        type: 'actions',
	                        title: 'Actions',
	                        show_bookmark_button: '',
	                        show_quick_view_button: '',
	                        show_compare_button: '',
	                    },
	                }
	            }
	        },

	        // Listing Fields.
	        fields: CASE27_TypeDesigner.schemes.fields,

	        // Single Listing Page Options.
	        single: CASE27_TypeDesigner.schemes.single,

	        // Result Template Options.
	        result: CASE27_TypeDesigner.schemes.result,

	        search: CASE27_TypeDesigner.schemes.search,

	        settings: CASE27_TypeDesigner.schemes.settings,
	    },

	    created: function() {
	        if ( window.location.hash ) {
	            var parts = window.location.hash.replace('#', '').split('.');
	            this.setTab( parts[0], parts[1] );
	        }

	        this.postid = jQuery('#case27-post-id').val();
	        this.blueprints.map_skins = CASE27.map_skins;
	        this.getListingMeta();
	    },

	    methods: {
	        setTab: function(tab, subtab) {
	            this.currentTab = tab;
	            this.currentSubTab = subtab;

	            // update url
	            window.location.hash = subtab ? tab+'.'+subtab : tab;
	        },

	        addStyle: function( rule ) {
	            if ( ! this.styles ) {
	                this.styles = document.createElement('style');
	                document.body.appendChild( this.styles );
	            }

	            this.styles.innerHTML += rule;
	        },

	        getPackageDefaultTitle: function( item ) {
	            if ( typeof this.state.settings.packages[ item.package ] === 'undefined' ) {
	                return '';
	            }

	            return this.state.settings.packages[ item.package ];
	        },

	        getPackageTitle: function( item ) {
	            if ( ! item.label ) {
	                return this.getPackageDefaultTitle( item );
	            }

	            return item.label;
	        },

	        getListingMeta: function() {
	            var data = CASE27_TypeDesigner.config;

	            // Setup Fields.
	            this.setupFields( data.fields.used );

	            // Setup Single Page Data.
	            this.setupSinglePage(data.single);

	            // Setup Result Template Data.
	            this.setupPreviewCard(data.result);

	            // Setup Search Forms Data.
	            this.search = jQuery.extend({}, this.search, data.search);
	            this.setupSearchForms();
	            this.setupOrderingOptions();
	            this.setupExploreTabs();

	            // Setup Settings Page Data.
	            this.settings = jQuery.extend({}, this.settings, data.settings);
	            this.setupReviewSettings();
	        },

	        setupFields: function( used ) {
	            var self = this;

	            this.fields.used = Object.keys( used ).map( function( key ) {
	                var field = used[ key ];
	                var preset = self.editor.preset_fields[ field.slug ];

	                // for used preset fields, update the default_label prop with the original preset label
	                if ( typeof preset !== 'undefined' ) {
	                    field.default_label = preset.label;
	                    preset._used = true;
	                }

	                return field;
	            } );

	            var has_title_field = false;
	            this.fields.used.forEach( function(el, key) {
	                if ( el.slug === 'job_title' ) {
	                    el.required = true;
	                    el.show_in_admin = true;
	                    el.show_in_compare = true;
	                    el.show_in_submit_form = true;
	                    el.conditional_logic = false;
	                    el.conditions = [];
	                    has_title_field = true;
	                }
	            } );

	            // title is always a required field
	            if ( ! has_title_field ) {
	                this.usePresetField( 'job_title' );
	            }
	        },

	        setupSinglePage: function(data) {
	            var self = this;

	            self.single = jQuery.extend({}, self.single, data);

	            self.single.menu_items = Object.keys(self.single.menu_items).map(function(key) {
	                var _defaults = jQuery.extend(true, {}, self.blueprints.menu_page.defaults, self.blueprints.menu_page[self.single.menu_items[key].page]);
	                var _used = jQuery.extend({}, _defaults, self.single.menu_items[key]);

	                // Make sure the used field doesn't have any extra unnecessary fields.
	                Object.keys(_used).map(function(subkey) {
	                    if (typeof _defaults[subkey] === 'undefined') {
	                        // console.log(subkey + ' is not present in original!');
	                        delete _used[subkey];
	                    }
	                });

	                return _used;
	            });

	            self.single.menu_items.map(function(menu_item) {
	                // Layout Blocks.
	                if (typeof menu_item.layout !== 'undefined') {
	                    // Multiple columns in single listing page were introduced later,
	                    // Go through each key containing column data, and convert it to required format.
	                    var columns = ['layout', 'sidebar'];
	                    columns.forEach( function( column ) {
	                        var menu_layout = [];
	                        menu_item[column].forEach( function(block, block_key) {
	                            if ( typeof self.blueprints.layout_blocks[block.type] === 'undefined' ) {
	                                return;
	                            }

	                            var defaults = jQuery.extend( true, {}, self.blueprints.layout_blocks[block.type] );

	                            var block = jQuery.extend({}, defaults, block);

	                            // Make sure the used field doesn't have any extra unnecessary fields.
	                            Object.keys(block).map(function(subkey) {
	                                if (typeof defaults[subkey] === 'undefined') {
	                                    delete block[subkey];
	                                }
	                            });

	                            // We have the layout block under 'block' and the blueprint block of the same type under '_block'.
	                            // Make sure the layout block gets the options array always from the blueprint object.
	                            if (defaults.options) {
	                                var _options = [];

	                                defaults.options.forEach(function(_option, _option_key) {
	                                    var opt = jQuery.extend({}, _option);
	                                    block.options.forEach(function(option, option_key) {
	                                        if (_option.name != option.name) return;

	                                        opt.value = option.value;
	                                    });

	                                    _options.push(opt);
	                                });

	                                block.options = _options;
	                            }

	                            // Default block icons used on previous versions didn't include the icon pack name.
	                            // Since they were all material icons, we just add the "mi" prefix to them.
	                            var default_icons = ['view_headline', 'insert_photo', 'view_module', 'map', 'email', 'layers', 'av_timer', 'attach_file', 'alarm', 'videocam', 'account_circle'];
	                            if ( typeof block.icon !== 'undefined' && default_icons.indexOf( block.icon ) !== -1 ) {
	                                block.icon = 'mi ' + block.icon;
	                            }

	                            menu_layout.push(block);
	                        } );

	                        menu_item[column] = menu_layout;
	                    } );
	                }

	                return menu_item;
	            })

	            self.single.quick_actions = self.single.quick_actions.map( function( action, key ) {
	                var updated = jQuery.extend( true, {}, self.blueprints.quick_actions[ action.action ] );

	                Object.keys( updated ).forEach( function( subkey ) {
	                    if ( typeof action[subkey] !== 'undefined' ) {
	                        updated[subkey] = action[subkey];
	                    }
	                } );

	                return updated;
	            });

	            self.single.cover_actions = self.single.cover_actions.map( function( action, key ) {
	                var updated = jQuery.extend( true, {}, self.blueprints.quick_actions[ action.action ] );

	                Object.keys( updated ).forEach( function( subkey ) {
	                    if ( typeof action[subkey] !== 'undefined' ) {
	                        updated[subkey] = action[subkey];
	                    }
	                } );

	                return updated;
	            });
	        },

	        setupPreviewCard: function( data ) {
	            var self = this;
	            if ( ! data ) {
	                return false;
	            }
	            // self.result = jQuery.extend({}, self.result, data.result);
	            data.footer.sections = data.footer.sections.map(function( section ) {
	                var _section = jQuery.extend( true, {}, self.blueprints.preview.sections[ section.type ] );

	                if ( ! _section ) {
	                    return false;
	                }

	                Object.keys( _section ).map(function( key ) {
	                    if ( typeof section[key] !== 'undefined' ) {
	                        _section[key] = section[key];
	                    };
	                });

	                return _section;
	            });

	            self.result = data;
	        },

	        setupSearchForms: function() {
	            var self = this;

	            // If the object structure for a facet has been changed e.g. an option has been added or removed,
	            // then make sure to update the existing facets with the new data structure.
	            this.search.advanced.facets = this.search.advanced.facets.map(function(facet) { return self.setupFacet(facet) });
	            this.search.basic.facets = this.search.basic.facets.map(function(facet) { return self.setupFacet(facet) });
	        },

	        setupOrderingOptions: function() {
	            if ( ! this.search.order.options.length ) {
	                this.searchTab().setDefaultOrderOptions();
	            }

	             this.search.order.options.forEach(function( option ) {
	                option.is_new = false;
	            });
	        },

	        setupExploreTabs: function() {
	        	console.log( this.blueprints.explore_tabs );
	            if ( ! this.search.explore_tabs.length ) {
	                this.search.explore_tabs = [];
	                this.searchTab().addTab( this.blueprints.explore_tabs['search-form'] );
	                this.searchTab().addTab( this.blueprints.explore_tabs['categories'] );
	            }
	        },

	        setupFacet: function( facet ) {
	            var blueprint = CASE27_TypeDesigner.blueprints.filters[ facet.type ];
	            if ( typeof blueprint === 'undefined' ) {
	                return facet;
	            }

	            var updated_facet = jQuery.extend({}, blueprint, facet);
	            updated_facet.options = [];

	            // Update the options. Need to loop through each option and extend the option object,
	            // to avoid multiple facets pointing to the same option object.
	            if (Array.isArray(blueprint.options)) {
	                blueprint.options.forEach(function(default_option) {
	                    var existing_option = facet.options.filter(function(opt) {
	                        return opt.name === default_option.name;
	                    })[0];

	                    // If this option has a value in the old object structure, get the old value.
	                    if (typeof existing_option !== 'undefined') {
	                        default_option.value = existing_option.value;
	                    }

	                    // Push a cloned version of the option to the facet object.
	                    updated_facet.options.push(jQuery.extend({}, default_option));
	                });
	            }

	            return updated_facet;
	        },

	        setupReviewSettings: function() {
	            if ( this.settings.reviews.ratings.categories.length < 1 ) {
	                this.settings.reviews.ratings.categories.push({
	                    id: 'rating',
	                    label: 'Overall Rating',
	                    label_l10n: {},
	                    is_new: false,
	                });
	            }

	            this.settings.reviews.ratings.categories.forEach(function( category ) {
	                category.is_new = false;
	            });
	        },

	        usePresetField: function( preset_key ) {
	            var preset = this.editor.preset_fields[ preset_key ];
	            if ( ! preset ) {
	                return;
	            }

	            var field = jQuery.extend( true, {}, preset );
	            this.fields.used.push( field );
	            preset._used = true;
	        },

	        addCustomField: function( field_type ) {
	            if ( ! this.editor.custom_fields[ field_type ] ) {
	                return;
	            }

	            var field = jQuery.extend( true, {}, this.editor.custom_fields[ field_type ] );
	            field.is_new = true;
	            var random_int = Math.floor(Math.random() * 9000) + 1000;
	            field.slug = `custom-field-${random_int}`;
	            this.fields.used.push( field );
	        },

	        deleteField: function( field_slug ) {
	            var label = this.fieldLabelBySlug( field_slug );
	            var nice_key = CASE27_TypeDesigner.fieldAliases[ field_slug ]
	                ? CASE27_TypeDesigner.fieldAliases[ field_slug ]
	                : field_slug;

	            if ( ! confirm( `Are you sure you want to delete "${label}" field?` ) ) {
	                return;
	            }

	            this.fields.used = this.fields.used.filter( function( used ) {
	                return used.slug !== field_slug;
	            } );

	            // if it's a preset, make it visible again
	            if ( this.editor.preset_fields[ field_slug ] ) {
	                delete this.editor.preset_fields[ field_slug ]._used;
	            }
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

	        editFieldMimeTypes: function(e, field) {
	            field.allowed_mime_types = {};
	            jQuery(e.target).val().map(function(el, i) {
	                var elData = el.split('=>');

	                if (!elData[0] || !elData[1]) return false;

	                field.allowed_mime_types[elData[0].trim()] = elData[1].trim();
	            });
	        },

	        slugify: function(str) {
	            return str.toString().trim().toLowerCase()
	                .replace(/\s+/g, "-")
	                .replace(/[^\w\-]+/g, "")
	                .replace(/\-\-+/g, "-")
	                .replace(/^-+/, "");
	        },

	        /*
	         * SINGLE PAGE TAB.
	         */
	        addMenuItem: function( menu_item ) {
	            var item = jQuery.extend( true, {}, this.blueprints.menu_page.defaults, this.blueprints.menu_page[ menu_item ] );
	            this.single.menu_items.push(item);
	        },

	        deleteMenuItem: function(menuItem) {
	            this.single.menu_items = this.single.menu_items.filter(function(item) {
	                return item !== menuItem;
	            });
	        },

	        addBlock: function( block_type, menu_item ) {
	        	console.log( this.blueprints );
	            if ( block_type == 'none' || typeof this.blueprints.layout_blocks[block_type] === 'undefined' ) {
	                return;
	            }

	            menu_item.layout.push(
	                jQuery.extend( true, {}, this.blueprints.layout_blocks[block_type] )
	            );
	        },

	        deleteBlock: function( block, column, menu_item ) {
	            menu_item[ column ] = menu_item[ column ].filter( function(el) {
	                return el !== block;
	            } );
	        },

	        moveBlock: function( block, column, menu_item ) {
	            var moveTo = column == 'layout' ? 'sidebar' : 'layout';

	            menu_item[ column ] = menu_item[ column ].filter(function( _block ) {
	                return _block !== block;
	            });

	            menu_item[ moveTo ].push(block);
	        },

	        getFooterSectionTitle( section ) {
	            var titles = {
	                categories: 'Terms',
	                host: 'Related Listing',
	                author: 'Author',
	                details: 'Details',
	                actions: 'Actions',
	            };

	            return titles[ section.type ] ? titles[ section.type ] : 'Section';
	        },


	        /*
	         * SEARCH PAGE TAB.
	         */
	        searchTab: function() {
	            var self = this;

	            return {
	                addOption: function( label, key, orderby, order, context, type, custom_type, ignore_priority ) {
	                    self.search.order.options.push({
	                        label: label || 'New option',
	                        key: key || 'new-option',
	                        ignore_priority: ignore_priority || false,
	                        is_new: true,
	                        clauses: [{
	                            orderby: orderby || 'date',
	                            order: order || 'DESC',
	                            context: context || 'option',
	                            type: type || 'CHAR',
	                            custom_type: custom_type || false, // use for entering custom type formats, such as DECIMAL(10, 2)
	                        }],
	                    });
	                },

	                removeOption: function( option ) {
	                    self.search.order.options = self.search.order.options.filter( function( _option ) {
	                        return option !== _option;
	                    });
	                },

	                optionType( field_key ) {
	                    var field = self.fieldByName( field_key );
	                    return field ? field.type : '';
	                },

	                addClause: function( option ) {
	                    option.clauses.push({
	                        orderby: 'date',
	                        order: 'DESC',
	                        context: 'option',
	                        type: 'CHAR',
	                        custom_type: false,
	                    });
	                },

	                removeClause: function( clause, option ) {
	                    option.clauses = option.clauses.filter( function( _clause ) {
	                        return clause !== _clause;
	                    });
	                },

	                setOptionKey: function( option ) {
	                    if ( ! option.is_new ) {
	                        return;
	                    }

	                    option.key = self.slugify( option.label );
	                },

	                setDefaultOrderOptions: function() {
	                    self.search.order.options = [];
	                    self.searchTab().addOption( 'Latest', 'latest', 'date', 'DESC', 'option' );
	                    self.searchTab().addOption( 'Top rated', 'top-rated', 'rating', 'DESC', 'option', 'DECIMAL(10,2)', false, true );
	                    self.searchTab().addOption( 'Random', 'random', 'rand', 'DESC', 'option' );
	                },

	                addTab: function( tab_type, label, icon, orderby, order, hide_empty ) {
	                    // if configuration is passed as an object
	                    if ( typeof tab_type === 'object' && tab_type !== null ) {
	                        self.search.explore_tabs.push( jQuery.extend( true, {}, tab_type ) );
	                        return;
	                    }

	                    self.search.explore_tabs.push( {
	                        type: tab_type,
	                        label: label,
	                        icon: icon,
	                        orderby: orderby || '',
	                        order: order || '',
	                        hide_empty: hide_empty || false,
	                    } );
	                },

	                removeTab: function( tab ) {
	                    self.search.explore_tabs = self.search.explore_tabs.filter( function( _tab ) {
	                        return tab !== _tab;
	                    });
	                },
	            };
	        },

	        // Quick actions.
	        quickActions: function() {
	            var self = this;

	            return {
	                remove: function( action ) {
	                    self.single.quick_actions = self.single.quick_actions.filter( function( _action ) {
	                        return _action !== action;
	                    } );
	                },

	                add: function( type ) {
	                    if ( ! self.blueprints.quick_actions[ type ] ) {
	                        return;
	                    }

	                    self.single.quick_actions.push( jQuery.extend( true, {}, self.blueprints.quick_actions[ type ] ) );
	                },
	            };
	        },

	        // Cover actions.
	        coverActions: function() {
	            var self = this;

	            return {
	                remove: function( action ) {
	                    self.single.cover_actions = self.single.cover_actions.filter( function( _action ) {
	                        return _action !== action;
	                    } );
	                },

	                add: function( type ) {
	                    if ( ! self.blueprints.quick_actions[ type ] ) {
	                        return;
	                    }

	                    self.single.cover_actions.push( jQuery.extend( true, {}, self.blueprints.quick_actions[ type ] ) );
	                },
	            };
	        },

	        // Cover details.
	        coverDetails: function() {
	            var self = this;

	            return {
	                remove: function( detail ) {
	                    self.single.cover_details = self.single.cover_details.filter( function( _detail ) {
	                        return _detail !== detail;
	                    } );
	                },

	                add: function( label, field, format, prefix, suffix ) {
	                    self.single.cover_details.push( {
	                        label: label || '',
	                        field: field || '',
	                        format: format || 'plain',
	                        prefix: prefix || '',
	                        suffix: suffix || '',
	                    } );
	                },
	            };
	        },

	        getFields( ...types ) {
	            var fields = this.fields.used;
	            if ( types.length ) {
	                return fields.filter( field => types.includes( field.type ) );
	            }

	            return fields;
	        },

	        getField( fieldKey ) {
	            return this.fields.used.find( field => field.slug === fieldKey );
	        },

	        fieldsByType: function(types) {
	            return this.fields.used.filter(function(field) {
	                return (typeof field === 'object') && (jQuery.inArray(field.type, types) !== -1);
	            }).map(function(field) {
	                return {slug: field.slug, label: field.label};
	            });
	        },

	        allFields: function() {
	            return this.fields.used.filter(function(field) {
	                return (typeof field === 'object');
	            }).map(function(field) {
	                return {slug: field.slug, label: field.label};
	            });
	        },

	        fieldByName: function(name) {
	            var field = this.fields.used.filter(function(field) {
	                return field.slug === name;
	            });

	            return field.length ? field[0] : false;
	        },

	        fieldLabelBySlug: function(slug) {
	            var field = this.fieldByName(slug);
	            return field ? field.label : false;
	        },

	        // Return fields in key => value pairs.
	        fieldsByTypeFormatted: function(types) {
	            if (!jQuery.isArray(types)) {
	                return types;
	            }

	            var fields = this.fieldsByType(types);
	            var formatted = {};

	            fields.forEach(function(field) {
	                formatted[field.slug] = field.label;
	            });

	            return formatted;
	        },

	        textFields: function() {
	            return this.fieldsByType( [
	                'text', 'checkbox', 'date', 'recurring-date', 'email', 'location', 'multiselect',
	                'number', 'password', 'radio', 'select', 'textarea', 'texteditor', 'wp-editor', 'url',
	            ] );
	        },

	        toggleRepeaterItem: function(e) {
	            jQuery(e.target).closest('.row-item').toggleClass('open');
	        },

	        capitalize: function(str) {
	            if (typeof str !== 'string' || !str.length) return str;

	            var words = str.split(/[\s,_-]+/);

	            for(var i = 0; i < words.length; i++) {
	              var word = words[i];
	              words[i] = word.charAt(0).toUpperCase() + word.slice(1);
	          }

	          return words.join(' ').replace(/^Job\s/, '');
	        },

	        formatLabel: function(str, show_field) {
	            var label = this.fieldByName(show_field) ? this.fieldByName(show_field).label : this.capitalize(show_field);

	            return this.capitalize( str.replace('[[field]]', label )
	                      .replace(/\[27-format(.*?)?\]/g, '')
	                      .replace(/\[\/27-format\]/g, '') );
	        },

	        /*
	         * CONDITIONS.
	         */
	        conditions: function() {
	            var self = this;

	            return {
	                addOrCondition: function( item ) {
	                    item.conditions.push([{
	                        key: '__listing_package',
	                        value: '',
	                        compare: '==',
	                    }]);
	                },

	                deleteConditionGroup: function( group, item ) {
	                    item.conditions = item.conditions.filter( function( conditionGroup ) {
	                        return group !== conditionGroup;
	                    });
	                },
	            };
	        },

	        getLabelParts( label, fallback = '' ) {
	            var tags = this.atWhoItems;
	            var parts = label.split( /\[\[+(.*?)\]\]/g ).filter( Boolean );
	            var parts = parts.map( part => {
	                var tag = tags[part];
	                if ( tag ) {
	                    return {
	                        type: 'tag',
	                        content: tag.displayLabel ? tag.displayLabel : tag.label,
	                    };
	                }

	                return { type: 'text', content: part };
	            } );

	            return parts.length ? parts : [ { type: 'empty', content: fallback } ];
	        },
	    },

	    computed: {
	        fields_json_string: function() {
	            return JSON.stringify(this.fields.used);
	        },

	        single_page_options_json_string: function() {
	            return JSON.stringify(this.single);
	        },

	        result_template_json_string: function() {
	            return JSON.stringify(this.result);
	        },

	        search_page_json_string: function() {
	            return JSON.stringify(this.search);
	        },

	        settings_page_json_string: function() {
	            return JSON.stringify(this.settings);
	        },

	        atWhoItems() {
	            var items = {};
	            this.fields.used.forEach( field => {
	                if ( field.is_ui ) {
	                    return;
	                }

	                var field_key = CASE27_TypeDesigner.fieldAliases[ field.slug ]
	                    ? CASE27_TypeDesigner.fieldAliases[ field.slug ]
	                    : field.slug;

	                items[ field_key ] = {
	                    slug: field_key,
	                    label: field.label,
	                    search: `${field.label} ${field_key}`.replace(/\s+/g, ''),
	                    classes: '',
	                };

	                if ( this.editor.modifiers[ field.type ] ) {
	                    Object.keys( this.editor.modifiers[ field.type ] ).forEach( modifier => {
	                        var _label = this.editor.modifiers[ field.type ][ modifier ];
	                        var label = _label.replace('%s', '').trim();
	                        var extendedLabel = _label.replace('%s', field.label).trim();
	                        items[ `${field_key}.${modifier}` ] = {
	                            slug: `${field_key}.${modifier}`,
	                            label: `&mdash; <span>${label}</span>`,
	                            search: `${field.label} ${label} ${field_key} ${modifier}`.replace(/\s+/g, ''),
	                            displayLabel: extendedLabel,
	                            classes: 'sub-item',
	                        };
	                    } );
	                }
	            } );

	            Object.keys( this.editor.special_keys ).forEach( ( special_key, i ) => {
	                var label = this.editor.special_keys[ special_key ];
	                items[ special_key ] = {
	                    slug: special_key,
	                    label: label,
	                    search: `${label} ${special_key}`.replace(/\s+/g, ''),
	                    classes: i === 0 ? 'divide-top' : '',
	                };
	            } );

	            return items;
	        },
	    }
	});
}