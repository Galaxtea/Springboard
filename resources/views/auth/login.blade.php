@extends('layouts.main')
@section('title') Login @endsection
@section('content')
	{{ html()->form('POST', '/login')->open() }}

	{{ html()->label('Username:', 'username') }}@if($errors->has('username'))<span class="alert danger">{{ $errors->get('username')[0] }}</span>@endif
	{{ html()->text('username') }}

	{{ html()->label('Password:', 'password') }}
	{{ html()->password('password') }}

	{{ html()->checkbox('remember') }}
	<label for="remember">Remember me</label>

	{{ html()->submit('Log In') }}
	{{ html()->form()->close() }}
@endsection('content')