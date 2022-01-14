    $(document).ready(function() {
          

          $( "#headquarter_id" ).prop( "disabled", true );
          $( "#department_id" ).prop( "disabled", true );
          $( "#group_id" ).prop( "disabled", true );
          
          if($("#company_id").val() != ""){
                 
                  $( "#headquarter_id" ).prop( "disabled",false );
                  var company_id = $("#company_id").val();

                  $( ".headquarter_id" ).each(function() {

                       $(this).show();
                     
                       if($(this).attr('data-value') !== company_id){

                           $(this).remove();
             
                       }

                   });
          }

          if($("#headquarter_id").val() != ""){

                    $( "#department_id" ).prop( "disabled",false );
                    var headquarter_id = $("#headquarter_id").val();
                    $( ".department_id" ).each(function() {

                         $(this).show();
                       
                         if($(this).attr('data-value') !== headquarter_id){
                                   
                             $(this).remove();
               
                         }

                     });
          }

         if($("#department_id").val() != ""){

                    $( "#group_id" ).prop( "disabled",false );
                    var department_id = $("#department_id").val();
                    $( ".group_id" ).each(function() {

                         $(this).show();
                       
                         if($(this).attr('data-value') !== department_id){
                                   
                             $(this).remove();
               
                         }

                     });
          }
      

    });
