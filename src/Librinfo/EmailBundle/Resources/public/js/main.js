$(document).ready(function () {

    templateSelect();
    checkIsTest();
    upload();
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
        tinymce.activeEditor.execCommand('mceInsertContent', false, data);
        tinymce.activeEditor.execCommand('mceEndUndoLevel', false, data);

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
function upload() {

    Dropzone.autoDiscover = false;

    var options = {
        url: "http://" + window.location.host + "/librinfo/email/ajax/upload",
        paramName: "file", // The name that will be used to transfer the file
        addRemoveLinks: true,
        autoProcessQueue: true
    };
    
    var dropzone = new Dropzone(".dropzone", options);
    
    //generation of a temporary email id
    var tempId = generateUUID();
    
    //register callback on dropzone send event
    dropzone.on("sending", function(file, xhr, formData){
        //add the id to the ajax call formData
        formData.append("temp_id", tempId);
    });
    //append the id to the form so it can be retrieved in CreateAction
    $('form[role="form"]').append('<input type="hidden" name="temp_id" value="' + tempId + '"/>');
    
}

function generateUUID() {
    
    var d = new Date().getTime();
    var uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g,function(c) {
        var r = (d + Math.random()*16)%16 | 0;
        d = Math.floor(d/16);
        return (c=='x' ? r : (r&0x7|0x8)).toString(16);
    });
    return uuid.toUpperCase();
}