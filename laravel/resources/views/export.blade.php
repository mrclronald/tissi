@extends('layouts.master')

@section('title', 'Export')

@section('content')
<form action="/export" method="post">
    <div class="form-group">
        <label for="filename"></label>
        <select name="filename" id="filename">
            @foreach ($directories as $directory)
                <option value="{{$directory->getFileName()}}">{{$directory->getFileName()}}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="template"></label>
        <select name="template" id="template">
            @foreach($templates as $value => $template)
                <option value="{{$value}}">{{$template}}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Export</button>
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
</form>
@stop