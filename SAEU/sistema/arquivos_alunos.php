<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html"); // Redirecionar para a página de login se não estiver logado
    exit();
}

require_once 'conexao.php';

$usuario_id = $_SESSION['usuario_id'];

function sanitizeFolderName($string) {
    // Remove caracteres especiais, espaços e converte para minúsculo
    $sanitized = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower(trim($string)));
    return $sanitized;
}

// Processamento de Upload de Arquivos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['arquivo'])) {
    $arquivo = $_FILES['arquivo'];
    $nome_original = $arquivo['name'];
    $tipo = $arquivo['type'];
    $tamanho = $arquivo['size'];
    $erro = $arquivo['error'];
    $tmp_name = $arquivo['tmp_name'];

    if ($erro === UPLOAD_ERR_OK) {
        $nome_sanitized = sanitizeFolderName($_SESSION['usuario_nome']);
        $usuario_pasta = 'uploads/' . $nome_sanitized . '_' . $_SESSION['usuario_id'] . '/';
        $nome_servidor = uniqid() . '_' . pathinfo($nome_original, PATHINFO_FILENAME) . '.' . pathinfo($nome_original, PATHINFO_EXTENSION);
        $caminho_servidor = $usuario_pasta . $nome_servidor;

        // Verificar se a pasta do usuário existe (segurança extra)
        if (!is_dir($usuario_pasta)) {
            mkdir($usuario_pasta, 0755, true); // Tenta criar novamente se não existir (improvável)
        }

        if (move_uploaded_file($tmp_name, $caminho_servidor)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO arquivos (id_usuario, nome_original, nome_servidor, tipo, tamanho) VALUES (:id_usuario, :nome_original, :nome_servidor, :tipo, :tamanho)");
                $stmt->bindParam(':id_usuario', $usuario_id, PDO::PARAM_INT);
                $stmt->bindParam(':nome_original', $nome_original, PDO::PARAM_STR);
                $stmt->bindParam(':nome_servidor', $nome_servidor, PDO::PARAM_STR);
                $stmt->bindParam(':tipo', $tipo, PDO::PARAM_STR);
                $stmt->bindParam(':tamanho', $tamanho, PDO::PARAM_INT);
                $stmt->execute();
                $mensagem_upload = "Arquivo enviado com sucesso!";
            } catch (PDOException $e) {
                $erro_upload = "Erro ao salvar informações do arquivo no banco de dados: " . $e->getMessage();
                if (file_exists($caminho_servidor)) {
                    unlink($caminho_servidor);
                }
            }
        } else {
            $erro_upload = "Erro ao mover o arquivo para a pasta pessoal.";
        }
    } else {
        $erro_upload = "Erro no upload do arquivo: " . $erro;
    }
}

// Listar arquivos do usuário
try {
    $stmt_arquivos = $pdo->prepare("SELECT id, nome_original, nome_servidor, tamanho, data_upload FROM arquivos WHERE id_usuario = :id_usuario ORDER BY data_upload DESC");
    $stmt_arquivos->bindParam(':id_usuario', $usuario_id, PDO::PARAM_INT);
    $stmt_arquivos->execute();
    $arquivos = $stmt_arquivos->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $erro_listar_arquivos = "Erro ao listar seus arquivos: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Arquivos</title>
    <link rel="stylesheet" href="styles/style_aluno.css">
</head>
<body>
    <div class="container">
        <div class="titulo"><h1>Meus Arquivos </h1> <p><a href="logout.php">Sair</a></p></div>
        <p> <b>Bem-vindo(a),</b> <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?> (Turma: <?php echo htmlspecialchars($_SESSION['turma_nome']); ?>)!</p>

        <h2>Upload de Arquivo</h2>
        <?php if (isset($erro_upload)): ?>
            <p class="error"><?php echo $erro_upload; ?></p>
        <?php endif; ?>
        <?php if (isset($mensagem_upload)): ?>
            <p class="success"><?php echo $mensagem_upload; ?></p>
        <?php endif; ?>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="arquivo">Selecionar Arquivo:</label>
                <input type="file" name="arquivo" id="arquivo" required>
            </div>
            <button type="submit" class="button">Enviar Arquivo</button>
        </form>

        <h2>Seus Arquivos</h2>
        <?php if (isset($erro_listar_arquivos)): ?>
            <p class="error"><?php echo $erro_listar_arquivos; ?></p>
        <?php endif; ?>
<?php if (!empty($arquivos)): ?>
    <table>
        <thead>
            <tr>
                <th>Nome do Arquivo</th>
                <th>Tamanho</th>
                <th>Data de Upload</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($arquivos as $arquivo): ?>
                <tr>
                    <td><?php echo htmlspecialchars($arquivo['nome_original']); ?></td>
                    <td class="file-size" data-bytes="<?php echo $arquivo['tamanho']; ?>"></td>
                    <td><?php echo date('d/m/Y H:i:s', strtotime($arquivo['data_upload'])); ?></td>
                    <td><a href="download.php?id=<?php echo $arquivo['id']; ?>">Download</a></td>
					<td><a href="deletar_arquivo.php?id=<?php echo $arquivo['id']; ?>" onclick="return confirm('Tem certeza que deseja deletar este arquivo?')">Deletar</a>
                    </td>
				</tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Nenhum arquivo enviado ainda.</p>
<?php endif; ?>
    </div>

<script>
    function formatBytes(bytes, decimals = 2) {
        if (!+bytes) return '0 Bytes';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`;
    }

    // Script para formatar os tamanhos dos arquivos após a página carregar
    document.addEventListener('DOMContentLoaded', function() {
        const sizeElements = document.querySelectorAll('.file-size');
        sizeElements.forEach(function(element) {
            const bytes = parseInt(element.getAttribute('data-bytes'));
            element.textContent = formatBytes(bytes);
        });
    });
</script>
</body>
</html>