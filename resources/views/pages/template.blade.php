@extends('layouts.templates.contained')

@section('pageTitle', $title)

@section('content')

    <h2>{{ $title }}</h2>

    {!! $content !!}

@endsection
