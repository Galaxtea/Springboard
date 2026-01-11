@extends('layouts.main')
@section('title') Error 404 @endsection
@section('content')
	<h3>Whoops! It looks like you took a wrong turn...</h3>
	@if($exception->getMessage())
		{{ $exception->getMessage() }}
	@else
		The page you are trying to access doesn't appear to exist.
	@endif
	Please check your link, try again, or come back later. If you believe this is an error, please submit a thread to the <a href="/forums/bugs">Bug Reports</a> forums.
@endsection