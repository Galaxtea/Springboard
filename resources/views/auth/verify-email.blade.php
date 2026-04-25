@extends('layouts.main')
@section('title') Verify Email @endsection
@section('content')
	Please verify your email before continuing.
	{{ html()->form('POST', '/email/send-verify')->open() }}
	{{ html()->submit('Resend Verification Email') }}
	{{ html()->form()->close() }}
@endsection