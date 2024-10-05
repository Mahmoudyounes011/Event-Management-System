<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <style>
        body {
            background-color: #2c3e50;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding: 0;
            color: #ecf0f1;
        }
        .container {
            width: 60%;
            background-color: #34495e;
            padding: 40px;
            border-radius: 4px;
            border: 3px solid #FF6347; /* Added orange border */
            box-shadow: 0px 0px 10px rgba(231, 76, 60,0.5);
            text-align: center;
        }
        h1 {
            color: #FF6347;
            margin-bottom: 20px;
        }
        p {
            color: #ecf0f1;
            line-height: 1.6;
        }
        .code {
            display: inline-block;
            background-color: #FF6347;
            padding: 5px;
            border: 2px solid #ecf0f1;
            color: #ecf0f1;
            font-weight: bold;
            margin: 30px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Reset Password</h1>

        <p>Your reset code is: <span class="code">{{ $resetCode }}</span></p>

        <p>Please enter this code to reset your password.</p>
    </div>
</body>
</html>
