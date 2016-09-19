@extends('slucaj.layouts.shell')

@section('container')
	
			<!-- HEADER -->
			<h1>{{ link_to_route('slucaj.index', 'IMEIS.admin') }} <small>v 0.1.3</small></h1>

			<hr />
			
			<!-- NAVIGATION -->
				<span> {{ link_to_route('slucaj.index', 'Posljednje potvrđene registracije', null, array('class' => 'btn btn-primary')) }}</span>
				<span> {{ link_to_route('slucaj.index', 'Posljednji dodani uređaji', array('novi'=>'da'), array('class' => 'btn btn-primary')) }}</span>
				<span> {{ link_to_route('slucaj.index', 'Posljednji mijenjani uređaji', array('mijenjani'=>'da'), array('class' => 'btn btn-primary')) }}</span>
				<span> {{ link_to_route('slucaj.create', 'Dodaj novi uređaj', null, array('class' => 'btn btn-primary')) }}</span>
				<span> {{ link_to_route('logout', 'logout ', null, array('class'=>'btn btn-danger')) }} </span>
			<hr />
				
			<!-- SEARCH 
			@yield('searchForm')
					-->
					
			<!-- MAIN -->
			@yield('main')
			

			<footer>
				<hr />
				<small>NOA IMEIS ADMIN // 2014 // 33pt</small>
			</footer> 
			
@stop