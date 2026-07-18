@extends('layouts.admin')
@section('title') Viewing Report @endsection
@section('crumbs') {{ Breadcrumbs::render('reports.view', $report->id) }} @endsection
@section('content')
	<h1>Viewing Report</h1>
	<b>{{ $report->title }}</b><br>
	<a href="/report/{{ $report->id }}">Link to Public Page</a><br>
	Report status<br>
	Report resolution<br>
	{{ $report->content }}<br>
	<a href="{{ $report->reportable->link_url }}">Link to reported page</a>
	<br><br>
	Reported by {!! $report->reporter->display_name !!}<br>
	<a href="{{ $report->link_source }}">Reported Content</a><br>
	Relevant user (if applicable) & most recent reports on them<br>
	@php
		$comments = $report->showComments();
		$id = $report->id;
	@endphp
	<x-comments.comment_section type="panelreport" :$comments :$id/>
@endsection