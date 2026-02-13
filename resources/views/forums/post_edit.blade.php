@extends('layouts.main')
@section('title') Edit Post @endsection
@section('crumbs') {{ Breadcrumbs::render('post_edit', $post->thread) }} @endsection
@section('content')
	<h4>Editing {{ $post->poster->username }}'s post on <i>{{ $post->thread->name }}</i></h4>
	<div class="form-post">
		<div class="post-user">
			{!! $post->poster->display_avatar !!}
			<b class="name">{!! $post->poster->display_name !!}</b>
		</div>
		<div class="post-text {{ $post->is_edited ? 'edited' : ''}}">
			<div class="post-top clearfix">
				<div class="timestamp">{{ $post->posted_at }}</div>
				<div class="buttons">
					@if($user)
						@if($user->id == $post->poster_id || $user->perms('can_msg_mod'))
							<a href="/forums/post/{{ $post->id }}/edit">E</a> |
							<a href="" class="msg-delete" data-id="{{ $post->id }}">X</a> |
						@endif
						<a href="/forums/post/{{ $post->id }}/report">!</a> |
						<a href="">L</a>
					@endif
				</div>
			</div>
			{!! $post->display_content !!}
			@if($post->is_edited)
				<div class="post-bottom">
					Edited by {!! $post->editor->display_name !!} on {{ $post->edited_at }}
					@if($user && $user->perms('can_msg_mod'))
						| <a href="/forums/post/{{ $post->id }}/history">Edit History</a>
					@endif
				</div>
			@endif
		</div>
	</div>
	<div class="well">
		<div>
			{{ html()->form('POST', '/forums/post/'.$post->id.'/edit')->open() }}
				{{ html()->hidden('post_id', $post->id) }}
				<div class="form-group">
					{{ html()->textareaBBC('content_bbc', $post->content, []) }}
				</div>
			{{ html()->submit('Edit Post')->class('btn') }}
			{{ html()->form()->close() }}
		</div>
	</div>
@endsection