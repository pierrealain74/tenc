/**
 * Register form profile picture field.
 *
 * @since 2.5.0
 */
jQuery( $ => {
    $('.picture-field input[type="file"]').on( 'change', e => {
        var preview = $( '.sign-in-form.register .picture-field .picture-preview' );
        preview.html('');

        if ( ! e.target.files[0] ) {
            return;
        }

        var reader = new FileReader();
        reader.onload = event => {
            var img = $(
                `<div class="review-gallery-image">
                    <span class="review-gallery-preview-icon">
                        <a class="review-gallery-image-remove" href="#">
                            <i class="mi delete"></i>
                        </a>
                    </span>
                </div>`
            ).css( 'background-image', `url(${event.target.result})` );
            $(img).appendTo(preview);
        }
        reader.readAsDataURL(e.target.files[0]);
    } );

    $('.picture-field').on( 'click', '.review-gallery-image-remove', e => {
        e.preventDefault();
        $('.picture-field input[type="file"]').val('').trigger('change');
    } );
} );

/**
 * Role switcher in registration form.
 *
 * @since 2.5.0
 */
jQuery( $ => {
    var primary_role = $('.mylisting-register .primary-role-fields > .fields-wrapper');
    var secondary_role = $('.mylisting-register .secondary-role-fields > .fields-wrapper');

    $('.mylisting-register input[name="mylisting_user_role"]:checked').val() === 'secondary'
        ? primary_role.detach()
        : secondary_role.detach();

    $('.mylisting-register input[name="mylisting_user_role"]').change( e => {
        if ( e.target.value === 'secondary' ) {
            primary_role.detach();
            secondary_role.appendTo( '.mylisting-register .secondary-role-fields' );
        } else {
            secondary_role.detach();
            primary_role.appendTo( '.mylisting-register .primary-role-fields' );
        }

        // active button style
        $(e.target).addClass('role-active');
        $(e.target).siblings().removeClass('role-active');
    } );
} );

/**
 * Handle form switch between login and register.
 *
 * @since 2.5.0
 */
jQuery( $ => {
    $('.login-tabs a').click( e => {
        e.preventDefault();

        if ( e.target.dataset.form === 'register' ) {
            $('.login-form-wrap').addClass('hide');
            $('.register-form-wrap').removeClass('hide');
        } else {
            $('.register-form-wrap').addClass('hide');
            $('.login-form-wrap').removeClass('hide');
        }

        $('.login-tabs li').removeClass('active');
        $(e.target).parents('li').addClass('active');
    } );

    // if the login form is active, focus the username input
    if ( ! $('.login-form-wrap').hasClass('hide') ) {
        $('.login-form-wrap #username').focus();
    }

    $('.showlogin').click( e => {
        e.preventDefault();
        $('.login-tabs a[data-form="login"]').click();
    } );
} );

window.onGoogleLibraryLoad = () => {
    jQuery( '.cts-google-signin' ).each( (i, el) => {
        var container = jQuery('.login-content'),
            gsci = jQuery("meta[name='google-signin-client_id']").attr("content");

        google.accounts.id.initialize({
            client_id: gsci,
            callback: (response) => {
                container.addClass('cts-processing-login');

                jQuery.ajax({
                    url: "".concat(CASE27.mylisting_ajax_url, "&action=cts_login_endpoint&security=").concat(CASE27.ajax_nonce),
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        network: 'google',
                        token: response.credential,
                        process: 'login',
                        user_role: jQuery(el).parents('form').find('input[name="mylisting_user_role"]:checked').val()
                    },
                    success: function success(response) {
                        if (response.status === 'error' && response.message) {
                            container.removeClass('cts-processing-login');
                            return alert(response.message);
                        }

                        var redirect_url = jQuery(el).parents('form').find('input[name="redirect"]').val();
                        redirect_url ? window.location.href = redirect_url : window.location.reload();
                    },
                    error: function error(xhr, status, _error) {
                        console.log('Failed', xhr, status, _error);
                        container.removeClass('cts-processing-login');
                    }
                });
            },
        });

        var loginWidth = container.width();

        google.accounts.id.renderButton(
            el,
            { theme: "outline", size: "large", width: loginWidth, logo_alignment: "center", locale: CASE27.google_btn_local }  // customization attributes
        );
        google.accounts.id.prompt(); // also display the One Tap dialog
    });
}

jQuery('.cts-facebook-signin').click( e => {
    e.preventDefault();

    var container = jQuery('.login-content');
    FB.login( response => {
        if ( ! response.authResponse ) {
            return;
        }

        container.addClass('cts-processing-login');
        jQuery.ajax( {
            url: `${CASE27.mylisting_ajax_url}&action=cts_login_endpoint&security=${CASE27.ajax_nonce}`,
            type: 'POST',
            dataType: 'json',
            data: {
                network: 'facebook',
                token: response.authResponse.accessToken,
                process: 'login',
                user_role: jQuery(e.target).parents('form').find('input[name="mylisting_user_role"]:checked').val(),
            },
            success: response => {
                if ( response.status === 'error' && response.message ) {
                    container.removeClass('cts-processing-login');
                    return alert( response.message );
                }

                var redirect_url = jQuery(e.target).parents('form').find('input[name="redirect"]').val();
                redirect_url ? ( window.location.href = redirect_url ) : window.location.reload();
            },
            error: ( xhr, status, error ) => {
                console.log('Failed', xhr, status, error);
                container.removeClass('cts-processing-login');
            },
        } );
    }, { scope: 'public_profile,email' } );
} );
