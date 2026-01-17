<link rel="stylesheet" href="{{ asset('css/login.css') }}">
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>KeyStone-Login</title>
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

</head>
<body>
  <div class="container">
    <div class="login-box" role="main" aria-labelledby="login-title">
      <h1 class="logo" id="login-title"><span class="orange">Key</span>stone</h1>
       <form method="POST" action="/login">
           @csrf
                 @error('name')
               <p class="text-danger">{{ $message }}</p>
           @enderror
              @error('password')
               <p class="text-danger">{{ $message }}</p>
           @enderror
        <input type="text" placeholder="Enter Username" name="name" aria-label="Username" value="{{ old('name') }}" />
     

        <input type="password" placeholder="Enter Password" name="password" aria-label="Password" />
        
        <button type="submit">Login</button>
      </form>
    </div>
  </div>
</body>
</html>
