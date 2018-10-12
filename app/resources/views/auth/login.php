<!doctype html>
<html lang="en" class="login">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Log in</title>

    <link href="/vendor/bootstrap.min.css" rel="stylesheet">
    <link href="/css/app.css" rel="stylesheet">
  </head>

  <body class="text-center">

    <form class="form-signin" method="post" action="/login">
      <h1 class="h3 mb-3 font-weight-normal">Please log in</h1>

      <?php if($error) { ?>

			  <div class="alert alert-danger" role="alert">
	  			<?= $error ?>
  			</div>

		  <?php	} ?>
      
      <label for="username" class="sr-only">Username</label>
      <input 
      	type="text" 
      	id="username"
      	name="username" 
      	class="form-control mb-2" 
      	placeholder="Username"
        value="<?= $username ?? '' ?>"
      	required 
      	autofocus>

      <label for="password" class="sr-only">Password</label>
      <input 
      	type="password" 
      	id="password"
      	name="password"
      	class="form-control" 
      	placeholder="Password" 
      	required>
      
      <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
    </form>
  </body>
</html>