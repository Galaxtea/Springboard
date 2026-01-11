<div class="sidebar">
	<div>
		@if($is_auth)
			Welcome back, {!! $user->display_name !!}!<br>
			{{ $user->pri_curr }} {{ $currencies->primary_name }}<br>
			{{ $user->sec_curr }} {{ $currencies->secondary_name }}<br>
		@else
			Welcome, traveller!
		@endif
	</div>
	<div class="flexbody">
		<span><a href="/online">{{ $online_count }} Online</a></span>
		<span class="cal">{!! $time !!}</span>
	</div>
	<div>Wow one more!</div>
</div>