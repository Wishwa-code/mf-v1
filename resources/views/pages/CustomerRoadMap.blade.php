<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asipiya Finance</title>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap");

        *,
        *::before,
        *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            --color: rgba(30, 30, 30);
            --bgColor: rgba(245, 245, 245);
            min-height: 100vh;
            display: grid;
            align-content: center;
            gap: 1rem; /* Reduce gap */
            padding: 1rem; /* Reduce padding */
            font-family: "Poppins", sans-serif;
            color: var(--color);
            background: var(--bgColor);
        }

        /* Media query for screens smaller than 600px */
        @media (max-width: 600px) {
            body {
                padding: 0.5rem; /* Further reduce padding */
                gap: 0.5rem; /* Further reduce gap */
                font-size: 0.75rem; /* Further reduce font size */
            }
        }

        h1 {
            text-align: center;
            font-size: 1.5rem; /* Reduce font size */
        }

        ul {
            --col-gap: 1rem; /* Reduce column gap */
            --row-gap: 1rem; /* Reduce row gap */
            --line-w: 0.15rem; /* Reduce line width */
            display: grid;
            grid-template-columns: var(--line-w) 1fr;
            grid-auto-columns: max-content;
            column-gap: var(--col-gap);
            list-style: none;
            width: min(60rem, 90%);
            margin-inline: auto;
        }

        /* line */
        ul::before {
            content: "";
            grid-column: 1;
            grid-row: 1 / span 20;
            background: rgb(225, 225, 225);
            border-radius: calc(var(--line-w) / 2);
        }

        /* row gaps */
        ul li:not(:last-child) {
            margin-bottom: var(--row-gap);
        }

        /* card */
        ul li {
            grid-column: 2;
            --inlineP: 1rem; /* Reduce inline padding */
            margin-inline: var(--inlineP);
            grid-row: span 2;
            display: grid;
            grid-template-rows: min-content min-content min-content;
        }

        /* date */
        ul li .date {
            --dateH: 2rem; /* Reduce date height */
            height: var(--dateH);
            margin-inline: calc(var(--inlineP) * -1);
            text-align: center;
            background-color: var(--accent-color);
            color: white;
            font-size: 1rem; /* Reduce font size */
            font-weight: 700;
            display: grid;
            place-content: center;
            position: relative;
            border-radius: calc(var(--dateH) / 2) 0 0 calc(var(--dateH) / 2);
        }

        /* date flap */
        ul li .date::before {
            content: "";
            width: var(--inlineP);
            aspect-ratio: 1;
            background: var(--accent-color);
            background-image: linear-gradient(rgba(0, 0, 0, 0.2) 100%, transparent);
            position: absolute;
            top: 100%;
            clip-path: polygon(0 0, 100% 0, 0 100%);
            right: 0;
        }

        /* circle */
        ul li .date::after {
            content: "";
            position: absolute;
            width: 1.5rem; /* Reduce circle size */
            aspect-ratio: 1;
            background: var(--bgColor);
            border: 0.2rem solid var(--accent-color); /* Reduce border size */
            border-radius: 50%;
            top: 50%;
            transform: translate(50%, -50%);
            right: calc(100% + var(--col-gap) + var(--line-w) / 2);
        }

        /* title descr */
        ul li .title,
        ul li .descr {
            background: var(--bgColor);
            position: relative;
            padding-inline: 1rem; /* Reduce padding */
        }

        ul li .title {
            overflow: hidden;
            padding-block-start: 1rem; /* Reduce padding */
            padding-block-end: 0.5rem; /* Reduce padding */
            font-weight: 500;
        }

        ul li .descr {
            padding-block-end: 1rem; /* Reduce padding */
            font-weight: 300;
        }

        /* shadows */
        ul li .title::before,
        ul li .descr::before {
            content: "";
            position: absolute;
            width: 80%; /* Reduce shadow width */
            height: 0.3rem; /* Reduce shadow height */
            background: rgba(0, 0, 0, 0.5);
            left: 50%;
            border-radius: 50%;
            filter: blur(3px); /* Reduce blur */
            transform: translate(-50%, 50%);
        }

        ul li .title::before {
            bottom: calc(100% + 0.1rem);
        }

        ul li .descr::before {
            z-index: -1;
            bottom: 0.2rem;
        }

        @media (min-width: 40rem) {
            ul {
                grid-template-columns: 1fr var(--line-w) 1fr;
            }

            ul::before {
                grid-column: 2;
            }

            ul li:nth-child(odd) {
                grid-column: 1;
            }

            ul li:nth-child(even) {
                grid-column: 3;
            }

            /* start second card */
            ul li:nth-child(2) {
                grid-row: 2 / 4;
            }

            ul li:nth-child(odd) .date::before {
                clip-path: polygon(0 0, 100% 0, 100% 100%);
                left: 0;
            }

            ul li:nth-child(odd) .date::after {
                transform: translate(-50%, -50%);
                left: calc(100% + var(--col-gap) + var(--line-w) / 2);
            }

            ul li:nth-child(odd) .date {
                border-radius: 0 calc(var(--dateH) / 2) calc(var(--dateH) / 2) 0;
            }
        }

        .credits {
            margin-top: 1rem;
            text-align: right;
        }

        .credits a {
            color: var(--color);
        }
    </style>
</head>
<body>
<h1>{{ $customer_name }}</h1>
<ul class="timeline">
    @foreach($customer_log as $item)
        @if($item->type === "Create Loan")
            <li style="--accent-color:#132c57">
                <div class="date">{{ $item->date }} ({{ $item->time }})</div>
                <div class="title">{{ $item->type }} ({{ $item->Full_Name }})</div>
                <div class="descr">{{ $item->description }}</div>
            </li>
        @elseif($item->type === "Penalty")
            <li style="--accent-color:#7a0404">
                <div class="date">{{ $item->date }} ({{ $item->time }})</div>
                <div class="title">{{ $item->type }} ({{ $item->Full_Name }})</div>
                <div class="descr">{{ $item->description }}</div>
            </li>
        @elseif($item->type === "Approve Loan")
            <li style="--accent-color:#04791a">
                <div class="date">{{ $item->date }} ({{ $item->time }})</div>
                <div class="title">{{ $item->type }} ({{ $item->Full_Name }})</div>
                <div class="descr">{{ $item->description }}</div>
            </li>
        @elseif($item->type === "Blacklist")
            <li style="--accent-color:#ff0000">
                <div class="date">{{ $item->date }} ({{ $item->time }})</div>
                <div class="title">{{ $item->type }} ({{ $item->Full_Name }})</div>
                <div class="descr">{{ $item->description }}</div>
            </li>
        @elseif($item->type === "Remove Blacklist")
            <li style="--accent-color:#46f100">
                <div class="date">{{ $item->date }} ({{ $item->time }})</div>
                <div class="title">{{ $item->type }} ({{ $item->Full_Name }})</div>
                <div class="descr">{{ $item->description }}</div>
            </li>
        @else
            <li style="--accent-color:#343030">
                <div class="date">{{ $item->date }} ({{ $item->time }})</div>
                <div class="title">{{ $item->type }} ({{ $item->Full_Name }})</div>
                <div class="descr">{{ $item->description }}</div>
            </li>
        @endif
    @endforeach
</ul>
</body>
</html>
