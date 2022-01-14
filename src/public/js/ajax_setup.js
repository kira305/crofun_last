            
            $(document).ready(function(){
        
                $(function() {
                  $.ajaxSetup({
                    headers: {
                      'X-CSRF-Token': $('meta[name="_token"]').attr('content')
                    }
                  });
                });

            });