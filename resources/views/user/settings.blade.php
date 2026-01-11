@extends('layouts.main')
@section('title') User Settings @endsection
@php
	$settings = $user->settings;
	$profile = $user->profile;
@endphp
@section('content')
	<h1>Editing User Settings</h1>
	<!-- Open forum for editing -->
	{{ html()->form('POST', '/settings')->open() }}

		{{ html()->label('Profile Description', 'content_bbc') }}
		{{ html()->textareaBBC('content_bbc', $profile->content_bbc, []) }}

<br><br>

		{{ html()->checkbox('allow_comments', $profile->allow_comments) }}
		<label for="allow_comments">Allow Profile Comments</label>
<br><br>

		{{ html()->checkbox('display_active', $settings->display_active) }}
		<label for="display_active">Display Activity</label>
<br><br>

		{{ html()->checkbox('allow_messages', $settings->allow_messages) }}
		<label for="allow_messages">Allow Messages</label>
<br><br>

		{{ html()->checkbox('allow_friends', $settings->allow_friends) }}
		<label for="allow_friends">Allow Friend Requests</label>
<br><br>

		{{ html()->label('Friend Code', 'friend_code') }}
		{{ html()->text('friend_code', $settings->friend_code, []) }}
<br><br>



		{{ html()->label('Username:', 'username') }}@if($errors->has('username'))<span class="alert danger">{{ $errors->get('username')[0] }}</span>@endif
		{{ html()->text('username', $user->username)->minlength(2)->maxlength(32) }}
<br><br>

		{{ html()->label('Email:', 'email') }}@if($errors->has('email'))<span class="alert danger">{{ $errors->get('email')[0] }}</span>@endif
		{{ html()->text('email', $settings->email) }}
<br><br>

		{{ html()->label('Password:', 'password') }}@if($errors->has('password'))<span class="alert danger">{{ $errors->get('password')[0] }}</span>@endif
		{{ html()->password('password') }}
<br><br>
		{{ html()->label('Confirm Password:', 'password_confirmation') }}@if($errors->has('password_confirmation'))<span class="alert danger">{{ $errors->get('password_confirmation')[0] }}</span>@endif
		{{ html()->password('password_confirmation') }}
<br><br>

		{{ html()->label('Birthday:', 'birthday') }} <small>You must be {{ Config::get('site_settings.age_required') }} years of age or older to register.</small> @if($errors->has('birthday'))<span class="alert danger">{{ $errors->get('birthday')[0] }}</span>@endif
		{{ html()->date('birthday', $settings->birthday)->disabled() }}
<br><br>

		{{ html()->label('Timezone:', 'timezone') }}@if($errors->has('timezone'))<span class="alert danger">{{ $errors->get('timezone')[0] }}</span>@endif
		{{ html()->text('timezone', $settings->timezone) }}
<br><br>





		{{ html()->submit('Update Settings')->class('btn') }}
	{{ html()->form()->close() }}
@endsection