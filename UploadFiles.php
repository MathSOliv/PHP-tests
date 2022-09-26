<?php

    include('conexao.php');

    if(isset($_GET['deletar'])) {

        $id = intval($_GET['deletar']);
        $result = mysqli_query($conexao, "SELECT * FROM arquivos WHERE idarquivos = '$id'");
        $arquivo = $result->fetch_assoc();

        if(unlink($arquivo['path'])) {
            $deu_certo = $result = mysqli_query($conexao, "DELETE FROM arquivos WHERE idarquivos = $id");
            if($deu_certo)
                echo "<p>Arquivo deletado com sucesso!</p>";
        }

    }

    function enviarArquivo($error, $size, $name, $tmp_name) {
        include('conexao.php');

        if($error)
            die("Falha ao enviar arquivo");

        if($size > 2097152)
            die("Arquivo muito grande!! Max: 2MB");

        $pasta = "arquivos/";
        $nomeDoArquivo = $name;
        $novoNomeDoArquivo = uniqid();
        $extensao = strtolower(pathinfo($nomeDoArquivo, PATHINFO_EXTENSION));

        if($extensao != "jpg" && $extensao != "png")
            die("Tipo de arquivo não aceito");

        $path = $pasta . $novoNomeDoArquivo . "." . $extensao;
        $deu_certo = move_uploaded_file($tmp_name, $path);
        if($deu_certo) {
            $result = mysqli_query($conexao, "INSERT INTO arquivos (nome, path, data_upload) VALUES ('$nomeDoArquivo', '$path', NOW());");        
            return true;
        }else
            return false;
    }
    

    if(isset($_FILES['arquivos'])) {
        $arquivos = $_FILES['arquivos'];
        $tudo_certo = true;
        foreach($arquivos['name'] as $index => $arq){
            $deu_certo = enviarArquivo($arquivos['error'][$index], $arquivos['size'][$index], $arquivos['name'][$index], $arquivos['tmp_name'][$index]);
            if(!$deu_certo)
                $tudo_certo = false;
        }
        if($tudo_certo)
            echo "<p>Todos os arquivos foram enviados com sucesso!</p>";
        else
            echo "<p>Falha ao enviar um ou mais arquivos!</p>";
    }

    $result = mysqli_query($conexao, "SELECT * FROM arquivos");

?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload</title>
</head>
<body>
    <form enctype="multipart/form-data" action="" method="post">
        <p><label for="">Selecione o arquivo</label></p>
        <input multiple name="arquivos[]" type="file">
        <button type="submit">Enviar arquivo</button>
    </form>


    <table border="1" cellpading="10">
        <thead>
            <th>Preview</th>
            <th>Arquivo</th>
            <th>Data de Envio</th>
            <th></th>
        </thead>
        <tbody>
            <?php
            while($arquivos = $result->fetch_assoc()){
            ?>
            <tr>
                <td><img height="50px" src="<?php echo $arquivos['path']; ?>"></td>
                <td><a target="_blank" href="<?php echo $arquivos['path']; ?>"><?php echo $arquivos['path']; ?></a></td>
                <td><?php echo date("d/m/Y H:i", strtotime($arquivos['data_upload'])) ; ?></td>
                <td><a href="imagem.php?deletar=<?php echo $arquivos['idarquivos']; ?>">Deletar</a></td>
            </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
</body>
</html>