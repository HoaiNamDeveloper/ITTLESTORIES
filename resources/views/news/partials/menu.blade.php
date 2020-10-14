<!-- Menu -->
<header class="clearfix">
	<nav id="main-menu" class="left navigation">
		<ul class="sf-menu no-bullet inline-list m0">
			<li><a href="">Trang Chủ</a></li>
			@foreach ($categories as $cate)
				<li>
					<a href="the-loai/{{ $cate->slug }}">{{ $cate->name }}</a>
	    		</li>
			@endforeach
    		<li><a href="news/create">Đăng</a></li>
		</ul>
	</nav>

	<div class="search-bar right clearfix">
		<form action="{{route('search')}}" method="get">
			<input name="key" type="text" data-value="Tìm kiếm">
			<input type="submit" value="">
		</form>
	</div>
</header>
