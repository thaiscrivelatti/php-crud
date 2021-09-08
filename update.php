<?php
// Inclui arquivo config
require_once "config.php";
 
// Define variaveis e inicializa com valor vazio
$nome = $endereco = $salario = "";
$nome_err = $endereco_err = $salario_err = "";
 
// Processa os formulario quando os dados sao submetidos
if(isset($_POST["id"]) && !empty($_POST["id"])){
    // Pega atributo escondido
    $id = $_POST["id"];
    
    // Valida nome
    $input_nome = trim($_POST["nome"]);
    if(empty($input_nome)){
        $nome_err = "Por favor, digite um nome.";
    } elseif(!filter_var($input_nome, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $nome_err = "Por favor, digite um nome válido.";
    } else{
        $nome = $input_nome;
    }
    
    // Valida endereco
    $input_endereco = trim($_POST["endereco"]);
    if(empty($input_endereco)){
        $endereco_err = "Por favor, digite um endereço.";     
    } else{
        $endereco = $input_endereco;
    }
    
    // Valida salario
    $input_salario = trim($_POST["salario"]);
    if(empty($input_salario)){
        $salario_err = "Por favor, digite um salário.";     
    } elseif(!ctype_digit($input_salario)){
        $salario_err = "Por favor, digite um valor positivo e inteiro.";
    } else{
        $salario = $input_salario;
    }
    
    // Valida erros do formulario antes de processar os dados
    if(empty($nome_err) && empty($endereco_err) && empty($salario_err)){
        // Prepara comando update
        $sql = "UPDATE funcionario SET nome=?, endereco=?, salario=? WHERE id=?";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Vincula variaveis com os parametros do comando
            mysqli_stmt_bind_param($stmt, "sssi", $param_nome, $param_endereco, $param_salario, $param_id);
            
            // Atribui parametros
            $param_nome = $nome;
            $param_endereco = $endereco;
            $param_salario = $salario;
            $param_id = $id;
            
            // Tentativa de execucao do comando
            if(mysqli_stmt_execute($stmt)){
                // Registros atualizados com sucesso. Redireciona para index.php
                header("location: index.php");
                exit();
            } else{
                echo "Oops! Alguma coisa deu errado. Por favor, tente novamente mais tarde.";
            }
        }
         
        // Fecha comando
        mysqli_stmt_close($stmt);
    }
    
    // Fecha conexao
    mysqli_close($link);
} else{
    // Valida a existencia do parametro id antes de processar os dados
    if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
        // Pega parametro da URL
        $id =  trim($_GET["id"]);
        
        // Prepara comando select
        $sql = "SELECT * FROM funcionario WHERE id = ?";
        if($stmt = mysqli_prepare($link, $sql)){
            // Vincula variaveis com os parametros do comando
            mysqli_stmt_bind_param($stmt, "i", $param_id);
            
            // Atribui parametros
            $param_id = $id;
            
            // Tentativa de execucao do comando
            if(mysqli_stmt_execute($stmt)){
                $result = mysqli_stmt_get_result($stmt);
    
                if(mysqli_num_rows($result) == 1){
                    /* Associa o resultado em um array. Como o resultado contem apenas uma
                    linha, nao eh preciso usar o comando while */
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    
                    // Recupera valor dos campos individualmente
                    $nome = $row["nome"];
                    $endereco = $row["endereco"];
                    $salario = $row["salario"];
                } else{
                    // URL nao contem parametro id valido. Redireciona para pagina de erro
                    header("location: error.php");
                    exit();
                }
                
            } else{
                echo "Oops! Alguma coisa deu errado. Por favor, tente novamente mais tarde.";
            }
        }
        
        // Fecha comando
        mysqli_stmt_close($stmt);
        
        // Fecha conexao
        mysqli_close($link);
    }  else{
        // URL nao contem parametro id. Redireciona para pagina de erro
        header("location: error.php");
        exit();
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Atualizar registro</title>
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
                    <h2 class="mt-5">Atualizar registro</h2>
                    <p>Por favor, edite os valores e salve para atualizar o registro do funcionário.</p>
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="nome" class="form-control <?php echo (!empty($nome_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $nome; ?>">
                            <span class="invalid-feedback"><?php echo $nome_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Address</label>
                            <textarea name="endereco" class="form-control <?php echo (!empty($endereco_err)) ? 'is-invalid' : ''; ?>"><?php echo $endereco; ?></textarea>
                            <span class="invalid-feedback"><?php echo $endereco_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Salary</label>
                            <input type="text" name="salario" class="form-control <?php echo (!empty($salario_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $salario; ?>">
                            <span class="invalid-feedback"><?php echo $salario_err;?></span>
                        </div>
                        <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="index.php" class="btn btn-secondary ml-2">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>