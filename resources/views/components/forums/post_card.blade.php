@props(['post'])
<a id="post_{{ $post->id }}"/>
<div class="forum-post clearfix {{ $post->is_deleted ? 'deleted' : '' }}">
	<div class="post-user">
		{!! $post->poster->display_avatar !!}
		<b class="name">{!! $post->poster->display_name !!}</b>
	</div>
	@if($user && ($user->isBlocked($post->poster_id) || $user->findBlock($post->poster_id)) && !$user->perms('block_bypass'))
	<div class="post-text">
		Blocked message.
	</div>
	@else
		<div class="post-text {{ $post->is_edited ? 'edited' : ''}}">
			<div class="post-top clearfix">
				<div class="timestamp">{{ $post->posted_at }}</div>
				<div class="buttons">
					@if($user && !$post->is_deleted)
						@if($user->id == $post->poster_id || $user->perms('can_msg_mod'))
							<a href="/forums/post/{{ $post->id }}/edit">E</a> |
							<a href="" class="msg-delete" data-id="{{ $post->id }}">X</a> |
						@endif
						<a href="/report/post/{{ $post->id }}">!</a> |
					@endif
					<a href="{{ $post->link }}">L</a>
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
	@endif
</div>