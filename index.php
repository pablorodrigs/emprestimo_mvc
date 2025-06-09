<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login - Facens</title>
    <link rel="stylesheet" href="app/template/css/login-style.css">
</head>
<body>
    <div class="login-container">
        <img src="app/template/img/logo_facens.png" alt="Facens">
        
        <form action="app/controllers/LoginController.php" method="POST">
            <label>Usuário</label>
            <input type="text" name="usuario" required>

            <label>Senha <a href="#">Esqueceu?</a></label>
            <input type="password" name="senha" required>

            <button type="submit">Entrar</button>
        </form>

        <div class="footer">Centro Universitário Facens</div>
    </div>
</body>
</html>
