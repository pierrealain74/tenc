jQuery(function($) {
	$('.wp-job-manager-file-upload').each(function(){
		$(this).fileupload({
			dataType: 'json',
			dropZone: $(this),
			url: CASE27.ajax_url + '?action=mylisting_upload_file&security=' + CASE27.ajax_nonce,
			maxNumberOfFiles: 1,
			formData: {
				script: true
			},
			add: function (e, data) {
				var $file_field     = $( this );
				var $form           = $file_field.closest( 'form' );
				var $uploaded_files = $file_field.parents('.file-upload-field').find('.job-manager-uploaded-files');

				// validate max count
				var max_count = parseInt( $(this).data('max_count'), 10 );
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
				var $uploaded_files = $file_field.parents('.file-upload-field').find('.job-manager-uploaded-files');
				var multiple        = $file_field.attr( 'multiple' ) ? 1 : 0;
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

						if ( multiple ) {
							$uploaded_files.append( html );
						} else {
							$uploaded_files.html( html );
						}
					}
				});

				$form.find(':input[type="submit"]').removeAttr( 'disabled' );
			}
		});
	});
});