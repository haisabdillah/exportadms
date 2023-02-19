<!DOCTYPE html>
<html>

<head>
    <title>Recapitulation Salary Total</title>
    {{-- <link rel="apple-touch-icon-precomposed" sizes="144x144"
        href="{{ asset('upload') . '/' . setting('favicon_logo') }}" />
    <link rel="apple-touch-icon-precomposed" sizes="152x152"
        href="{{ asset('upload') . '/' . setting('favicon_logo') }}" />
    <link rel="icon" type="image/png" href="{{ asset('upload') . '/' . setting('favicon_logo') }}" sizes="32x32" />
    <link rel="icon" type="image/png" href="{{ asset('upload') . '/' . setting('favicon_logo') }}" sizes="16x16" /> --}}
    <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous"> -->
    <style>
        table {
            table-layout: fixed;
        }
        table,
        th,
        td {

            border: 1px solid black;
            border-collapse: collapse;
        }
    </style>
</head>

<body>
    <div style="text-align: center; margin-bottom: 30px">
        <h3 style="margin-bottom: -12px"><b>Recapitulation Absent Overview</b></h3>
        <p style="margin-bottom: -12px">{{$department}}</p>
        {{-- <p style="margin-bottom: -12px"> Period {{ $salaryPeriod->salary_period }} -
            {{ \Carbon\Carbon::parse($salaryPeriod->salary_date_until_cut_off)->format('F') }}</p> --}}
        {{-- @if (isset($department) || isset($work_unit))
        <p style="margin-bottom: -12px">{{ isset($department) ? 'Department: '.$department : null }} / {{ isset($work_unit) ? 'Work Unit: '.$work_unit : null}}</p>
        @endif --}}
        @if (isset($date_from) && isset($date_until))
            <p style="margin-bottom: -12px">{{ $date_from }} - {{ $date_until }}</p>
        @endif
        <p style="margin-bottom: -12px">Count: {{$data->count()}}</p>

    </div>
    @foreach ($data as $employee)
    <div style="page-break-inside: avoid;">
        <table style="font-size: 13px;width:100%">
            <tr style="font-size: 16px; text-align:center">
                <td colspan="{{count($date_range[0])}}">{{$employee->first()->name ?? '-'}} ({{$employee->first()->badgenumber ?? null}})</td>
            </tr>
            @foreach ($date_range as $dateRange)
                <tr>
                    @foreach ($dateRange as $item)
                    <th>
                        {{$item->format('d/m D')}}
                    </th>
                    @endforeach
                </tr>
                <tr>
                    @foreach ($dateRange as $item)
                    <td style="width:1px">
                        {{str_replace(',','-',$employee->where('date',$item->format('Y-m-d'))->pluck('time')->implode(',')) ?? "-"}}
                    </td>
                    @endforeach
                </tr>
            @endforeach
        </table>
</div>

        <div style="margin-top: 10px"></div>
        @endforeach
            {{-- <tr>
                <th>
                    <p>Name : {{ $item->first()->name }} ({{(int)$item->first()->badgenumber}}) </p>
                </th>
            </tr>
            <tr>

            </tr>
            <tr>
                @foreach ($date_range as $dateRange)
                    @php
                        $absent = $item->where('date', '=', $dateRange->format('Y-m-d'));
                    @endphp
                    <td style="   word-wrap: break-word;s">
                        @if ($absent)
                            @foreach ($absent as $item2)
                                {{ $item2->time }}
                            @endforeach
                        @endif
                    </td>
                @endforeach
            </tr> --}}
</body>

</html>
