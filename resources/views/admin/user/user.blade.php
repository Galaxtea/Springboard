@extends('layouts.admin')
@section('title') Account Info {{ $player->username }} @endsection
@section('crumbs') {{ Breadcrumbs::render('admin.user', $player->username, $player->id) }} @endsection
@section('content')
	<h2>{{ $player->username }}'s Account Info</h2>
	Username: {{ $player->username }}<br>
	Email: {{ $player->settings->email }}<br>
	Is Verified<br>
	Joined Date: {{ $player->created_at->format('M d Y') }}<br>
	Last Active: {{ ($player->active_at ?? $player->created_at)->diffForHumans() }} - {{ ($player->active_at ?? $player->created_at)->format('M d Y h:m') }}<br>
	Birthday: {{ $player->settings->birthday->format('M d Y') }}<br>
	Rank: {{ $player->rank->name }}<br>
	Upgrades<br>
	Is Banned<br>
	Latest IP (<a href="/panel/user/{{ $player->id }}/ips">link to IP history</a>): {{ $recent_ip->ip_address }}<br>
	Activity List<br>
	Communications<br>
	Report History<br>
	Staff Notes<br>
	Reset Password<br>
	Deactivate Account<br>
@endsection