            // An array of dates ( 'dd-mm-yyyy' )
            var highlight_dates = [

                    @foreach($days as $day)
                       
                       "{{ $day }}",
                       
                    @endforeach

                ];
            
            $(document).ready(function(){

                  
                $(function() {
                  $.ajaxSetup({
                    headers: {
                      'X-CSRF-Token': $('meta[name="_token"]').attr('content')
                    }
                  });
                });
                // Initialize datepicker
                $('#datepicker').datepicker({
                    beforeShowDay: function(date){
                        
                        var month = date.getMonth()+1;
                        var year = date.getFullYear();
                        var day = date.getDate();
                        
                        // Change format of date
                        if(month < 10){ month = '0' + month;}
                        if(day < 10){ day = '0' + day}
                        var newdate = year+"-"+month+'-'+day;
                        
                        // Set tooltip text when mouse over date
                        var tooltip_text = "New event on "+newdate;

                        // Check date in Array
                        if(jQuery.inArray(newdate, highlight_dates) != -1){
                            return [true, "highlight" ];
                        }
                        return [false];
                    },
                    dateFormat: 'yy-mm-dd'
                });

                $('#start_date').datepicker({
                    beforeShowDay: function(date){
                        
                        var month = date.getMonth()+1;
                        var year = date.getFullYear();
                        var day = date.getDate();
                        
                        // Change format of date
                        if(month < 10){ month = '0' + month;}
                        if(day < 10){ day = '0' + day}
                        var newdate = year+"-"+month+'-'+day;

                        // Set tooltip text when mouse over date
                        var tooltip_text = "New event on "+newdate;

                        // Check date in Array
                        if(jQuery.inArray(newdate, highlight_dates) != -1){
                            return [true, "highlight", tooltip_text ];
                        }
                        return [false];
                    },
                    dateFormat: 'yy-mm-dd'
                });

                $('#end_date').datepicker({
                    beforeShowDay: function(date){
                        
                        var month = date.getMonth()+1;
                        var year = date.getFullYear();
                        var day = date.getDate();
                        
                        // Change format of date
                        if(month < 10){ month = '0' + month;}
                        if(day < 10){ day = '0' + day}
                        var newdate = year+"-"+month+'-'+day;

                        // Set tooltip text when mouse over date
                        var tooltip_text = "New event on "+newdate;

                        // Check date in Array
                        if(jQuery.inArray(newdate, highlight_dates) != -1){
                            return [true, "highlight", tooltip_text ];
                        }
                        return [false];
                    },
                    dateFormat: 'yy-mm-dd'
                });

            });