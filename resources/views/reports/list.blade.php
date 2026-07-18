@extends('layouts.main')
@section('title') Viewing Your Reports @endsection
@section('crumbs') {{ Breadcrumbs::render() }} @endsection
@section('content')
	<h1>Viewing Your Reports</h1>
	@foreach($reports as $report)
		<a href="{{ $report->link }}">{{ $report->title }}</a><br>
	@endforeach
@endsection