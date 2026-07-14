@props(['comment'])
<div class="comment">
	{!! $comment->poster->display_icon !!}
	{!! $comment->poster->display_name !!} posted {{ $comment->posted_at }}
	<br>{!! $comment->content_html !!}
</div>