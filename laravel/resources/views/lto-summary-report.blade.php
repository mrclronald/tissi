<table>
    @foreach($data as $key=>$datum)
        <tr>
            <td height="20" colspan="11" style="text-align: center; font-weight: bold;font-size: 15;">
                SUMMARY REPORT OF ANTI-OVERLOADING OPERATION
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold;">DATE</td>
            <td colspan="2"
                style="font-weight: bold; text-align:center; border-bottom: 1px solid #000;">  {{$date}}</td>
            <td colspan="3" style="font-weight: bold;">AREA OF OPERATION: {{$areaOfOperation}}</td>
            <td colspan="5" style="font-weight: bold;">TEAM /AFFILATION: {{$teamOrAffiliaion}}</td>
        </tr>
        <tr>
            <td colspan="11"></td>
        </tr>
        <tr>
            <td height="45" valign="middle"
                style="font-size: 11;border: 1px solid #000;wrap-text:true;font-weight: bold;text-align: center">
                NO.
            </td>

            <td valign="middle"
                style="font-size: 11;border: 1px solid #000;wrap-text:true;;font-weight: bold;text-align: center">MV
                PLATE NO.
            </td>

            <td valign="middle"
                style="font-size: 9;border: 1px solid #000;wrap-text:true;font-weight: bold;text-align: center">MV
                TYPE
                (Private/ For-Hire/ Gov.t' / Diplomat)
            </td>

            <td valign="middle"
                style="font-size: 9;border: 1px solid #000;wrap-text:true;font-weight: bold;text-align: center">MV
                TYPE (Bus/
                Jeepney/ Van/ Truck etc.)
            </td>

            <td valign="middle" colspan="2"
                style="font-size: 11;border: 1px solid #000;wrap-text:true;font-weight: bold;text-align: center">
                TRADE NAME
            </td>

            <td valign="middle"
                style="font-size: 11;border: 1px solid #000;wrap-text:true; font-weight: bold;text-align: center">YEAR
                MODEL
            </td>

            <td valign="middle"
                style="font-size: 11;border: 1px solid #000;wrap-text:true; font-weight: bold;text-align: center">GVW/ Axle
                Load
            </td>

            <td  valign="middle"
                style="font-size: 10;border: 1px solid #000;wrap-text:true; font-weight: bold;text-align: center">REMARKS
                (Passed or
                Failed)
            </td>

            <td  valign="middle"
                style="font-size: 9;border: 1px solid #000;wrap-text:true; font-weight: bold;text-align: center">ACTION
                TAKEN CONFISCATED
                ITEMS/ IMPOUNDED MV
            </td>
            <td valign="middle"
                style="font-size: 11;border: 1px solid #000;wrap-text:true;font-weight: bold;text-align: center">GVW /
                AXLE
            </td>
        </tr>

        @foreach ($datum as $row)
            <tr style="@if(in_array($row['NO.'], $failedRows))
                    background-color: #ff0000;
            @elseif(in_array($row['NO.'], $failedExemptedRows))
                    background-color: #00b0f0;
            @endif">
                @foreach ($row as $header => $value)
                    @if($header === 'TRADE NAME')
                        <td colspan="2" style="border: 1px solid #000;">{{$value}}</td>
                    @elseif(empty($header))

                    @else
                        <td style="border: 1px solid #000">{{$value}}</td>
                    @endif
                @endforeach
            </tr>
        @endforeach

        <tr style="border: none;">
            <td>SUMMARY:</td>
            <td></td>
            <td></td>
            <td>DATA RECORDED BY:</td>
            <td></td>
            <td></td>
            <td></td>
            <td>NOTED BY:</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr style="border: none;">
            <td colspan="11"></td>
        </tr>
        <tr>
            <td style="border:none;font-size:8;font-weight: bold;">TOTAL MV WEIGHED :</td>
            <td style="border:none;"></td>
            <td style="border:none;font-weight: bold;">{{$totalMVWeighed}}</td>
            <td style="border:none;font-weight: bold;" colspan="8"></td>
        </tr>
        <tr>
            <td style="font-size:9;font-weight: bold;">NO. OF MV PASSED :</td>
            <td></td>
            <td style="font-weight: bold;">{{$totalMVPassed}}</td>
            <td></td>
            <td colspan="3" style="border-top: 1px solid #000; font-weight: bold;">(Signature Over Printed Name/ Date)
            </td>
            <td></td>
            <td colspan="3" style="border-top: 1px solid #000; font-weight: bold;">(Signature Over Printed Name/ Date)
            </td>
        </tr>
        <tr>
            <td style="font-size:9;font-weight: bold;">NO. OF MV FAILED :</td>
            <td></td>
            <td style="font-weight: bold;">{{$totalMVFailed}}</td>
            <td></td>
            <td style="font-weight: bold;" colspan="3">Recorder</td>
            <td></td>
            <td style="font-weight: bold;" colspan="3">TEAM LEADER</td>
        </tr>
        <tr>
            <td colspan="11"></td>
        </tr>
        <tr>
            <td style="background-color: #ff0000"></td>
            <td style="font-weight: bold;">Overload</td>
            <td style="background-color: #92d050"></td>
            <td style="font-weight: bold;">Prime Commodity</td>
            <td style="background-color: #00b0f0"></td>
            <td style="font-weight: bold;">GVW Exempted 12-2/12-3</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td style="font-weight: bold;">PAGE {{$key+1}} of {{count($data)}}</td>
        </tr>
    @endforeach
</table>