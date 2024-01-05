Vue.component( 'double-checkbox-filter', {
    props: {
        listingType: String,
        filterKey: String,
        location: String,
        ajaxParams: String,
        label: String,
        preSelected: Array,
        multiple: Boolean,
    },

    data() {
        return {
            selected: this.multiple ? [] : '',
        };
    },

    created() {
        this.selected = this.multiple
            ? this.filters[ this.filterKey ].split(',')
            : this.filters[ this.filterKey ];
    },

    mounted() {
        this.$nextTick( () => {
            new MyListing.CustomSelect( this.$refs.select );
            this.$root.$on( 'reset-filters:'+this.listingType, () => {
                this.selected = this.multiple ? [] : '';
                this.filters[ this.filterKey ] = '';
                jQuery( this.$refs.select ).val( this.selected )
                    .trigger('change').trigger('select2:close');
            } );
        } );
    },

    methods: {
        handleChange(e) {
            this.selected = this.multiple
                ? ( Array.isArray(e.detail.value) ? e.detail.value : [] )
                : ( typeof e.detail.value === 'string' ? e.detail.value : '' );

            this.updateInput();
        },

        updateInput() {
            var value = this.multiple ? this.selected.filter(Boolean).join(',') : this.selected;
            this.filters[ this.filterKey ] = value;
            this.$emit( 'input', value, {
                filterType: this.$options.name,
                filterKey: this.filterKey,
                location: this.location,
            } );
        },

        isSelected( choice ) {
            if ( ! this.multiple ) {
                return choice === this.selected;
            }

            return this.selected.includes( choice );
        },
    },

    computed: {
        filters() {
            return this.$root.types[ this.listingType ].filters;
        },
    },
} );