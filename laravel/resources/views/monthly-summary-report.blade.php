<table>
    <tr>
        <td valign="middle" height="20" colspan="12"
            style="text-align: center; font-weight: bold; font-family: 'Times New Roman';">
            SUMMARY REPORT OF ANTI-OVERLOADING OPERATION
        </td>
    </tr>
    <tr>
        <td colspan="2">STATION/PLACE OF APPREHENDED:</td>
        <td colspan="3"></td>
        <td colspan="3">FOR THE MONTH OF:</td>
        <td colspan="4"></td>
    </tr>
    <tr>
        <td rowspan="2" width="9" style="text-align:center;font-weight:bold;border:1px solid #000;">DATE</td>
        <td rowspan="2" width="33.86" style="text-align:center;font-weight:bold;border:1px solid #000;">NAME OF
            OWNER/ADDRESS
        </td>
        <td rowspan="2" width="18.71" style="text-align:center;font-weight:bold;border:1px solid #000;">TRADE NAME</td>
        <td rowspan="2" width="15" style="text-align:center;font-weight:bold;border:1px solid #000;">PLATE NO.</td>
        <td rowspan="2" width="18" style="text-align:center;font-weight:bold;border:1px solid #000;">CODE NO. / VEHICLE
            TYPE
        </td>
        <td rowspan="2" width="15" style="text-align:center;font-size:10;font-weight:bold;border:1px solid #000;">GVW AS WEIGHED</td>
        <td width="25.29" style="text-align:center;font-size:7;border:1px solid #000;" colspan="3"
            valign="middle"
            height="32">OVERLOAD / EXCESS LOAD (Kgs)
        </td>
        <td rowspan="2" width="25" style="text-align:center;font-weight:bold;border:1px solid #000;">APPREHENDING OFFICER
        </td>
        <td rowspan="2" width="25" style="text-align:center;font-weight:bold;border:1px solid #000;">CONFISCATED ITEM</td>
    </tr>

    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td width="8.43" style="font-size:8;wrap-text:true;font-weight:bold;border:1px solid #000;">13,500 / AXLE</td>
        <td width="8.43" style="font-size:8;font-weight:bold;border:1px solid #000;">GVW</td>
        <td width="8.43" style="font-size:8;wrap-text:true;font-weight:bold;border:1px solid #000;">BOTH AXLE-GVW</td>
        <td></td>
        <td></td>
    </tr>


    @foreach ($data as $row)
        <tr>
            @foreach ($row as $header => $value)
                @if($header === 'NAME OF OWNER')
                    <td style="border: 1px solid #000;"></td>
                @elseif($header === 'ADDRESS')
                @elseif($header === 'DATE')
                    <td valign="middle"
                        style="wrap-text:true;text-align:center;font-size:9;border: 1px solid #000;">{{date('m/d/Y', strtotime($value))}}</td>
                @elseif($header === 'GVW AS WEIGHED')
                    <td width="8" valign="middle"
                        style="wrap-text:true;text-align:center;font-size:8;border: 1px solid #000;">{{$value}}</td>
                @else
                    <td style="wrap-text:true;text-align:center;font-size:9;border: 1px solid #000;">{{$value}}</td>
                @endif
            @endforeach
        </tr>
    @endforeach

    <tr></tr>
    <tr></tr>
    <tr>
        <td colspan="2">PREPARED BY:</td>
        <td></td>
        <td colspan="3">SUBMITTED BY:</td>
        <td colspan="5"></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="2" style="text-align:center;font-weight:bold">ARMANDO C. ZAMORA</td>
        <td></td>
        <td></td>
        <td colspan="5" style="text-align:center;font-weight:bold">ANTONIO B. FERNANDEZ</td>
    </tr>
    <tr>
        <td colspan="1"></td>
        <td colspan="2" style="text-align:center">ATOME, TEAM LEADER</td>
        <td></td>
        <td></td>
        <td colspan="5" style="text-align:center">CHIEF, MAINTENANCE SECTION</td>
        <td style="text-align:right">page 1</td>
    </tr>

</table>