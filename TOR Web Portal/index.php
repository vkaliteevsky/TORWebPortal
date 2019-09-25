<? require_once($_SERVER['DOCUMENT_ROOT'] . '/php/auth/login.php'); ?>
<html>
    <head>
		<meta charset="utf-8" />
        <title>Вход в систему</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.8/css/all.css">
        <link rel="stylesheet" href="css/lk_style.css">
    </head>
    <body>
        <div class="content">
            <div class="row">
                <div class= "col-xl-3 col-lg-4 col-md-8 col-sm-9 col-12 mx-auto">

                    <div class="card login-card">
                        <div class="card-body">
                            <h4 class="card-title text-center mb-4 mt-1">Вход в личный кабинет</h4>
                            <hr>
							<p class="text-success text-center">
								<? if ($isAuthOk == -1) {
									echo "<font color=\"red\">Введен неверный логин или пароль</font>";
								} else {
									echo "Введите Ваш логин и пароль";
								} ?>
							</p>
                            <form method="POST" action="">
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"> <i class="fa fa-user"></i> </span>
                                        </div>
                                        <input name="login" class="form-control" placeholder="Login">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"> <i class="fa fa-lock"></i> </span>
                                            </div>
                                        <input name="password" class="form-control" placeholder="******" type="password">
                                    </div>
                                </div>
								<!-- 
                                <div class="form-group">
                                    <a class="a-btn" href="create/">Войти</a>
                                </div>
								-->
                                
                                <div class="form-group">
                                    <button class="btn btn-block btn_green" type="submit" style="margin: auto;"> Войти  </button>
                                </div>
                                
                                <!-- <p class="text-center"><a href="#" class="btn">Forgot password?</a></p> -->
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
		<script>
			function tryLogin() {
				var login = document.getElementById("login").value;
				var password = document.getElementById("password").value;
				$.ajax({
					type: 'POST',
					url: '/php/auth/login.php',
					data: { 
						'login': login, 
						'password': password
					},
					success: function(msg){
						alert("Server Response:\n" + msg);
						if (msg.localeCompare("200")==0) {
							alert("Succ.");
						} else {
							alert("Neg");
						}
					}
				});
			}

		</script>
    </body>
</html>