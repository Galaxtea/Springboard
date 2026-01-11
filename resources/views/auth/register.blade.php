@extends('layouts.main')
@section('title') Register Account @endsection
@section('content')
	<div>
		{{ html()->form('POST', '/register')->open() }}
		<!-- Check configs here for registration settings -->
		@if(!$open_reg)
			{{ html()->label('Registration Code:', 'reg_code') }}@if($errors->has('reg_code'))<span class="alert danger">{{ $errors->get('reg_code')[0] }}</span>@endif
			{{ html()->text('reg_code') }}
		@endif


		<!--  -->
		{{ html()->label('Username:', 'username') }}@if($errors->has('username'))<span class="alert danger">{{ $errors->get('username')[0] }}</span>@endif
		{{ html()->text('username')->minlength(2)->maxlength(32) }}

		{{ html()->label('Email:', 'email') }}@if($errors->has('email'))<span class="alert danger">{{ $errors->get('email')[0] }}</span>@endif
		{{ html()->text('email') }}

		{{ html()->label('Password:', 'password') }}@if($errors->has('password'))<span class="alert danger">{{ $errors->get('password')[0] }}</span>@endif
		{{ html()->password('password') }}
		{{ html()->label('Confirm Password:', 'password_confirmation') }}@if($errors->has('password_confirmation'))<span class="alert danger">{{ $errors->get('password_confirmation')[0] }}</span>@endif
		{{ html()->password('password_confirmation') }}

		{{ html()->label('Birthday:', 'birthday') }} <small>You must be {{ Config::get('site_settings.age_required') }} years of age or older to register.</small> @if($errors->has('birthday'))<span class="alert danger">{{ $errors->get('birthday')[0] }}</span>@endif
		{{ html()->date('birthday') }}

		{{ html()->label('Referrer:', 'referrer') }}@if($errors->has('referrer'))<span class="alert danger">{{ $errors->get('referrer')[0] }}</span>@endif
		{{ html()->text('referrer') }}

		{{ html()->checkbox('tos') }}
		<label for="tos">I definitely agree :)</label>@if($errors->has('tos'))<span class="alert danger">{{ $errors->get('tos')[0] }}</span>@endif

		{{ html()->checkbox('privacy') }}
		<label for="privacy">Mmhhm agree to this too fo sho</label>@if($errors->has('privacy'))<span class="alert danger">{{ $errors->get('privacy')[0] }}</span>@endif

		{{ html()->submit('Register an account') }}
		{{ html()->form()->close() }}
	</div>
@endsection('content')