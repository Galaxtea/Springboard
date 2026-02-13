@extends('layouts.admin')
@section('title') User List @endsection
@section('crumbs') {{ Breadcrumbs::render() }} @endsection
@section('content')
	<h2>User List</h2>
	<table><tr><th>Username</th><th></th><th>Seen</th></tr>
		@foreach($players as $player)
			<tr><td><a href="/panel/user/{{ $player->id }}">{{ $player->username }}</a></td><td><a href="/user/{{ $player->id }}">profile</a></td><td>{{ ($player->active_at ?? $player->created_at)->diffForHumans() }}</td></tr>
		@endforeach
	</table>
	{!! $players->render() !!}
@endsection