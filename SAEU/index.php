<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Arquivos Escolar</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .container { /* Mantendo o estilo geral */
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 500px;
            text-align: center; /* Centralizando o conteúdo */
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
        }

        p {
            color: #555;
            margin-bottom: 20px;
        }

        .link-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 30px;
            width: 100%;
        }

        .link-button {
            display: block;
            padding: 12px 20px;
            text-align: center;
            text-decoration: none;
            color: #fff;
            background-color: #007bff;
            border-radius: 6px;
            transition: background-color 0.3s ease;
        }

        .link-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Bem-vindo ao Sistema de Arquivos Escolar</h1>
        <p>Selecione uma das opções abaixo:</p>

        <div class="link-container">
            <a href="cadastro_turma.php" class="link-button">Cadastrar Turma (Administrador)</a>
            <a href="cadastro_usuario.php" class="link-button">Cadastrar Usuário (Aluno)</a>
            <a href="login.php" class="link-button">Fazer Login (Aluno)</a>
        </div>
    </div>
</body>
</html>