@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        @foreach($products as $product)
        <div class="col-12 col-sm-6 col-md-3">
        <div class="card" style="width: 100%">
            <img src="uploads/products/{{ $product->ProductImages()->first()->name }}" class="card-img-top" alt="..." height="250">
  <div class="card-body">
    <h5 class="card-title">{{ $product->name }}</h5>
    <p class="card-text" style="height:40px;overflow: hidden">{{ $product->description }}</p>
    <p><strong>Price: </strong> {{ $product->price }}Rs</p>
    <a href="{{ route('add.to.cart', $product->id) }}" class="btn btn-primary">Add to cart</a>
  </div>
</div>
        </div>
          @endforeach
    </div>
</div>
@endsection