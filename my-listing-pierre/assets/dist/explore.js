/**
 * Component for rendering the header part of the results section.
 *
 * @since 2.4
 */
Vue.component( 'results-header', {
    data() {
        return {
            currentPage: 0,
            totalPages: 0,
            hasPrevPage: false,
            hasNextPage: false,
            foundPosts: null,
            resultCountText: '',
            target: null,
        };
    },

    mounted() {
        this.$nextTick( () => {
            this.$root.$on( 'update-results', (response) => {
                var activeType = this.$root.activeType;

                this.target = activeType.taxonomies[ activeType.tab ]
                    ? activeType.taxonomies[ activeType.tab ]
                    : activeType.filters;

                this.resultCountText = response.showing;
                this.totalPages = parseInt( response.max_num_pages, 10 );
                this.currentPage = parseInt( this.target.page, 10 );

                this.hasPrevPage = ( this.currentPage - 1 ) >= 0;
                this.hasNextPage = ( this.currentPage + 1 ) < this.totalPages;

                this.foundPosts = parseInt( response.found_posts, 10 );
            } );
        } );
    },

    methods: {
        getNextPage() {
            this.target.page = this.currentPage + 1;
            this.target.preserve_page = true;
            this.$root._getListings( 'next-page' );
        },

        getPrevPage( note ) {
            this.target.page = this.currentPage - 1;
            this.target.preserve_page = true;
            this.$root._getListings( 'previous-page' );
        },
    },
} );

// Explore Listings.
MyListing.Explore_Init = () => {
    var el = document.querySelector('.cts-explore');

    if ( ! el || el.dataset.inited ) {
        return;
    }

    el.dataset.inited = true;

    MyListing.Explore = new Vue({
        el: el,

        data: {
            activeType: false,
            types: CASE27_Explore_Settings.ListingTypes,
            template: CASE27_Explore_Settings.Template,
            loading: false,
            last_request: null,
            found_posts: null,
            state: {
                mobileTab: CASE27_Explore_Settings.ActiveMobileTab,
            },
            isMobile: window.matchMedia( 'screen and (max-width: 1200px)' ).matches,
            map: false,
            mapExpanded: false,
            mapProvider: CASE27.map_provider,
            dragSearch: false,
            compare: [],
            suspendDragSearch: false,
            baseUrl: CASE27_Explore_Settings.ExplorePage
                ? CASE27_Explore_Settings.ExplorePage
                : window.location.href.replace( window.location.search, '' ),
        },

        beforeMount: function() {
            window.matchMedia( 'screen and (max-width: 1200px)' ).addListener( e => this.isMobile = e.matches );

            // this will also request the initial list of search results
            this.setType( CASE27_Explore_Settings.ActiveListingType );

            if ( this.isMobile ) {
                /**
                 * On mobile, taxonomy tabs are disabled. If we're on a single term
                 * page, then only load the requested term, without parent or child term
                 * navigation. Otherwise, always default to the 'search-form' tab.
                 */
                Object.keys( this.types ).forEach( function( key ) {
                    var type = this.types[ key ];
                    var taxonomy = type.taxonomies[ type.tab ];
                    if ( ! ( taxonomy && taxonomy.activeTermId ) ) {
                        type.tab = 'search-form';
                    }
                }.bind(this) );
            }

            /**
             * Save scroll position of the results view, so that it's possible
             * to view a listing, go back, and continue where you left off.
             *
             * @since 2.4
             */
            var saveScroll = e => {
                var scrollPosition = this.isMobile ? jQuery(document).scrollTop() : jQuery('.finder-listings').scrollTop();

                if ( scrollPosition > 0 ) {
	                window.history.replaceState( null, null, this.updateUrlParameter(
	                    window.location.href, 'sp', Math.round( scrollPosition )
	                ) );
                }
            };

            window.addEventListener( 'beforeunload', saveScroll );
            window.addEventListener( 'unload', saveScroll );

            this.jQueryReady();
        },

        methods: {
            setType: function( type ) {
                if ( ! this.types[ type ] ) {
                    return;
                }

                this.activeType = this.types[ type ];
            },

            /**
    		 * Debounced wrapper for `_getListings`.
             */
            getListings: MyListing.Helpers.debounce( function( context, forceGet ) {
                if ( this.isMobile && forceGet !== true ) {
                    return;
                }

                this._getListings( context );
            }, 500 ),

            /**
    		 * Short debounced wrapper for `_getListings`.
             */
            getListingsShort: MyListing.Helpers.debounce( function( context, forceGet ) {
                if ( this.isMobile && forceGet !== true ) {
                    return;
                }

                this._getListings( context );
            }, 250 ),

            filterChanged( value, event ) {
                // activeType.is_first_load check added becaused of results not loading on Explore mobile when a location is
                // passed through the URL, and the location filter is not the primary filter.
                if ( this.isMobile && event.location !== 'primary-filter' && ! event.forceGet && ! this.activeType.is_first_load ) {
                    return;
                }

                if ( event.shouldDebounce === false ) {
                    this._getListings( `${event.filterType}:${event.filterKey}` );
                } else {
                    this.getListings( `${event.filterType}:${event.filterKey}`, true );
                }
            },

            /**
    		 * Perform a new ajax request to get search results based on active listing type's filters.
    		 *
    		 * @since 1.0
             */
            _getListings: function( context ) {
                if ( CASE27.env === 'dev' ) {
    				console.log( '%c Get Listings ['+context+']', 'background-color: darkred; color: #fff;' );
                }

                this.loading = true;

                // reset listings added for comparison
                this._clearCompareListing();

                var self = this;
                var listing_type = this.activeType;

                if ( ! this.activeType.filters.preserve_page ) {
                    this.activeType.filters.page = 0;
                }

                var tax = this.activeType.taxonomies[ this.activeType.tab ];
                if ( typeof tax !== 'undefined' && tax.activeTermId !== 0 ) {
                    var form_data = {
                        context: 'term-search',
                        taxonomy: tax.tax,
                        term: tax.activeTermId,
                        page: tax.page,
                        sort: this.activeType.filters.sort,

                        // add support for nearby order in single term page
                        search_location: this.activeType.filters.search_location,
                        lat: this.activeType.filters.lat,
                        lng: this.activeType.filters.lng,
                        proximity: this.activeType.filters.proximity,
                        proximity_units: this.activeType.filters.proximity_units,
                    }
                } else {
                    var form_data = this.activeType.filters;
                }

                var request_body = {
                    form_data: form_data,
                    listing_type: this.activeType.slug,
                    listing_wrap: CASE27_Explore_Settings.ListingWrap
                };

                if ( CASE27_Explore_Settings.DisplayAd && CASE27_Explore_Settings.AdPublisherID && CASE27_Explore_Settings.AdSlotID && CASE27_Explore_Settings.AdInterval ) {
                	request_body.display_ad = CASE27_Explore_Settings.DisplayAd;
                	request_body.pub_id = CASE27_Explore_Settings.AdPublisherID;
                	request_body.slot_id = CASE27_Explore_Settings.AdSlotID;
                	request_body.ad_interval = CASE27_Explore_Settings.AdInterval;
                }
                
                var request_body_string = JSON.stringify( request_body );

                // if no search-form arguments have changed, no need to perform an additional ajax request
                if ( this.activeType.last_response && ( request_body_string === this.activeType.last_request_body ) ) {
                    if ( CASE27.env === 'dev' ) {
                        console.warn( 'Ignoring call to getListings, no search arguments have changed.' );
                    }

                    var last_response = this.activeType.last_response;
                    this.updateUrl();

                    setTimeout( function() {
                        this.loading = false;
                        if ( this.activeType.last_response ) {
                            this.updateView( last_response, context );
                        }
                    }.bind(this), 200 );
                    return;
                }

                // log differences in search arguments if they have changed, for dev purposes only
                if ( CASE27.env === 'dev' && this.activeType.last_request_body ) {
                    console.log( '%c Getting listings, arguments diff:', 'color: #a370ff' );
                    console.table( objectDiff( JSON.parse( this.activeType.last_request_body ).form_data, JSON.parse( request_body_string ).form_data ) );
                }

                // run new ajax request
                this.updateUrl();

                jQuery.ajax( {
                    url: CASE27.mylisting_ajax_url + '&action=get_listings&security=' + CASE27.ajax_nonce,
                    type: 'GET',
                    dataType: 'json',
                    data: request_body,
                    beforeSend: function( xhr, settings ) {
                        if ( self.last_request ) {
                            self.last_request.abort();
                        }
                        self.last_request = xhr;
                    },
                    success: function( response ) {
                        if ( ! ( typeof response === 'object' ) ) {
                            return;
                        }

                		// cache responses only for search-form tab
    	            	if ( listing_type.tab === 'search-form' ) {
    	                    listing_type.last_response = response;
    	                    listing_type.last_request_body = request_body_string;
    	                }

    	                // if the active listing type changed during the ajax request, then don't update views
    					if ( listing_type.slug === self.activeType.slug ) {
    	                    self.loading = false;
    	                    self.updateView( response, context );
                        }
                    },
                } );
            },

            updateUrl: function() {
                if ( ! window.history || CASE27_Explore_Settings.DisableLiveUrlUpdate ) {
                    return false;
                }

                var filters  = this.activeType.filters;
                var params   = {};

                if ( ! window.location.search && CASE27_Explore_Settings.IsFirstLoad ) {
                    return false;
                }

                // add current listing type to params, unless we're in the default
                // listing type, in which case it would be redundant so it's skipped
                if ( this.activeType.index !== 0 ) {
                    params['type'] = this.activeType.slug;
                }

                // add tab to the url unless it's the default tab
                if ( this.activeType.tab !== this.activeType.defaultTab ) {
                    params['tab'] = this.activeType.tab;
                }

                if ( this.activeType.tab === 'search-form' ) {
                    Object.keys( filters ).forEach( function( filter ) {
                        // Get filter value.
                        var value = filters[filter];
                        var key = filter;

                        if ( filter == 'proximity_units' ) {
                            return false;
                        }

                        if ( ( filter === 'lat' || filter === 'lng' ) && value && typeof filters['search_location'] !== 'undefined' && filters['search_location'].length ) {
                            var key = filter;
                            var length = value.toString().indexOf('-') > -1 ? 9 : 8;
                            value = value.toString().substr( 0, length );
                        }

                        if ( filter == 'proximity' && ( ! filters['lat'] || ! filters['lng'] ) ) {
                            return false;
                        }

                        // Page is saved from 0, so add one when displaying in the url.
                        if ( filter === 'page' && value > 0 ) {
                            value += 1;
                            key = 'pg'; // 'page' filter is reserved by WordPress.
                        }

                        // Add filter to url.
                        if ( value && typeof value.length !== 'undefined' && value.length ) {
                            params[key] = value;
                        } else if ( typeof value === 'number' && value ) {
                            params[key] = value;
                        }
                    } );
                }

                if ( this.currentTax && this.currentTax.activeTerm ) {
                    var link = this.currentTax.activeTerm.link;
                    if ( this.currentTax.page > 0 ) {
                        var link = this.updateUrlParameter( link, 'pg', ( this.currentTax.page + 1 ) );
                    }

                    // single term tab
                    window.history.replaceState( null, null, link );
                } else {
                    var querystring = jQuery.param(params).replace(/%2C/g, ',');
                    window.history.replaceState( null, null, this.baseUrl + ( querystring.trim().length ? '?'+querystring : '' ) );
                }
            },

            updateView: function(response, context) {
                var data = response;
                var self = this;
                this.activeType.is_first_load = false;

                this.found_posts = response.found_posts;

                this.$emit( 'update-results', response, context );

                this.activeType.filters.preserve_page = false;

                // Append Listings.
                if (jQuery('.finder-listings .results-view').length) {
                    jQuery('.finder-listings .results-view').html(data.html);
                }

                // Append listings on template type 2.
                if (jQuery('.fc-type-2-results').length) {
                    jQuery('.fc-type-2-results').html(data.html);
                }

                var isotope = () => {
                    if ( typeof jQuery('.results-view.grid').data('isotope') !== 'undefined' ) {
                        jQuery('.results-view.grid').isotope('destroy');
                    }

                    var gridSettings = { itemSelector: '.grid-item' };
                    if ( jQuery('body').hasClass('rtl') ) {
                        gridSettings.originLeft = false;
                    }
                    jQuery('.results-view.grid').isotope( gridSettings );
                };

                setTimeout( () => {
                    jQuery( this.$el ).find('ins').each( (i, el) => {
                        if ( el.dataset.adsbygoogleStatus !== 'done' ) {
                            (adsbygoogle = window.adsbygoogle || []).push({});
                        }
                    } );

                    setTimeout( () => isotope(), 10 );
                }, 10 );

                setTimeout( () => isotope(), 10 );

                jQuery('.lf-background-carousel').owlCarousel({
                    margin:20,
                    items:1,
                    loop: true,
                });

                jQuery('[data-toggle="tooltip"]').tooltip({
                    trigger: 'hover',
                });

                // Append pagination.
                if (jQuery('.c27-explore-pagination').length) {
                    jQuery('.c27-explore-pagination').html( data.pagination );
                    jQuery('.c27-explore-pagination a').each( function() {
                        var pg = jQuery(this).data('page');
                        var url = window.location.href;
                        jQuery(this).attr( 'href', self.updateUrlParameter( url, 'pg', pg ) );
                    } );
                }

                // restore scroll position on first load
                // @todo: implement for explore alternate
                if ( CASE27_Explore_Settings.ScrollPosition > 50 ) {
                    setTimeout( () => {
                        var sp = CASE27_Explore_Settings.ScrollPosition;
                        this.isMobile ? jQuery(document).scrollTop(sp) : jQuery('.finder-listings').scrollTop(sp);
                        CASE27_Explore_Settings.ScrollPosition = 0;
                    }, 30 );
                } else {
                    /* Scroll to top of results */
                    // Explore alternate, with scroll-to-results enabled.
                    if (jQuery('.finder-container .fc-one-column').length && CASE27_Explore_Settings.ScrollToResults) {
                        jQuery('.finder-container .fc-one-column').animate( { scrollTop: jQuery('.finder-search').outerHeight() } );
                    }

                    // Desktop
                    if ( window.matchMedia("(min-width: 1200px)").matches ) {
                        // Explore default template
                        if ( jQuery('.finder-container .fc-default .finder-listings').length ) {
                            jQuery('.finder-container .fc-default .finder-listings').animate( { scrollTop: 0 } );
                        }

                        // Explore alternate with scroll-to-results disabled
                        if ( context === 'pagination' && ! CASE27_Explore_Settings.ScrollToResults && jQuery('.finder-container .fc-one-column').length ) {
                            jQuery('.finder-container .fc-one-column').animate( { scrollTop: jQuery('.finder-search').outerHeight() } );
                        }

                        if ( context === 'pagination' ) {
							jQuery('html, body').animate( {
							    scrollTop: jQuery( this.$el ).offset().top,
							} );
                        }
                    } else {
                        // mobile
                        if ( this.state.mobileTab === 'results' ) {
                            this._resultsScrollTop();
                        }
                    }
                }

                this.updateMap();
                CASE27_Explore_Settings.IsFirstLoad = false;
            },

            _resultsScrollTop: function() {
                jQuery('html, body').animate({
                    scrollTop: jQuery('#c27-explore-listings').offset().top - 100,
                }, 'slow');
            },

            _compareListing: function() {
                jQuery('#comparison-view').modal('show').addClass('loading-modal');
                jQuery('#comparison-view .modal-dialog').hide();
                jQuery('#comparison-view .loader-bg').show();
                jQuery.ajax( {
                    url: CASE27.mylisting_ajax_url + '&action=compare_listings&security=' + CASE27.ajax_nonce,
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        listing_ids: this.compare,
                    },
                    success: function( response ) {
                        if ( ! ( typeof response === 'object' ) ) {
                            return;
                        }

                        // console.log(response);
                        jQuery('#comparison-view').removeClass('loading-modal');
                        jQuery('#comparison-view .modal-content').html(response.html);
                        jQuery('#comparison-view .loader-bg').hide();
                        jQuery('#comparison-view .modal-dialog').show();
                    },
                } );
            },

            _clearCompareListing: function() {
                this.compare = [];
                jQuery('.lf-item-container.compare-chosen .c27-compare-button i').removeClass('remove').addClass('add');
                jQuery('.lf-item-container.compare-chosen').removeClass('compare-chosen');
            },

            setupMap: function() {
                var map_id = jQuery(this.$el).find('.finder-map .map').attr('id');

                if ( ! MyListing.Maps.getInstance( map_id ) ) {
                    return false;
                }

                this.map = MyListing.Maps.getInstance( map_id ).instance;
                var map = this.map;
                MyListing.Geocoder.setMap( map );

                if ( CASE27_Explore_Settings.DragSearchEnabled ) {
                    var dragHandler = MyListing.Helpers.debounce( () => {
                    	if ( ! this.dragSearch || this.suspendDragSearch ) {
                    		return;
                    	}

                        var center = this.map.getCenter();
                        this.activeType.filters.lat = center.getLatitude();
                        this.activeType.filters.lng = center.getLongitude();
                        this.activeType.filters.search_location = CASE27_Explore_Settings.DragSearchLabel;

                        // calculate proximity dynamically
                        var dimensions = this.map.getDimensions();
                        var proximity = Math.max( dimensions.width, dimensions.height ) / 2;

                        var proximityRef = this.$refs[ `${this.activeType.slug}_proximity` ];

                        // convert kilometers to miles if necessary
                        if ( proximityRef && proximityRef.units === 'mi' ) {
                            proximity = proximity / 1.609;
                        }

                        this.activeType.filters.proximity = Math.round( proximity * 100 ) / 100;

                        if ( ! ( jQuery('body').hasClass('modal-open') || jQuery('body').hasClass('modal-closed') ) ) {
                            this._getListings( 'map_drag' );
                        }
                    }, 300, { leading: true, trailing: false } );

                    this.map.addListenerOnce( 'updated_markers', () => {
                        setTimeout( () => {
                            var eventToListen = this.mapProvider === 'mapbox' ? 'moveend' : 'idle';
                            this.map.addListener( eventToListen, dragHandler );
                        }, 100 );
                    } );
                }

                // geolocation control
                if ( navigator.geolocation ) {
                    var dialog = false;
                    var marker = false;
                    var locationControl = document.getElementById( 'explore-map-location-ctrl' );
                    locationControl.addEventListener( 'click', function() {
                        navigator.geolocation.getCurrentPosition(
                            function( position ) {
                                if ( ! marker ) {
                                    marker = new MyListing.Maps.Marker( {
                                        position: new MyListing.Maps.LatLng( position.coords.latitude, position.coords.longitude ),
                                        map: map,
                                        template: { type: 'user-location' },
                                    } );
                                }

                                map.setZoom( CASE27_Explore_Settings.Map.default_zoom );
                                map.setCenter( marker.getPosition() );
                            },
                            function( error ) {
                                if ( ! dialog ) {
                                    dialog = new MyListing.Dialog( { message: CASE27.l10n.geolocation_failed } );
                                }

                                if ( ! dialog.visible ) {
                                    dialog.refresh();
                                    dialog.show();
                                }
                            }
                        );
                    } );

                    this.map.addControl( locationControl );
                }
            },

            updateMap: function() {
                var self = this;

                if ( ! self.map ) {
                    // Fallback in case the results are loaded ahead of the map being fully working.
                    // In which case, we need to update the map once it's loaded.
                    if ( document.getElementsByClassName('finder-map').length ) {
                        var mapInterval = setInterval(function() {
                            if ( self.map ) {
                                clearInterval( mapInterval );
                                self.updateMap();
                            }
                        }, 200);
                    }

                    return;
                };

                self.map.$el.removeClass('mylisting-map-loading');
                self.map.removeMarkers();
                self.map.trigger( 'updating_markers' );
                // this.map.clusterer.refresh();
                // console.log('Map: ', this.map);

                var bounds = new MyListing.Maps.LatLngBounds();
                jQuery(this.$el).find('.results-view .lf-item-container').each( function(i, el) {
                    var $el = jQuery(el);
                    if ( ! $el.data('latitude') || ! $el.data('longitude') ) {
                        return;
                    }

                    var marker = new MyListing.Maps.Marker( {
                        position: new MyListing.Maps.LatLng( $el.data('latitude'), $el.data('longitude') ),
                        map: self.map,
                        popup: new MyListing.Maps.Popup( { content: '<div class="lf-item-container lf-type-2">' + $el.html() + '</div>' } ),
                        template: {
                            type: 'advanced',
                            thumbnail: $el.data('thumbnail'),
                            icon_name: $el.data('category-icon'),
                            icon_background_color: $el.data('category-color'),
                            icon_color: $el.data('category-text-color'),
                            listing_id: $el.data('id'),
                        }
                    } );
                    self.map.markers.push( marker );
                    bounds.extend( marker.getPosition() );
                } );

                if ( ! this.dragSearch || CASE27_Explore_Settings.IsFirstLoad ) {
                    if ( ! bounds.empty() ) {
                        self.map.fitBounds( bounds );
                    }

                    // Prevent large zooming when one listing only is found.
                    if ( self.map.getZoom() > 17 ) {
                        self.map.setZoom(17);
                    }

                    // Show world map when no results are returned.
                    if ( self.map.markers.length < 1 || bounds.empty() ) {
                        self.map.setZoom( CASE27_Explore_Settings.Map.default_zoom );
                        self.map.setCenter( new MyListing.Maps.LatLng( CASE27_Explore_Settings.Map.default_lat, CASE27_Explore_Settings.Map.default_lng ) );
                    }
                }

                self.map.trigger( 'updated_markers' );
            },

            resetFilters: function(e) {
                if ( e && e.target ) {
                    var icon = jQuery(e.target).find('i');
                    icon.removeClass('fa-spin');
                    setTimeout( function() {
                        icon.addClass('fa-spin');
                    }, 5 );
                }

                var order_filter = jQuery('.search-filters.type-id-' + this.activeType.id);

                order_filter.find('.panel-dropdown').removeClass('active');
                order_filter.find('.panel-dropdown').removeClass('active_filter');
                
                this.$emit( 'reset-filters' );
                this.$emit( 'reset-filters:'+this.activeType.slug );
            },

            jQueryReady: function() {
                var self = this;

                // @todo: optimize
                jQuery( function( $ ) {
                    $('body').on( 'click', '.c27-explore-pagination a', function(e) {
                        e.preventDefault();

                        var page = parseInt( $(this).data( 'page' ), 10 ) - 1;
                        if ( self.activeType.taxonomies[ self.activeType.tab ] ) {
                            self.activeType.taxonomies[ self.activeType.tab ].page = page;
                        }
                        self.activeType.filters.page = page;
                        self.activeType.filters.preserve_page = true;
                        self._getListings( 'pagination' );
                    } );

                    jQuery('.col-switch').click(function(e) {
                        self.map.trigger('resize');
                    });

                    jQuery('body').on('mouseenter', '.results-view .lf-item-container.listing-preview', function() {
                        jQuery( '.marker-container .marker-icon.' + jQuery(this).data('id') ).addClass('active');
                    });

                    jQuery('body').on('mouseleave', '.results-view .lf-item-container.listing-preview', function() {
                        jQuery( '.marker-container .marker-icon.' + jQuery(this).data('id') ).removeClass('active');
                    });
                } );
            },

            termsExplore: function( taxonomy, term, loadMore ) {
                var self = this;
                this.activeType.tab = taxonomy;
                var tax = this.activeType.taxonomies[ this.activeType.tab ];
                var loadMore = loadMore || false;
                var tab = this.activeType.tabs[ this.activeType.tab ] || {};

                // prevent execution if a term query is already in progress
                if ( tax.termsLoading ) {
                    return;
                }

                if ( term === 'active' ) {
                    term = tax.activeTerm;
                }

                // reset active taxonomy values
                tax.activeTerm = false;
                if ( ! loadMore ) {
                    tax.terms = false;
                }

                // determine term id; if available, get the active term object right away
                if ( typeof term === 'object' && term.term_id ) {
                    tax.activeTermId = term.term_id;
                    tax.activeTerm = term;
                } else if ( ! isNaN( parseInt( term, 10 ) ) ) {
                    tax.activeTermId = term;
                } else {
                    tax.activeTermId = 0;
                }

                // since we have the id for the term we're loading, we can simultaneously load
                // the listing results while the detailed term data is getting fetched.
                if ( ! this.activeType.filters.preserve_page ) {
                    this.currentTax.page = 0;
                }

                // add support for nearby order in taxonomy tabs
                var order_filter = jQuery('.search-filters.type-id-' + this.activeType.id + ' .orderby-filter');

                if ( order_filter.hasClass('has-proximity-clause') ) {
                    this.$emit( 'request-location:'+this.activeType.slug );
                } else {
    	            this._getListings( 'terms-explore' );
                }

                // prepare cache
                if ( typeof CASE27_Explore_Settings.TermCache[ this.activeType.slug ] === 'undefined' ) {
                    CASE27_Explore_Settings.TermCache[ this.activeType.slug ] = {};
                }
                if ( typeof CASE27_Explore_Settings.TermCache[ this.activeType.slug ][ tax.tax ] === 'undefined' ) {
                    CASE27_Explore_Settings.TermCache[ this.activeType.slug ][ tax.tax ] = {};
                }

                // see if this term has already been fetched and stored in cache
                var termCache = CASE27_Explore_Settings.TermCache[ this.activeType.slug ][ tax.tax ][ tax.activeTermId ];
                if ( termCache ) {
                    if ( ( ! loadMore || ( loadMore && ! termCache.hasMore ) ) && typeof termCache.pages[ tax.termsPage ] !== 'undefined' ) {
                        // console.log('Getting terms from cache for parent_id: '+tax.activeTermId+' and page #'+tax.termsPage);
                        tax.activeTerm = termCache.details;
                        tax.hasMore = termCache.hasMore;
                        tax.terms = [];
                        Object.keys( termCache.pages ).forEach( function( page ) {
                            Object.keys( termCache.pages[ page ] ).forEach( function( term_id ) {
                                tax.terms.push( termCache.pages[ page ][ term_id ] );
                            } );
                        } );
                        this.updateUrl();
                        return;
                    } else {
                        // increment termsPage to query next page of results
                        tax.termsPage = termCache.currentPage + 1;
                    }
                } else {
                    // no term cache, make sure to load the first page
                    tax.termsPage = 0;
                }

                // console.warn('Querying terms for parent_id: '+tax.activeTermId+' and page #'+tax.termsPage);

                // term not available in cache, query it
                // console.log('Querying terms for parent_id: '+tax.activeTermId);
                tax.termsLoading = true;
                jQuery.ajax( {
                    url: CASE27.mylisting_ajax_url + '&action=explore_terms&security=' + CASE27.ajax_nonce,
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        taxonomy: tax.tax,
                        parent_id: tax.activeTermId,
                        type_id: this.activeType.id,
                        page: tax.termsPage,
                        per_page: CASE27_Explore_Settings.TermSettings.count,
                        orderby: tab.orderby,
                        order: tab.order,
                        hide_empty: tab.hide_empty ? 'yes' : 'no',
                        // hierarchical: tab.hierarchical ? 'yes' : 'no',
                    },
                    success: function( response ) {
                        tax.termsLoading = false;
                        if ( response.success != true ) {
                            return new MyListing.Dialog( { message: response.message } );
                        }

                        var cache = CASE27_Explore_Settings.TermCache[ self.activeType.slug ][ tax.tax ];
                        if ( ! cache[ tax.activeTermId ] ) {
                            cache[ tax.activeTermId ] = { details: {}, pages: {} };
                        }

                        tax.activeTerm = response.details;
                        tax.hasMore = response.more;

                        // store in cache
                        cache[ tax.activeTermId ].details = response.details;
                        cache[ tax.activeTermId ].hasMore = response.more;
                        cache[ tax.activeTermId ].currentPage = tax.termsPage;
                        cache[ tax.activeTermId ].pages[ tax.termsPage ] = response.children;

                        if ( loadMore ) {
                            // append new terms
                            Object.keys( response.children ).forEach( function( term_id ) {
                                tax.terms.push( response.children[ term_id ] );
                            } );
                        } else {
                            tax.terms = [];
                            Object.keys( cache[ tax.activeTermId ].pages ).forEach( function( page ) {
                                Object.keys( cache[ tax.activeTermId ].pages[ page ] ).forEach( function( term_id ) {
                                    tax.terms.push( cache[ tax.activeTermId ].pages[ page ][ term_id ] );
                                } );
                            } );
                        }

                        self.updateUrl();
                    },
                } );
            },

            termsGoBack: function( term ) {
                this.termsExplore( this.activeType.tab, term.parent );
                if ( parseInt( term.parent, 10 ) !== 0 ) {
                    this.currentTax.page = 0;
                    this.getListings( 'terms-go-back' );
                }
            },

            /**
             * Add or update a key-value pair in the URL query parameters.
             *
             * @link  https://gist.github.com/niyazpk/f8ac616f181f6042d1e0
             * @since 2.3.2
             */
            updateUrlParameter: function( uri, key, value ) {
                // remove the hash part before operating on the uri
                var i = uri.indexOf('#');
                var hash = i === -1 ? ''  : uri.substr(i);
                     uri = i === -1 ? uri : uri.substr(0, i);

                var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
                var separator = uri.indexOf('?') !== -1 ? "&" : "?";
                if (uri.match(re)) {
                    uri = uri.replace(re, '$1' + key + "=" + value + '$2');
                } else {
                    uri = uri + separator + key + "=" + value;
                }
                return uri + hash;  // finally append the hash as well
            },

            hasValidLocation( listingType ) {
                if ( ! ( this.types[ listingType ] ) ) {
                    return false;
                }

                var filters = this.types[ listingType ].filters;
                return filters.lat && filters.lng && filters.search_location;
            },

            toggleMap( expanded ) {
                this.mapExpanded = expanded;
                setTimeout(() => this.map.trigger('refresh'), 5);
            },
        },

        computed: {
            currentTax: function() {
                return this.activeType.taxonomies[ this.activeType.tab ];
            },

            currentTermName() {
            	var name = this.currentTax.activeTerm ? this.currentTax.activeTerm.name : '&nbsp;'
            	return `<h1 class="category-name">${name}</h1>`;
            },

            showBackToFilters() {
                var tabs = this.activeType.tabs;
                return tabs['search-form'] && ( this.isMobile || Object.keys( tabs ).length === 1 );
            },

            containerStyles() {
                var styles = 'top:0;';
                if ( ! this.isMobile ) {
                    var header = document.querySelector('header.header');
                    if ( header ) {
                        var rect = header.getBoundingClientRect();
                        styles += `height: calc(100vh - ${Math.round(rect.height+rect.y)}px);`;
                    }
                }

                return styles;
            }
        },

        watch: {
            activeType: function() {
                if ( ! this.activeType ) {
                    return;
                }

                if ( this.activeType.tab === 'search-form' ) {
                    var order_filter = jQuery('.search-filters.type-id-' + this.activeType.id + ' .orderby-filter');
                    var should_geocode_location = this.activeType.filters.search_location
                    	&& ! ( this.activeType.filters.lat || this.activeType.filters.lng );

                	if ( this.activeType.last_response ) {

                		/**
    					 * When switching between types, only trigger an ajax request the first time.
                		 */
                        this.loading = false;
                        this.updateUrl();
                        this.updateView( this.activeType.last_response, 'switch-listing-type' );
                    } else if ( order_filter.hasClass('has-proximity-clause') || should_geocode_location ) {

    	            	/**
    					 * If the default sorting option is `nearby-listings`, then call `getNearbyListings()`
    					 * which will request the initial list of search results.
    	            	 */
                        this.$emit( 'request-location:'+this.activeType.slug );
                    } else {

    					/**
    					 * Finally, if `getListings` hasn't yet been called by the other methods, call it directly.
    					 */
                        this._getListings( 'switch-listing-type' );
                    }
                } else {
                    // we're on a taxonomy tab
                    var term_id = parseInt( this.activeType.taxonomies[ this.activeType.tab ].activeTermId, 10 );
                    if ( ! isNaN( term_id ) && term_id !== 0 ) {
                        // if we're in a single term page, then load the term
                        this.termsExplore( this.activeType.tab, 'active' );
                    } else {
                        // if the default tab is set to a taxonomy, load the taxonomy terms
                        this.termsExplore( this.activeType.tab );
                    }
                }
            },
        },
    });
};

MyListing.Explore_Init();
document.addEventListener( 'DOMContentLoaded', MyListing.Explore_Init );