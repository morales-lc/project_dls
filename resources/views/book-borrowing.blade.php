<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Borrowing & Returning</title>
    <style>
        body {
            font-family: 'Segoe UI', Roboto, Arial, sans-serif;
            background: #f9f9fb;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .header-text {
            font-size: 2.2rem;
            font-weight: 700;
            text-align: center;
            color: #e83e8c;
            margin: 40px auto 20px;
            /* reduced top & bottom spacing */
            letter-spacing: 1px;
            animation: fadeInDown 0.8s ease-in-out;
        }

        /* Divider */
        .divider {
            width: 1200px;
            height: 5px;
            background: #e83e8c;
            border-radius: 3px;
            margin: 0 auto 40px;
            /* sits closer to header, adds bottom spacing */
            animation: growWidth 1s ease-in-out;
        }

        /* Section Titles */
        .section-title {
            color: #e83e8c;
            font-weight: 700;
            font-size: 1.6rem;
            margin: 2rem 0 1rem;
            position: relative;
        }

        .section-title::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: -6px;
            width: 160px;
            height: 3px;
            background: #e83e8c;
            border-radius: 2px;
        }

        /* Instructions */
        .instructions {
            font-size: 1.1rem;
            line-height: 1.7;
            background: #fff;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .instructions:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }

        .instructions ol {
            margin-left: 1.5rem;
            padding-left: 0.5rem;
        }

        .instructions li {
            margin-bottom: 1rem;
        }

        .instructions a {
            color: #e83e8c;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .instructions a:hover {
            color: #c21765;
            text-decoration: underline;
        }

        sub {
            font-size: 0.85rem;
            color: #666;
        }

        /* Animations */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes growWidth {
            from {
                width: 0;
            }

            to {
                width: 80px;
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-text {
                font-size: 1.8rem;
                padding: 0 1rem;
            }

            .instructions {
                padding: 1.5rem;
            }

            .section-title {
                font-size: 1.3rem;
            }
        }
    </style>
</head>

<body>
    @include('navbar')


    <div class="header-text">Online Borrowing & Returning</div>

    <div class="container py-5">
        <div class="divider"></div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="instructions">
                    <div class="section-title">Borrowing</div>
                    <ol>
                        <li>Log in using your <strong>@lccdo.edu.ph</strong> email.</li>
                        <li>Search the catalog you want to borrow:
                            <a href="{{ route('dashboard') }}" target="_blank">Search Here</a>
                        </li>
                        <li>
                            Select your desired catalog/book and press <em>“Request Borrow”</em> button.
                            This will redirect you to the LiRA Web Form with fields pre-filled.<br>
                            <sub>The library staff will endeavor to process your request within 3–5 working days.</sub>
                        </li>
                        <li>You will be notified when the material(s) can be picked up through your contact details provided in LiRA.</li>
                        <li>
                            When notified, proceed to the designated area indicated by LiRA.
                            Sign the two book receipts and drop one copy in the designated Drop Box.
                        </li>
                    </ol>

                    <div class="section-title mt-4">Returning</div>
                    <ol>
                        <li>Return materials on the specified due date indicated in the book receipt.</li>
                        <li>
                            Drop the returned material(s) in the Return Drop Box located outside
                            the Learning Commons entrance or at the Guard House.
                            Notices will be posted at the Guard House for instructions.
                        </li>
                        <li>You will receive an email confirmation once the returned material(s) are recorded.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="divider"></div>

    @include('footer')
</body>

</html>