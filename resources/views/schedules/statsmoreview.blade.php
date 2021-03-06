            @if(count($matchScheduleData))
            @foreach($matchScheduleData as $schedule)
            <div class="row">
            <div id="schedule_{{$schedule['id']}}" class="schedule_list clearfix">
            	<div class="deskview hidden-xs">
                    <div id="teamone_{{$schedule['scheduleteamone']['id']}}" class="col-sm-4 score_view_img">
                        <p><!--<img src="{{ asset('/uploads/'.config('constants.PHOTO_PATH.TEAMS_FOLDER_PATH').'/'.$schedule['scheduleteamone']['url']) }}" onerror="this.onerror=null;this.src='{{ asset('/images/default-profile-pic.jpg') }}';" height="30" width="30">-->
                         {!! Helper::Images($schedule['scheduleteamone']['url'],config('constants.PHOTO_PATH.TEAMS_FOLDER_PATH'),array('class'=>'img-circle img-border','height'=>90,'width'=>90) )!!}</p>
                        @if(isset($schedule['scheduleteamone']['result']))
                            <span>{{ $schedule['scheduleteamone']['result'] }}</span>
                        @endif
                    </div>    
    
                    <div id="center_div" class="col-sm-4">
                        <p class="vs_text">
                            <span>
                                <a href="{{ url('/team/members',[$schedule['a_id']]) }}">
                                {{$schedule['scheduleteamone']['name']}}
                                </a>
                            </span>  
                           vs 
                            <span>@if(isset($schedule['scheduleteamtwo']['name']))
                                    <a href="{{ url('/team/members',[$schedule['b_id']]) }}">{{$schedule['scheduleteamtwo']['name']}}
                                    </a>
                                  @else  
                                    {{trans('message.bye')}}
                                  @endif  
                            </span>
                         </p> 
                        <p class="vs_date">
                            <span>{{ $schedule['match_start_date'] }}</span>
                            <span class='sports_text'>{{ isset($schedule['sport']['sports_name'])?$schedule['sport']['sports_name']:'' }}</span>
                            @if($schedule['match_type']!='other')
                                <span class='match_type_text'>({{ $schedule['match_type']=='odi'?strtoupper($schedule['match_type']):ucfirst($schedule['match_type']) }})</span>
                            @endif
                        </p>
                        <p><a href="{{ url('match/scorecard/edit/'.$schedule['id']) }}" class="add_score_but">{{$schedule['winner_text']}}</a></p>
                    </div>    
                    
                    <div id="teamtwo_{{$schedule['scheduleteamtwo']['id']}}" class="col-sm-4 score_view_img">
                        <p><!--<img src="{{ asset('/uploads/'.config('constants.PHOTO_PATH.TEAMS_FOLDER_PATH').'/'.$schedule['scheduleteamtwo']['url']) }}" onerror="this.onerror=null;this.src='{{ asset('/images/default-profile-pic.jpg') }}';" height="30" width="30">-->
                         {!! Helper::Images($schedule['scheduleteamtwo']['url'],config('constants.PHOTO_PATH.TEAMS_FOLDER_PATH'),array('class'=>'img-circle img-border','height'=>90,'width'=>90) )!!}</p>
                            @if(isset($schedule['scheduleteamone']['result']))
                            <span>{{ isset($schedule['scheduleteamtwo']['result'])?$schedule['scheduleteamtwo']['result']:'BYE' }}</span>
                        @endif
                    </div>
                </div>
                
				<div class="mobview hidden-sm hidden-lg">
                    <div class="row">
                        <div id="teamone" class="col-xs-5 score_view_img">
                            <p>
                               {!! Helper::Images($schedule['scheduleteamone']['url'],config('constants.PHOTO_PATH.TEAMS_FOLDER_PATH'),array('class'=>'img-circle img-border','height'=>90,'width'=>90) )!!}
                            </p>
                            <p>
                            	@if(isset($schedule['scheduleteamone']['result']))
                                    <span>{{ $schedule['scheduleteamone']['result'] }}</span>
                                @endif
                            </p>    
                            <p class="vs_text">
                                <span>
                                    <a href="{{ url('/team/members',[$schedule['a_id']]) }}">
                                    {{$schedule['scheduleteamone']['name']}}
                                    </a>
                                </span> 
                            </p> 
                        </div>    
                
                        <div id="center_div" class="col-xs-2">
                             <span style="position: absolute; margin-top: 25px;">vs</span>
                        </div>
                
                        <div id="teamtwo" class="col-xs-5 score_view_img">
                        <p>
                            {!! Helper::Images($schedule['scheduleteamtwo']['url'],config('constants.PHOTO_PATH.TEAMS_FOLDER_PATH'),array('class'=>'img-circle img-border','height'=>90,'width'=>90) )!!} 
                        </p>
                        <p>
                        	@if(isset($schedule['scheduleteamone']['result']))
                                <span>{{ isset($schedule['scheduleteamtwo']['result'])?$schedule['scheduleteamtwo']['result']:'BYE' }}</span>
                            @endif
                        </p>
                        <p class="vs_text">
                            <span>@if(isset($schedule['scheduleteamtwo']['name']))
                                    <a href="{{ url('/team/members',[$schedule['b_id']]) }}">{{$schedule['scheduleteamtwo']['name']}}
                                    </a>
                                  @else  
                                    {{trans('message.bye')}}
                                  @endif  
                            </span>
                        </p> 
                    </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-12">
                           <?php 
                        $schedule['match_start_date'] = trim($schedule['match_start_date']);
                        if (strpos($schedule['match_start_date'], ':') == false)
                        {
                                $schedule['match_start_date'] = DateTime::createFromFormat('d/m/Y', $schedule['match_start_date'])->format('jS F, Y');
                        }
                        else
                        {
                                $schedule['match_start_date'] = DateTime::createFromFormat('d/m/Y g:i A', $schedule['match_start_date'])->format('jS F, Y g:i A');
                        }
                    ?>
                     
                    <p class="vs_date">                       
                        <span><b>{{ $schedule['match_start_date'] }}</b></span>
                        <span class='sports_text'>{{ isset($schedule['sport']['sports_name'])?$schedule['sport']['sports_name']:'' }}</span>
                        @if($schedule['match_type']!='other')
                            <span class='match_type_text'>({{ $schedule['match_type']=='odi'?strtoupper($schedule['match_type']):ucfirst($schedule['match_type']) }})</span>
                        @endif

                    </p>
                            <span class=''>{{Helper::getMatchDetails($schedule['id'])->address}}</span><br>
                   Status: <span class='sports_text'>{{ ucfirst($schedule['match_status']) }}</span> <br>
                    Scores: <span class=''>{{Helper::getMatchDetails($schedule['id'])->scores}} </span> <br>
                    @if(!is_null(Helper::getMatchDetails($schedule['id'])->winner_id))
                            <span class='red'>Winner: {{Helper::getMatchDetails($schedule['id'])->winner}} </span>
                                
                    @endif 
                        <a href="{{ url('match/scorecard/edit/'.$schedule['id']) }}" class="add_score_but">{{$schedule['winner_text']}}</a>
                    </div>
                 </div>
           	</div>
            </div>    
            @endforeach
            @endif

     

<script type="text/javascript">
    $(document).ready(function() { 
		var offset = {{$offset}};
    	$("#offset").val(offset);
        if(offset>=global_record_count)
        {
            $("#viewmorediv").remove();
	}
		
    });
</script>   
