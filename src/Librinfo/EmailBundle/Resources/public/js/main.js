$(document).ready(function(){
    
    templateSelect();
});

function templateSelect(){
    
    $('select.template_select').click(function(){
        
        var templateId = $(this).val();
        
        getTemplate(templateId);
        
//        $("abbr.select2-search-choice-close").click(function(){
//     
//        console.log("hello");
//        });
    });
}

function getTemplate(templateId){
    
    $.get("http://" + window.location.host + "/librinfo/email/email/getTemplate/" + templateId, function(data){
       
            tinymce.activeEditor.execCommand('mceInsertContent', false, data);
            tinymce.activeEditor.execCommand('mceEndUndoLevel', false, data);
        
    });
}