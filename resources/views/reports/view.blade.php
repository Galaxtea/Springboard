@extends('layouts.main')
@section('title') Viewing Report @endsection
@section('crumbs') {{ Breadcrumbs::render('reports.view', $report->id) }} @endsection
@section('content')
	<h1>Viewing Report</h1>
	<b>{{ $report->title }}</b><br>
	@if($user->perms('can_reports'))
		<a href="/panel/report/{{ $report->id }}">Link to Admin Page if has can_reports perm</a><br>
	@endif
	Report status<br>
	Report resolution<br>
	{{ $report->content }}<br>
	<a href="{{ $report->reportable->link_url }}">Link to reported page</a>
	<br><br>
	@php
		$comments = $report->showComments();
		$id = $report->id;
		$can_comment = true;
		if($report->status == 'closed') $can_comment = false;
	@endphp
	<x-comments.comment_section type="report" :$comments :$id :$can_comment/>
@endsection