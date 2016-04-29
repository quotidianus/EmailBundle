$(document).ready(function () {

    templateSelect();
    checkIsTest();
    setupDropzone();
});

//handles retrieving and insertion of template into main content
function templateSelect() {

    $('select.template_select').click(function () {

        getTemplate($(this).val());
    });
}

function getTemplate(templateId) {
    //ajax call to retrieve the template content
    $.get("http://" + window.location.host + "/librinfo/email/ajax/getTemplate/" + templateId, function (data) {
        //insertion of the content into the tinyMce editor
        tinyMceInsert(data);
    });
}

//handles checking and disabling of isTest checkbox
function checkIsTest() {

    var url = window.location.href;
    var action = url.split("/").pop();
    var checkbox = $("input.is_test");

    checkbox.iCheck('check');

    if (action === 'create' || action === 'duplicate') {

        checkbox.iCheck('disable');
    }

}

//handles attachments upload
function setupDropzone() {

    Dropzone.autoDiscover = false;

    var template = '<div class="table table-striped" class="files" id="previews"> <div id="template" class="file-row"> <!-- This is used as the file preview template --> <div> <span class="preview"><img data-dz-thumbnail /></span> </div> <div> <p class="name" data-dz-name></p> <strong class="error text-danger" data-dz-errormessage></strong> </div> <div> <button data-dz-remove class="btn btn-danger delete"> <i class="glyphicon glyphicon-trash"></i> </button> <button class="btn btn-info inline"> <i class="glyphicon glyphicon-arrow-up"></i> </button> </div> <div> <p class="size" data-dz-size></p> <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"> <div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress></div> </div> </div> </div> </div>';

    var options = {
        url: "http://" + window.location.host + "/librinfo/email/ajax/upload",
        paramName: "file",
        uploadMultiple: false,
        maxFiles: 5,
        previewTemplate: template,
        clickable: ".add_files"
    };
    //init dropzone
    var dropzone = new Dropzone(".dropzone", options);

    //generation of a temporary email id
    var tempId = generateUUID();

    //append the id to the form so it can be retrieved in CreateAction
    $('form[role="form"]').append('<input type="hidden" name="temp_id" value="' + tempId + '"/>');

    //register callback on dropzone send event
    dropzone.on("sending", function (file, xhr, formData) {
        //add the id to the ajax call formData
        formData.append("temp_id", tempId);
        console.log(file);
    });

    $('.clear').click(function (e) {

        e.preventDefault();
        dropzone.removeAllFiles(true);
    });

    $('.add_files').click(function (e) {

        e.preventDefault();
    });

//    $('button.upload').click(function (e) {
//        e.preventDefault();
//        dropzone.processQueue();
//    });

    dropzone.on("removedfile", function (file) {

        $.get("http://" + window.location.host + "/librinfo/email/ajax/upload/remove/" + file.name + "/" + file.size, function (response) {

            console.log(response);
        });
    });

    dropzone.on("addedfile", function (file) {

        $('button.inline').attr("file_name", file.name);
        $('button.inline').attr("file_size", file.size);
    });

    inline(dropzone);
}

function inline(dropzone) {

    $('.dropzone').on('click', '.inline', function (e) {
        e.preventDefault();
        
        var fileName = $(this).attr('file_name');
        var fileSize = $(this).attr('file_size');
        
        $.get("http://" + window.location.host + "/librinfo/email/ajax/insert/" + fileName + "/" + fileSize, function(data){
            
            tinyMceInsert(data);
        });
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