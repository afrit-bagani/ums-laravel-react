<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Receipt - {{ $student->registration_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            color: #000;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table {
            margin-bottom: 20px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
        }
        .header-logo {
            color: #c92a2a;
            font-size: 28px;
            font-weight: bold;
            vertical-align: middle;
        }
        .header-title-small {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            padding-left: 10px;
            border-left: 2px solid #999;
            vertical-align: middle;
        }
        .header-center {
            text-align: center;
            vertical-align: middle;
        }
        .header-center h1 {
            margin: 0 0 5px 0;
            font-size: 20px;
            text-transform: uppercase;
        }
        .header-center p {
            margin: 0;
            font-size: 11px;
        }
        .receipt-title-container {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
        }
        .receipt-title {
            display: inline-block;
            border: 1px solid #000;
            padding: 5px 20px;
            font-weight: bold;
            text-transform: uppercase;
            background-color: #fff;
            z-index: 10;
            position: relative;
        }
        .title-line {
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            border-top: 1px solid #000;
            z-index: 1;
        }
        .section-box {
            border: 1px solid #000;
            padding: 20px 15px 15px 15px;
            margin-bottom: 30px;
            position: relative;
        }
        .section-title {
            position: absolute;
            top: -10px;
            left: 15px;
            background-color: #fff;
            padding: 0 5px;
            font-weight: bold;
            font-size: 13px;
        }
        .details-table td {
            padding: 5px 0;
            vertical-align: top;
        }
        .details-label {
            width: 150px;
            font-weight: bold;
        }
        .details-colon {
            width: 20px;
        }
        .uppercase {
            text-transform: uppercase;
        }
        .watermark {
            position: absolute;
            top: 50px;
            left: 50%;
            margin-left: -150px;
            width: 300px;
            text-align: center;
            font-size: 60px;
            font-weight: bold;
            color: rgba(0, 0, 0, 0.15);
            border: 4px dashed rgba(0, 0, 0, 0.15);
            padding: 10px;
            transform: rotate(-10deg);
            letter-spacing: 15px;
            z-index: 0;
        }
        .footer-sign {
            text-align: right;
            margin-top: 50px;
            margin-bottom: 20px;
        }
        .footer-sign div {
            font-weight: bold;
        }
        .footer-note {
            text-align: center;
            font-size: 11px;
            font-style: italic;
            border-top: 1px solid #ccc;
            padding-top: 10px;
            margin-top: 20px;
            color: #555;
        }
        .footer-meta {
            width: 100%;
            font-size: 10px;
            color: #777;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        
        <!-- Header -->
        <table class="header-table">
            <tr>
                <td width="30%">
                    <table>
                        <tr>
                            <td class="header-logo">7U</td>
                            <td class="header-title-small">
                                The Neotia<br>University
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="70%" class="header-center">
                    <h1>The Neotia University</h1>
                    <p>Diamond Harbour Road, Sarisha Hat, Sarisha, West Bengal - 743368, India</p>
                </td>
            </tr>
        </table>

        <!-- Receipt Title -->
        <div class="receipt-title-container">
            <div class="title-line"></div>
            <div class="receipt-title">Payment Receipt</div>
        </div>

        <!-- Student Details -->
        <div class="section-box">
            <div class="section-title">Student Details</div>
            <table class="details-table">
                <tr>
                    <td class="details-label">Receipt Date</td>
                    <td class="details-colon">:</td>
                    <td>{{ $student->payment_date ? \Carbon\Carbon::parse($student->payment_date)->format('d/m/Y') : 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="details-label">Name</td>
                    <td class="details-colon">:</td>
                    <td class="uppercase">{{ $student->full_name }}</td>
                </tr>
                <tr>
                    <td class="details-label">Registration No.</td>
                    <td class="details-colon">:</td>
                    <td>{{ $student->registration_number }}</td>
                </tr>
                <tr>
                    <td class="details-label">Course</td>
                    <td class="details-colon">:</td>
                    <td>{{ $student->course_name }}</td>
                </tr>
                <tr>
                    <td class="details-label">Contact No.</td>
                    <td class="details-colon">:</td>
                    <td>{{ $student->mobile_no }}</td>
                </tr>
                <tr>
                    <td class="details-label">Installment</td>
                    <td class="details-colon">:</td>
                    <td>{{ $student->fee_type ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="details-label">Payment Type</td>
                    <td class="details-colon">:</td>
                    <td class="uppercase">{{ $student->payment_method ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>

        <!-- Instrument Details -->
        <div class="section-box" style="min-height: 120px;">
            <div class="section-title">Instrument Details</div>
            
            <div class="watermark">P A I D</div>

            <table class="details-table" style="position: relative; z-index: 10;">
                <tr>
                    <td class="details-label">Instrument No</td>
                    <td class="details-colon">:</td>
                    <td>{{ $student->transaction_id ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="details-label">Drawn On</td>
                    <td class="details-colon">:</td>
                    <td class="uppercase">{{ $student->payment_method ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="details-label">Instrument Dt</td>
                    <td class="details-colon">:</td>
                    <td>{{ $student->payment_date ? \Carbon\Carbon::parse($student->payment_date)->format('d/m/Y') : 'N/A' }}</td>
                </tr>
            </table>
        </div>

        <!-- Footer Space -->
        <div class="footer-sign">
            <div>For THE NEOTIA UNIVERSITY</div>
        </div>

        <!-- Print Footer -->
        <div class="footer-note">
            This is computer generated Receipt and does not require signature.
        </div>
        
        <table class="footer-meta">
            <tr>
                <td align="left">HRCL</td>
                <td align="right">{{ now()->format('d/m/Y h:i A') }}</td>
            </tr>
        </table>

    </div>
</body>
</html>
