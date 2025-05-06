<?php
// Save this file as index.php
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Work Pause</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            font-family: 'Poppins', Arial, sans-serif;
            color: #ffffff;
            text-align: center;
            background: radial-gradient(circle, #4e54c8, #8f94fb);
            overflow: hidden;
        }

        .splash-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            flex-direction: column;
        }

        .splash-container h1 {
            font-size: 4em;
            margin-bottom: 20px;
            animation: fadeInDown 2s ease;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.5);
        }

        .splash-container p {
            font-size: 1.5em;
            margin-bottom: 30px;
            animation: fadeInUp 2.5s ease;
            max-width: 600px;
            line-height: 1.6;
        }

        .cta-button {
            background: linear-gradient(90deg, #ff8a00, #e52e71);
            color: #fff;
            padding: 15px 40px;
            font-size: 1.3em;
            font-weight: bold;
            border: none;
            border-radius: 30px;
            text-decoration: none;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            animation: bounceIn 3s ease;
        }

        .cta-button:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4);
        }

        .circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            animation: float 6s ease-in-out infinite;
            z-index: 0;
        }

        .circle.one {
            width: 150px;
            height: 150px;
            top: 10%;
            left: 20%;
            animation-delay: 0s;
        }

        .circle.two {
            width: 200px;
            height: 200px;
            bottom: 15%;
            right: 25%;
            animation-delay: 3s;
        }

        .circle.three {
            width: 100px;
            height: 100px;
            bottom: 25%;
            left: 10%;
            animation-delay: 1.5s;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes bounceIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(20px);
            }
        }
    </style>
</head>
<body>
    <div class="splash-container">
        <h1>Welcome to Work Pause</h1>
        <p>
            <?php
            echo "Your journey to better work-life balance starts here.";
            echo "<br>";
            echo "Today is " . date("l, F j, Y");
            ?>
        </p>
        <a class="cta-button" href="login.php">Enter</a>
        

    </div>
    <div class="circle one"></div>
    <div class="circle two"></div>
    <div class="circle three"></div>
</body>
</html>
