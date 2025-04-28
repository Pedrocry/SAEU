<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
	
    <div class="container">
        <center><h1>Login</h1></center>
		<br>
		
		<br>
        <?php
            session_start();
            if (isset($_SESSION['erro_login'])): ?>
                <p class="error"><?php echo $_SESSION['erro_login']; ?></p>
                <?php unset($_SESSION['erro_login']);
            endif;
        ?>
        <form action="processa_login.php" method="POST">
		<center>
            <div class="form-group">
                <label  for="email">Email:</label>
                <input style="height:40px; width:70%;" type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="senha">Senha:</label>
                <input style="height:40px; width:70%;" type="password" id="senha" name="senha" required>
            </div>
            <button type="submit" class="button">Entrar</button>
			<center>
        </form>
        <p>Ainda não tem uma conta? <a href="cadastro_usuario.php">Cadastre-se</a></p>
    </div>
</body>
</html>