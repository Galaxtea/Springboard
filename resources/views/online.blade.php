@extends('layouts.main')
@section('title') Online Users @endsection
@section('crumbs') {{ Breadcrumbs::render() }} @endsection
@section('content')
	<h2>Online Users</h2>
	@foreach($online_list as $active_user)
		{!! $active_user->display_name !!} {{ !$active_user->settings->display_active ? '(Invisible)' : '' }}
		<br>
	@endforeach
	{!! $online_list->render() !!}
@endsection