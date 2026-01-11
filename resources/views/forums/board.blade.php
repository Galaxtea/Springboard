@extends('layouts.main')
@section('title') {{ $board->name }} @endsection
@section('crumbs') {{ Breadcrumbs::render('board', $board) }} @endsection
@section('content')
	<h4>{{ $board->name }}</h4>
	@if(count($board->subboards))
		<h5>Subboards</h5>
		<div class="row mb-5">
			@foreach($board->subboards as $subboard)
				@include('components.forum_board_card', ['board' => $subboard])
			@endforeach
		</div>
	@endif
	<div class="btn-row">
		@if($is_auth && ($board->can_new || $user->perms('forum_boost')))
			<a href="/forums/{{ $board->slug }}/new" class="btn">Start Thread</a>
		@endif
	</div>
	<h5 class="mb-0">Threads</h5>
	@foreach($threads as $thread)
	<!-- Should we hide for both sides of a block???? -->
		@if(!$user->findBlock($thread->poster_id))
			<div class="thread">
				<div class="thread-icon">
					{!! $thread->display_icon !!}
				</div>
				<div class="thread-name">
					@foreach($thread->tags as $tag)
						{!! $tag->display !!}
					@endforeach
					@if($thread->subbedBy($user->id))
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
		@endif
	@endforeach
	{!! $threads->render() !!}
@endsection