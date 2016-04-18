$(document).ready(function(){
    
    templateSelect();
});

function templateSelect(){
    
    $('select.template_select').click(function(){
        
        var templateId = $(this).val();
        
        getTemplate(templateId);
    });
    
//    $(".select2-search-choice-close").on("mousedown", function(){
//       
//        console.log("hello");
//    });
}

function getTemplate(templateId){
    
    $.get("getTemplate/" + templateId, function(data){
        
        tinymce.activeEditor.execCommand('mceInsertContent', false, data);
        tinymce.activeEditor.execCommand('mceEndUndoLevel', false, data);
    });
}