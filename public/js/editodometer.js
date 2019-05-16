
//
//function removecar(id)
//{
//    if(confirm("Do you want permanently delete this parameter?"))
//    {                    
//        $.get("/admin/parameters/remove/"+id, function(data){            
//            if(data.success == true) {
//                $("#parameter_"+id).css("background-color", "#C20000");
//                $("#parameter_"+id).fadeOut();
//            }
//            else {
//                alert("Couldn't delete parameter:".data.error);
//            }
//        });                    
//    }
//}