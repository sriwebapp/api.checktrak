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
    </style>
</head>
<body>
    <h4 class="text-center text-primary">{{ $transmittal->company->name }}</h4>
    <h5 class="text-center">Transmittal Report</h5>
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
            <td style="width: 27%;">
                <span class="legend">Total No. of Checks:</span>
                <span>{{ $transmittal->checks->count() }} pc/s</span>
            </td>
        </tr>

        <tr>
            <td>
                <span class="legend">Transmitted to:</span>
                <span>{{ $transmittal->inchargeUser->name }}</span>
            </td>
            <td>
                <span class="legend">Total Amount:</span>
                <span>Php {{ number_format($transmittal->checks->sum('amount'), 2, '.', ',') }}</span>
            </td>
        </tr>

        <tr>
            <td>
                <span class="legend">Date Transmitted:</span>
                <span>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $transmittal->date)->format('M d, Y') }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="legend">Date due for return:</span>
                <span>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $transmittal->due)->format('M d, Y') }}</span>
            </td>
        </tr>
    </table>

    <div style="font-size: 10px; margin-left: 50px; font-style: italic;">
        <p style="margin: 0">Please attach this report to the <span style="font-weight: bold;">RETURN CHECK TRANSMITTAL</span>, to be returned to HO with the unclaimed checks.</p>
        <p>Note that unclaimed checks should tally to the unsigned portion of this transmittal.</p>
    </div>

    <table class="table table-bordered table-condensed table-striped">
        <thead>
            <tr class="text-center info" style="font-size: 12px; font-weight: 200;">
                <td style="vertical-align: middle; width: 4%;">#</td>
                <td style="vertical-align: middle; width: 8%;">Date</td>
                <td style="vertical-align: middle; width: 8%;">Check #</td>
                <td style="vertical-align: middle; width: 22%;">Payee Name</td>
                <td style="vertical-align: middle; width: 29%;">Details</td>
                <td style="vertical-align: middle; width: 9%;">Amount</td>
                <td style="vertical-align: middle; width: 12%; padding: 0;">
                    <div>Received By</div>
                    <div style="font-size: 6px; font-weight: normal; font-style: italic;">Signature over printed name</div>
                    <div style="font-size: 6px; font-weight: normal; font-style: italic;">( Please write name legibly )</div>
                </td>
                <td style="vertical-align: middle; width: 8%;">Date Received</td>
            </tr>
        </thead>
        <tbody style="font-size: 10px">
            @foreach ($transmittal->checks()->orderBy('number')->get() as $check)
                <tr class="text-center">
                    <td style="vertical-align: middle;">{{ $loop->index + 1 }}</td>
                    <td style="vertical-align: middle;">{{ \Carbon\Carbon::createFromFormat('Y-m-d', $check->date)->format('m/d/Y') }}</td>
                    <td style="vertical-align: middle;">{{ $check->number }}</td>
                    <td style="vertical-align: middle;">{{ $check->payee->name }}</td>
                    <td style="vertical-align: middle;">{{ $check->details }}</td>
                    <td style="vertical-align: middle;">{{ number_format($check->amount, 2, '.', ',') }}</td>
                    <td style="padding: 20px"></td>
                    <td></td>
                </tr>
            @endforeach

            @if ( $transmittal->checks->count() < 14 )
                @for ($i = $transmittal->checks->count(); $i < 14; $i++)
                    <tr class="text-center">
                        <td style="padding: 20px"></td>
                        <td></td>
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
                <td class="text-center">{{ number_format($transmittal->checks->sum('amount'), 2, '.', ',') }}</td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <table class="my_table">
        <tr>
            <td>
                <span class="legend">Prepared by:</span>
                <span>{{ $transmittal->user->name }}</span>
            </td>
            <td style="font-size: 10px; font-weight: bold; text-align: right; padding-bottom: 30px; width: 46%">I hereby certify that the checks I received were accounted and complete.</td>
        </tr>

        <tr>
            <td></td>
            <td style="border-top: 1px solid grey; font-size: 11px; text-align: center;">
                SIGNATURE OVER PRINTED NAME
            </td>
        </tr>
    </table>
</body>
</html>
