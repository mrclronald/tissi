<table>
    <tr>
        <td valign="middle" height="20" colspan="12"
            style="text-align: center; font-weight: bold; font-family: 'Times New Roman';">
            WEEKLY SUMMARY REPORT OF ANTI-OVERLOADING OPERATION
        </td>
    </tr>
    <tr>
        <td colspan="2">STATION/PLACE OF APPREHENDED:</td>
        <td colspan="3">{{$areaOfOperation}}</td>
        <td colspan="3">FOR THE MONTH OF:</td>
        <td colspan="4">{{$date}}</td>
    </tr>
    <tr>
        <td width="9" style="wrap-text:true;font-weight:bold;border:1px solid #000;" rowspan="2">DATE</td>
        <td width="30" style="wrap-text:true;font-weight:bold;border:1px solid #000;" rowspan="2">NAME OF OWNER</td>
        <td width="18.86" style="font-weight:bold;border:1px solid #000;" rowspan="2">ADDRESS</td>
        <td width="18.71" style="font-weight:bold;border:1px solid #000;" rowspan="2">TRADE NAME</td>
        <td width="15" style="font-weight:bold;border:1px solid #000;" rowspan="2">PLATE NO.</td>
        <td width="18" style="wrap-text:true;font-weight:bold;border:1px solid #000;" rowspan="2">CODE NO. / VEHICLE
            TYPE
        </td>
        <td width="15" style="wrap-text:true;font-weight:bold;border:1px solid #000;" rowspan="2">GVW AS WEIGHED</td>
        <td width="25.29" style="wrap-text:true;font-weight:bold;btext-align:center;border:1px solid #000;" colspan="3" valign="middle"
            height="32">OVERLOAD / EXCESS LOAD (Kgs)
        </td>
        <td width="25" style="wrap-text:true;font-weight:bold;border:1px solid #000;" rowspan="2">APPREHENDING OFFICER
        </td>
        <td width="25" style="font-weight:bold;border:1px solid #000;" rowspan="2">CONFISCATED ITEM</td>
    </tr>

    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td width="8.43" style="wrap-text:true;font-weight:bold;border:1px solid #000;">13,500 / AXLE</td>
        <td width="8.43" style="font-weight:bold;border:1px solid #000;">GVW</td>
        <td width="8.43" style="wrap-text:true;font-weight:bold;border:1px solid #000;">BOTH AXLE-GVW</td>
        <td></td>
        <td></td>
    </tr>

    @foreach ($data as $row)
        <tr>
            @foreach ($row as $header => $value)
                <td style="border:1px solid #000">{{$value}}</td>
            @endforeach
        </tr>
    @endforeach

    <tr></tr>
    <tr></tr>
    <tr>
        <td colspan="2">PREPARED BY:</td>
        <td colspan="2"></td>
        <td colspan="3">CHECKED & SUBMITTED BY:</td>
        <td colspan="5"></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="3" style="border-bottom: 6px solid #000"></td>
        <td></td>
        <td></td>
        <td colspan="5" style=" border-bottom: 6px solid #000"></td>
    </tr>
    <tr>
        <td colspan="1"></td>
        <td colspan="3" style="text-align:center">ATOME, TEAM LEADER</td>
        <td></td>
        <td></td>
        <td colspan="5" style="text-align:center">NCR, INSPECTOR</td>
        <td style="text-align:right">page 1</td>
    </tr>
</table>