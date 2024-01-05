( $ => {
    $('.profile-tab-toggle').on( 'click', e => {
        e.stopPropagation();
        e.preventDefault();
        $('.profile-menu li.active').removeClass('active');
        $(e.target).parent().addClass('active');

        var currentTab = $('.listing-tab.tab-active');
        var section_id = $(e.target).data('section-id');
        var newTab = $('.listing-tab#profile_tab_' + section_id );

        if ( currentTab.attr('id') === 'profile_tab_' + section_id ) {
            currentTab.addClass('tab-same');
            setTimeout( () => currentTab.removeClass('tab-same'), 100 );
            return;
        }

        currentTab.addClass('tab-hiding');
        setTimeout( () => {
            currentTab.removeClass('tab-active tab-hiding').addClass('tab-hidden');
            newTab.addClass('tab-showing');

            setTimeout( () => {
                newTab.removeClass('tab-hidden tab-showing').addClass('tab-active').trigger('mylisting:single:tab-switched');
                jQuery(document).trigger('mylisting/single:tab-switched');
                // Isotope
                if ($('body').hasClass('rtl')) {
                    $('.grid').isotope({
                        originLeft: false,
                    });
                } else {
                    $('.grid').isotope();
                }
            }, 25 );
        }, 200 );
    } );
} )(jQuery);

jQuery( document ).ready( function( $ ) {
    
    $('.effacer').each( function( i, el ) {
        $(this).on('click', function( e ) {
            $(this).parents('.panel-dropdown').removeClass('active');
            $(this).parents('.panel-dropdown').removeClass('active_filter');
            new MyListing.CustomSelect( $(this).parents('.panel-dropdown').find('select') );
            jQuery( $(this).parents('.panel-dropdown').find('select') ).val( [] ).trigger('change').trigger('select2:close');
            $(this).parents('.panel-dropdown').find('.tags-nav').find("input:checked").each(function (i, ob) { 
                $(ob).attr('checked', false);
                $(ob).trigger('click');
            });

            var range = $(this).parents('.panel-dropdown').find( '.mylisting-range-slider');

            if ( range ) {

                var sliderOpts = {
                    range: range.data('type') === 'single' ? 'min' : true,
                    min: range.data('min'),
                    max: range.data('max'),
                    step: range.data('step')
                };

                // set default value for single slider
                if ( range.data('type') === 'single' ) {
                    sliderOpts.value = range.data('value')
                        ? parseFloat( range.data('value') )
                        : ( range.data('behavior') === 'upper' ? range.data('min') : range.data('max') );

                    if ( range.data('behavior') === 'upper' ) {
                        sliderOpts.classes = {
                            'ui-slider': 'reverse-dir',
                        };
                    }
                }

                // set default values for range slider
                if ( range.data('type') === 'range' ) {
                    var values = range.data('value').split('..');
                    sliderOpts.values = [
                        values[0] ? parseFloat( values[0] ) : range.data('min'),
                        values[1] ? parseFloat( values[1] ) : range.data('max'),
                    ];
                }

                // init range slider
                jQuery( range.find('.slider-range') ).slider( sliderOpts );
            }
        });
    });

    $('.pannel-proximity-content .effacer').on('click', function() {
        $(this).parents('.pannel-proximity-content').removeClass('active');
        $(this).parents('.pannel-proximity-content').removeClass('active_filter');
        var defaultValue = $(this).parents('.pannel-proximity-content').data('default'),
            label = $(this).parents('.pannel-proximity-content').data('label'),
            units = $(this).parents('.pannel-proximity-content').data('units');

        $('.location-filter input').val('').trigger('input');
    });

    $('.effacer').on('click', function( e ) {
        $(this).parents('.panel-dropdown').removeClass('active');
        $(this).parents('.panel-dropdown').removeClass('active_filter');
        new MyListing.CustomSelect( $(this).parents('.panel-dropdown').find('select') );
        jQuery( $(this).parents('.panel-dropdown').find('select') ).val( [] ).trigger('change').trigger('select2:close');
        $(this).parents('.panel-dropdown').find('.tags-nav').find("input:checked").each(function (i, ob) { 
            $(ob).attr('checked', false);
            $(ob).trigger('click');
        });

        var range = $(this).parents('.panel-dropdown').find( '.mylisting-range-slider');

        if ( range ) {

            var sliderOpts = {
                range: range.data('type') === 'single' ? 'min' : true,
                min: range.data('min'),
                max: range.data('max'),
                step: range.data('step')
            };

            // set default value for single slider
            if ( range.data('type') === 'single' ) {
                sliderOpts.value = range.data('value')
                    ? parseFloat( range.data('value') )
                    : ( range.data('behavior') === 'upper' ? range.data('min') : range.data('max') );

                if ( range.data('behavior') === 'upper' ) {
                    sliderOpts.classes = {
                        'ui-slider': 'reverse-dir',
                    };
                }
            }

            // set default values for range slider
            if ( range.data('type') === 'range' ) {
                var values = range.data('value').split('..');
                sliderOpts.values = [
                    values[0] ? parseFloat( values[0] ) : range.data('min'),
                    values[1] ? parseFloat( values[1] ) : range.data('max'),
                ];
            }

            // init range slider
            jQuery( range.find('.slider-range') ).slider( sliderOpts );
        }
    });

    $('.valider').each( function( i, el ) {
        $(this).on('click', function( e ) {
            $(this).parents('.panel-dropdown').removeClass('active');
            $(this).parents('.pannel-proximity-content').hide();
        });
    });

    $('.valider').on('click', function( e ) {
        $(this).parents('.panel-dropdown').removeClass('active');
        $(this).parents('.pannel-proximity-content').hide();
    });

    var address = $('.gropup-start .location-filter input');
    address.on( 'autocomplete:change', function(e) {
        if ( ! e.detail.place || ! e.detail.place.latitude || ! e.detail.place.longitude ) {
            return;
        }

        $('.pannel-proximity-content').show();
    });

    $('select#price_converter, #price, #car-price, #prix-du-produit, #votre-prix').on( 'input, change', function() {
        
        var currentValue = $('select#price_converter').val();
        var currentRate = $('select#price_converter').data('rate');
        var price = $('#price, #car-price, #prix-du-produit, #votre-prix');

        if ( ! price.val() || ! currentValue ) {
            return;
        }

        var $prefix = ' BTC';

        if ( currentValue == 'ETHEUR' ) {
            $prefix = ' ETH';
        } else if ( currentValue == 'EURUSDT' ) {
            $prefix = ' USDT';
        }

        if ( typeof currentRate[currentValue] != "undefined" ) {
            $('.fieldset-converted_price').addClass('show');
            $('.ml-converted-price').removeClass('hide');
            var htmlprice = price.val() / currentRate[currentValue];
            $('.ml-converted-price span').html( parseFloat(htmlprice) + $prefix );
        }

        // $.ajax({
        //     url: CASE27.mylisting_ajax_url + '&action=get_converted_price&security=' + CASE27.ajax_nonce,
        //     type: 'POST',
        //     dataType: 'json',
        //     data: { symbol : currentValue, price: price.val() },
        //     success: function( response ) {
        //         if ( response.status && response.price ) {
        //             $('.ml-converted-price').removeClass('hide');
        //             $('.ml-converted-price span').html( response.price );
        //         }
        //     },
        // });
    });
});

jQuery(document).ready(function($){
    if ( ! MyListing.Explore ) {
        return;
    }

    MyListing.Explore.filterChanged = function( value, event ) {

        if ( MyListing.Explore.isMobile.isMobile && event.location !== 'primary-filter' && ! event.forceGet && ! MyListing.Explore.isMobile.activeType.is_first_load ) {
            return;
        }

        jQuery('.panel-dropdown.active').addClass('active_filter');

        if ( event.shouldDebounce === false ) {
            MyListing.Explore._getListings( `${event.filterType}:${event.filterKey}` );
        } else {
            MyListing.Explore.getListings( `${event.filterType}:${event.filterKey}`, true );
        }
    }
});

jQuery( function( $ ) {
    var modal = $('#profile_tab_reviews');
    var validation = modal.find('.validation-message');

    modal.find('.review-submit').on( 'click', function(e) {
        e.preventDefault();
        validation.hide();
        var fields = modal.find('form').serialize();

        modal.find('.sign-in-box').addClass('cts-processing-login'); // Adds a loading animation.
        $.ajax( {
            url: CASE27.ajax_url + '?action=author_review_request&security=' + CASE27.ajax_nonce,
            type: 'POST',
            dataType: 'json',
            data: fields,
            success: function( response ) {
                console.log( response.status )
                if ( response.status === 'error' ) {
                    validation.html('<em>'+response.message+'</em>').show()
                }

                if ( response.status === 'success' ) {
                    var url = window.location.href;
                    var redirect = url+'?'+'rating-submitted=1';
                    location.replace( redirect );
                }
            },
        } );
    } );
} );

jQuery(document).ready(function($){

    $('.fieldset-price_converter select').on('select2:open', function() {
        setTimeout( () => {
            $('.select2-search--hide').hide();
        }, 100 );
    });

    $('.cts-autocomplete-input').on('input, change', function() {
        $('.explore-filter.proximity-filter, .explore-filter.pannel-proximity-content').show();
    });

    function close_panel_dropdown() {
        $('.panel-dropdown').removeClass("active");
        $('.fs-inner-container.content').removeClass("faded-out");
        $('.select2-search--dropdown.select2-search--hide, .pannel-proximity-content').hide();
    }

    $('.panel-dropdown a').on('click', function(e) {
        if ( $(this).parent().is(".active") ) {
            close_panel_dropdown();
        } else {
            close_panel_dropdown();
            $(this).parent().addClass('active');
            new MyListing.CustomSelect( $(this).parent().find('select') );

            $(this).parent().find('select').select2("open");

            // $('.select2-container--open input').attr('placeholder', $(this).parent().find('select').attr('placeholder') );
            $('.select2-search--dropdown.select2-search--hide').show();
        }

        e.preventDefault();
    });

    // Apply / Close buttons
    $('.panel-buttons button,.panel-buttons span.panel-cancel').on('click', function(e) {
        $('.panel-dropdown').removeClass('active');
        $('.fs-inner-container.content').removeClass("faded-out");
    });

    // Closes dropdown on click outside the conatainer
    var mouse_is_inside = false;

    $('.proximity-filter').hover(function(){
        mouse_is_inside=true;
    }, function(){
        console.log( )
        mouse_is_inside=false;
    });
});
