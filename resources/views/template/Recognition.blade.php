<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Recognition Certificate</title>

    <style>
        @font-face {
            font-family: 'Great Vibes';
            src: url("{{ realpath(storage_path('fonts/GreatVibes-Regular.ttf')) }}") format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        /* Red Hat Display for Description */
        @font-face {
            font-family: 'Red Hat Display';
            src: url("{{ realpath(storage_path('fonts/RedHatDisplay-Regular.ttf')) }}") format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @page {
            margin: 0;  /* crucial for zero margins */
        }

        body {
            margin: 0;
            padding: 0;
        }

        .certificate {
            position: relative;
            width: 2000px;   /* match your certificate resolution */
            height: 1414px; /* exact */
            background: url("{{ public_path('certificates/Recognition.png') }}") no-repeat center center;
            background-size: contain;
        }

        .name {
            position: absolute;
            top: 520px;  /* roughly where 45% was visually */
            left: 50%;
            transform: translateX(-50%);
            font-family: 'Great Vibes', cursive;
            font-size: 120px; /* exactly your Canva size */
            color: #537235;
            text-align: center;
            white-space: nowrap;
        }

        .body-font {
            font-size: 32px; /* exactly as you wrote */
        }

        .description {
            position: absolute;
            top: 700px;  /* roughly where 50% was visually */
            left: 50%;
            transform: translateX(-50%);
            font-family: 'Red Hat Display', sans-serif;
            color: #1b2413;
            text-align: center;
            width: 75%;   /* your width */
            line-height: 1.1;
            white-space: normal;
        }

        .date {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="certificate">
    <div class="name">{{ $awardeeName }}</div>
    <div class="description body-font">
        <p>{{ $achievement }}</p>
        <p class="date body-font">Award Issued on {{ $issuedDate }}</p>
    </div>
</div>
</body>
</html>
