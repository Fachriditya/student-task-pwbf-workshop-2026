@extends('layouts.app')

@section('content')
<h3>Dashboard</h3>
<p>Selamat datang, {{ Auth::user()->name }}</p>
@endsection