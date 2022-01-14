    $(document).on('change', '#headquarter_id', function () {
       
         $('#department_id').prop('selectedIndex',0);
      
         $( "#department_id" ).prop( "disabled", false );

         $('#group_id').prop('selectedIndex',0);

         $( "#group_id" ).prop( "disabled", true );
         
         var headquarter_id = $("#headquarter_id").val();


         if(headquarter_id == ""){
               
               $('#department_id').prop('selectedIndex',0);
               $('#group_id').prop('selectedIndex',0);
         	     $( "#department_id" ).prop( "disabled", true );
               $( "#group_id" ).prop( "disabled", true );

         }else {
               
               $( "#department_id" ).prop( "disabled", false );
                var id = $("#headquarter_id").val();
                          
					        $.ajax({

					           type:'POST',

					           url:'/department/ajax',

					           data: {

    						        "headquarter_id": id

						        },

					           success:function(data){
                                  
                                
                                  remove_old_department();
                                  print_data_department(data.departments);
                              
                                  if(cro_value.p == 1){

                                     p_remove_department();

                                   }

					           },

					           error: function (exception) {
                 //         alert(123);
								         // alert(exception.responseText);

								     }

					        });
         }
         


    });
     
        function print_data_department(data){
                   

          $.each(data, function(i, item) {

					    $("#department_id").append("<option data-value='"+data[i].headquarters_id+"' class = 'department_id' value='"+data[i].id+"'>"+htmlspecialchars(data[i].department_name)+"</option>");


					});

        }

        function remove_old_department(){

        	   $( ".department_id" ).each(function() {

		             $(this).remove();


		        });
        }

        function p_remove_department(){
           
            $( ".department_id" ).each(function() {
               
                if(jQuery.inArray( parseInt($(this).val()),cro_value.department_id_list ) < 0){

                  $(this).remove();

                }
            });

        }