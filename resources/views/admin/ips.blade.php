@extends('layouts.admin')
@section('title') IP List @endsection
@section('content')
	<h2>IP List</h2>
	@foreach($uses as $use)
		{{ $use->ip_address }} used by {{ $use->user_id }} from {{ $use->created_at->format('d H:i') }} to {{ $use->updated_at->format('d H:i') }}
		<br>
	@endforeach
	{!! $uses->render() !!}
@endsection