@extends('layouts.main')
@section('title') Blocked Users @endsection
@section('content')
	@foreach($blocks as $block)
		{!! $block->isBlocking->display_name !!} Note: {{ $block->self_note }}
		{{ html()->form('POST', '/unblock/'.$block->blocked_id)->open() }}
		{{ html()->submit('Unblock User') }}
		{{ html()->form()->close() }}
		<br>
	@endforeach
	{!! $blocks->render() !!}
@endsection