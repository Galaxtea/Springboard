@extends('layouts.main')
@section('title') {{ $profile->username }}'s Profile @endsection
@section('crumbs') {{ Breadcrumbs::render('profile', $profile->username, $profile->id) }} @endsection
@section('content')
	<h1>Viewing {{ $profile->username }}'s Profile</h1>
	@if($user && ($is_blocked = $profile->findBlock($user->id)) && !$user->perms('block_bypass'))
		{{ $profile->username }} has blocked you :c
	@else
		{{ $profile->seen($user ? $user->perms('can_reports') : false) }}
	@endif
<br><br><br>
	@if($user && $profile->id != $user->id)
		@if($block = $user->findBlock($profile->id))
			{{ html()->form('POST', '/unblock/'.$profile->id)->open() }}
			{{ html()->submit('Unblock User') }}
			{{ html()->form()->close() }}
		@endif
<br><br><br>

		{{ html()->form('POST', '/block/'.$profile->id)->open() }}
		@if($block)
			{{ html()->label('Personal Note:', 'self_note') }}<br>
			{{ html()->textarea('self_note', $block->self_note) }}
<br>
			{{ html()->submit('Update Note') }}
		@else
			{{ html()->submit('Block User') }}
		@endif
		{{ html()->form()->close() }}
<br><br><br><br>
		@if(!$block && !$is_blocked)
			@php
				$outFriend = $user->findFriend($profile->id);
				$inFriend = $profile->findFriend($user->id);
			@endphp
			<h3>Friends</h3>
			@if(!$outFriend && !$inFriend)
			<!-- There is no friend request, so allow sending. -->
				{{ html()->form('POST', '/friend/'.$profile->id)->open() }}
				{{ html()->submit('Send Request') }}
				{{ html()->form()->close() }}
			@elseif($outFriend)
			<!-- Check if they are ALREADY friends, and have an unfriend button if so -->
				@if($outFriend->status == 'Accepted')
					{{ html()->form('POST', '/unfriend/'.$profile->id)->open() }}
					{{ html()->submit('Unfriend') }}
					{{ html()->form()->close() }}
				@else
					{{ html()->form('POST', '/unfriend/'.$profile->id)->open() }}
					{{ html()->submit('Cancel Request') }}
					{{ html()->form()->close() }}
				@endif
			@elseif($inFriend)
			<!-- Check if there is a pending friend request, and which direction. From user, cancel. To user, accept / reject. -->
					{{ html()->form('POST', '/friend/'.$profile->id)->open() }}
					{{ html()->submit('Accept Request') }}
					{{ html()->form()->close() }}

					{{ html()->form('POST', '/unfriend/'.$profile->id)->open() }}
					{{ html()->submit('Reject Request') }}
					{{ html()->form()->close() }}
			@endif
		@endif
	@endif
@endsection