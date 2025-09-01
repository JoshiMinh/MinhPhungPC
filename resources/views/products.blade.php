@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="container my-4">
    <h1 class="mb-4">Products</h1>
    <ul>
        @foreach($products as $product)
            <li>{{ $product->name }} - {{ $product->price }}</li>
        @endforeach
    </ul>
</div>
@endsection
