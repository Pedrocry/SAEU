<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit();
}

//requisição do arquivo para conectar com o db
require_once 'conexao.php';

$usuario_id = $_SESSION['usuario_id'];

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $arquivo_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

    try {
        $stmt = $pdo->prepare("SELECT nome_original, nome_servidor FROM arquivos WHERE id = :id AND id_usuario = :id_usuario");
        $stmt->bindParam(':id', $arquivo_id, PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario', $usuario_id, PDO::PARAM_INT);
        $stmt->execute();
        $arquivo = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($arquivo) {
            $nome_sanitized = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower(trim($_SESSION['usuario_nome'])));
            $pasta_usuario = 'uploads/' . $nome_sanitized . '_' . $usuario_id . '/';
            $caminho_arquivo = $pasta_usuario . $arquivo['nome_servidor'];


            if (file_exists($caminho_arquivo)) {
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . $arquivo['nome_original'] . '"');
                header('Content-Length: ' . filesize($caminho_arquivo));
                header('Cache-Control: no-cache, no-store, must-revalidate');
                header('Pragma: no-cache');
                header('Expires: 0');

                readfile($caminho_arquivo);
                exit();
            } else {
                $_SESSION['erro_download'] = "Arquivo não encontrado no servidor.";
                header("Location: arquivos_alunos.php"); 
                exit();
            }
        } else {
            $_SESSION['erro_download'] = "Arquivo não encontrado ou você não tem permissão para acessá-lo.";
            header("Location: arquivos_alunos.php"); 
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['erro_download'] = "Erro ao buscar informações do arquivo: " . $e->getMessage();
        header("Location: arquivos_alunos.php");
        exit();
    }
} else {
    $_SESSION['erro_download'] = "ID inválido.";
    header("Location: arquivos_alunos.php");
    exit();
}
?>