@props(['thread', 'board' => $thread->board])
<div class="thread">
	<div class="thread-icon">
		{!! $thread->display_icon !!}
	</div>
	<div class="thread-name">
		@foreach($thread->tags as $tag)
			{!! $tag->display !!}
		@endforeach
		@if($user && $thread->subbedBy($user->id))
			<i>subbed</i>
		@endif
		<a href="/forums/{{ $board->slug }}/{{ $thread->id }}">{{ $thread->name }}</a>
		<small class="thread-poster">Started by {!! $thread->poster->display_name !!}</small>
	</div>
	<small class="thread-posts">
		{{ $thread->post_counter }}
	</small>
	<small class="thread-latest">
		Latest post by {!! $thread->latest->poster->display_name !!}<br>
		Posted {!! $thread->latest->posted_at !!}
	</small>
</div>