    $(document).on('change', '#department_id', function () {

         $('#group_id').prop('selectedIndex',0);
      
         $( "#group_id" ).prop( "disabled", false );
         var department_id = $("#department_id").val();

  
         if(department_id == ""){

             $('#group_id').prop('selectedIndex',0);
         	   $( "#group_id" ).prop( "disabled", true );

         }else {
                
              $( "#group_id" ).prop( "disabled",false);
              var id = $("#department_id").val();
                               
					        $.ajax({

					           type:'POST',

					           url:'/group/ajax',

					           data: {

						          "department_id": id

						        },

					           success:function(data){
                                
                           remove_old_group();
                           print_data_group(data.groups);

                           if(cro_value.p == 1){
                            p_remove_group();
                           }

					           },

					           error: function (exception) {
                       
               //         alert(123);
								       // alert(exception.responseText);

								}

					        });
         }
         


    });
     
    async  function print_data_group(data){
                   
                  
                   $.each(data, function(i, item) {

                   
					             $("#group_id").append("<option class = 'group_id' value='"+data[i].id+"'>"+htmlspecialchars(data[i].group_name)+"</option>");
                       

				          	});

        }


    async function remove_old_group(){
              

        	   $( ".group_id" ).each(function() {

		             $(this).remove();


		        });
        }

    async function p_remove_group(){
           
            $( ".group_id" ).each(function() {

                if(jQuery.inArray(parseInt($(this).val()),cro_value.group_id_list ) < 0){

                  $(this).remove();

                }
            });

       }