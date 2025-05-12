<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Turma</title>
    <link rel="stylesheet" href="styles/style_turmaCad.css">
    <style>
        body{background-color: #e9f5ec;}
        #password-prompt {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            text-align: center;
        }

        #password-prompt label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        #password-prompt input[type="password"] {
            padding: 8px;
            border: 1px solid #388e3c;
            border-radius: 4px;
            font-size: 16px;
            margin-bottom: 10px;
        }

        #password-prompt button {
            background-color: #388e3c;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        #password-prompt button:hover {
            background-color: #0056b3;
        }

        #cadastro-form {
            display: none; /* O formulário fica inicialmente oculto */
        }
    </style>
</head>
<body>
    <div class="container">
        <div id="password-prompt">
            <h2>Acesso Administrativo</h2>
            <label for="admin_password">Senha:</label>
            <input type="password" id="admin_password">
            <button onclick="checkAdminPassword()">Acessar</button>
            <p id="password-error" style="color: red; margin-top: 10px; display: none;">Senha incorreta.</p>
        </div>

        <div id="cadastro-form">
            <h1>Cadastro de Nova Turma</h1>
            <?php
                session_start();
                if (isset($_SESSION['erro_cadastro_turma'])): ?>
                    <p class="error"><?php echo $_SESSION['erro_cadastro_turma']; ?></p>
                    <?php unset($_SESSION['erro_cadastro_turma']);
                endif;
                if (isset($_SESSION['mensagem_cadastro_turma'])): ?>
                    <p class="success"><?php echo $_SESSION['mensagem_cadastro_turma']; ?></p>
                    <?php unset($_SESSION['mensagem_cadastro_turma']);
                endif;
            ?>
            <form action="processa_cadastro_turma.php" method="POST">
                <div class="form-group">
                    <label for="nome">Nome da Turma:</label>
                    <input type="text" id="nome" name="nome" required>
                </div>
                <div class="form-group">
                    <label for="descricao">Descrição (opcional):</label>
                    <textarea id="descricao" name="descricao"></textarea>
                </div>
                <button type="submit" class="button">Cadastrar Turma</button>
            </form>
            <p><a href="index.php">Voltar</a></p>
        </div>
    </div>

    <script>
        const correctPassword = "qwe123"; // Defina aqui a senha de administrador
        const passwordPrompt = document.getElementById('password-prompt');
        const cadastroForm = document.getElementById('cadastro-form');
        const passwordError = document.getElementById('password-error');
        const passwordInput = document.getElementById('admin_password');

        function checkAdminPassword() {
            if (passwordInput.value === correctPassword) {
                passwordPrompt.style.display = 'none';
                cadastroForm.style.display = 'block';
                passwordError.style.display = 'none';
            } else {
                passwordError.style.display = 'block';
                passwordInput.value = ''; // Limpa o campo de senha
            }
        }

        // Permite o acesso ao pressionar Enter no campo de senha
        passwordInput.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                checkAdminPassword();
            }
        });
    </script>
</body>
</html>