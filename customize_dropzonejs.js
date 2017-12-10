/* https://stackoverflow.com/questions/23716048/non-ajax-post-using-dropzone-js */
Dropzone.autoDiscover = false;
jQuery(document).ready(function () {
	//jQuery("#commentform").attr("enctype","multipart/form-data");
    Dropzone.autoDiscover = false;
    jQuery("#media-uploader").dropzone({
       	url: 'someurl',
       	autoProcessQueue: false,
        addRemoveLinks: true,
        paramName: 'testfile',
        init: function() {
	        dzClosure = this; // Makes sure that 'this' is understood inside the functions below.
	
	        // for Dropzone to process the queue (instead of default form behavior):
	        document.getElementById("submit").addEventListener("click", function(e) {
	            // Make sure that the form isn't actually being sent.
	            e.preventDefault();
	            e.stopPropagation();
	            dzClosure.processQueue();
	        });
	
	        this.on("success", function (file, response) {
	            $('#hidden_image_name').val(response);
	        });
	
	    }
        
    });
    
});




/*
	Dropzone.autoDiscover = false;
jQuery("#media-uploader").dropzone({
    url: dropParam.upload,
    acceptedFiles: 'image/*',
    success: function (file, response) {
        file.previewElement.classList.add("dz-success");
        file['attachment_id'] = response; // push the id for future reference
        var ids = jQuery('#media-ids').val() + ',' + response;
        jQuery('#media-ids').val(ids);
    },
    error: function (file, response) {
        file.previewElement.classList.add("dz-error");
    },
    // update the following section is for removing image from library
    addRemoveLinks: true,
    removedfile: function(file) {
        var attachment_id = file.attachment_id;        
        jQuery.ajax({
            type: 'POST',
            url: dropParam.delete,
            data: {
                media_id : attachment_id
            }
        });
        var _ref;
        return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;        
    }
});
*/