
<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.3/css/bootstrap.min.css" rel="stylesheet" />
    <style>
      /* Estilo para a barra de navegação */
      .navbar {
        background-color: #dc3545; /* Cor vermelha */
        padding: 10px 0;
      }

      .navbar a {
        color: white;
        padding: 10px 20px;
        text-decoration: none;
        font-weight: bold;
      }

      .navbar a:hover {
        background-color: #c82333; /* Cor de hover */
        border-radius: 5px;
      }

      /* Estilo para o conteúdo da página */
      .content {
        margin-top: 80px; /* Espaço para a barra de navegação */
      }

      /* Estilo para o título "MOTOCROSS SHOP" */
      .flame-title {
        font-size: 3rem;
        font-weight: bold;
        color: #333;
        text-align: center;
      }
    </style>
  </head>

  <body>
    <!-- Barra de Navegação -->
     
    <div class="row">
        <div class="col-md-12 text-center mt-5">
          <h1 class="flame-title">TABELAS</h1> 
      
    <nav class="navbar">
      <a href="datatablelogin.php">Login</a>
      <a href="datatableclientes.php">Clients</a>
      <a href="datatablemotas.php">Motorbikes</a>
      <a href="datatableacessorios.php">Accessories</a>
    </nav>

   
<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "lojademotas";
// Create a connection
$conn = mysqli_connect($servername, $username, $password,$database);

// Die if connection was not successful
if (!$conn){
    die("Sorry we failed to connect: ". mysqli_connect_error());
}
else{
session_start();

if(!isset($_SESSION['idlogin']) || $_SESSION['idlogin']!=true){
    header("location: datatableacessorios.php");
    exit;
}
}
?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    
    <title>Welcome - <?php echo $_SESSION['nome']?></title>
  </head>
  <body>
  
    <div class="container my-3">
    <div class="alert alert-success" role="alert-close" >
      <h4 class="alert-heading">Welcome - <?php echo $_SESSION['nome']?></h4>
      <p>Welcome to Sumeet's Login page. You are logged in as <?php echo $_SESSION['nome']?>. You successfully got into this page, i hope you have a good experience.</p>
      <hr>
      <p class="mb-0">Whenever you need to, be sure to logout <a href="/logout.php"> using this link.</a></p>
   

    </div>
  </div>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

  </body>
</html>



