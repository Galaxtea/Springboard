@extends('layouts.main')
@section('title') Reviewing post edit history @endsection
@section('content')
	<h4>Reviewing post edit history in {{ $post->thread->name }}</h4>
	@foreach($edits as $edit)
		<a id="post_{{ $edit->id }}"/>
		<div class="form-post">
			<div class="post-user">
				{!! $edit->post->poster->display_avatar !!}
				<b class="name">{!! $edit->post->poster->display_name !!}</b>
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