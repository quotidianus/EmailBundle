$(document).ready(function(){
    
    templateSelect();
    checkIsTest();
    
});

function templateSelect(){
    
    $('select.template_select').click(function(){
        
        getTemplate($(this).val());
        
//        $("abbr.select2-search-choice-close").click(function(){
//     
//        console.log("hello");
//        });
    });
}

function getTemplate(templateId){
    
    $.get("http://" + window.location.host + "/librinfo/email/getTemplate/" + templateId, function(data){
       
            tinymce.activeEditor.execCommand('mceInsertContent', false, data);
            tinymce.activeEditor.execCommand('mceEndUndoLevel', false, data);
        
    });
}

function checkIsTest(){
    
    var url = window.location.href;
    var action = url.split("/").pop();
    var checkbox = $("input.is_test");
    
   checkbox.iCheck('check');
    
    if(action === 'create'){
        
        checkbox.iCheck('disable');
    }
   
}