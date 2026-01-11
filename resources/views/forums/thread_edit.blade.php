@extends('layouts.main')
@section('title') Create New Thread @endsection
@section('content')
	<h4>Creating a new thread in {{ $board->name }}</h4>
	{{ html()->form('POST', '/forums/'.$board->slug.'/new')->open() }}
		<div class="form-group row">
			<div class="col-md-3">
				{{ html()->label('Thread Title:', 'name') }}
			</div>
			<div class="col-md-9">
				{{ html()->text('name')->class('form-control' . ($errors->has('name') ? ' is-invalid' : null)) }}
				@error('name')<small class="form-error">{{ $message }}</small>@enderror
			</div>
		</div>
		@if(count($tags))
			<div class="form-group">
				Tags:
				@foreach($tags as $tag)
					{{ html()->checkbox('tags['.$tag->id.']')->id('tags['.$tag->id.']') }} <label for="tags[{{ $tag->id }}]">{!! $tag->display_full !!}</label>
				@endforeach
			</div>
		@endif
		<div class="form-group">
			{{ html()->textareaBBC('content_bbc', null, []) }}
		</div>
		@if($user->perms('can_msg_mod'))
			<div class="form-group">
				{{ html()->checkbox('is_sticky')->id('is_sticky') }} {{ html()->label('Sticky Thread', 'is_sticky') }}
				<br>
				{{ html()->checkbox('is_locked')->id('is_locked') }} {{ html()->label('Lock Thread', 'is_locked') }}
			</div>
		@endif
	{{ html()->submit('Create Thread')->class('btn') }}
	{{ html()->form()->close() }}
@endsection