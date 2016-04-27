$(document).ready(function () {

    templateSelect();
    checkIsTest();
    upload();

    //$("div.sonata-ba-form-actions").append('<button class="btn btn-info persist-preview" type="submit" name="btn_preview"><i class="fa fa-eye"></i>Send</button>');
});

function templateSelect() {

    $('select.template_select').click(function () {

        getTemplate($(this).val());
    });
}

function getTemplate(templateId) {

    $.get("http://" + window.location.host + "/librinfo/email/ajax/getTemplate/" + templateId, function (data) {

        tinymce.activeEditor.execCommand('mceInsertContent', false, data);
        tinymce.activeEditor.execCommand('mceEndUndoLevel', false, data);

    });
}

function checkIsTest() {

    var url = window.location.href;
    var action = url.split("/").pop();
    var checkbox = $("input.is_test");

    checkbox.iCheck('check');

    if (action === 'create' || action === 'duplicate') {

        checkbox.iCheck('disable');
    }

}

function upload() {

    Dropzone.autoDiscover = false;

    var options = {
        url: "http://" + window.location.host + "/librinfo/email/ajax/upload",
        paramName: "file", // The name that will be used to transfer the file
        addRemoveLinks: true
    };

    var dropzone = new Dropzone(".dropzone", options);
    
}