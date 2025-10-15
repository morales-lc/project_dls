<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Netzone</title>
    <style>
        .header-banner {
            position: relative;
            width: 100%;
            height: 300px;
            background: url('{{ asset("images/IMG_1462.JPG") }}') center center/cover no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .header-banner::after {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
        }

        .header-text {
            position: relative;
            z-index: 2;
            font-size: 2.2rem;
            font-weight: bold;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.6);
        }

        .divider {
            width: 100%;
            height: 4px;
            background: #e83e8c;
            border-radius: 2px;
            margin: 2rem 0;
        }

        .netzone-images {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin: 2rem 0;
        }

        .netzone-images img {
            width: 420px;
            max-width: 100%;
            height: 220px;
            object-fit: cover;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.10);
        }

        .netzone-title {
            color: #e83e8c;
            font-size: 1.7rem;
            font-weight: bold;
            text-align: center;
            margin: 2rem 0 1rem;
        }

        .netzone-desc {
            text-align: center;
            font-size: 1.15rem;
            color: #333;
            margin-bottom: 2rem;
        }

        .reminders-title {
            font-size: 1.2rem;
            font-weight: bold;
            text-align: center;
            margin: 2rem 0 1rem;
        }

        .reminders-list {
            max-width: 700px;
            margin: 0 auto 2rem;
            font-size: 1.08rem;
            color: #333;
        }

        .reminders-list li {
            margin-bottom: 1rem;
        }

        .reminders-list .red {
            color: #e83e8c;
            font-weight: bold;
        }

        .reminders-list .orange {
            color: #d63384;
            font-weight: bold;
        }

        .netzone-images img {
            width: 420px;
            max-width: 100%;
            height: 220px;
            object-fit: cover;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.10);
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .netzone-images img:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        }

        /* Modal styles */
        .image-modal {
            display: none;
            position: fixed;
            z-index: 9999;
            inset: 0;
            background: rgba(0, 0, 0, 0.8);
            justify-content: center;
            align-items: center;
        }

        .image-modal img {
            max-width: 90%;
            max-height: 80%;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5);
            animation: zoomIn 0.3s ease;
        }

        .image-modal.close {
            display: none;
        }

        .image-modal span {
            position: absolute;
            top: 20px;
            right: 30px;
            color: #fff;
            font-size: 2rem;
            font-weight: bold;
            cursor: pointer;
        }

        @keyframes zoomIn {
            from {
                transform: scale(0.8);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .reminders-card {
            background: #fff;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            margin: 2rem auto;
            max-width: 800px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .reminders-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>

<body>
    @include('navbar')
    <div class="header-banner">
        <div class="header-text">Netzone</div>
    </div>
    <div class="container py-5">
        <div class="divider"></div>
        <div class="netzone-images">
            <img src="{{ asset('images/netzone1.png') }}" alt="Netzone 1">
            <img src="{{ asset('images/netzone2.png') }}" alt="Netzone 2">
        </div>
        <div class="netzone-title">Netzone</div>
        <div class="netzone-desc">
            The College Library provides 30 terminals for the use of students. The Senior High School has its own Internet Room and has 10 terminals for use. In addition, there are spaces for students to use their laptops or notebooks.
        </div>
        <div class="reminders-card">
            <div class="reminders-title">Reminders While Using the Netzone</div>
            <ul class="reminders-list">
                <li class="red">BE RESPECTFUL! Always treat the computer lab equipment AND your teacher and classmates the way that you would want your belongings and yourself to be treated.</li>
                <li class="red">No food or drinks near the computers. NO EXCEPTIONS.</li>
                <li class="orange">Enter the Netzone quietly and work quietly. There are other individuals who may be using the Netone. Please be respectful.</li>
                <li class="orange">Surf safely! Only visit assigned websites. Some web links can contain viruses or malware. Others may contain inappropriate content.</li>
                <li class="orange">Clean up your work area before you leave.</li>
                <li class="orange">Do not change computer settings or backgrounds.</li>
                <li class="orange">For your saving and printing needs, proceed to the Concierge</li>
            </ul>
        </div>
    </div>
    @include('footer')

    <div class="image-modal" id="imageModal">
        <span id="closeModal">&times;</span>
        <img id="modalImg" src="" alt="Expanded view">
    </div>


    <script>
        const images = document.querySelectorAll('.netzone-images img');
        const modal = document.getElementById('imageModal');
        const modalImg = document.getElementById('modalImg');
        const closeModal = document.getElementById('closeModal');

        images.forEach(img => {
            img.addEventListener('click', () => {
                modal.style.display = 'flex';
                modalImg.src = img.src;
            });
        });

        closeModal.addEventListener('click', () => {
            modal.style.display = 'none';
        });

        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    </script>
</body>

</html>