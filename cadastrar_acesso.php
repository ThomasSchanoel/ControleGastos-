<?php

if(count($_POST) > 0){

    include("lib/conexao.php");
    include("lib/mail.php");

    $erro = false;
    $id = $_POST['id'];
    $email = $_POST['email'];
    $senhaDescriptografada = $_POST['senha'];

    if(strlen($_POST['senha']) < 6 && strlen($senhaDescriptografada) > 16){
        echo "<p><b>A senha deve ter entre 6 e 16 caracteres!</b></p>";
        $erro = true;
    }

    if(empty($id)) {
        echo "<p><b>Preencha o id!</b></p>";
        $erro = true;
    }else{
        $sql_id = "SELECT id FROM usuarios";
            $query_id = $mysqli->query($sql_id) or die($mysqli->error);
            while($compra = $query_id->fetch_assoc()){
                if($id == $compra['id']){
                    echo "<p><b>Este id já está cadastrado!</b></p>";
                    $erro = true;
                    break;
                }
            }
    }

    if(empty($email)) {
        echo "<p><b>Preencha o e-mail!</b></p>";
        $erro = true;
    } else {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "O endereço de email é considerado inválido.";
            $erro = true;
        }
    }
    

    if(!$erro){
        $senha = password_hash($senhaDescriptografada, PASSWORD_DEFAULT);
        $sql_code = "INSERT INTO usuarios (id, senha) VALUES ('$id', '$senha')";
        $itsAllRight = $mysqli->query($sql_code) or die($mysqli->error);
        if($itsAllRight) {

            $assunto = "Sua conta foi criada!!";
            $mensagem = "<h1>Uhuuuul!!</h1>
            <p>Seu acesso foi criado com sucesso!!</p>
            <p>
                <b>ID:</b> $id<br>
                <b>Senha:</b> $senhaDescriptografada
            </p>
            <p>Para fazer login, acesse <a href='https://tschanoel.com.br/controlegastos/login.php'>aqui.</a></p>
            <p>Por favor, respeite o prazo de produção do seu site. </p>
            <p>Atenciosamente, <br>Equipe TSchanoel</p>";
            enviarEmail("$email", $assunto, $mensagem, $nomeDestinatario = '', $anexo = null);

            $assunto = "Conta criada!!";
            $mensagem = "<h1>Atenção!!</h1>
            <p>Um acesso foi criado com sucesso!!</p>
            <p>
                <b>ID:</b> $id<br>
                <b>Senha:</b> $senhaDescriptografada
            </p>
            <p> Data: " . date('d/m/Y H:i:s') . "</p>";
            enviarEmail("schanoel500@gmail.com", $assunto, $mensagem, $nomeDestinatario = '', $anexo = null);


            echo "<p><b>Acesso cadastrado com sucesso!</b></p>";
            unset($_POST);
        }
    }
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar acesso</title>
</head>
<body>
    <h1>Cadastrar acesso</h1>
    
    <form method="POST" action="">
        <p>
            <label>ID:</label>
            <input value="<?php if(isset($_POST['id'])) echo $_POST['id']; ?>" name="id" type="text" placeholder="Escolha ID para acesso ao sistema">
        </p>
        <p>
            <label>Senha:</label>
            <input value="<?php if(isset($_POST['senha'])) echo $_POST['senha']; ?>" name="senha" type="password">
        </p>
        <p>
            <label>E-mail:</label>
            <input value="<?php if(isset($_POST['email'])) echo $_POST['email']; ?>" name="email" type="text">
        </p>
        <p>
            <button type="submit">Salvar acesso</button>
        </p>
    </form>
   
</body>
</html>