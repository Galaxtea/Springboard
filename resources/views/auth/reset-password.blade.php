@extends('layouts.main')
@section('title') Reset Password @endsection
@section('content')
	{{ html()->form('POST', '/reset-password')->open() }}
		{{ html()->hidden('token', request()->route('token')) }}

		{{ html()->label('Email:', 'email') }}@if($errors->has('email'))<span class="alert danger">{{ $errors->get('email')[0] }}</span>@endif
		{{ html()->email('email') }}

		{{ html()->label('Password:', 'password') }}@if($errors->has('password'))<span class="alert danger">{{ $errors->get('password')[0] }}</span>@endif
		{{ html()->password('password') }}
		{{ html()->label('Confirm Password:', 'password_confirmation') }}@if($errors->has('password_confirmation'))<span class="alert danger">{{ $errors->get('password_confirmation')[0] }}</span>@endif
		{{ html()->password('password_confirmation') }}

	{{ html()->submit('Reset Password') }}
	{{ html()->form()->close() }}
@endsection