@extends('layouts.master')

@section('title', 'Upload')

@section('content')
<form action="/upload" method="post" enctype="multipart/form-data">
    <label class="custom-file">
        <input type="file" name="transactions" id="file" class="custom-file-input">
        <span class="custom-file-control"></span>
    </label>
    <button type="submit" class="btn btn-primary">Upload</button>
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
</form>
@stop