<div class="navbar">
	@if($user?->perms('can_panel'))
		<li><a href="/panel">Admin Panel</a></li>
	@else
		<li><a href="/">Home</a></li>
	@endif
	<li><a href="/forums">Forums</a></li>
	@if($user)
		<li><a href="/adopts">Adopts</a></li>
		<li class="logo"><a href="/"><img src="/images/layout/logo.png"></a></li>
		<li><a href="/">Inventory</a></li>
		<li><a href="/">Explore</a></li>
		<li><a>
			{{ html()->form('POST', '/logout')->open() }}
			{{ html()->submit('Log Out') }}
			{{ html()->form()->close() }}
		</a></li>
	@else
		<li class="logo"><a href="/"><img src="/images/layout/logo.png"></a></li>
		<li><a href="/login">Login</a></li>
		<li><a href="/register">Register</a></li>
	@endif
</div>