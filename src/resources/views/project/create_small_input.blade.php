@extends('layouts.app')
@section('content')
@section('breadcrumbs', Breadcrumbs::render('project/create'))

      <div class="row">
        <div class="col-md-12">

          <ul class="timeline">
	          	<li>

	              <div class="timeline-item">

	                <div class="timeline-body">

				            <div class="box-body">
				            	   @if (isset($message))


								    <p class="message" >{{ $message }}</p>


								    @endif
				            	    @if ($message = Session::get('message'))


						              <p class="" style="text-align: center;color: green">{{ $message }}</p>


								    @endif
								    @if ($errors->has('message'))

                                        <span class="text-danger">{{ $errors->first('message') }}</span>


            	                    @endif

						        <form id="create_project" method="post"
						        action="{{ url('project/create?company_id='.request()->company_id.'&customer_id='.request()->customer_id) }}">

								        <div class="row">
                                        	<div class="col-md-1">
								                  <label class="input_lable">会社コード</label>
								             </div>
								             <div class="col-md-1">
							                	 <div class="form-group">
							                        <select style="width: 200px" id="company_id" name="company_id" >

								                    <option
								                        value="{{$company_id}}">{{Crofun::getCompanyById($company_id)->abbreviate_name}}
								                    </option>

													</select>

							                     </div>
								             </div>
                                        </div>
                                        <div class="row">
                                        	<div class="col-md-1">
								                  <label class="input_lable">顧客コード</label>
								             </div>
								             <div class="col-md-1">
							                	 <div class="form-group">

							                       <p type="text" class="form-control input-sm" >

                                                    {{  Crofun::getClientById($customer_id)->client_code_main  }}

							                        </p>

							                     </div>
								             </div>
								               @if ($errors->has('client_code'))

                                                    <span class="text-danger">{{ $errors->first('client_name') }}</span>

            	                                @endif

                                        </div>
                                        <div class="row">
								                <div class="col-md-1">
								                  <label class="input_lable">顧客名</label>
								                </div>
								                <div class="col-md-5">
								                     <div class="form-group">

								                     <p type="text" class="form-control input-sm" >
								                        @if(Crofun::getClientById($customer_id)->client_name_ab)
								                        {{Crofun::getClientById($customer_id)->client_name_ab}}
								                        @else
								                        {{Crofun::getClientById($customer_id)->client_name}}
								                        @endif
								                     </p>

								                     </div>

								                </div>
								                @if ($errors->has('client_name'))

                                                    <span class="text-danger">{{ $errors->first('client_name') }}</span>

            	                                @endif
						                </div>
                                        <div class="row">
								                <div class="col-md-1 ">
								                  <label class="input_lable">与信希望限度額(顧客単位)</label>
								                </div>
								                <div class="col-md-1">
								                	 <div class="form-group">
								                       <input type="text" disabled  style="text-align: right;width: 200px"
								                       name="credit_expect" id="credit_expect"
								                       @if (isset($credit_expect))
									                       value= "{{ number_format( $credit_expect / 1000)}}"
								                       @endif
								                       >
								                     </div>
								                </div>
                                                <div class="col-md-1 ">
								                  <label class="input_lable">取引想定合計額(顧客単位)</label>
								                </div>
								                <div class="col-md-1">
								                	 <div  class="form-group">
								                       <input style="float: left; text-align: right;width: 200px" disabled type="text" id="transaction" name="transaction"
						                       		   @if (isset($transaction))
								                       value="{{ number_format($transaction /1000) }}"
								                       @endif
								                       >
								                     </div>
								                </div>
						                </div>

                                        <div class="row">
								                <div class="col-md-1 ">
								                  <label class="input_lable"><b>プロジェクトコード</b><sup>※</sup></label>
								                </div>
								                <div class="col-md-1">
								                	 <div class="form-group">
								                       <input type="text"
								                       name="project_code" id="project_code"
								                       value="{{ old('project_code')}}"
								                       style="width: 200px;text-align: right;">
								                     </div>
								                </div>
                                                <div class="col-md-1 ">
								                  <label class="input_lable"><b>プロジェクト名</b><sup>※</sup></label>
								                </div>
								                <div class="col-md-2">
								                	 <div  class="form-group">
								                       <input style="float: left;width: 200px" type="text"  name="project_name"  value="{{ old('project_name')}}"
								                        >
								                     </div>
								                </div>
						                </div>
                                        <div class="row">
                                        	<div class="col-md-1">

								             </div>
								             <div class="col-md-1">
                                             @if ($errors->has('project_code'))
                                                <div class="form-group">
                	                            <span class="text-danger">{{ $errors->first('project_code') }}</span>
                                                </div>
            	                             @endif
								             </div>

								             <div class="col-md-1">

								             </div>
								             <div class="col-md-1">
                                             @if ($errors->has('project_name'))
                                                 <div class="form-group">
                	                            <span class="text-danger">{{ $errors->first('project_name') }}</span>
                                                </div>
            	                             @endif
								             </div>

                                        </div>
                                        <div class="row">
								                <div class="col-md-1 ">
								                  <label class="input_lable"><b>事業本部</b><sup>※</sup></label>
								                </div>
								                <div class="col-md-1">
								                	 <div class="form-group">
									                    <select style="width: 200px"  id="headquarter_id" name="headquarter_id" >
									                    <option> </option>
												          @foreach($headquarters as $headquarter)
									                    <option class="headquarter_id" id="headquarter_id"
									                      {{ old('headquarter_id') == $headquarter->id ? 'selected' : '' }}
									                    data-value="{{ $headquarter->company_id }}"
									                    value="{{$headquarter->id}}">{{$headquarter->headquarters}}
									                    </option>

								                          @endforeach
														</select>

								                     </div>
								                </div>
                                                <div class="col-md-1 ">
								                  <label class="input_lable"><b>部署</b><sup>※</sup></label>
								                </div>
								                <div class="col-md-1">
								                	 <div  class="form-group">
									                    <select  style="width: 200px" id="department_id" name="department_id" >
									                    <option > </option>
												          @foreach($departments as $department)
									                    <option class="department_id"
									                      {{ old('department_id') == $department->id ? 'selected' : '' }}
									                    data-value="{{ $department->headquarter()->id }}"
									                    value="{{$department->id}}">{{$department->department_name}}
									                    </option>
								                          @endforeach
														</select>

								                     </div>
								                </div>
						                </div>
						                <div class="row">
                                        	<div class="col-md-1">

								             </div>
								             <div class="col-md-1">
                                             @if ($errors->has('headquarter_id'))
                                                <div class="form-group">
                	                            <span class="text-danger">{{ $errors->first('headquarter_id') }}</span>
                                                </div>
            	                             @endif
								             </div>

								             <div class="col-md-1">

								             </div>
								             <div class="col-md-1">
                                             @if ($errors->has('department_id'))
                                                 <div class="form-group">
                	                            <span class="text-danger">{{ $errors->first('department_id') }}</span>
                                                </div>
            	                             @endif
								             </div>

                                        </div>
                                        <div class="row">
								                <div class="col-md-1 ">
								                  <label class="input_lable"><b>担当Grp</b><sup>※</sup></label>
								                </div>
								                <div class="col-md-1">
								                	 <div class="form-group">
									                   <select style="width: 200px"  id="group_id" name="group_id" >
									                    <option> </option>
												          @foreach($groups as $group)

										                    <option class="group_id"
										                      {{ old('group_id') == $group->id ? 'selected' : '' }}
										                    data-value="{{ $group->department()->id }}"
										                    value="{{$group->id}}">{{$group->group_name}}
										                    </option>

								                          @endforeach
													  </select>

								                     </div>
								                </div>
                                            @if ($errors->has('group_id'))
                                                <div class="form-group">
                	                            <span class="text-danger">{{ $errors->first('group_id') }}</span>
                                                </div>
            	                             @endif
						                </div>
						                <div class="row">
								                <div class="col-md-1 ">
								                  <label class="input_lable">集計コード</label>
								                </div>
								                <div class="col-md-1">
								                	 <div class="form-group">
								                       <input type="text"
								                       name="get_code" id="get_code"
								                       value="{{ old('get_code')}}"
								                       style="width: 200px">

								                     </div>
								                </div>
                                                <div class="col-md-1 ">
								                  <label class="input_lable">集計コード名</label>
								                </div>
								                <div class="col-md-1">
								                	 <div  class="form-group">
								                       <input style="float: left;width: 200px" type="text" id="get_code_name" name="get_code_name"  value="{{ old('get_code_name')}}"
								                       >

								                     </div>
								                </div>
						                </div>

						                <div class="row">
                                        	<div class="col-md-1">

								             </div>
								             <div class="col-md-1">
                                             @if ($errors->has('get_code'))
                                                <div class="form-group">
                	                            <span class="text-danger">{{ $errors->first('get_code') }}</span>
                                                </div>
            	                             @endif
								             </div>

								             <div class="col-md-1">

								             </div>
								             <div class="col-md-1">
                                             @if ($errors->has('get_code_name'))
                                                 <div class="form-group">
                	                            <span class="text-danger">{{ $errors->first('get_code_name') }}</span>
                                                </div>
            	                             @endif
								             </div>

                                        </div>
						                <div class="row">

							                <div class="col-md-1 ">
							                  <label class="input_lable"><b>取引想定額</b></label>
							                </div>
							                <div class="col-md-3">

							                	 <div  class="form-group">
							                       <input type="text" id="transaction_money" name="transaction_money"
							                       @if(old('transaction_money'))
								                       value="{{ old('transaction_money')}}"
							                       @endif
							                       style="width: 200px;text-align: right;">
							                        <input type="hidden" name="customer_id" value="{{$customer_id}}">

							                     </div>


							                </div>
                                       		@if ($errors->has('transaction_money'))
                                                <div class="form-group">
                	                            <span class="text-danger">{{ $errors->first('transaction_money') }}</span>
                                                </div>
            	                             @endif

						                </div>

					                  @csrf

						                <div class="row">
								                <div class="col-md-1 ">
								                  <label class="input_lable">単発</label>
								                </div>
								                <div class="col-md-1">
								                   <div class="form-group">

								                    <input type="checkbox" name="one_shot">

								                   </div>
								                </div>
                                                <div class="col-md-1 ">
								                  <label class="input_lable">スポット取引想定</label>
								                </div>
								                <div class="col-md-1">
								                	 <div  class="form-group">
								                       <input type="text" id=""  style="text-align: right;width: 200px" name="transaction_shot"
								                       @if(old('transaction_shot'))
									                       value="{{ old('transaction_shot')}}"
								                       @endif
								                       >
								                        <input type="hidden" name="customer_id" value="{{$customer_id}}">

								                     </div>
								                </div>
						                </div>

						                <div class="row">
                                        	<div class="col-md-1">

								             </div>
								             <div class="col-md-1">

								             </div>

								             <div class="col-md-1">

								             </div>
								             <div class="col-md-1">
                                             @if ($errors->has('transaction_shot'))
                                                 <div class="form-group">
                	                            <span class="text-danger">{{ $errors->first('transaction_shot') }}</span>
                                                </div>
            	                             @endif
								             </div>

                                        </div>
						                <div class="row" id="change_reason" >
                                        	  <div class="col-md-3 offset-md-3">
                                                   <label class="input_lable">備考</label>
                                        	  </div>
                                        	  <div class="col-xs-2">
								                	<textarea rows="5" cols="120" name="note">
								                		@if(isset($note))
								                			{{ $note }}
							                			@endif

								                	</textarea>
								               </div>

                                        </div>
                                        <br>



						              <br>


						             <div class="row">
						                	　　　<br>

								                <div class="col-md-4">

								                <button type="button" id="form_submit"  style="float:right;width: 200px;" class="btn btn-primary">登録</button>

								                </div>
                                                <div class="col-md-4">

								                 <a href="{{route('customer_edit', ['id' => $customer_id])}}" style="float: left;width: 200px;" class="btn btn-danger">戻る</a>



								                </div>
				                     </div>

				              </form>
				            <!-- /.box-body -->
				          </div>


	              </div>
	            </li>
	            <li>


					<!--               <h4 class="text-red" style="text-align: center;">検索要件を入力してください</h4> -->


	            </li>

          </ul>
        </div>
      </div>
<script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha384-nvAa0+6Qg9clwYCGGPpDQLVpLNn0fRaROjHqs13t4Ggj3Ez50XnGQqc/r8MhnRDZ" crossorigin="anonymous"></script>

<script type="text/javascript">

    $(document).ready(function() {



        $("#form_submit").click(function(){

                        var company_id =  $('#company_id').val();

        				$.ajax({

					           type:'POST',
					           url:'/project/getcode',
					           data: {

    						        "company_id"   : company_id
						        },

					           success:function(data){


                                   $('#project_code').val(data.num);
                                   $('#create_project').submit();


					           },

					           error: function (exception) {

								         alert(exception.responseText);

								}

					        });

		 });



    });
</script>
<script src="{{ asset('select/icontains.js') }}" ></script>

<script src="{{ asset('select/comboTreePlugin.js') }}" ></script>


@endsection
