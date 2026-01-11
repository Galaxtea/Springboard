@extends('layouts.main')
@section('title') Manage Thread @endsection
@section('content')
	<h4>Manage Thread</h4>
	<div class="row">
		<div class="col-6">
			<h6>Thread Info</h6>
			<b>Title:</b> {!! $thread->display_name !!}<br>
			<b>Poster:</b> {!! $thread->poster->display_name !!}<br>
			<b>Posted At:</b> {!! $thread->posted_at !!}<br>
			<b>Latest Activity:</b> {!! $thread->latest->posted_at !!}<br>
			<b>Is Deleted:</b> {{ $thread->is_deleted ? 'Yes, since '.$thread->removed_at : 'No' }}<br>
			<div class="text-center form-group">
				@if($thread->is_deleted)
					{{ html()->form('POST', '/forums/'.$thread->id.'/restore')->open() }}
					{{ html()->submit('Undelete Thread')->class('btn') }}
				@else
					{{ html()->form('POST', '/forums/post/'.$thread->first->id.'/delete')->open() }}
					{{ html()->submit('Delete Thread')->class('btn btn-danger') }}
				@endif
				{{ html()->form()->close() }}
			</div>
			<br>
			<div class="form-group">
				<h6>Migrate Thread</h6>
				<b>Primary Board:</b> {!! $thread->board->display_name !!}<br>
				<small><i>This dictates the base permissions for the board - whether regular players are able to view and post on the thread.</i></small>
				<div class="text-center">
					{{ html()->form('POST', '/forums/'.$thread->id.'/move')->open() }}
						{{ html()->select('board', $boards)->class('form-control') }}
						{{ html()->submit('Move Thread')->class('btn') }}
					{{ html()->form()->close() }}
				</div>
			</div>
			<br>
			<div class="form-group">
				<table class="alternate text-center">
					<tr><th colspan="2">Thread Clones</th></tr>
					@foreach($thread->boards as $board)
						@if($board->id != $thread->orig_board_id)
							<tr>
								<td>{{ $board->name }}</td>
								<td>
									{{ html()->form('POST', '/forums/'.$thread->id.'/unclone')->open() }}
										{{ html()->hidden('remove', $board->id) }}
										{{ html()->submit('Remove')->class('btn btn-sm btn-danger') }}
									{{ html()->form()->close() }}
								</td>
							</tr>
						@endif
					@endforeach
				</table>
				<div class="text-center">
					{{ html()->form('POST', '/forums/'.$thread->id.'/clone')->open() }}
						{{ html()->select('clone', $boards)->class('form-control') }}
						{{ html()->submit('Clone To Board')->class('btn btn-primary') }}
					{{ html()->form()->close() }}
				</div>
			</div>
		</div>
		<div class="col-6">
			{{ html()->form('POST', '/forums/'.$thread->id.'/edit')->open() }}
			<div class="form-group">
				<h6>Modify</h6>
				{{ html()->label('Thread Title:', 'name') }}
				{{ html()->text('name', $thread->name)->class('form-control') }}

				{{ html()->label('Primary Poster:', 'poster') }}
				{{ html()->text('poster', $thread->poster->id.' - '.$thread->poster->username)->class('form-control') }}
				<br>
				{{ html()->checkbox('is_sticky', $thread->is_sticky)->id('is_sticky') }} {{ html()->label('Sticky Thread', 'is_sticky') }}
				<br>
				{{ html()->checkbox('is_locked', $thread->is_locked)->id('is_locked') }} {{ html()->label('Lock Thread', 'is_locked') }}
			</div>
			@if(count($tags))
				<div class="form-group">
					<h6>Tags</h6>
					@foreach($tags as $tag)
						{{ html()->checkbox('tags['.$tag->id.']', in_array($tag->id, $thread->tagged))->id('tags['.$tag->id.']') }} <label for="tags[{{ $tag->id }}]">{!! $tag->display_full !!}</label><br>
					@endforeach
				</div>
			@endif
			<div class="text-center">
				{{ html()->submit('Update Thread')->class('btn') }}
			</div>
			{{ html()->form()->close() }}
		</div>
	</div>
@endsection