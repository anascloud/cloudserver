<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reset Password Code</title>
    <style>
        /* Simple styling for the email */
        .copy-button {
            background-color: #4CAF50; /* Green */
            border: none;
            color: white;
            padding: 10px 24px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h1>Password Reset Code</h1>
    <p>Your password reset code is:</p>
    <h2>{{ $resetCode }}</h2>
    <p>Click the button below to copy the code to your clipboard:</p>
    <button class="copy-button" onclick="copyCode('{{ $resetCode }}')">Copy Code</button>

    <script>
        function copyCode(code) {
            navigator.clipboard.writeText(code).then(() => {
                alert('Code copied to clipboard');
            }, (err) => {
                console.error('Could not copy code: ', err);
            });
        }
    </script>
    <p>If you did not request a password reset, no further action is required.</p>
</body>
</html>