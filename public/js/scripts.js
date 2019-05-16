//$(document).ready(function () {
//    // Button trigger radio button change
//    $("button.btn-radio").click(function () {
//        var id_name = $(this).attr("btn-radio");
//        var name = $("#" + id_name).attr("name");
//        $("input[name^='" + name + "']").removeAttr("checked");
//        $("#" + $(this).attr("btn-radio")).attr('checked', true);
//        $(".btn-radio").removeClass("clicked");
//        $(this).addClass("clicked");
////        alert($("input[name^='" + name + "']:checked").val());
//    });
//});

$(document).ready(function (){
    
    // Suggestion text length

    
});



function ConfirmationCheck(checkbox, button) {
    if($(checkbox).is(':checked'))
    {
        $(button).prop('disabled', '').removeClass('disabled');
    }
    else 
    {
        $(button).prop('disabled', 'disabled').addClass('disabled');
    }
}





