function importer(setup) {
    var uploader, fileError;

    fileError = false;

    uploader = new plupload.Uploader({
        runtimes: 'html5,flash,silverlight,html4',
        url: setup.url,
        browse_button : 'fileInput',
        chunk_size: setup.chunkSize,
        filters : {
            mime_types: [
                {title : "csv files", extensions : "csv"},
            ]
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        flash_swf_url : '/vendor/jildertmiedema/laravel-plupload/js/Moxie.swf',
        silverlight_xap_url : '/vendor/jildertmiedema/laravel-plupload/js/Moxie.xap',     
        init: {
            PostInit: function() {
                $('#uploadContainer .message').html('<p><b>Select File To Import</b></p><p>Allowed file type: csv</p>');

                $('#uploadContainer #importButton').click(function() {
                    uploader.start();
                    return false;
                });
            },
            FilesAdded: function(up, files) {
                plupload.each(files, function(file) {
                    $('#uploadContainer .message').html('<p><b>Selected File</b></p><div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <button type="button" class="btn btn-default btn-sm remove-file"><i class="fa fa-trash-o"></i> Remove </button></div>');

                    $('.remove-file').click(function() {
                        up.removeFile(file.id);
                    });
                });

                $('#uploadContainer #fileInput').addClass('hidden');
                $('#uploadContainer #importButton').removeClass('hidden');
            },
            FilesRemoved: function(up, files) {
                $('#uploadContainer .message').html('<p><b>Select File To Import</b></p>');

                $('#uploadContainer #fileInput').removeClass('hidden');
                $('#uploadContainer #importButton').addClass('hidden');

                up.refresh();
            },
            FileUploaded: function(up, file, res) {
                var data = JSON.parse( res.response );
                if ( data.hasOwnProperty('errors') ) {
                    fileError = true;
                    var errorMsg = '<ul class="list-group">';

                    $.each(data.errors, function(index, el) {
                        errorMsg += '<li class="list-group-item list-group-item-danger">' + el[0] + '</li>';
                    });;

                    errorMsg += '</ul>';
                    $('#uploadContainer .message').append(errorMsg);
                } else {
                    $('#uploadContainer #nextButton').attr('href', data.result.url);
                }                
            },
            UploadProgress: function(up, file) {
                $('#uploadContainer .remove-file').addClass('hidden');
                $('#uploadContainer .progress').removeClass('hidden');

                $('#uploadContainer .progress-bar').width(file.percent + '%');
                $('#uploadContainer .progress-bar').attr('aria-valuenow', file.percent);
                $('#uploadContainer .progress-bar').html(file.percent + '%');
            },
            UploadComplete: function(up, file) {
                $('#uploadContainer #importButton').addClass('hidden');
                if ( fileError ) {
                    $('#uploadContainer #backButton').removeClass('hidden');
                } else {
                    $('#uploadContainer #nextButton').removeClass('hidden');
                }
                up.refresh();
            },
            Error: function(up, err) {
                if ( err.code == '-601' ) {
                    $('#uploadContainer .alert .alert-container').html("Error #" + err.code + ": Invalid file format");
                } else {
                    $('#uploadContainer .alert .alert-container').html("Error #" + err.code + ": " + err.message);
                }
                if ( $('#uploadContainer .alert').hasClass('hidden') )
                    $('#uploadContainer .alert').removeClass('hidden');

                $('#uploadContainer .alert').fadeIn();

                setTimeout(function() {
                    $('#uploadContainer .alert').fadeOut();
                }, 4000);
            },
        }
    });

    uploader.init();
}
