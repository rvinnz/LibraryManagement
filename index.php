<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZPPSU Library System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="assets/icons/favicon.png">
    <style>
        /* Simple Internal CSS */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            /* Darkened overlay so text pops */
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('assets/img/background.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
        }

        .logo {
            width: 140px;
            margin-bottom: 20px;
            animation: float 3s ease-in-out infinite;
        }

        h1 {
            font-size: 42px;
            margin: 0 0 10px;
            font-weight: 700;
            text-shadow: 0 2px 10px rgba(0,0,0,0.5);
        }

        p {
            font-size: 18px;
            margin-bottom: 40px;
            opacity: 0.9;
            max-width: 600px;
            line-height: 1.6;
        }

        /* Button Container to sit side-by-side */
        .button-group {
            display: flex;
            gap: 20px;
        }

        /* Common Button Styles */
        .btn {
            padding: 14px 40px;
            font-size: 18px;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            transition: 0.3s;
            border: 2px solid transparent;
        }

        /* Primary Button (Log In) - Maroon */
        .btn-primary {
            background-color: #800000; /* ZPPSU Maroon */
            color: white;
            border-color: #800000;
        }

        .btn-primary:hover {
            background-color: #a00000;
            border-color: #a00000;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(128, 0, 0, 0.4);
        }

        /* Secondary Button (Sign Up) - Transparent/White */
        .btn-secondary {
            background-color: transparent;
            color: white;
            border-color: white;
        }

        .btn-secondary:hover {
            background-color: white;
            color: #800000;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(255, 255, 255, 0.2);
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        /* Mobile Responsiveness */
        @media (max-width: 600px) {
            h1 { font-size: 32px; }
            p { font-size: 16px; padding: 0 20px; }
            .button-group { flex-direction: column; gap: 15px; width: 80%; }
            .btn { width: 100%; box-sizing: border-box; }
        }
    </style>
</head>
<body>

    <img src="assets/img/Zamboanga_Peninsula_Polytechnic_State_University.png" alt="ZPPSU Logo" class="logo">
    
    <h1>ZPPSU Library Management</h1>
    <p>Your gateway to knowledge. Access thousands of books and resources instantly.</p>

    <div class="button-group">
        <a href="login.php" class="btn btn-primary">Log In</a>
        <a href="signup.php" class="btn btn-secondary">Sign Up</a>
    </div>

</body>
</html>