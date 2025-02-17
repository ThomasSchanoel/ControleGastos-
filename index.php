<?php 
if(isset($_POST['id']) && isset($_POST['senha'])){
    include("lib/conexao.php");
    $id = $mysqli->escape_string($_POST['id']);
    $senha = $_POST['senha'];

    $sql_code = "SELECT * FROM usuarios WHERE id = '$id'";
    $sql_query = $mysqli->query($sql_code) or die($mysqli->error);

    $error_message = "";

    $redirecionamentos = [
        "1605" => "thomas/index.php",
        "2606" => "bia/index.php"
    ];

    if($sql_query->num_rows == 0){
        $error_message = "O ID informado está incorreto.";
    } else{
        $usuario = $sql_query->fetch_assoc();
        if(!password_verify($senha, $usuario['senha'])){
            $error_message = "A senha está incorreta.";
        } else{
            if(!isset($_SESSION)){
                session_start();
            }
            $_SESSION['usuario'] = $usuario['id'];
            if (array_key_exists($usuario['id'], $redirecionamentos)) {
                header("Location: " . $redirecionamentos[$usuario['id']]);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <div class="login-container">
        <h1>Login</h1>
        <?php if(!empty($error_message)): ?>
            <div class="error-message"> <?php echo $error_message; ?> </div>
        <?php endif; ?>
        <form action="" method="POST">
            <div class="form-group">
                <label for="id">ID</label>
                <input value="<?php if(isset($_POST['id'])) echo $_POST['id']; ?>" type="text" id="id" name="id" placeholder="Digite seu ID" required>
            </div>
            <div class="form-group">
                <label for="senha">Senha</label>
                <input value="<?php if(isset($_POST['senha'])) echo $_POST['senha']; ?>" type="password" id="senha" name="senha" placeholder="Digite sua senha" required>
            </div>
            <button type="submit" class="btn-login">Entrar</button>
        </form>
    </div>
</body>
</html>

