@inject('onlines', 'App\Services\User\OnlineService')
@inject('reports', 'App\Services\Admin\ReportService')
<div class="sidebar">
	<div>
		{!! $user->display_icon !!}<br>
		Welcome back, {!! $user->display_name !!}!<br>
		{{ $reports::count() }} active reports
	</div>
	<div class="flexbody">
		<span><a href="/online">{{ $onlines::count() }} Online</a></span>
		<span class="cal">{!! \Carbon\Carbon::siteClock() !!}</span>
	</div>
	<div>Wow one more!</div>
</div>