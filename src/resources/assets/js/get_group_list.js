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




					           },

					           error: function (exception) {

								       alert(exception.responseText);

								}

					        });
         }
         


    });
     
        function print_data_group(data){
                   
                   
                   $.each(data, function(i, item) {

					             $("#group_id").append("<option class = 'group_id' value='"+data[i].id+"'>"+data[i].group_name+"</option>");
                       

				          	});

        }

        function remove_old_group(){
              

        	   $( ".group_id" ).each(function() {

		             $(this).remove();


		        });
        }

