<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill-better-table@1.2.10/dist/quill-better-table.css">
    <title></title>
    <style>
        {{--@font-face {--}}
        {{--    font-family: 'IskoolaPota';--}}
        {{--    src: url('{{ public_path('fonts/Iskoola Pota Regular.ttf') }}') format('truetype');--}}
        {{--    font-weight: normal;--}}
        {{--    font-style: normal;--}}
        {{--}--}}

        body {
            font-family: sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .header, .footer {
            text-align: center;
            margin-bottom: 20px;
        }
        .header-img, .footer-img {
            width: 100%;
            max-width: 800px;
            height: auto;
        }
        .header h1, .header p {
            margin: 5px 0;
            color: #343a40;
        }
        .content p {
            margin: 10px 0;
            /*text-align: justify;*/
            line-height: 1.6;
            color: #495057;
        }
        .buttons {
            text-align: center;
        }
        .buttons button {
            margin: 10px;
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .buttons button:hover {
            background-color: #0056b3;
        }
        @media print {
            body {
                margin: 0;
            }
            .container {
                width: 100%;
                border: none;
                box-shadow: none;
            }
            .buttons {
                display: none;
            }
        }
        @page {
            size: A4;
            margin: 20mm;
        }
    </style>
    <style>
        .content {
            color: black !important;
            font-size: 16px;
        }

        .content * {
            color: black !important;
            font-size: 16px;
        }
    </style>
</head>
<body>
{{--<div class="buttons">--}}
{{--    <button id="downloadPdf">Download Agreement</button>--}}
{{--</div>--}}
<div class="container" id="content">
    <div class="header">
        @if ($header_image)
            <img src="{{ $header_image }}" class="header-img">
        @endif
    </div>
    <div class="content">
        {!! $loan_number_txt !!}
{{--        {{ $loan_number_txt }}--}}
    </div>

    <div class="footer">
        @if ($footer_image)
            <img src="{{ $footer_image }}" alt="Company Footer" class="footer-img">
        @endif
    </div>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.3.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
    document.getElementById('downloadPdf').addEventListener('click', function() {
        const { jsPDF } = window.jspdf;

        const content = document.getElementById('content');
        const buttons = document.querySelector('.buttons');

        // Hide buttons before generating PDF
        buttons.style.display = 'none';

        html2canvas(content).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const pdf = new jsPDF('p', 'mm', 'a4');
            const imgWidth = 210; // A4 width in mm
            const imgHeight = (canvas.height * imgWidth) / canvas.width;
            pdf.addImage(imgData, 'PNG', 0, 0, imgWidth, imgHeight);
            pdf.save('agreement.pdf');

            // Show buttons again after generating PDF
            buttons.style.display = 'block';
        });
    });
</script>
</body>
</html>

