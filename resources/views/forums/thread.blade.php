@extends('layouts.main')
@section('title') {{ $thread->name }} in {{ $board->name }} @endsection
@section('crumbs') {{ Breadcrumbs::render('thread', $thread) }} @endsection
@section('content')
	@if($thread->is_deleted)
		<div class="alert-warning">
			This thread has been deleted and therefore cannot be replied to.
			@if($user->perms('can_msg_mod'))
				As a message moderator, you can <a href="/forums/manage/{{ $thread->id }}">manage the thread</a> to undelete it.
			@endif
		</div>
	@endif
	@if($is_auth)
		<div class="btn-row">
			@if($user->perms('can_msg_mod'))
				<a href="/forums/manage/{{ $thread->id }}" class="btn">Manage Thread</a>
			@elseif($thread->poster_id == $user->id)
				<a href="/thread/manage/{{ $thread->id }}" class="btn">Manage Thread</a>
			@endif

			<!-- Check if the user is subscribed to the thread -->
			@if(!$thread->subbedBy($user->id))
				{{ html()->form('POST', '/forums/thread/'.$thread->id.'/sub')->open() }}
				{{ html()->submit('Subscribe') }}
				{{ html()->form()->close() }}
			@else
				{{ html()->form('POST', '/forums/thread/'.$thread->id.'/unsub')->open() }}
				{{ html()->submit('Unsubscribe') }}
				{{ html()->form()->close() }}
			@endif
		</div>
	@endif
	<h2>{{ $thread->name }}</h2>
	@foreach($posts as $post)
		<!-- Check if the viewer has been blocked by or has blocked the poster (make an 'ignore blocks' perm for staffs) -->
		<x-forums.post_card :$post/>
	@endforeach
	{!! $posts->render() !!}

	@if(!$is_auth)
		<div class="alert-warning">
			You must <a href="/login">Log In</a> or <a href="/register">Sign Up</a> before you can post on the forums.
		</div>
	@elseif($thread->is_deleted)
		<div class="alert-warning">
			This thread has been deleted and therefore cannot be replied to.
			@if($user->perms('can_msg_mod'))
				As a message moderator, you can <a href="/forums/manage/{{ $thread->id }}">manage the thread</a> to undelete it.
			@endif
		</div>
	@elseif($thread->is_locked && !$user->perms('can_msg_mod'))
		<div class="alert-warning">
			This thread is locked and cannot be replied to.
		</div>
	@elseif($board->can_post || $user->perms('private_forum'))
		@if($thread->is_locked)
			<div class="alert-warning">
				This thread is locked and cannot be replied to by general users. As a message moderator, you can <a href="/forums/manage/{{ $thread->id }}">manage the thread</a> to unlock it.
			</div>
		@endif
		@if($user->isBlocked($thread->poster_id) && !$user->perms('block_bypass'))
			((((you been blocked so you can't post here))))
		@else
			<div class="well">
				<div>
					{{ html()->form('POST', '/forums/'.$thread->id.'/post')->open() }}
						<div class="form-group">
							{{ html()->textareaBBC('content_bbc', null, []) }}
						</div>
					{{ html()->submit('Submit Post')->class('btn') }}
					{{ html()->form()->close() }}
				</div>
			</div>
		@endif
	@else
		<div class="alert-warning">
			You do not have permission to post on this thread.
		</div>
	@endif
@endsection
@push('scripts')
	<script type="text/javascript">
		$('.msg-delete').on('click', function(e) {
			e.preventDefault();
			if(confirm("Are you sure you would like to delete this post?")) {
				var id = $(this).data('id');
				$.post('/forums/post/'+id+'/delete', {'_token':'{{ csrf_token() }}','js':true}, function(data) {
					if(data['success'] === false) alert(data['errors'][0]);
					if(data['redirect']) {
						window.location.href = '/forums/{{ $board->slug }}';
					} else {
						location.reload();
					}
				}).fail(function() {
					alert("Unable to process the request. Please try again.");
				});
			}
		});
	</script>
@endpush