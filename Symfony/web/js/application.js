jQuery(document).ready(function(){
    $("input.date_picker").datepicker({ dateFormat: "yy-mm-dd" });
    $("#flash_alert").addClass("ui-state-error").addClass("ui-corner-bottom");
    $("ul#goals_list").menu();

    $(document).ajaxError( function(e, xhr, options){
        if("401" == xhr.responseText)
        {
            $(location).attr('href','/login');
        }
    });
});