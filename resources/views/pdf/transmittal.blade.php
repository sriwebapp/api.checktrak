<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @page { margin: 10 15 0 15; }
    </style>
</head>
<body>
    <h4 class="text-center text-primary">{{ $transmittal->company->name }}</h4>
    <h5 class="text-center">Transmittal Report</h5>

    <hr style="margin: 5">

    <div style="font-size: 13px;">
        <p>
            <span class="text-muted">Transmittal Reference:</span>
            <span>{{ $transmittal->ref }}</span>
        </p>

        <p class="pull-right">
            <span class="text-muted">Total No. of Checks:</span>
            <span>{{ $transmittal->checks->count() }} pc/s</span>
        </p>

        <p>
            <span class="text-muted">Transmitted to:</span>
            <span>{{ $transmittal->inchargeUser->name }}</span>
        </p>

        <p>
            <span class="text-muted">Date Transmitted:</span>
            <span>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $transmittal->date)->format('M d, Y') }}</span>
        </p>

        <p>
            <span class="text-muted">Date due for return:</span>
            <span>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $transmittal->due)->format('M d, Y') }}</span>
        </p>
    </div>

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
                    <div style="font-size: 8px; font-weight: normal; font-style: italic;">Signature over printed name</div>
                    <div style="font-size: 8px; font-weight: normal; font-style: italic;">( Please write name legibly )</div>
                </td>
                <td style="vertical-align: middle; width: 8%;">Date Received</td>
            </tr>
        </thead>
        <tbody style="font-size: 10px">
            @foreach ($transmittal->checks as $check)
                <tr class="text-center">
                    <td>{{ $loop->index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $check->date)->format('m/d/Y') }}</td>
                    <td>{{ $check->number }}</td>
                    <td>{{ $check->payee->name }}</td>
                    <td>{{ $check->details }}</td>
                    <td>{{ number_format($check->amount, 2, '.', ',') }}</td>
                    <td></td>
                    <td></td>
                </tr>
            @endforeach

            @if ( $transmittal->checks->count() < 12 )
                @for ($i = $transmittal->checks->count(); $i < 12; $i++)
                    <tr class="text-center">
                        <td>{{ $i + 1 }}</td>
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

    <p style="font-size: 13px;" class="text-muted">Prepared by:</p>
    <div style="border-bottom: 1px solid grey; height: 15px; width: 200px;" ></div>
    <p style="font-size: 11px;">SYSTEM USER</p>

    <p style="font-size: 10px; font-weight: bold; margin-top: 20px;">I Hereby certify that the checks I received were accounted and complete.</p>
    <div style="border-bottom: 1px solid grey; height: 20px; width: 200px;" ></div>
    <p style="font-size: 11px;">SIGNATURE OVER PRINTED NAME</p>

</body>
</html>
