@extends('layouts.admin')
@section('title') IP History {{ $player->username }} @endsection
@section('crumbs') {{ Breadcrumbs::render('admin.user', $player->username, $player->id) }} @endsection
@section('content')
	<h2>{{ $player->username }}'s IP History</h2>
	<table><tr><th>IP</th><th>First Active</th><th>Latest Active</th><th>Users</th></tr>
		@foreach($ips as $ip)
			<tr><td>{{ $ip->ip_address }}</td><td>{{ $ip->created_at }} wowee</td><td>{{ $ip->updated_at->diffForHumans() }}{{ $ip->updated_at }}</td><td><a href="/panel/ip/{{ $ip->ip_address }}">{{ count($ip->uses) }}</a></td></tr>
		@endforeach
	</table>
	{!! $ips->render() !!}
@endsection