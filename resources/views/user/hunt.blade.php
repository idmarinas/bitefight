@extends('index')

@section('content')
	<h1>{{__('user.hunt_header', ['user' => e(user()->getName())])}}</h1>
	<p>{{__('user.hunt_header_line')}}</p>
	<br>
	<div>
		<div id="humanHunting">
			<div class="wrap-top-left">
				<div class="wrap-top-right">
					<div class="wrap-top-middle"></div>
				</div>
			</div>
			<div class="wrap-left clearfix">
				<div id="humanhunt" class="wrap-content wrap-right">
					<h2>{{user_race_logo_small()}}{{__('user.human_hunt_header')}}</h2>
					
					<div @if(user()->getApNow() > 1)onclick="doHunt(1)"@endif class="mjs" style="position:relative; height:121px; cursor:pointer;">
						<img src="{{asset('img/hunt/city1.jpg')}}" alt="{{__('user.human_hunt_hunt_1')}}"/>
						<div id="mjInfo_1" style="text-align:left;">
							<table class='noBackground' border="0" style="display:block;">
								<tr>
									<td nowrap style='font-size: 1.1em;'><b>{{__('user.human_hunt_hunt_1')}}</b> ( 1 {{action_point_image_tag()}} )</td>
								</tr>
							</table>
							<table class='noBackground' border="0" style="display:none;">
								<tr>
									<td nowrap style='font-size: 1.1em;'><b>{{__('user.human_hunt_hunt_1')}}</b> ( 1 {{action_point_image_tag()}} )</td>
								</tr>
								<tr>
									<td nowrap>{{__('user.human_hunt_chance_of_success')}}: {{$hunt1Chance}}%</td>
								</tr>
								<tr>
									<td nowrap>{{prettyNumber($hunt1Reward)}} {{gold_image_tag()}} + {{$hunt1Exp}} {{__('general.experience')}}</td>
								</tr>
							</table>
						</div>
					</div>
					<div class="btn-left center">
						<div class="btn-right">
							<button @if(user()->getApNow() > 1) onclick="doHunt(1)" @endif class="btn">{{__('user.human_hunt_hunt_1')}}</button>
						</div>
					</div>
					<br/>
					<div @if(user()->getApNow() > 1) onclick="doHunt(2)" @endif class="mjs" style="position:relative; height:121px; cursor:pointer;">
						<img src="{{asset('img/hunt/city2.jpg')}}" alt="{{__('user.human_hunt_hunt_2')}}"/>
						<div id="mjInfo_2" style="text-align:left;">
							<table class='noBackground' border="0" style="display:block;">
								<tr>
									<td nowrap style='font-size: 1.1em;'><b>{{__('user.human_hunt_hunt_2')}}</b> ( 1 {{action_point_image_tag()}} )</td>
								</tr>
							</table>
							<table class='noBackground' border="0" style="display:none;">
								<tr>
									<td nowrap style='font-size: 1.1em;'><b>{{__('user.human_hunt_hunt_2')}}</b> ( 1 {{action_point_image_tag()}} )</td>
								</tr>
								<tr>
									<td nowrap>{{__('user.human_hunt_chance_of_success')}}: {{$hunt2Chance}}%</td>
								</tr>
								<tr>
									<td nowrap>{{prettyNumber($hunt2Reward)}} {{gold_image_tag()}} + {{$hunt2Exp}} {{__('general.experience')}}</td>
								</tr>
							</table>
						</div>
					</div>
					<div class="btn-left center">
						<div class="btn-right">
							<button @if(user()->getApNow() > 1) onclick="doHunt(2)" @endif class="btn">{{__('user.human_hunt_hunt_2')}}</button>
						</div>
					</div>
					<br/>
					<div @if(user()->getApNow() > 2) onclick="doHunt(3)" @endif class="mjs" style="position:relative; height:121px; cursor:pointer;">
						<img src="{{asset('img/hunt/city3.jpg')}}" alt="{{__('user.human_hunt_hunt_3')}}"/>
						<div id="mjInfo_3" style="text-align:left;">
							<table class='noBackground' border="0" style="display:block;">
								<tr>
									<td nowrap style='font-size: 1.1em;'><b>{{__('user.human_hunt_hunt_3')}}</b> ( 2 {{action_point_image_tag()}} )</td>
								</tr>
							</table>
							<table class='noBackground' border="0" style="display:none;">
								<tr>
									<td nowrap style='font-size: 1.1em;'><b>{{__('user.human_hunt_hunt_3')}}</b> ( 2 {{action_point_image_tag()}} )</td>
								</tr>
								<tr>
									<td nowrap>{{__('user.human_hunt_chance_of_success')}}: {{$hunt3Chance}}%</td>
								</tr>
								<tr>
									<td nowrap>{{prettyNumber($hunt3Reward)}} {{gold_image_tag()}} + {{$hunt3Exp}} {{__('general.experience')}}</td>
								</tr>
							</table>
						</div>
					</div>
					<div class="btn-left center">
						<div class="btn-right">
							<button @if(user()->getApNow() > 2) onclick="doHunt(3)" @endif class="btn">{{__('user.human_hunt_hunt_3')}}</button>
						</div>
					</div>
					<br/>
					<div @if(user()->getApNow() > 2) onclick="doHunt(4)" @endif class="mjs" style="position:relative; height:121px; cursor:pointer;">
						<img src="{{asset('img/hunt/city4.jpg')}}" alt="{{__('user.human_hunt_hunt_4')}}"/>
						<div id="mjInfo_4" style="text-align:left;">
							<table class='noBackground' border="0" style="display:block;">
								<tr>
									<td nowrap style='font-size: 1.1em;'><b>{{__('user.human_hunt_hunt_4')}}</b> ( 2 {{action_point_image_tag()}} )</td>
								</tr>
							</table>
							<table class='noBackground' border="0" style="display:none;">
								<tr>
									<td nowrap style='font-size: 1.1em;'><b>{{__('user.human_hunt_hunt_4')}}</b> ( 2 {{action_point_image_tag()}} )</td>
								</tr>
								<tr>
									<td nowrap>{{__('user.human_hunt_chance_of_success')}}: {{$hunt4Chance}}%</td>
								</tr>
								<tr>
									<td nowrap>{{prettyNumber($hunt4Reward)}} {{gold_image_tag()}} + {{$hunt4Exp}} {{__('general.experience')}}</td>
								</tr>
							</table>
						</div>
					</div>
					<div class="btn-left center">
						<div class="btn-right">
							<button @if(user()->getApNow() > 2) onclick="doHunt(4)" @endif class="btn">{{__('user.human_hunt_hunt_4')}}</button>
						</div>
					</div>
					<br/>
					<div @if(user()->getApNow() > 3) onclick="doHunt(5)" @endif class="mjs" style="position:relative; height:121px; cursor:pointer;">
						<img src="{{asset('img/hunt/city5.jpg')}}" alt="{{__('user.human_hunt_hunt_5')}}"/>
						<div id="mjInfo_5" style="text-align:left;">
							<table class='noBackground' border="0" style="display:block;">
								<tr>
									<td nowrap style='font-size: 1.1em;'><b>{{__('user.human_hunt_hunt_5')}}</b> ( 3 {{action_point_image_tag()}} )</td>
								</tr>
							</table>
							<table class='noBackground' border="0" style="display:none;">
								<tr>
									<td nowrap style='font-size: 1.1em;'><b>{{__('user.human_hunt_hunt_5')}}</b> ( 3 {{action_point_image_tag()}} )</td>
								</tr>
								<tr>
									<td nowrap>{{__('user.human_hunt_chance_of_success')}}: {{$hunt5Chance}}%</td>
								</tr>
								<tr>
									<td nowrap>{{prettyNumber($hunt5Reward)}} {{gold_image_tag()}} + {{$hunt5Exp}} {{__('general.experience')}}</td>
								</tr>
							</table>
						</div>
					</div>
					<div class="btn-left center">
						<div class="btn-right">
							<button @if(user()->getApNow() > 3) onclick="doHunt(5)" @endif class="btn">{{__('user.human_hunt_hunt_5')}}</button>
						</div>
					</div>
					<script type="text/javascript">
						$('.mjs').hover(
							function() {
								$(this).children('div').animate({height: '90'});
								$(this).children('div').children('table:first-child').hide();
								$(this).children('div').children('table:last-child').show();
							},
							function() {
								$(this).children('div').animate({height: '35'});
								$(this).children('div').children('table:first-child').show();
								$(this).children('div').children('table:last-child').hide();
							}
						);
					</script>
				</div>
			</div>
			<div class="wrap-bottom-left">
				<div class="wrap-bottom-right">
					<div class="wrap-bottom-middle"></div>
				</div>
			</div>
		</div>
		<script type="text/javascript">
			function doHunt(type)
			{
				var url = "{{url('/hunt/human')}}/"+type+"?_token={{csrf_token()}}";
				window.location.replace(url);
			}

			function docheckbox(id)
			{
				var $totemSearch = $('#totemsearch'),
					$onlyTotemSearch = $('#onlytotemsearch');

				if ($totemSearch.is(':checked') && id == 'totemsearch')
				{
					$onlyTotemSearch.removeAttr('checked');
				}
				if ($onlyTotemSearch.is(':checked') && id == 'onlytotemsearch')
				{
					$totemSearch.removeAttr('checked');
				}
			}
		</script>
	</div>
@endsection