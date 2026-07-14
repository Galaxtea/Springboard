@props(['comments', 'id', 'type', 'can_comment'])
<div>
	@if(isset($can_comment) && $can_comment == false)
		Comments are disabled
	@else
		{{ html()->form('POST', "/comment/{$type}/{$id}")->open() }}
			<div class="form-group">
				{{ html()->textareaBBC('content_bbc', null, []) }}
			</div>
		{{ html()->submit('Submit Comment')->class('success') }}
		{{ html()->form()->close() }}
	@endif
</div>
@foreach($comments as $comment)
	<x-comments.post_card :$comment/>
@endforeach
{!! $comments->render() !!}