@extends('layouts.main')
@section('title') {{ $board->name }} @endsection
@section('crumbs') {{ Breadcrumbs::render('board', $board) }} @endsection
@section('content')
	<h2>{{ $board->name }}</h2>
	@if(count($board->subboards))
		<h3>Sub-Boards</h3>
		<div class="boards">
			@foreach($board->subboards as $subboard)
				<x-forums.board_card :board="$subboard"/>
			@endforeach
		</div>
	@endif
	<h3>Threads</h3>
	<div class="btn-row">
		@if($user && ($board->can_new || $user->perms('forum_boost')))
			<a href="/forums/{{ $board->slug }}/new" class="btn">Start Thread</a>
		@endif
	</div>
	@foreach($threads as $thread)
	<!-- Should we hide for both sides of a block???? -->
		@if(!$user || !$user->findBlock($thread->poster_id))
			<x-forums.thread_card :$thread/>
		@endif
	@endforeach
	{!! $threads->render() !!}
@endsection