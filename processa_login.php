<?php
session_start();

require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['erro_login'] = "Por favor, insira um email válido.";
        header("Location: login.html");
        exit();
    }

    if (empty($senha)) {
        $_SESSION['erro_login'] = "A senha é obrigatória.";
        header("Location: login.html");
        exit();
    }

    try {
        $stmt = $pdo->prepare("SELECT id, nome, senha, id_turma, (SELECT nome FROM turmas WHERE id = usuarios.id_turma) AS nome_turma FROM usuarios WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            // Senha verificada com sucesso, iniciar sessão
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['turma_id'] = $usuario['id_turma'];
            $_SESSION['turma_nome'] = $usuario['nome_turma'];
            header("Location: arquivos_alunos.php"); // Redirecionar para a página de arquivos
            exit();
        } else {
            $_SESSION['erro_login'] = "Email ou senha incorretos.";
            header("Location: login.html");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['erro_login'] = "Erro ao verificar o login: " . $e->getMessage();
        header("Location: login.html");
        exit();
    }
} else {
    // Se alguém tentar acessar este arquivo diretamente sem ser por POST
    header("Location: login.html");
    exit();
}
?>