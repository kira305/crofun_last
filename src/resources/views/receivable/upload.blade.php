@extends('layouts.app')
@section('content')
@section('breadcrumbs', Breadcrumbs::render('customer/create'))
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script type="text/javascript" src="{{ asset('js/MonthPicker.js') }}"></script>
      <div class="row" >
        <div class="col-md-12">

          <ul class="timeline">
	          	<li>

	              <div class="timeline-item">

	                <div class="timeline-body">


				            <div class="box-body">
				            	    @if ($message = Session::get('message'))


								              <p class="" style="text-align: center;color: green">{{ $message }}</p>


								    @endif
								    <p hidden id="server_err" style="text-align: center;color: red">{{trans('message.save_fail')}}</p>
						        <form id="create_customer" method="post" action="{{ url('customer/create') }}">

								        <div class="row">
                                        	<div class="col-md-1">
								                  <label style="float: right;">会社コード</label>
								             </div>
								             <div class="col-md-2">
								                	 <div class="form-group">
								                        <select class="form-control"  id="company_id" name="company_id" >
												          @foreach($companies as $company)
									                    <option
									                      {{ old('company_id') == $company->id ? 'selected' : '' }}
									                        value="{{$company->id}}">{{$company->abbreviate_name}}
									                    </option>

								                          @endforeach
														</select>

								                     </div>
								             </div>
								              <span id="message1" hidden class="text-danger">{{trans('validation.company_code')}}</span>
                                        </div>
                                        <div class="row">
                                        	<div class="col-md-1">
								                  <label style="float: right;">取込データ</label>
								             </div>
								             <div class="col-md-2">
								                	 <div class="form-group">
								                        <select v-model = "import_type" class="form-control" id="import_type" name="import_type" >
                                                          <option value="1">売上</option>
                                                          <option value="2">売掛金残</option>
														</select>

								                     </div>
								             </div>
								              <span id="message2" hidden class="text-danger">
								              {{trans('message.file_type')}}</span>
                                        </div>
                                        <div class="row">
                                        	<div class="col-md-1">
								                  <label style="float: right;">取得年月</label>
								             </div>
								             <div class="col-md-2">
								                	 <div class="form-group">

								                       <input id="get_time" value=""  name="get_time" type="text" size="70"   class="form-control" >

								                     </div>
								             </div>

								              <span id="message3" hidden class="text-danger">{{trans('message.get_time_import')}}
								              </span>

                                        </div>

						             </form>
						              <br>
                                     <div class="row">

                                        <div class="col-md-1"></div>
                                        <div class="col-md-1">

								        <form id="upload" action="{{ url('customer/upload') }}" method="post" enctype="multipart/form-data">
									      <span class="btn btn-primary btn-file" >
											テータ取込
											<input  v-on:change="showdata" type="file" id="input_file" name="file_data">
											@csrf
										  </span>
                                        </form>

								        </div>
                                        <div class="col-md-1">
                                        	  <span id="message4" hidden class="text-danger">{{trans('validation.file_upload_fomat')}}
								              </span>
                                        </div>

                                     </div>
                                     <br>

				            <!-- /.box-body -->
				          </div>


	              </div>
	            </li>
	            <li>


				<div class="timeline-item">


				            <div   hidden id="upload_status" class="box-body">

								        <div class="row">
                                        	<div class="col-md-1">
								                  <label style="float: right;">取込データ</label>
								             </div>
								             <div class="col-md-2">
								                	 <div class="form-group">
								                        <p id="import_type_1" class="form-control"></p>
								                     </div>
								             </div>
                                        </div>
                                        <div class="row">
                                        	<div class="col-md-1">
								                  <label style="float: right;">取込ステータス</label>
								             </div>
								             <div class="col-md-2">
								                	 <div class="form-group">
								                        <p id="import_status" class="form-control"></p>
								                     </div>
								             </div>
                                        </div>
                                        <div class="row">
                                        	<div class="col-md-1">
								                  <label style="float: right;">データ件数</label>
								             </div>
								             <div class="col-md-2">
								                	 <div class="form-group">
								                        <p id="total_data" class="form-control"></p>
								                     </div>
								             </div>
                                        </div>
                                        <div class="row">
                                        	<div class="col-md-1">
								                  <label style="float: right;">ファイル名</label>
								             </div>
								             <div class="col-md-2">
								                	 <div class="form-group">
								                        <p id="file_name" class="form-control"></p>
								                     </div>
								             </div>
                                        </div>
                                        <div class="row">
                                        	<div class="col-md-1">
								                  <label style="float: right;">取込日</label>
								             </div>
								             <div class="col-md-2">
								                	 <div class="form-group">
								                        <p class="form-control" id="get_time_1"></p>
								                     </div>
								             </div>
                                        </div>
                                        <div hidden id="err_file" class="row">
                                        	<div class="col-md-1">
								                  <label style="float: right;">エラーファイル</label>
								             </div>
								             <div class="col-md-2">
								                	 <div class="form-group">
								                        <p class="form-control" id="err_file_name"></p>
								                        <input type="hidden" id="import_id" value="">
								                     </div>
								             </div>
								             <div class="col-md-1">
		                                          <button id="csv" class="btn btn-primary">Dowload</button>
								             </div>
                                        </div>
                                     <br>


				          </div>


	              </div>


	            </li>


          </ul>
        </div>

      </div>

<script type="text/javascript">

      $("#get_time").MonthPicker({
		Button: false ,
		MonthFormat: 'yy/mm'

	  });
      $( document ).ready(function() { //when input change event then submit form


		  $( "#input_file" ).change(function() {

			  $( "#upload" ).submit();

		  });

		  $('#upload').submit(function(event) { // submit form and get data to new object form

			var file_data    = $('#input_file')[0].files[0];
			var company_id   = $('#company_id').val();
            var check = 0;
			if(company_id == ''){

				 $('#message1').show();
                 check = 1;
			}

			var import_type  = $('#import_type').val();

            if(import_type == ''){

				 $('#message2').show();
                  check = 1;
			}

            if($('#get_time').val() == ''){

				 $('#message3').show();
                  check = 1;
			}

            if(check == 1){

                $('#input_file').val('');
            	event.preventDefault();
            	return;
            }
			var result       = $('#get_time').val().split('/');
			var get_time     = result[0]+'-'+result[1]+'-01 00:00:00'; // change format of date

			var form  = new FormData(); // create new form data

			form.append('file_data', file_data);
            form.append('company_id', company_id);
            form.append('import_type', import_type);
            form.append('get_time', get_time);

			$.ajax({
			    url: '/pca/upload',
			    data: form,
			    cache: false,
			    contentType: false,
			    processData: false,
			    type: 'POST',
			    success:function(response) {

			        $('#upload_status').show();

			        $('#import_type_1').text(response.type);
			        $('#file_name').text(response.file_name);
			        $('#total_data').text(response.data_total);
			        $('#import_status').text(response.status);
			        $('#get_time_1').text(response.time);

                    $('#input_file').val('');
                    $('#err_file').hide(); // if get new data then remove old message
                    $('#message1').hide();
                    $('#message2').hide();
                    $('#message3').hide();
                    $('#message4').hide();

                    if(response.status_code == 302){  // if return code 302 then show errors element

                           $('#err_file').show();
                           $('#err_file_name').text(response.file_name_err);
                           $('#import_id').val(response.import_id);

			        }

                    if(response.status_code == 401){ // if has errors validation then show message4

                           $('#upload_status').hide();
                           $('#message4').show();
			        }
			    },

			    error: function (exception) {

                        $('#err_file').show(); // if has server errors then show errors and error content
                         alert(exception.responseText);
                        if(exception.status == 500){

                           $('#server_err').show();

                        }

                        $('#input_file').val('');


				}
			});

            event.preventDefault();

		  });

          $( "#csv" ).click(function(event) { // csv dowload button click event

                var import_id = $("#import_id").val();
                document.location.href = "/err/dowload?import_id="+import_id;

		  });

      });



</script>
<script src="{{ asset('select/icontains.js') }}" ></script>

<script src="{{ asset('select/comboTreePlugin.js') }}" ></script>


@endsection
