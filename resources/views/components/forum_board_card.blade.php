<div class="col-md-6">
	<div class="board-card clearfix">
		<div class="side">
			{!! $board->display_icon !!}<br>
			<span class="count">{{ $board->thread_counter }}</span>
		</div>
		<div class="info">
			{!! $board->display_name !!}
			<span class="desc"><i>{{ $board->description }}</i></span>
			@if(count($board->subboards))
				<div class="subs">
					Sub-Boards:
					@foreach($board->subboards as $sub)
						<li>{!! $sub->display_name !!}</li>
					@endforeach
				</div>
			@endif
		</div>
	</div>
</div>