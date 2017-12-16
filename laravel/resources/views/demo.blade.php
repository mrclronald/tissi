<table>
    <tr>
    <td valign="middle" rowspan="3">Big title</td>

    <td valign="top">Bold cell</td>

    <td valign="top">Bold cell</td>

    <td valign="top">Italic cell</td>

    <td valign="top">Cell with width of 100</td>

    <td valign="top">Cell with height of 100</td>
</tr>

@foreach ($data[0] as $row)
<tr>
    @foreach ($row as $column)
        <td>{{$column}}</td>
    @endforeach
</tr>
@endforeach
</table>