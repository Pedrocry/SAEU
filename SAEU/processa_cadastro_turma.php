<?php
session_start();

// Inclui o arquivo de conexão com o banco de dados
require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
     $disciplina01 = filter_input(INPUT_POST, 'disciplina01', FILTER_SANITIZE_STRING);
      $disciplina02 = filter_input(INPUT_POST, 'disciplina02', FILTER_SANITIZE_STRING);
       $disciplina03 = filter_input(INPUT_POST, 'disciplina03', FILTER_SANITIZE_STRING);

    $descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_STRING);

    if (empty($nome)) {
        $_SESSION['erro_cadastro_turma'] = "O nome da turma é obrigatório.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO turmas (nome, descricao, disciplina01, disciplina02, disciplina03) VALUES (:nome, :descricao, :disciplina01, :disciplina02, :disciplina03)");
            $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
            
            $stmt->bindParam(':disciplina01', $disciplina01, PDO::PARAM_STR);
            $stmt->bindParam(':disciplina02', $disciplina02, PDO::PARAM_STR);
            $stmt->bindParam(':disciplina03', $disciplina03, PDO::PARAM_STR);
            $stmt->bindParam(':descricao', $descricao, PDO::PARAM_STR);

            $stmt->execute();
            $_SESSION['mensagem_cadastro_turma'] = "Turma cadastrada com sucesso!";
			
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') { // Código de erro para chave duplicada
                $_SESSION['erro_cadastro_turma'] = "Já existe uma turma com este nome.";
            } else {
                $_SESSION['erro_cadastro_turma'] = "Erro ao cadastrar a turma: " . $e->getMessage();
            }
        }
    }
}
?>
<script>alert("TURMA CADASTRADA COM SUCESSO!!!")

window.location.href = "index.php";</script>

<?php

// Redireciona de volta para a página de cadastro de turma
//header("Location: index.php");
exit();
?>