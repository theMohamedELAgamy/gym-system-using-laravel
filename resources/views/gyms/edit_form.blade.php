@extends('layouts.app')

@section('content')
<h1>Edit Gym </h1>
<form method='POST' enctype="multipart/form-data" action="{{ route('gyms.update', $gym->id)  }}">
    @csrf
    @method('PUT')

    <center><img src='/gyms_images/{{ $gym->cover_image_path }}' width=300 /></center>

    <label for=" fname">Name :</label><br>
    <input type="text" id="name" value='{{$gym->name}}'name="name"><br><br>
    <label for="fname">City Name :</label><br>
    <input type="text" id="city_name"  value='{{$gym->city_name}}' name="city_name"><br><br>
    <label for="exampleFormControlTextarea1" class="form-label">Gym Cover :</label><br>
    <input type="file" rows="3" id="exampleFormControlTextarea1" class="form-control" name="image" /><br><br>
    <input type="submit" value="Submit">
</form>
@endsection