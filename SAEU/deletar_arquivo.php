<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit();
}

require_once 'conexao.php';

$usuario_id = $_SESSION['usuario_id'];

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $arquivo_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

    try {
        // Buscar informações do arquivo para deletar do servidor
        $stmt_select = $pdo->prepare("SELECT nome_servidor FROM arquivos WHERE id = :id AND id_usuario = :usuario_id");
        $stmt_select->bindParam(':id', $arquivo_id, PDO::PARAM_INT);
        $stmt_select->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt_select->execute();
        $arquivo = $stmt_select->fetch(PDO::FETCH_ASSOC);

        if ($arquivo) {
            $nome_servidor = $arquivo['nome_servidor'];
            $caminho_arquivo = 'uploads/' . sanitizeFolderName($_SESSION['usuario_nome']) . '_' . $usuario_id . '/' . $nome_servidor;

            // Excluir o arquivo do servidor
            if (file_exists($caminho_arquivo)) {
                if (unlink($caminho_arquivo)) {
                    // Excluir a entrada do banco de dados
                    $stmt_delete = $pdo->prepare("DELETE FROM arquivos WHERE id = :id AND id_usuario = :usuario_id");
                    $stmt_delete->bindParam(':id', $arquivo_id, PDO::PARAM_INT);
                    $stmt_delete->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
                    if ($stmt_delete->execute()) {
                        $_SESSION['mensagem_exclusao'] = "Arquivo deletado com sucesso!";
                    } else {
                        $_SESSION['erro_exclusao'] = "Erro ao deletar informações do arquivo no banco de dados.";
                        // Log do erro: error_log("Erro ao deletar arquivo do banco de dados: " . print_r($stmt_delete->errorInfo(), true));
                    }
                } else {
                    $_SESSION['erro_exclusao'] = "Erro ao deletar o arquivo do servidor.";
                    // Log do erro: error_log("Erro ao deletar arquivo do servidor: " . $caminho_arquivo);
                }
            } else {
                $_SESSION['erro_exclusao'] = "Arquivo não encontrado no servidor.";
            }
        } else {
            $_SESSION['erro_exclusao'] = "Arquivo não encontrado ou você não tem permissão para deletá-lo.";
        }

    } catch (PDOException $e) {
        $_SESSION['erro_exclusao'] = "Erro ao processar a exclusão: " . $e->getMessage();
        // Log do erro: error_log("Erro PDO ao deletar arquivo: " . $e->getMessage());
    }

} else {
    $_SESSION['erro_exclusao'] = "ID do arquivo inválido.";
}

header("Location: arquivos_alunos.php");
exit();

function sanitizeFolderName($string) {
    $sanitized = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower(trim($string)));
    return $sanitized;
}
?>