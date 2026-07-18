@extends('layouts.main')
@section('title') Login @endsection
@section('content')
	@if($errors->has('login'))
		<div class="alert danger">{{ $errors->get('login')[0] }}</div>
	@endif

	{{ html()->form('POST', '/login')->open() }}

	{{ html()->label('Email:', 'email') }}@if($errors->has('email'))<span class="alert danger">{{ $errors->get('email')[0] }}</span>@endif
	{{ html()->text('email') }}

	{{ html()->label('Password:', 'password') }}@if($errors->has('password'))<span class="alert danger">{{ $errors->get('password')[0] }}</span>@endif
	{{ html()->password('password') }}

	{{ html()->checkbox('remember') }}
	<label for="remember">Remember me</label>

	{{ html()->submit('Log In') }}
	{{ html()->form()->close() }}



	<br><br><br><a href="/forgot-password">Forgot Password?</a>
@endsection