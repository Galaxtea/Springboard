<div class="navbar">
	<li><a href="/">Home</a></li>
	<li><a href="/forums">Forums</a></li>
	@if(Auth::check())
		<li><a href="/adopts">Adopts</a></li>
		<li class="logo"><a href="/"><img src="/images/layout/logo.png"></a></li>
		<li><a href="">Inventory</a></li>
		<li><a href="">Explore</a></li>
		<li>
			{{ html()->form('POST', '/logout')->open() }}
			{{ html()->submit('Log Out') }}
			{{ html()->form()->close() }}
		</li>
	@else
		<li class="logo"><a href="/"><img src="/images/layout/logo.png"></a></li>
		<li><a href="/login">Login</a></li>
		<li><a href="/register">Register</a></li>
	@endif
</div>