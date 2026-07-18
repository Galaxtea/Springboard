@extends('layouts.main')
@section('title') Creating Report @endsection
@section('crumbs') {{ Breadcrumbs::render() }} @endsection
@section('content')
	<h1>Creating Report</h1>
	{{ html()->form('POST', '/report/new'.$query)->open() }}
		{{ html()->label('Report Type:', 'main_cat') }}
		{{ html()->select('main_cat', $categories['main_cats'], $main_cat) }}
		{{ html()->select('sub_cat', $categories['sub_cats']) }}
		<br>
		{{ html()->label('Report Title:', 'title') }} {{ html()->text('title') }}
		<br>
		{{ html()->label('Report Description:', 'content') }}<br>
		{{ html()->textarea('content') }}
		<br>
		{{ html()->checkbox('block') }} {{ html()->label('Block User', 'block') }}
		<br>
		{{ html()->submit('File Report') }}
	{{ html()->form()->close() }}
@endsection
@push('scripts')
	<script type="module">
		let sub_array = {!! json_encode($categories['main_subs']) !!};

		$('#main_cat').on('change', function() {
			let category = $('#main_cat').val();
			let subs = sub_array[category];

			$('#sub_cat option').css('display', 'none');
			subs.forEach(function(value) {
				$('#sub_cat option[value="'+value+'"]').css('display', 'unset');
			});
			$('#sub_cat').val(subs[0]);
		}).trigger('change');
	</script>
@endpush