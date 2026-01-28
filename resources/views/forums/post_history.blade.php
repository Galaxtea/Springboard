@extends('layouts.main')
@section('title') Reviewing post edit history @endsection
@section('crumbs') {{ Breadcrumbs::render('post_history', $post->thread) }} @endsection
@section('content')
	<h4>Reviewing post edit history in {{ $post->thread->name }}</h4>
	<x-forums.post_card :$post/>
	@foreach($edits as $edit)
		<a id="post_{{ $edit->id }}"/>
		<div class="forum-post clearfix">
			<div class="post-user">
				{!! $edit->editor->display_avatar !!}
				<b class="name">{!! $edit->editor->display_name !!}</b>
			</div>
			<div class="post-text">
				<div class="post-top clearfix">
					<div class="timestamp">{{ $edit->posted_at }}</div>
				</div>
				{!! $edit->display_content !!}
			</div>
		</div>
	@endforeach
	{!! $edits->render() !!}
@endsection