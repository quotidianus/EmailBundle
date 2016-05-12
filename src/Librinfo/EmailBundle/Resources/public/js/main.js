$(document).ready(function () {
    
    var form = $('.sonata-ba-form form');
    var confirmExit = form.serialize();
    console.log(confirmExit);

    templateSelect();
    checkIsTest();
    setupDropzone();
    
    //make form not dirty 
    $('.sonata-ba-form form').confirmExit();
});

//handles retrieving and insertion of template into main content
function templateSelect() {

    $('select.template_select').click(function () {

        getTemplate($(this).val());
    });
}

//retrieves template content and inserts it into tinymce editor
function getTemplate(templateId) {
    
    $.get("http://" + window.location.host + "/librinfo/email/ajax/getTemplate/" + templateId, function (data) {
        
        tinyMceInsert(data);
    });
}

// Returns the current action (show/list/edit) from url
function getAction() {

    var url = window.location.href;

    return url.split("/").pop();
}

//Get current email id from url
function getEmailId() {

    var splitUrl = window.location.href.split("/");

    splitUrl.pop();

    return splitUrl.pop();
}

//handles checking and disabling of isTest checkbox
function checkIsTest() {
    
    var action = getAction();
    var checkbox = $("input.is_test");

    checkbox.iCheck('check');

    if (action === 'create' || action === 'duplicate') {

        checkbox.iCheck('disable');
    }

}

//handles attachments upload
function setupDropzone() {

    Dropzone.autoDiscover = false;

    var template = '<div class="table table-striped" class="files" id="previews">' +
            '<div id="template" class="file-row">' +
            '<div> <span class="preview"><img data-dz-thumbnail /></span></div>' +
            '<div> <p class="name" data-dz-name></p> <strong class="error text-danger" data-dz-errormessage></strong> </div>' +
            '<div> <button data-dz-remove class="btn btn-danger delete"> <i class="glyphicon glyphicon-trash"></i> </button>' +
            '<button class="btn btn-info inline"> <i class="glyphicon glyphicon-arrow-up"></i></button> </div>' +
            '<div> <p class="size" data-dz-size></p> ' +
            '<div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">' +
            '<div class="progress-bar progress-bar-info" style="width:0%;" data-dz-uploadprogress></div> </div> </div> </div> </div>';

    var options = {
        url: "http://" + window.location.host + "/librinfo/email/ajax/upload",
        paramName: "file",
        uploadMultiple: false,
        maxFiles: 5,
        maxFileSize: 5,
        previewTemplate: template,
        clickable: ".add_files"
    };
    //init dropzone plugin
    var dropzone = new Dropzone(".dropzone", options);

    var tempId = '';

    tempId = generateUUID();

    //append the id to the form so it can be retrieved in CreateAction
    $('form[role="form"]').append('<input type="hidden" name="temp_id" value="' + tempId + '"/>');

    //register callback on dropzone send event
    dropzone.on("sending", function (file, xhr, formData) {
        //add the id to the ajax call formData
        formData.append("temp_id", tempId);
    });

    //event listener for th button that clears the upload queue
    $('.clear').click(function (e) {

        e.preventDefault();
        dropzone.removeAllFiles(true);
    });

    //prevent submitting of the form when add files button is clicked
    $('.add_files').click(function (e) {

        e.preventDefault();
    });

//    $('button.upload').click(function (e) {
//        e.preventDefault();
//        dropzone.processQueue();
//    });

    dropzone.on("queuecomplete", function (progress) {

        updateProgressBar(0);
    });

    //handles removal of already uploaded files
    dropzone.on("removedfile", function (file) {

        var tempId = $('input[name="temp_id"]').attr("value");

        $.get("http://" + window.location.host + "/librinfo/email/ajax/upload/remove/" + file.name + "/" + file.size + "/" + tempId, function (response) {

            console.log(response);
        });
    });

    
    dropzone.on("addedfile", function (file) {

        //file size validation
        if (file.size > 5 * 1024 * 1024) {

            dropzone.cancelUpload(file);
            dropzone.emit("error", file, "Max file size(5mb) exceeded");
        }
        
        updateProgressBar(1);
        
        //replace generated tempId with existing files tempId
        if (getAction() == 'edit') {

            $('input[name="temp_id"]').attr("value", file.tempId);
        }

        //add file info to html tag for ajax call
        $('button.inline').attr("file_name", file.name);
        $('button.inline').attr("file_size", file.size);
    });

    //retrieve existing attachments in edit action
    if (getAction() == 'edit')
        retrieveAttachments(dropzone);

    inline(dropzone);

}

//retrieves img tag generated from file and inserts it into tinymce editor
function inline(dropzone) {

    var tempId = $('input[name="temp_id"]').attr("value");

    $('.dropzone').on('click', '.inline', function (e) {
        e.preventDefault();

        var fileName = $(this).attr('file_name');
        var fileSize = $(this).attr('file_size');

        $.get("http://" + window.location.host + "/librinfo/email/ajax/insert/" + fileName + "/" + fileSize + "/" + tempId, function (data) {

            tinyMceInsert(data);
        });
    });
}

function retrieveAttachments(dropzone) {

    var emailId = getEmailId();

    $.get("http://" + window.location.host + "/librinfo/email/ajax/upload/load/" + emailId, function (data) {

        var files = JSON.parse(data);

        for (var i = 0; i < files.length; i++) {

            dropzone.emit("addedfile", files[i]);
            dropzone.emit("complete", files[i]);
        }
    });
}

function generateUUID() {

    var d = new Date().getTime();
    var uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
        var r = (d + Math.random() * 16) % 16 | 0;
        d = Math.floor(d / 16);
        return (c == 'x' ? r : (r & 0x7 | 0x8)).toString(16);
    });
    return uuid.toUpperCase();
}

function tinyMceInsert(data) {

    tinymce.activeEditor.execCommand('mceInsertContent', false, data);
    tinymce.activeEditor.execCommand('mceEndUndoLevel', false, data);
}

function updateProgressBar(e) {

    if (e === 1) {
        $('.progress').addClass("progress-striped");
        $('.progress-bar').removeClass("progress-bar-success");
        $('.progress-bar').addClass("progress-bar-info");
    } else {
        $('.progress-bar').removeClass("progress-bar-info");
        $('.progress-bar').addClass("progress-bar-success");
        $('.progress').removeClass("progress-striped");
    }
}