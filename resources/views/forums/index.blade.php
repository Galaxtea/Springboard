@extends('layouts.main')
@section('title') Forums @endsection
@section('crumbs') {{ Breadcrumbs::render() }} @endsection
@section('content')
	@foreach($forums as $forum_cat => $boards)
		<h4>{{ $forum_cat }}</h4>
		<div class="row mb-4">
			@foreach($boards as $board)
				@include('components.forum_board_card')
			@endforeach
		</div>
	@endforeach
@endsection