<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certificate of Completion</title>
    <style>
        @page {
            margin: 0px;
        }
        body {
            margin: 0px;
            font-family: 'Helvetica', 'Arial', sans-serif;
            background-color: #ffffff;
            color: #333333;
        }
        .container {
            width: 100%;
            height: 100%;
            padding: 50px;
            box-sizing: border-box;
            background-image: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);
            border: 20px solid #10b981;
            position: relative;
        }
        .header {
            text-align: center;
            margin-top: 40px;
        }
        .header h1 {
            font-size: 50px;
            color: #065f46;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 5px;
        }
        .header h2 {
            font-size: 24px;
            color: #10b981;
            margin-top: 10px;
            font-weight: normal;
        }
        .content {
            text-align: center;
            margin-top: 60px;
        }
        .content p {
            font-size: 20px;
            color: #4b5563;
        }
        .student-name {
            font-size: 40px;
            font-weight: bold;
            color: #111827;
            margin: 20px 0;
            border-bottom: 2px solid #10b981;
            display: inline-block;
            padding-bottom: 5px;
        }
        .program-name {
            font-size: 28px;
            font-weight: bold;
            color: #047857;
            margin: 20px 0;
        }
        .footer {
            margin-top: 80px;
            text-align: center;
        }
        .signature-line {
            width: 300px;
            border-bottom: 1px solid #111827;
            margin: 0 auto;
            margin-bottom: 10px;
        }
        .org-name {
            font-size: 18px;
            font-weight: bold;
            color: #374151;
        }
        .qr-code {
            position: absolute;
            bottom: 50px;
            right: 50px;
            text-align: center;
        }
        .qr-code img {
            width: 120px;
            height: 120px;
        }
        .qr-text {
            font-size: 10px;
            color: #6b7280;
            margin-top: 5px;
        }
        .certificate-no {
            position: absolute;
            bottom: 50px;
            left: 50px;
            font-size: 14px;
            color: #6b7280;
        }
        .date {
            position: absolute;
            top: 50px;
            right: 50px;
            font-size: 16px;
            color: #4b5563;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="date">
            Date: {{ \Carbon\Carbon::parse($certificate->issue_date)->format('F d, Y') }}
        </div>
        
        <div class="header">
            <h1>Certificate of Completion</h1>
            <h2>SkillConnect Excellence Award</h2>
        </div>

        <div class="content">
            <p>This is to certify that</p>
            <div class="student-name">{{ $enrollment->student->user->name }}</div>
            <p>has successfully completed the training program</p>
            <div class="program-name">{{ $program->name }}</div>
            <p>conducted by <strong>{{ $program->organization->name }}</strong>.</p>
            <p style="font-size: 16px; margin-top: 30px;">
                Demonstrating outstanding dedication and skill acquisition in the field of {{ $program->category->name ?? 'Professional Development' }}.
            </p>
        </div>

        <div class="footer">
            <div style="display: inline-block; text-align: center;">
                <div class="signature-line"></div>
                <div class="org-name">{{ $program->organization->name }}</div>
                <div style="font-size: 14px; color: #6b7280;">Authorized Signatory</div>
            </div>
        </div>

        <div class="certificate-no">
            Certificate No: <strong>{{ $certificate->verification_code }}</strong><br>
            Verify at: {{ url('/verify/certificate/'.$certificate->verification_code) }}
        </div>

        <div class="qr-code">
            <img src="data:image/svg+xml;base64,{{ base64_encode($qrCode) }}" alt="QR Code">
            <div class="qr-text">Scan to Verify</div>
        </div>
    </div>
</body>
</html>
