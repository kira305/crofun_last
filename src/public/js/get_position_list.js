         
    $(document).on('change', '#company_id', function () {

        //初期値に戻す
         $('#position_id').prop('selectedIndex',0);
               
         var company_id = $("#company_id").val();

         if(company_id == ""){
               
               $('#position_id').prop('selectedIndex',0);
               $( "#position_id" ).prop( "disabled", true );
         }else {

                $( "#position_id" ).prop( "disabled", false );

                var id = $("#company_id").val();
                          
                            $.ajax({

                               type:'POST',

                               url:'/position/ajax',

                               data: {

                                    "company_id": id

                                },

                               success:function(data){
                                  
                                
                                
                                  remove_old_position();
                                  print_data_position(data.positions);


                               },

                               error: function (exception) {

                                      alert(exception.responseText);

                                     }

                            });
         }
         
   


    });

        function print_data_position(data){
                   
          //新しい値をセット
            $.each(data, function(i, item) {

              $("#position_id").append("<option data-value='"+data[i].company_id+"' class = 'position_id' value='"+data[i].id+"'>"+data[i].position_name+"</option>");


          });

        }

        function remove_old_position(){

             $( ".position_id" ).each(function() {

                 $(this).remove();


            });
        }
