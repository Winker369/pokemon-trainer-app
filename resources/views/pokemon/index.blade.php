@extends('layouts.app')

<!-- Scripts -->
@vite(['resources/js/pages/pokemon.js'])

@section('content')
<pokemon :resource_list='@json($resourceList)'/>
@endsection
