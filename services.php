<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Service</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
         body {
        background-color: #F7F2EB;
        font-family: 'Poppins', sans-serif;
        margin: 0;
        padding: 0;
        transition: margin-left 0.3s ease-in-out;
    }

    .container {
        background: white;
        border-radius: 10px;
        width: 100vw;
        margin-left: 70px;
        margin-right: 1em;
        margin-top: 5em;
        padding: 20px;
        transition: margin-left 0.3s ease-in-out, width 0.3s ease-in-out;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    }

    /* Adjust form position when sidebar is open */
    body.sidebar-open .container {
        width: calc(100vw - 300px);
        margin-left: 300px;
    }

    h2 {
        margin-bottom: 20px;
        color: #6E3387;
        font-size: 24px;
        font-weight: bold;
    }

    form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    /* Input, Textarea, Select Styling */
    input, textarea, select {
        padding: 12px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 16px;
        background: #f9f9f9;
        font-family: 'Poppins', sans-serif;
        transition: all 0.3s ease-in-out;
    }

    input:focus, textarea:focus, select:focus {
        border-color: #6E3387;
        outline: none;
        box-shadow: 0px 0px 5px rgba(110, 51, 135, 0.5);
    }

    input[type="file"] {
        padding: 5px;
    }

    /* Label Styling */
    label {
        font-size: 16px;
        font-weight: bold;
        color: #6E3387;
        margin-bottom: 5px;
        display: block;
    }

    /* Custom Select Styling */
    .custom-select {
        position: relative;
    }

    .custom-select select {
        appearance: none;
        cursor: pointer;
    }

    .custom-select::after {
        content: "▼";
        font-size: 14px;
        color: #6E3387;
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
    }

    /* Button Styling */
    .btn {
        background: #D6BE3E;
        color: white;
        padding: 12px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: bold;
        font-size: 16px;
        transition: 0.3s;
    }

    .btn:hover {
        background: #C5A634;
    }
    </style>
</head>
<body>

<?php include 'components/admin-services.php'; ?>

    <script>
        function toggleMenu() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            const body = document.body;

            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
            body.classList.toggle("sidebar-open"); // Moves the form when sidebar opens
        }
    </script>

</body>
</html>
