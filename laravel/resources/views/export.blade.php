@extends('layouts.master')

@section('title', 'Export')

@section('content')
<form action="/export" method="post">
    <div class="form-group">
        <label for="daterange">Date Range</label>

        <input class="form-control" type="text" name="from_date" />
        <input class="form-control" type="text" name="to_date" />
    </div>
    <div class="form-group">
        <label for="filename">File</label>
        <select class="form-control" name="filename" id="filename">
            @foreach ($directories as $directory)
                <option value="{{$directory->getFileName()}}">{{$directory->getFileName()}}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="template">Template</label>
        <select class="form-control" name="template" id="template">
            @foreach($templates as $value => $template)
                <option value="{{$value}}">{{$template}}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Export</button>
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
</form>
@stop

@section('script')
<script type="text/javascript">
$(function() {
    $('input[name="from_date"]').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
          format: 'YYYY-MM-DD'
        }
    });

    $('input[name="to_date"]').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
          format: 'YYYY-MM-DD'
        }
    });
});
</script>
@stop