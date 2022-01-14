         
    $(document).on('change', '#company_id', function () {

        //初期値に戻す
         $('#rule_id').prop('selectedIndex',0);
               
         var company_id = $("#company_id").val();

         if(company_id == ""){
               
               $('#rule_id').prop('selectedIndex',0);
               $( "#rule_id" ).prop( "disabled", true );
         }else {

                $( "#rule_id" ).prop( "disabled", false );

                var id = $("#company_id").val();
                          
                            $.ajax({

                               type:'POST',

                               url:'/rule/ajax',

                               data: {

                                    "company_id": id

                                },

                               success:function(data){
                                  
                                
                                
                                  remove_old_rule();
                                  print_data_rule(data.rule);


                               },

                               error: function (exception) {

                                      alert(exception.responseText);

                                     }

                            });
         }

    });

        function print_data_rule(data){
                   
          //新しい値をセット
            $.each(data, function(i, item) {

              $("#rule_id").append("<option data-value='"+data[i].rule_id+"' class = 'rule_id' value='"+data[i].id+"'>"+data[i].rule+"</option>");

          });

        }

        function remove_old_rule(){

             $( ".rule_id" ).each(function() {

                 $(this).remove();


            });
        }
