<div class="navbar">
	<li><a href="/panel">Admin Panel</a></li>
	<li><a href="/panel/reports">Reports</a></li>
	<li><a href="/panel/users">Users</a></li>
	<li class="logo"><a href="/"><img src="/images/layout/logo.png"></a></li>
	<li><a href="/"></a></li>
	<li><a href="/panel/settings">Site Settings</a></li>
	<li><a>
		{{ html()->form('POST', '/logout')->open() }}
		{{ html()->submit('Log Out') }}
		{{ html()->form()->close() }}
	</a></li>
</div>