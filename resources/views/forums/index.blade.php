@extends('layouts.main')
@section('title') Forums @endsection
@section('crumbs') {{ Breadcrumbs::render() }} @endsection
@section('content')
	@foreach($forums as $forum_cat => $boards)
		<h2>{{ $forum_cat }}</h2>
		<div class="boards">
			@foreach($boards as $board)
				<x-forums.board_card :$board/>
			@endforeach
		</div>
	@endforeach
@endsection