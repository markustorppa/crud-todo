<!doctype html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <meta charset="utf-8">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ env('APP_NAME') }} | {{ $title }}</title>

  <meta name="description" content="TODO">

  <link href="{{ asset('css/main.css') }}" rel="stylesheet" type="text/css" media="all" />

</head>

<body>
