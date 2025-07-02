<?php
if (isset($_POST['enviar'])) {
    // Diretório onde a imagem será salva
    $diretorio = 'uploads/';

    // Verifica se o diretório de uploads existe, senão cria
    if (!is_dir($diretorio)) {
        mkdir($diretorio, 0777, true);
    }

    // A variável $_FILES contém as informações do arquivo enviado
    $imagem = $_FILES['imagem'];

    // Obtém o nome do arquivo e a extensão
    $nome_imagem = basename($imagem['name']);
    $extensao = strtolower(pathinfo($nome_imagem, PATHINFO_EXTENSION));

    // Define os tipos de arquivo permitidos
    $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif'];

    // Verifica se a extensão do arquivo é permitida
    if (in_array($extensao, $extensoes_permitidas)) {
        // Define o caminho completo para salvar a imagem
        $caminho_imagem = $diretorio . $nome_imagem;

        // Verifica se houve algum erro no envio
        if ($imagem['error'] == 0) {
            // Move a imagem para o diretório de uploads
            if (move_uploaded_file($imagem['tmp_name'], $caminho_imagem)) {
                echo "Imagem enviada com sucesso!";
            } else {
                echo "Erro ao salvar a imagem.";
            }
        } else {
            echo "Erro no upload da imagem.";
        }
    } else {
        echo "Somente arquivos com as extensões JPG, JPEG, PNG ou GIF são permitidos.";
    }
}
?>
