<style>
.ui-widget-content{z-index:9999;}
.ui-autocomplete {
    position: absolute;
}
#hid_schedule_type{display: none;}
#container_my_team,#container_opp_team {
    display: block; 
    position:relative
} 
</style>

<!-- Modal -->
{!! Form::open(['route' => 'addschedule','class'=>'form-horizontal','method' => 'POST','id' => 'frm_add_schedule']) !!} 
<div class="modal fade"  id="myModal" role="dialog">
	<div class="modal-dialog sj_modal sportsjun-forms">
	  <!-- Modal content-->
	  <div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title">{{ trans('message.schedule.fields.schedulematch') }}</h4>
		</div>
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
@if (session('status'))
	<div class="alert alert-success">
		{{ session('status') }}
	</div>
@endif
                <div class="alert alert-success" id="div_success_tourney" style="display:none;"></div>
		<div class="alert alert-danger" id="div_failure_tourney" style="display:none;"></div>
		<div class="modal-body">
	        <div class="sportsjun-forms sportsjun-container wrap-2 sportsjun-forms-modal">
		        <div class="form-body">
					<div class="section" id="hid_schedule_type">
						<label class="form_label">{{   trans('message.team.fields.sports') }}<span  class='required'>*</span> </label>
	                    <div class="section colm colm6 pad-r40">
                            <div class="option-group field">
									<label class="option">
										{!! Form::radio('scheduletype', 'team' ,'true', ['id' => 'scheduletype']) !!} <span class="radio"></span> Team
									</label>
								@if ($errors->has('scheduletype')) <p class="help-block">{{ $errors->first('scheduletype') }}</p> @endif
                            </div>                    
	                    </div>
					</div>
					<div class="section">
						<label class="form_label">{{   trans('message.schedule.fields.myteam') }}<span  class='required'>*</span> </label>						
						<label for="myteam" class="field prepend-icon">
							{!! Form::text('myteam',(!empty($team_name)?$team_name:''), array('required','class'=>'gui-input','placeholder'=>trans('message.schedule.fields.myteam'),'id'=>'myteam','readonly','autocomplete'=>'off')) !!}
							<div id="container_my_team"></div>
							{!! Form::hidden('my_team_id', (!empty($teamId)?$teamId:''), array('id' => 'my_team_id')) !!}
							{!! Form::hidden('sports_id', Request::segment(4) , array('id' => 'sports_id')) !!}
							@if ($errors->has('myteam')) <p class="help-block">{{ $errors->first('myteam') }}</p> @endif
							<label for="myteam" class="field-icon"><i class="fa fa-user"></i></label>
						</label>
					</div>
					<div class="section">
						<label class="form_label">{{   trans('message.schedule.fields.opponentteam') }}<span  class='required'>*</span> </label>					
						<label for="oppteam" class="field prepend-icon">
							{!! Form::text('oppteam',null, array('required','class'=>'gui-input','placeholder'=>trans('message.schedule.fields.opponentteam'),'id'=>'oppteam','autocomplete'=>'off')) !!}
							<div id="container_opp_team"></div>
							{!! Form::hidden('opp_team_id', '', array('id' => 'opp_team_id')) !!}
							{!! Form::hidden('schedule_id', null , array('id' => 'schedule_id')) !!}
							@if ($errors->has('oppteam')) <p class="help-block">{{ $errors->first('oppteam') }}</p> @endif
							<label for="oppteam" class="field-icon"><i class="fa fa-user"></i></label>
						</label>
					</div>
					<div class="section">
						<label class="form_label">{{   trans('message.schedule.fields.playertype') }}<span  class='required'>*</span> </label>						
						<label class="field select">
							{!! Form::select('player_type', $player_types,null,['class'=>'gui-input','placeholder'=>trans('message.schedule.fields.playertype'),'id'=>'player_type']
							) !!}					
							@if ($errors->has('player_type')) <p class="help-block">{{ $errors->first('player_type') }}</p> @endif
							<i class="arrow double"></i> 
						</label>
					</div>
					<div class="section">
						<label class="form_label">{{   trans('message.schedule.fields.matchtype') }}<span  class='required'>*</span> </label>						
						<label class="field select">
							{!! Form::select('match_type',$match_types,null,['class'=>'gui-input','placeholder'=>trans('message.schedule.fields.matchtype'),'id'=>'match_type']
							) !!}
							@if ($errors->has('match_type')) <p class="help-block">{{ $errors->first('match_type') }}</p> @endif
							<i class="arrow double"></i> 
						</label>
					</div>					
					<div class="section">
						<label class="form_label">{{   trans('message.schedule.fields.start_date') }}<span  class='required'>*</span> </label>							
						<label for="match_start_date" class="field prepend-icon">
							<div class='input-group date' id='matchStartDate'>
								{!! Form::text('match_start_date',null, array('required','class'=>'gui-input','placeholder'=>trans('message.schedule.fields.start_date'),'id'=>'match_start_date')) !!}
								<span class="input-group-addon">
									<span class="glyphicon glyphicon-calendar"></span>
								</span>
								@if ($errors->has('match_start_date')) <p class="help-block">{{ $errors->first('match_start_date') }}</p> @endif
							</div>
						</label>
					</div>
                            
                    <div class="section">
						<label class="form_label">{{   trans('message.schedule.fields.start_time') }}</label><label for="match_start_time" class="field prepend-icon">
							<div class='input-group date' id='matchStartTime'>
								{!! Form::text('match_start_time',null, array('class'=>'gui-input','placeholder'=>trans('message.schedule.fields.start_time'),'id'=>'match_start_time')) !!}
								<span class="input-group-addon">
									<span class="glyphicon glyphicon-calendar"></span>
								</span>
								@if ($errors->has('match_start_time')) <p class="help-block">{{ $errors->first('match_start_time') }}</p> @endif
							</div>
						</label>
					</div>
					<div class="section">
						<label class="form_label">{{   trans('message.schedule.fields.venue') }}<span  class='required'>*</span> </label>    						
						<label for="venue" class="field prepend-icon">
							{!! Form::text('venue',null, array('required','class'=>'gui-input','placeholder'=>trans('message.schedule.fields.venue'),'id'=>'venue')) !!}
							{!! Form::hidden('facility_id', '', array('id' => 'facility_id')) !!}
							{!! Form::hidden('is_edit', '', array('id' => 'is_edit')) !!}
							@if ($errors->has('venue')) <p class="help-block">{{ $errors->first('venue') }}</p> @endif
							<label for="venue" class="field-icon"><i class="fa fa-user"></i></label>
						</label>
					</div>
					@include ('common.address',['mandatory'=>''])
				</div>
	        </div>
        </div>
		<div class="modal-footer">
			<button type="button" name="save_schedule" id="save_schedule" class="button btn-primary">Schedule</button>
			<button type="button" class="button btn-secondary" data-dismiss="modal">Close</button>
		</div>
	  </div>
	</div>
</div>
{!! Form::close() !!}
{!! JsValidator::formRequest('App\Http\Requests\AddSchedulesRequest', '#frm_add_schedule'); !!}
<script type="text/javascript">
    $(document).ready(function() {
                $("#save_schedule").show();
		$(".modal").on("hidden.bs.modal", function(){
			$(".modal-body1").html("");
		});
		$("#matchStartDate").datetimepicker({ format: '{{ config("constants.DATE_FORMAT.JQUERY_DATE_FORMAT") }}' });
		$('#matchStartTime').datetimepicker({ format: '{{ config("constants.DATE_FORMAT.JQUERY_TIME_FORMAT") }}' });
		//on page load
		$("#schedule_match_btn").click(
			function()
			{
				$("#myteam").val('{{(!empty($team_name)?$team_name:'')}}');
				$("#my_team_id").val({{(!empty($teamId)?$teamId:'')}});
				//populating radio button based on selected radio button and default is team
				var $radios = $('input:radio[name=scheduletype]');
				$radios.filter('[value=Team]').prop('checked', true);
				if($('input[name=scheduletype]:checked').val() === "team")
				{
				$("#my_team").html('My Team');
				$("#opp_team").html('Opponent Team');
				}
				else
				{
				$("#my_team").html('My Player');
				$("#opp_team").html('Opponent Player');
				}
			}
		);

		//on radio button change
		$('input[name=scheduletype]').change(
			function()
			{
				//if team is selected
				if($('input[name=scheduletype]:checked').val() === "team")
				{
					$("#my_team").html('My Team');
					$("#opp_team").html('Opponent Team');
					$("#oppteam").val('');
					$("#opp_team_id").val('');
					$("#myteam").val('');
					$("#my_team_id").val('');
					$("#myteam").autocomplete( "option", "source", base_url+"/oppositeteamdetails?team_id="+{{ Request::segment(3) }}+"&sport_id="+{{ Request::segment(4) }}+"&scheduled_type=team");
					$("#oppteam").autocomplete( "option", "source", base_url+"/oppositeteamdetails?team_id="+{{ Request::segment(3) }}+"&sport_id="+{{ Request::segment(4) }}+"&scheduled_type=team");
				}
				else //if player is selected
				{
					$("#my_team").html('My Player');
					$("#opp_team").html('Opponent Player');
					$("#oppteam").val('');				
					$("#opp_team_id").val('');
					$("#myteam").val('');
					$("#my_team_id").val('');					
					$("#myteam").autocomplete( "option", "source", base_url+"/oppositeteamdetails?team_id="+{{ Request::segment(3) }}+"&sport_id="+{{ Request::segment(4) }}+"&scheduled_type=player");
					$("#oppteam").autocomplete( "option", "source", base_url+"/oppositeteamdetails?team_id="+{{ Request::segment(3) }}+"&sport_id="+{{ Request::segment(4) }}+"&scheduled_type=player");
				}			
			}
		);

		//for autocomplete my team or player		
		$("#myteam").autocomplete({
			source: base_url+"/myteamdetails?team_id="+{{ Request::segment(3) }}+"&sport_id="+{{ Request::segment(4) }}+"&scheduled_type="+$('input[name=scheduletype]:checked').val(),
			minLength: 3,
			change: function(event,ui) { 
			if (ui.item==null || ui.item==undefined) 
			{ 
				$("#myteam").val('');
				$("#myteam").focus(); 
			} 
			},
			select: function(event, ui) {
				$('#my_team_id').val(ui.item.id);
			},
			appendTo: "#container_my_team"
		});

		//for autocomplete opponent team or player		
		$("#oppteam").autocomplete({
			source: base_url+"/oppositeteamdetails?team_id="+{{ Request::segment(3) }}+"&sport_id="+{{ Request::segment(4) }}+"&scheduled_type="+$('input[name=scheduletype]:checked').val(),
			minLength: 3,
			change: function(event,ui) { 
				if (ui.item==null || ui.item==undefined) 
				{ 
					$("#oppteam").val('');
					$("#oppteam").focus(); 
				} 
			},			
			select: function(event, ui) {
				$('#opp_team_id').val(ui.item.id);
			},
			appendTo: "#container_opp_team"
		});	

		//for autocomplete facilities
		/*$("#venue").autocomplete({
			source: base_url+"/facilities",
			minLength: 3,		
			select: function(event, ui) {
			$('#facility_id').val(ui.item.id);
			}
		});*/
    });
	
 	//save match schedule
	$("#save_schedule").click(function(){
		if($('#frm_add_schedule').valid()) //if form is valid
		{
                        $("#save_schedule").before("<div id='loader'></div>");
                        $("#loader").html("<img src="+base_url+"/images/loaderwhite_21X21.gif>");
                        $("#save_schedule").hide();
			$("#frm_add_schedule").ajaxSubmit({
				url: base_url + '/addschedule', 
				type: 'get',
				dataType:'json',
				success:function(data){
					if(data.success)
					{
						// console.log(data.success);
						$("#div_success_tourney").text(data.success);
						$("#div_success_tourney").show();
					}
					else
					{
						// console.log(data.success);
						$("#div_failure_tourney").text(data.failure);
						$("#div_failure_tourney").show();
                                                $("#loader").remove();
                                                $("#save_schedule").show();
					}
					$('.modal .modal-body').animate({scrollTop:0},500);
					//on success reload the page
					window.setTimeout(function(){location.reload()},2000)

				},
				error: function ( xhr, status, error) {
					//on error get the errors and display
                                        $("#loader").remove();
                                        $("#save_schedule").show();
                                        var data=xhr.responseText;
					var parsed_data = JSON.parse(data);
					$.each(parsed_data, function(key, value) {
						if(key ===	'start_time')//if error thrown is for date picker
						{
							$("#"+key).parent().parent().parent().addClass('has-error');	
							$("#"+key).parent().parent().append(getErrorHtml(value, key, '_'+key));
						}
						else //if other errors
						{
							$("#"+key).parent().parent().addClass('has-error');	
							$("#"+key).parent().append(getErrorHtml(value, key, '_'+key));
						}
						
					});
				}
			}); 
		}else{
			return true;
		}
	});
	
	//function to build span on error
	function getErrorHtml(formErrors , id, flag )
	{
		var o = '<span id="'+id+'-error" class="help-block error-help-block" >';
		o += formErrors;
		o += '</span>';
		return o;
	}

</script>