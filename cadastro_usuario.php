<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Usuário</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Cadastro de Novo Usuário</h1>
        <?php
            session_start();
            if (isset($_SESSION['erro_cadastro_usuario'])): ?>
                <p class="error"><?php echo $_SESSION['erro_cadastro_usuario']; ?></p>
                <?php unset($_SESSION['erro_cadastro_usuario']);
            endif;
            if (isset($_SESSION['mensagem_cadastro_usuario'])): ?>
                <p class="success"><?php echo $_SESSION['mensagem_cadastro_usuario']; ?></p>
                <?php unset($_SESSION['mensagem_cadastro_usuario']);
            endif;
        ?>
        <form action="processa_cadastro_usuario.php" method="POST">
            <div class="form-group">
                <label for="id_turma">Turma:</label>
                <select id="id_turma" name="id_turma" required>
                    <option value="">Selecione a Turma</option>
                    <?php
                        require_once 'conexao.php';
                        try {
                            $stmt = $pdo->query("SELECT id, nome FROM turmas ORDER BY nome");
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['nome']); ?></option>
                            <?php endwhile;
                        } catch (PDOException $e) {
                            echo "<option value=''>Erro ao carregar turmas</option>";
                            // Log do erro para análise: error_log("Erro ao carregar turmas: " . $e->getMessage());
                        }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="nome">Nome Completo:</label>
                <input type="text" id="nome" name="nome" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            <div class="form-group">
                <label for="confirmar_senha">Confirmar Senha:</label>
                <input type="password" id="confirmar_senha" name="confirmar_senha" required>
            </div>
            <button type="submit" class="button">Cadastrar</button>
        </form>
        <p><a href="index.php">Voltar</a></p>
    </div>
</body>
</html>