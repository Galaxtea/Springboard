@inject('onlines', 'App\Services\User\OnlineService')
<div class="sidebar">
	<div>
		Welcome back, {!! $user->display_name !!}!<br>
		(open report count)
	</div>
	<div class="flexbody">
		<span><a href="/online">{{ $onlines::count() }} Online</a></span>
		<span class="cal">{!! \Carbon\Carbon::siteClock() !!}</span>
	</div>
	<div>Wow one more!</div>
</div>