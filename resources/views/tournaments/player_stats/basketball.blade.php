@if (count($player_standing))
     <h4><b>{{ config('constants.BASKETBALL_STATS.BASKETBALL_STATISTICS')}}</b></h4>
    <div class=" stats-table" id='teamStatsDiv'>
    <table class="table">
        <thead>
            <tr>
                <th>PLAYER </th>
                <th>TEAM </th>
                <th>{{ config('constants.STATISTICS.MATCHES')}}</th>
                <th>1 P</th>
                <th>2 P</th>
                <th>3 P</th>
                <th>T POINTS</th>
                <th>{{ config('constants.BASKETBALL_STATS.FOULS')}}</th>
<!--                <th>{{ config('constants.BASKETBALL_STATS.GOALS_SAVED')}}</th>
                <th>{{ config('constants.BASKETBALL_STATS.GOALS_ASSIST')}}</th>
                <th>{{ config('constants.BASKETBALL_STATS.GOALS_PENALTIES')}}</th>-->
            </tr>
        </thead>
        <tbody>
            @foreach($player_standing as $statistic)  
            <tr>
                <td><a href='/editsportprofile/{{$statistic->team_id}}' class="text-primary">

                    @if($statistic->url!='')
                                <!--<img class="fa fa-user fa-fw fa-2x" height="42" width="42" src="{{ url('/uploads/user_profile/'.$statistic->url) }}" onerror="this.onerror=null;this.src='{{ asset('/images/default-profile-pic.jpg') }}';">-->
                                
                         
                                    {!! Helper::Images($statistic->url,'user_profile',array('class'=>'img-circle img-border img-responsive lazy','height'=>52,'width'=>52) )!!}
                             
                                
                                @else
                            <!--    <img  class="fa fa-user fa-fw fa-2x" height="42" width="42" src="{{ asset('/images/default-profile-pic.jpg') }}">-->
                              
                                    {!! Helper::Images('default-profile-pic.jpg','images',array('class'=>'img-circle img-border img-responsive lazy','height'=>52,'width'=>52) )!!}
                                
                    
                    @endif

                    {{$statistic->player_name}}</a></td>                
                <td><a href='/team/members/{{$statistic->team_id}}' class="text-primary">{{$statistic->team_name}}</a></td>
                <td>{{$statistic->matches}}</td>
                <td>{{$statistic->points_1}}</td>
                <td>{{$statistic->points_2}}</td>
                <td>{{$statistic->points_3}}</td>
                <td>{{$statistic->total_points}}</td>
                <td {{$statistic->fouls>0?"class=red":''}}>{{$statistic->fouls}}</td>
<!--                <td>{{$statistic->goals_saved}}</td>
                <td>{{$statistic->goal_assist}}</td>
                <td>{{$statistic->goal_penalties}}</td>-->
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
    @else
    
    <div class="sj-alert sj-alert-info">
                       {{ trans('message.sports.nostats')}}
</div>
    @endif