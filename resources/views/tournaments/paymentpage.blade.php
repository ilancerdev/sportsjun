@extends('layouts.app')
@section('content')

<div class="">
<div class="sportsjun-wrap">
<div class="sportsjun-forms sportsjun-container wrap-2">


<div class="form-header header-primary register_form_head"><h4 class='register_form_title'>Payment Form</h4></div>
@if($errors->any())
<h4 class="error_validation">{{$errors->first()}}</h4>
@endif
<div class="form-body">
{!! Form::open(array('url' => 'tournaments/paymentform', 'method' => 'post')) !!}

<div class="row">
{!! Form::hidden("cart_id", $id) !!}



<div class="col-sm-5">
      <div class="section">
          <label class="field prepend-icon">
            {!! Form::text("firstname", $user_data->firstname, array('required','class'=>'gui-input','placeholder' => 'Name' )) !!}
            
            </label>
           </div>
        </div>

<div class="col-sm-6">
  <div class="section">
    <label class="field prepend-icon">
        {!! Form::text("address", $user_data->address, array('required','class'=>'gui-input','placeholder' => 'Address' )) !!}
            
            
           
            </label>
     
    </div>
 </div>
<!-- end section -->  



</div><!--end row -->



<div class="row">
@if(isset($countries))
<div class="col-sm-4">
    <div class="section">
        <label class="form_label">{{ trans('message.common.fields.country') }}	<span  class='required'>*</span></label>
        <label class="field select">
            {!! Form::select('country',$countries, 101, array('required','id'=>'country_id','class'=>'form-control','onchange'=>'displayCountries(this.value)','autocomplete'=>'off','placeholder'=>trans('message.common.fields.country'))) !!}
            @if ($errors->has('country_id')) <p class="help-block">{{ $errors->first('country_id') }}</p> @endif
            <i class="arrow double"></i>
        </label>
    </div>
</div>
@endif

<div class="col-sm-4">
    <div class="section">
    <label class="form_label">{{ trans('message.common.fields.state') }}	<span  class='required'>*</span></label>
        <label class="field select">
    
            {!! Form::select('state',$states, null, array('required','id'=>'state_id','class'=>'form-control states','onchange'=>'displayStates(this.value)','autocomplete'=>'off','placeholder'=>trans('message.common.fields.state'))) !!}
               @if ($errors->has('state_id')) <p class="help-block">{{ $errors->first('state_id') }}</p> @endif
            <i class="arrow double"></i>                    
        </label>  
    </label>
    </div>
</div>
<!-- end section -->  
<div class="col-sm-4">
    <div class="section">
    <label class="form_label">{{ trans('message.common.fields.city') }}	<span  class='required'>*</span> </label>
        <label class="field select">
             {!! Form::select('city',$cities, null, array('required','id'=>'city_id','class'=>'form-control cities','id'=>'city_id','placeholder'=>trans('message.common.fields.city'))) !!}		 	
            @if ($errors->has('city')) <p class="help-block">{{ $errors->first('city_id') }}</p> @endif
            <i class="arrow double"></i>                    
        </label>  
    </label>
    </div>
</div>
</div><!-- end section --> 




<!-- <div class="row">




<div class="col-sm-5">
      <div class="section">
          <label class="field prepend-icon">
             {!! Form::text("country", $user_data->country, array('required','class'=>'gui-input','placeholder' => 'Country' )) !!}
            
            </label>
           </div>
        </div>

<div class="col-sm-6">
  <div class="section">
    <label class="field prepend-icon">
            {!! Form::text("state", '', array('required','class'=>'gui-input','placeholder' => 'State' )) !!}
            
           
            </label>
     
    </div>
 </div>
<!-- end section 



</div><!--end row --> 



<div class="row">




<div class="col-sm-5">
      <div class="section">
          <label class="field prepend-icon">
          {!! Form::text("phone", $user_data->contact_number, array('required','class'=>'gui-input','placeholder' => 'Country' )) !!}
            
            </label>
           </div>
        </div>

<div class="col-sm-6">
  <div class="section">
    <label class="field prepend-icon">
              {!! Form::text("zipcode", '', array('required','class'=>'gui-input','placeholder' => 'Zipcode' )) !!}
            
           
            </label>
     
    </div>
 </div>
<!-- end section -->  



</div><!--end row -->


<!-- <div class="row">




<div class="col-sm-5">
      <div class="section">
          <label class="field prepend-icon">
            {!! Form::text("phone", $user_data->contact_number, array('required','class'=>'gui-input','placeholder' => 'Country' )) !!}
            
            </label>
           </div>
        </div>

<div class="col-sm-6">
  <div class="section">
    <label class="field prepend-icon">
             
             
           
            </label>
     
    </div>
 </div>
<!-- end section 



</div><!--end row --> 

















{!! Form::submit('Proceed to Pay', array('class' => 'button btn-primary')) !!}


{!! Form::close() !!}
 
</div>

</div>
</div>
</div>
@endsection