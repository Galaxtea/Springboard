<div class="crumbs">
	@unless($breadcrumbs->isEmpty())
		@foreach($breadcrumbs as $crumb)
			@if(!$loop->first)
				/
			@endif
			@if(!is_null($crumb->url) && !$loop->first)
				<a href="{{ $crumb->url }}">{{ $crumb->title }}</a>
			@else
				{{ $crumb->title }}
			@endif
		@endforeach
	@endunless
</div>