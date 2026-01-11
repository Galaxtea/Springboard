@extends('layouts.main')
@section('title') Friended Users @endsection
@section('content')
	@foreach($pending as $inRequest)
		{!! $inRequest->isRequesting->display_name !!}
		{{ html()->form('POST', '/friend/'.$inRequest->friend_id)->open() }}
		{{ html()->submit('Accept Request') }}
		{{ html()->form()->close() }}

		{{ html()->form('POST', '/unfriend/'.$inRequest->friend_id)->open() }}
		{{ html()->submit('Reject Request') }}
		{{ html()->form()->close() }}
		<br>
	@endforeach

	@foreach($friends as $friend)
		{!! $friend->display_name !!}
		{{ html()->form('POST', '/unfriend/'.$friend->id)->open() }}
		{{ html()->submit('Unfriend') }}
		{{ html()->form()->close() }}
		<br>
	@endforeach
	{!! $friends->render() !!}
@endsection