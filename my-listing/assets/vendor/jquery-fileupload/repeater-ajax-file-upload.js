jQuery(document).ready(function($) {
    
    RemoveUploadedFile = function( element ) {
        element.find('.remove-uploaded-file').click( function(e) {
            e.preventDefault();
            $(this).parents('.uploaded-file').remove();
        } );
        }

    RepeaterUploadFiles = function( selfRepeaterItem ) {

                selfRepeaterItem.find('.wp-job-manager-file-upload').each(function(){
                    $(this).fileupload({
                        dataType: 'json',
                        dropZone: $(this),
                        url: CASE27.ajax_url + '?action=mylisting_repeater_upload_file&security=' + CASE27.ajax_nonce,
                        maxNumberOfFiles: 1,
                        formData: {
                            script: true
                        },
                        add: function (e, data) {
                            var $file_field     = $( this );
                            var $form           = $file_field.closest( 'form' );
                            var $uploaded_files = $file_field.parent().find('.job-manager-uploaded-files');

                            // validate max count
                    var max_count = parseInt( $(this).data('max_count'), 1 );
                            var total_file_count = $uploaded_files.find('.uploaded-file').length + data.originalFiles.length;

                            if ( ! isNaN( max_count ) && max_count > 1 && total_file_count > max_count ) {
                                // workaround to trigger the maxlength alert only once
                                if ( data.files[0].name === data.originalFiles[0].name ) {
                                    window.alert( CASE27.l10n.file_limit_exceeded.replace( '%d', max_count ) );
                                }

                                return;
                            }

                            // Validate file type
                            var allowed_types = $(this).data('file_types');
                            if ( allowed_types ) {
                                var acceptFileTypes = new RegExp( '(\.|\/)(' + allowed_types + ')$', 'i' );
                                if ( data.files[0].name.length && ! acceptFileTypes.test( data.files[0].name ) ) {
                                    window.alert( CASE27.l10n.invalid_file_type + ' ' + allowed_types );
                                    return;
                                }
                            }

                            // validation complete, proceed with the upload
                            $form.find(':input[type="submit"]').attr( 'disabled', 'disabled' );
                            data.context = $('<progress value="" max="100"></progress>').appendTo( $uploaded_files );
                            data.submit();
                        },
                        progress: function (e, data) {
                            var progress        = parseInt(data.loaded / data.total * 100, 10);
                            data.context.val( progress );
                        },
                        fail: function (e, data) {
                            var $file_field     = $( this );
                            var $form           = $file_field.closest( 'form' );

                            if ( data.errorThrown ) {
                                window.alert( data.errorThrown );
                            }

                            data.context.remove();

                            $form.find(':input[type="submit"]').removeAttr( 'disabled' );
                        },
                        done: function (e, data) {
                            var $file_field     = $( this );
                            var $form           = $file_field.closest( 'form' );
                            var $uploaded_files = $file_field.parent().find('.job-manager-uploaded-files');
                    var multiple        = 0;
                            var image_types     = [ 'jpg', 'gif', 'png', 'jpeg', 'jpe', 'webp' ];

                            data.context.remove();

                            // Handle JSON errors when success is false
                            if( typeof data.result.success !== 'undefined' && ! data.result.success ){
                                window.alert( data.result.data );
                            }

                            $.each(data.result.files, function(index, file) {
                                if ( file.error ) {
                                    window.alert( file.error );
                                } else {
                                    var html;
                                    if ( $.inArray( file.extension, image_types ) >= 0 ) {
                                        html = $.parseHTML( CASE27.js_field_html_img );
                                        $( html ).find('.job-manager-uploaded-file-preview img').attr( 'src', file.attachment_url );
                                    } else {
                                        html = $.parseHTML( CASE27.js_field_html );
                                        $( html ).find('.job-manager-uploaded-file-name code').text( file.name );
                                    }

                                    $( html ).find('.input-text').val( file.encoded_guid );
                                    $( html ).find('.input-text').attr( 'name', 'current_' + $file_field.attr( 'name' ) );

                                        $uploaded_files.html( html );

                            RemoveUploadedFile( selfRepeaterItem );
                                }
                            });

                            $form.find(':input[type="submit"]').removeAttr( 'disabled' );
                        }
                    });
                });
                
        RemoveUploadedFile( selfRepeaterItem );
    }

    SetUploadedFiles = function( el ) {
        var box = el.find('.repeater-field-wrapper');
        var uploaded_files = el.data('uploaded-list');

        if ( uploaded_files == '' ) {
            return;
        }

        box.each( function( i ) {
            if ( typeof(uploaded_files[ i ] ) === "undefined" || uploaded_files[ i ] === null || uploaded_files[ i ] === '' ) {
                return;
            }

            if ( uploaded_files.length === 0 ) {
            } else {
                var newThis = $(this);

                if ( uploaded_files[ i ] ) {
                    var uploadContainer = newThis.find('.job-manager-uploaded-files'),
                        $file_field = newThis.find('.wp-job-manager-file-upload'),
                        uploadCurrentFiles = uploaded_files[ i ];

                    var image_types     = [ 'jpg', 'gif', 'png', 'jpeg', 'jpe', 'webp' ];

                    var html;
                    html = $.parseHTML( CASE27.js_field_html_img );
                    $( html ).find('.job-manager-uploaded-file-preview img').attr( 'src', uploadCurrentFiles );
                    
                    $( html ).find('.input-text').val( uploadCurrentFiles );
                    $( html ).find('.input-text').attr( 'name', 'current_' + $file_field.attr( 'name' ) );

                    uploadContainer.append( html );
                }

                RemoveUploadedFile( el );
            }
                } );
    }

    MyListing.ResturantRepeater = function( el, args ) {
        var self = jQuery( el );
        this.el = jQuery(el);

        this.el.repeater( {
            initEmpty: true,
            show: function() {
                var selfRepeaterItem = $( this ),
                    input = selfRepeaterItem.find(':input[type="file"]'),
                    label = input.parents('.file-upload-field').find('label.upload-file'),
                    id = input.attr('id') + selfRepeaterItem.index();

                input.attr('id', id);
                label.attr('for', id);

                selfRepeaterItem.show();

                selfRepeaterItem.attr('data-index', selfRepeaterItem.index() );
                selfRepeaterItem.attr('id',  'repeater-' + selfRepeaterItem.index() );

                RepeaterUploadFiles( selfRepeaterItem );
            },
        } ).setList( this.el.data('list') );

        SetUploadedFiles( self );
    }

    $('#submit-job-form .resturant-menu-repeater').each( function( i, el ) {
        new MyListing.ResturantRepeater( el );
    } );

    RepeaterHandleUpload = function( selfRepeaterItem ) {

        var image_template = `
            <div class="uploaded-file">
                <span class="uploaded-image-preview"></span>
                <a class="remove-uploaded-file"><i class="mi delete"></i></a>
                <input type="hidden" class="input-text">
            </div>`;

        // markup for other file previews
        var file_template = `
            <div class="uploaded-file">
                <span class="uploaded-file-preview">
                    <i class="mi insert_drive_file uploaded-file-icon"></i>
                    <code></code>
                </span>
                <a class="remove-uploaded-file"><i class="mi delete"></i></a>
                <input type="hidden" class="input-text">
            </div>`;
            
        selfRepeaterItem.find( '.listing-file-upload-input' ).click( function(e) {
            e.preventDefault();

            var parent = $(this).parents('.file-upload-field');
            var uploads = parent.find('.job-manager-uploaded-files');
            var field_name = parent.find('.input-text.outer-photo').attr('name');

            // If the media frame already exists, reopen it.
            if ( $(this).data('file_frame') ) {
                $(this).data('file_frame').open();
                return;
            }

            var file_frame = wp.media.frames.file_frame = wp.media( { multiple: false } );
            file_frame.open();
            $(this).data( 'file_frame', file_frame );

            // on select attachment(s)
            file_frame.on( 'select', function() {
                file_frame.state().get('selection').each( function( attachment ) {
                    attachment = attachment.toJSON();

                    if ( attachment.type === 'image' ) {
                        var file = $(image_template);
                        file.find( '.uploaded-image-preview' ).css( 'background-image', 'url("'+attachment.url+'")' );
                                } else {
                        var file = $(file_template);
                        file.find( 'code' ).text( attachment.filename );
                                }

                    file.find( 'input' ).attr( 'name', 'current_'+field_name ).val( attachment.url );
                    uploads.html( file );

                    selfRepeaterItem.find('.remove-uploaded-file').click( function(e) {
                        e.preventDefault();
                        $(this).parents('.uploaded-file').remove();
                    } );
                        } );
                });
        });
    }

    RepeaterHandleFiles = function( el ) {
        var box = el.find('.repeater-field-wrapper');
        var uploaded_files = el.data('uploaded-list');

        if ( uploaded_files == '' ) {
            return;
        }

        box.each( function( i ) {
            if ( typeof(uploaded_files[ i ]) === "undefined" || uploaded_files[ i ] === null ) {
                return;
            }

            if ( uploaded_files.length === 0 ) {
            } else {
                var newThis = $(this);

                if ( uploaded_files[ i ] ) {
                    var uploadContainer = newThis.find('.job-manager-uploaded-files'),
                        $file_field = newThis.find('.input-text.outer-photo'),
                        uploadCurrentFiles = uploaded_files[ i ];

                    var image_types     = [ 'jpg', 'gif', 'png', 'jpeg', 'jpe', 'webp' ];

                        var html;
                        html = $.parseHTML( CASE27.js_field_html_img );
                    $( html ).find('.job-manager-uploaded-file-preview img').attr( 'src', uploadCurrentFiles );
                        
                    $( html ).find('.input-text').val( uploadCurrentFiles );
                        $( html ).find('.input-text').attr( 'name', 'current_' + $file_field.attr( 'name' ) );

                        uploadContainer.append( html );
                }

                el.find('.remove-uploaded-file').click( function(e) {
                    e.preventDefault();
                    $(this).parents('.uploaded-file').remove();
                } );
            }
        } );
    }

    MyListing.AdminResturantRepeater = function( el, args ) {
        var self = jQuery( el );
        this.el = jQuery(el);

        this.el.repeater( {
            initEmpty: true,
            show: function() {
                var selfRepeaterItem = $( this ),
                    input = selfRepeaterItem.find(':input[type="file"]'),
                    label = input.parent().find('label.upload-file'),
                    id = input.attr('id') + selfRepeaterItem.index();

                input.attr('id', id);
                label.attr('for', id);

                selfRepeaterItem.show();

                selfRepeaterItem.attr('data-index', selfRepeaterItem.index() );
                selfRepeaterItem.attr('id',  'repeater-' + selfRepeaterItem.index() );

                RepeaterHandleUpload( selfRepeaterItem );
            },
        } ).setList( this.el.data('list') );

        RepeaterHandleFiles( self );
    }

    $('.ml-admin-listing-form .resturant-menu-repeater').each( function( i, el ) {
        new MyListing.AdminResturantRepeater( el );
    } );
});