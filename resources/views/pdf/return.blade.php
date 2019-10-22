@php
    $checks = $transmittal->checks()->where('status_id', '<>', 2/*!transmitted*/)->orderBy('number')->get();

    $checks->map( function($check) {
        $claimed = $check->history->first( function($h) {
            return $h->action_id === 4 && $h->active === 1;
        });
        $check->claimed = $claimed ? $claimed->date : null;
        return $check;
    });
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @page { margin: 10 15 10 15; }
        .my_table{
            width: 100%;
            max-width: 100%;
            background-color: transparent;
            color: black;
            margin: 10px 0 10px 0;
            font-size: 13px;
        }
        .my_table td{
            padding: 0.4rem;
        }
        /*.my_table td:nth-child(3) {
            width: 17%;
            text-align: right;
        }*/
        .my_table .legend {
            color: grey;
            font-size: .8rem;
        }
        #footer {
            position: fixed;
            right: 0px;
            bottom: 10px;
            text-align: right;
            font-size: 10px;
            color: grey;
        }
        #footer .page:after { content: counter(page, decimal); }
        .box {
            font-style: italic;
            font-size: 9px;
            padding: 0;
            margin: 0;
        }
        .box:before {
            height: 12px;
            width: 20px;
            position: absolute;
            border: 1px solid grey;
            transform: translateX(-30px);
        }
    </style>
</head>
<body>
    <h4 class="text-center text-primary">{{ $transmittal->company->name }}</h4>
    <h5 class="text-center">Return Check Transmittal</h5>
    <div id="footer">
        <p class="page">{{ $transmittal->ref }} Page </p>
    </div>

    {{-- <hr style="margin: 5"> --}}
    <table class="my_table">
        <tr>
            <td>
                <span class="legend">Transmittal Reference:</span>
                <span>{{ $transmittal->ref }}</span>
            </td>
            <td style="width: 29%;">
                <span class="legend">Total No. of Checks Received:</span>
                <span>{{ $checks->count() }} pc/s</span>
            </td>
        </tr>

        <tr>
            <td>
                <span class="legend">Transmitted to:</span>
                <span>Disbursement Group</span>
            </td>
            <td>
                <span class="legend">Total No. of Checks Claimed:</span>
                <span>{{ $checks->where('claimed', '<>', null)->count() }} pc/s</span>
            </td>
        </tr>

        <tr>
            <td>
                <span class="legend">Date Due For Return:</span>
                <span>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $transmittal->due)->format('M d, Y') }}</span>
            </td>
            <td>
                <span class="legend">Total No. of Checks for Return:</span>
                <span>{{ $checks->where('claimed', null)->count() }} pc/s</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="legend">Date transmitted to HO:</span>
                <span>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $transmittal->returned)->format('M d, Y') }}</span>
            </td>
            <td>
                <span class="legend">Total Amount:</span>
                <span>Php {{ number_format($checks->where('claimed', null)->sum('amount'), 2, '.', ',') }}</span>
            </td>
        </tr>
    </table>

    <table class="table table-bordered table-condensed table-striped">
        <thead>
            <tr class="text-center success" style="font-size: 12px; font-weight: 200;">
                <td style="vertical-align: middle; width: 4%;">#</td>
                <td style="vertical-align: middle; width: 8%;">Date</td>
                <td style="vertical-align: middle; width: 8%;">Check #</td>
                <td style="vertical-align: middle; width: 30%;">Payee Name</td>
                <td style="vertical-align: middle; width: 33%;">Details</td>
                <td style="vertical-align: middle; width: 9%;">Amount</td>
                <td style="vertical-align: middle; width: 8%;">Claimed</td>
            </tr>
        </thead>
        <tbody style="font-size: 10px">
            @foreach ($checks as $check)
                <tr class="text-center {{ $check->claimed ? 'warning' : ''}}">
                    <td style="vertical-align: middle;">{{ $loop->index + 1 }}</td>
                    <td style="vertical-align: middle;">{{ \Carbon\Carbon::createFromFormat('Y-m-d', $check->date)->format('m/d/Y') }}</td>
                    <td style="vertical-align: middle;">{{ $check->number }}</td>
                    <td style="vertical-align: middle;">{{ $check->payee->name }}</td>
                    <td style="vertical-align: middle;">{{ $check->details }}</td>
                    <td style="vertical-align: middle;">{{ number_format($check->amount, 2, '.', ',') }}</td>
                    <td style="vertical-align: middle;">
                        {{ $check->claimed ? \Carbon\Carbon::createFromFormat('Y-m-d', $check->claimed)->format('m/d/Y') : '' }}
                    </td>
                </tr>
            @endforeach

            @if ( $checks->count() < 20 )
                @for ($i = $checks->count(); $i < 20; $i++)
                    <tr>
                        <td style="padding: 12px;"> </td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endfor
            @endif

            <tr style="font-weight: bold">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-right">Grand Total</td>
                <td class="text-center">{{ number_format($checks->where('claimed', null)->sum('amount'), 2, '.', ',') }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <table class="my_table">
        <tr>
            <td>
                <span class="legend">Prepared by:</span>
                <span>{{ $transmittal->returnedBy->name }}</span>
            </td>
            <td style="font-size: 10px; font-weight: bold; text-align: right; padding-bottom: 30px; width: 46%">I hereby certify that the checks I received were accounted and complete.</td>
        </tr>

        <tr>
            <td></td>
            <td style="border-top: 1px solid grey; font-size: 11px; text-align: center;">
                SIGNATURE OVER PRINTED NAME
            </td>
        </tr>

        <tr>
            <td></td>
            <td style="padding-top: 20px;">
                <p class="box">Attached with the TRANSMITTAL REPORT</p>
                <p class="box">Attached with Returned Checks</p>
                <p class="box">Unclaimed checks tallies with the unsigned portion of the TRANSMITTAL REPORT</p>
            </td>
        </tr>
    </table>
</body>
</html>
