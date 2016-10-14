$(document).ready(function () {

    templateSelect();
    checkIsTest();
    inline();

    //reset confirmexit plugin
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

    $.get('/librinfo/email/ajax/getTemplate/' + templateId, function (data) {

        tinyMceInsert(data);
    });
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

//retrieves img tag generated from file and inserts it into tinymce editor
function inline() {

    var tempId = $('input[name="temp_id"]').attr("value");

    $('.dropzone').on('click', '.inline', function (e) {
        e.preventDefault();

        var fileName = $(this).attr('file_name');
        var fileSize = $(this).attr('file_size');

        $.get('/librinfo/email/ajax/insert/' + fileName + '/' + fileSize + '/' + tempId, function (data) {

            tinyMceInsert(data);
        });
    });
}

function tinyMceInsert(data) {

    tinymce.activeEditor.execCommand('mceInsertContent', false, data);
    tinymce.activeEditor.execCommand('mceEndUndoLevel', false, data);
}