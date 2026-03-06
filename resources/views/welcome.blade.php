<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap Css -->
    <link href="{{ URL::asset('css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <title>Welcome - BCity</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            text-align: center;
            color: white;
            padding: 40px;
            max-width: 600px;
        }
        h1 {
            font-size: 3.5rem;
            margin-bottom: 20px;
            font-weight: 700;
        }
        p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            opacity: 0.95;
            line-height: 1.6;
        }
        .btn-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        button {
            padding: 12px 30px;
            font-size: 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-primary {
            background-color: white;
            color: #667eea;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        .btn-secondary {
            background-color: transparent;
            color: white;
            border: 2px solid white;
        }
        .btn-secondary:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to my BCity assessment</h1>
        <p>Use the below links to jump right in.</p>
        <div class="btn-group">
            <a class="btn btn-primary"href="/clients">Clients</a>
            <a class="btn btn-primary" href="/contacts">Contacts</a>
        </div>
    </div>
</body>
</html>