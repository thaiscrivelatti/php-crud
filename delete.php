<?php
// Processa operacao de exclusao apos a confirmacao
if(isset($_POST["id"]) && !empty($_POST["id"])){
    // Inclui arquivo config
    require_once "config.php";
    
    // SQL de exclusao de registro
    $sql = "DELETE FROM funcionario WHERE id = ?";
    
    if($stmt = mysqli_prepare($link, $sql)){
        // Vincula as variaveis com os parametros
        mysqli_stmt_bind_param($stmt, "i", $param_id);
        
        // Atribui parametros 
        $param_id = trim($_POST["id"]);
        
        // Executa SQL
        if(mysqli_stmt_execute($stmt)){
            // Registros excluidos com sucesso. Redireciona para index.php
            header("location: index.php");
            exit();
        } else{
            echo "Oops! Algo deu errado. Tente novamente mais tarde.";
        }
    }
     
    mysqli_stmt_close($stmt);
    
    // Fecha conexao
    mysqli_close($link);
} else{
    // Procura a existencia do parametro id
    if(empty(trim($_GET["id"]))){
        // URL nao possui o parametro id. Redireciona para pagina error.php
        header("location: error.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Record</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .wrapper{
            width: 600px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mt-5 mb-3">Excluir registro</h2>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="alert alert-danger">
                            <input type="hidden" name="id" value="<?php echo trim($_GET["id"]); ?>"/>
                            <p>Tem certeza que deseja deletar esse registro?</p>
                            <p>
                                <input type="submit" value="Sim" class="btn btn-danger">
                                <a href="index.php" class="btn btn-secondary">Não</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>