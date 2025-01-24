<?php
include "./conexão/conexao.php";
include "./resposta/resposta.php";
include "./validações/validacoes.php";
include "./token/decode_token.php";
date_default_timezone_set('America/Sao_Paulo');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: *');

// Decodifica o token e verifica autorização
$token = decode_token($_POST['id']);
if (!$token || $token == "erro") {
    resposta(200, false, "Não autorizado");
    exit;
}

function validarEntradas($dados)
{
    $erros = [];

    if (empty($dados['nome'])) {
        $erros[] = "O campo nome é obrigatório.";
    }

    if (!empty($_FILES['image']['name']) && !validar_img($_FILES)) {
        $erros[] = "A imagem enviada é inválida.";
    }

    return $erros;
}

$erros = validarEntradas($_POST);
if (!empty($erros)) {
    resposta(200, false, implode(" ", $erros));
    exit;
}

$idUsuario = $token->id;
controla($_POST, $_FILES, $idUsuario);

function controla($dados, $arquivos, $idUsuario)
{
    $conexao = conecta_bd();
    if (!$conexao) {
        resposta(200, false, "Erro ao conectar ao servidor.");
        return;
    }

    try {
        // Insere novo livro e retorna o ID
        $stmt = $conexao->prepare('INSERT INTO livro_publi (user_id) VALUES (:user_id) RETURNING id');
        $stmt->bindParam(':user_id', $idUsuario);
        $stmt->execute();
        $idLivro = $stmt->fetchColumn();

        // Define diretório de destino
        $destino = "../livros/$idUsuario/" . $dados['nome'] . "_$idLivro/";
        if (!is_dir($destino) && !mkdir($destino, 0777, true)) {
            resposta(200, false, "Erro ao criar diretório para o livro.");
            return;
        }

        // Atualiza campos opcionais
        salvaNome($conexao, $idLivro, $dados['nome']);
        if (!empty($dados['classificacao'])) {
            salvaClasse($conexao, $idLivro, $dados['classificacao']);
        }

        if (!empty($dados['selecao'])) {
            salaGen($conexao, $idLivro, $dados['selecao']);
        }

        if (!empty($arquivos['image']['tmp_name'])) {
            salvaFoto($conexao, $idLivro, $arquivos, $destino);
        }

        salvaFim($conexao, $idLivro);
        resposta(200, true, $idLivro);

    } catch (PDOException $e) {
        resposta(200, false, "Erro no banco de dados: " . $e->getMessage());
    }
}

function salvaNome($conexao, $idLivro, $nome)
{
    $stmt = $conexao->prepare('UPDATE livro_publi SET nome = :nome WHERE id = :id');
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':id', $idLivro);
    $stmt->execute();
}

function salvaClasse($conexao, $idLivro, $classificacao)
{
    $stmt = $conexao->prepare('UPDATE livro_publi SET classificacao = :classificacao WHERE id = :id');
    $stmt->bindParam(':classificacao', $classificacao);
    $stmt->bindParam(':id', $idLivro);
    $stmt->execute();
}

function salaGen($conexao, $idLivro, $selecao)
{
    $generos = json_encode(array_keys(array_filter(json_decode($selecao, true))));
    $stmt = $conexao->prepare('UPDATE livro_publi SET genero = :genero WHERE id = :id');
    $stmt->bindParam(':genero', $generos);
    $stmt->bindParam(':id', $idLivro);
    $stmt->execute();
}

function salvaFoto($conexao, $idLivro, $arquivos, $destino)
{
    $extensao = pathinfo($arquivos['image']['name'], PATHINFO_EXTENSION);
    $nomeUnico = $idLivro . '_' . time() . '.' . $extensao;

    if (move_uploaded_file($arquivos['image']['tmp_name'], $destino . $nomeUnico)) {
        $stmt = $conexao->prepare('UPDATE livro_publi SET imagem = :imagem WHERE id = :id');
        $stmt->bindParam(':imagem', $nomeUnico);
        $stmt->bindParam(':id', $idLivro);
        $stmt->execute();
    } else {
        resposta(200, false, "Erro ao salvar a imagem.");
    }
}

function salvaFim($conexao, $idLivro)
{
    $data = date('Y-m-d H:i:s');
    $textoVazio = json_encode([]);
    $stmt = $conexao->prepare('UPDATE livro_publi SET tempo = :tempo, texto = :texto, pronto = :pronto WHERE id = :id');
    $stmt->bindParam(':tempo', $data);
    $stmt->bindParam(':texto', $textoVazio);
    $stmt->bindParam(':pronto', $textoVazio);
    $stmt->bindParam(':id', $idLivro);
    $stmt->execute();
}

?>