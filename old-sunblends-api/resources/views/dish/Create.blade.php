@extends('dish.layout')
@section('content')
 
<div class="card">
  <div class="card-header">Dish Page</div>
  <div class="card-body">
      
      <form action="{{ url('dish') }}" method="post">
        {!! csrf_field() !!}
        <label>Dish Name</label></br>
        <input type="text" name="Dish" id="dish" class="form-control"></br>
        <label>Category</label></br>
        <input type="text" name="Category" id="category" class="form-control"></br>
        <label>Calories</label></br>
        <input type="text" name="Calories" id="calories" class="form-control"></br>
        <label>Price</label></br>
        <input type="text" name="Price" id="price" class="form-control"></br>
        <input type="submit" value="Save" class="btn btn-success"></br>
    </form>
   
  </div>
</div>
 
@stop