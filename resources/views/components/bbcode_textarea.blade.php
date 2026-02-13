
<div class="post-preview forum-post clearfix" style="display:none;">
	<div class="post-user">
		{!! $user->display_avatar !!}
		<b class="name">{!! $user->display_name !!}</b>
	</div>
	<div class="post-text">
		<div class="post-top clearfix">
			<div class="timestamp">Previewing Post</div>
		</div>
		<div class="bbc-preview"></div>
	</div>
</div>

<div class="bbc-tags">
	<span name="bold" onclick="bbcTag('[b]','[/b]', '{{ $name }}')"><b>B</b></span> |
	<span name="italics" onclick="bbcTag('[i]','[/i]', '{{ $name }}')"><i>I</i></span> |
	<span name="underline" onclick="bbcTag('[u]','[/u]', '{{ $name }}')"><u>U</u></span> |
	<span name="strike" onclick="bbcTag('[s]','[/s]', '{{ $name }}')"><strike>S</strike></span> |
	<span name="left" onclick="bbcTag('[left]','[/left]', '{{ $name }}')">L</span> |
	<span name="right" onclick="bbcTag('[right]','[/right]', '{{ $name }}')">R</span> |
	<span name="center" onclick="bbcTag('[center]','[/center]', '{{ $name }}')">C</span> |
	<span name="justify" onclick="bbcTag('[justify]','[/justify]', '{{ $name }}')">J</span> |
	<span name="sub" onclick="bbcTag('[sub]','[/sub]', '{{ $name }}')"><sub>sub</sub></span> |
	<span name="sup" onclick="bbcTag('[sup]','[/sup]', '{{ $name }}')"><sup>sup</sup></span>
	@if($user->perms('can_msg_mod'))
		| <span name="more" onclick="bbcTag('[more]','', '{{ $name }}')">M</span>
	@endif
</div>

{{ html()->textarea($name, $default)->id($name)->class('form-control' . ($errors->has($name) ? ' is-invalid' : null))->rows(7) }}
@error($name)<br><small class="form-error">{{ $message }}</small>@enderror


<span onclick="previewPost('{{ $name }}')">Preview Post</span>


@pushOnce('scripts', 'bbcode.js')
	<script type="text/javascript" src="{{ asset('js/bbcode.js') }}"></script>
@endPushOnce