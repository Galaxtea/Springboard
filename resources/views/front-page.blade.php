@extends('layouts.main')
@section('content')
	@foreach($news_posts as $news)
		{!! $news->display !!}
		<br><br><br>
	@endforeach
	{{ $news_posts->render() }}
@endsection