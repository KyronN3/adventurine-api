<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certificate of Recognition</title>
    <style>
        @font-face {
            font-family: 'Khatija Calligraphy';
            src: url("{{ storage_path('fonts/Khatija-Calligraphy.ttf') }}") format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            background: #f0f0f0;
        }

        .certificate {
            position: relative;
            width: 1241px;
            height: 1755px;
            background-image: url("{{ public_path('certificates/recognition-cert.png') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            font-family: 'Segoe UI', sans-serif;
            color: #000;
        }

        /* Certificate elements */
        .name {
            position: absolute;
            top: 575px;
            width: 100%;
            text-align: center;
            font-family: 'Khatija Calligraphy', cursive;
            font-size: 64px;
            font-weight: bold;
        }

        .citation {
            position: absolute;
            top: 750px;
            width: 80%;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
            font-family: 'Segoe UI', sans-serif;
            font-size: 24px;
        }

        .title {
            position: absolute;
            top: 800px;
            width: 70%;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
            font-family: 'Segoe UI', sans-serif;
            font-size: 42px;
            font-weight: bold;
        }

        .description {
            position: absolute;
            top: 920px;
            width: 80%;
            max-width: 1200px;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
            font-family: 'Segoe UI', sans-serif;
            font-size: 24px;
            line-height: 1.5;
        }

        .issue-date {
            position: absolute;
            top: 1175px;
            width: 100%;
            text-align: center;
            font-family: 'Segoe UI', sans-serif;
            font-size: 24px;
        }
    </style>
</head>
<body>

<div class="certificate">
    <div class="name">{{ $name }}</div>
    <div class="citation">{{ $citation }}</div>
    <div class="title">{{ $title }}</div>
    <div class="description">{{ $description }}</div>
    <div class="issue-date">{{ $issueDate }}</div>
</div>

</body>
</html>
