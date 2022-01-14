         
    $(document).on('change', '#company_id', function () {


         $('#headquarter_id').prop('selectedIndex',0);
      
         $( "#department_id" ).prop( "disabled", true );
         $('#department_id').prop('selectedIndex',0);
         $('#group_id').prop('selectedIndex',0);

         $( "#department_id" ).prop( "disabled", true );

         $( "#group_id" ).prop( "disabled", true );
         
         var company_id = $("#company_id").val();

         if(company_id == ""){
               
               $('#headquarter_id').prop('selectedIndex',0);
               $('#department_id').prop('selectedIndex',0);
               $('#group_id').prop('selectedIndex',0);
               $( "#headquarter_id" ).prop( "disabled", true );

               $( "#department_id" ).prop( "disabled", true );

               $( "#group_id" ).prop( "disabled", true );

         }else {

                $( "#headquarter_id" ).prop( "disabled", false );

                var id = $("#company_id").val();
                          
                            $.ajax({

                               type:'POST',

                               url:'/headquarter/ajax',

                               data: {

                                    "company_id": id

                                },

                               success:function(data){
                                  
                                
                                
                                  remove_old_headquarter();
                                  print_data_headquarter(data.headquarters);


                               },

                               error: function (exception) {

                                      alert(exception.responseText);

                                     }

                            });
         }
         
   


    });

        function print_data_headquarter(data){
                   

            $.each(data, function(i, item) {

              $("#headquarter_id").append("<option data-value='"+data[i].company_id+"' class = 'headquarter_id' value='"+data[i].id+"'>"+data[i].headquarters+"</option>");


          });

        }

        function remove_old_headquarter(){

             $( ".headquarter_id" ).each(function() {

                 $(this).remove();


            });
        }
