<?php
session_start();

require_once 'conexao.php';

// Define o caminho base para as pastas dos usuários
$base_path = 'uploads/';

function sanitizeFolderName($string) {
    // Remove caracteres especiais, espaços e converte para minúsculo
    $sanitized = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower(trim($string)));
    return $sanitized;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_turma = filter_input(INPUT_POST, 'id_turma', FILTER_VALIDATE_INT);
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    $erros = [];

    if (!$id_turma) {
        $erros[] = "Por favor, selecione uma turma.";
    }
    if (empty($nome)) {
        $erros[] = "O nome é obrigatório.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = "Por favor, insira um email válido.";
    }
    if (empty($senha)) {
        $erros[] = "A senha é obrigatória.";
    }
    if ($senha !== $confirmar_senha) {
        $erros[] = "A senha e a confirmação de senha não coincidem.";
    }

    if (!empty($erros)) {
        $_SESSION['erro_cadastro_usuario'] = implode("<br>", $erros);
    } else {
        // Verificar se o email já existe
        try {
            $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = :email");
            $stmt_check->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt_check->execute();
            if ($stmt_check->fetchColumn() > 0) {
                $_SESSION['erro_cadastro_usuario'] = "Este email já está cadastrado.";
            } else {
                // Hash da senha antes de salvar no banco de dados
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

                $stmt_insert = $pdo->prepare("INSERT INTO usuarios (id_turma, nome, email, senha) VALUES (:id_turma, :nome, :email, :senha)");
                $stmt_insert->bindParam(':id_turma', $id_turma, PDO::PARAM_INT);
                $stmt_insert->bindParam(':nome', $nome, PDO::PARAM_STR);
                $stmt_insert->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt_insert->bindParam(':senha', $senha_hash, PDO::PARAM_STR);

                if ($stmt_insert->execute()) {
                    $usuario_id = $pdo->lastInsertId(); // Obtém o ID do usuário recém-inserido
                    $nome_sanitized = sanitizeFolderName($nome);
                    $user_folder = $base_path . $nome_sanitized . '_' . $usuario_id . '/'; // Inclui nome e ID

                    if (!is_dir($user_folder)) {
                        if (mkdir($user_folder, 0755, true)) {
                            $_SESSION['mensagem_cadastro_usuario'] = "Cadastro realizado com sucesso! Uma pasta pessoal foi criada para você.";
                        } else {
                            $_SESSION['mensagem_cadastro_usuario'] = "Cadastro realizado com sucesso! Houve um erro ao criar sua pasta pessoal.";
                            // Log do erro na criação da pasta: error_log("Erro ao criar pasta para o usuário " . $usuario_id . " (" . $nome . ")");
                        }
                    } else {
                        $_SESSION['mensagem_cadastro_usuario'] = "Cadastro realizado com sucesso! Sua pasta pessoal já existe.";
                    }
                } else {
                    $_SESSION['erro_cadastro_usuario'] = "Erro ao cadastrar o usuário. Tente novamente.";
                    // Log do erro ao inserir usuário: error_log("Erro ao cadastrar usuário: " . print_r($stmt_insert->errorInfo(), true));
                }
            }
        } catch (PDOException $e) {
            $_SESSION['erro_cadastro_usuario'] = "Erro no banco de dados: " . $e->getMessage();
            // Log do erro no banco de dados: error_log("Erro no banco de dados (cadastro usuário): " . $e->getMessage());
        }
    }

    header("Location: cadastro_usuario.php");
    exit();
} else {
    // Se alguém tentar acessar este arquivo diretamente sem ser por POST
    header("Location: cadastro_usuario.php");
    exit();
}
?>