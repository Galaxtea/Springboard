@extends('layouts.main')
@section('title') Forgot Password @endsection
@section('content')
	{{ html()->form('POST', '/forgot-password')->open() }}
	{{ html()->label('Email:', 'email') }}
	{{ html()->email('email') }}
	{{ html()->submit('Reset Password') }}
	{{ html()->form()->close() }}
@endsection