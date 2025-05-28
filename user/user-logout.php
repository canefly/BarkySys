<?php include_once 'helpers/head.php' ?>
<?php
session_start();
session_destroy(); // Destroy all sessions

// Redirect to login page after 1 second
header("refresh:1;url=user-login.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging Out...</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        /* Color variables for consistency */
        :root {
            --bg-muted: #F7F2EB;
            --text: #333333;
            --primary: #6E3387;
        }
        body {
            font-family: 'Poppins', sans-serif;
            text-align: center;
            padding: 50px;
            background-color: var(--bg-muted);
            color: var(--text);
            margin: 0;
        }
        .message {
            font-size: 20px;
            color: var(--primary);
            margin-bottom: 10px;
        }
        .redirect {
            font-size: 14px;
            color: var(--text);
        }
    </style>
</head>
<body>
    <h2 class="message">You have been logged out.</h2>
    <p class="redirect">Redirecting to login page...</p>
</body>
</html>
