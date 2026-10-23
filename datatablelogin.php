<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
    <link href="bootstrap-4.5.3-dist/css/bootstrap.css" rel="stylesheet" />
    <link href="estilos.css" rel="stylesheet" />
  </head>
  <body>
    <div class="container">
      <!--carousel-->
      <div class="row">
        <div class="col-12">
          <div
            id="carouselExampleIndicators"
            class="carousel slide"
            data-ride="carousel"
          >
            <ol class="carousel-indicators">
              <li
                data-target="#carouselExampleIndicators"
                data-slide-to="0"
                class="active"
              ></li>
              <li
                data-target="#carouselExampleIndicators"
                data-slide-to="1"
              ></li>
              <li
                data-target="#carouselExampleIndicators"
                data-slide-to="2"
              ></li>
            </ol>
            <div class="carousel-inner">
              <div class="carousel-item active">
                <img src="imagens/um.png" class="d-block w-100" alt="..." />
              </div>
              <div class="carousel-item">
                <img src="imagens/um.png" class="d-block w-100" alt="..." />
              </div>
              <div class="carousel-item">
                <img src="imagens/um.jfif" class="d-block w-100" alt="..." />
              </div>
            </div>
            <a
              class="carousel-control-prev"
              href="#carouselExampleIndicators"
              role="button"
              data-slide="prev"
            >
              <span
                class="carousel-control-prev-icon"
                aria-hidden="true"
              ></span>
              <span class="sr-only">Previous</span>
            </a>
            <a
              class="carousel-control-next"
              href="#carouselExampleIndicators"
              role="button"
              data-slide="next"
            >
              <span
                class="carousel-control-next-icon"
                aria-hidden="true"
              ></span>
              <span class="sr-only">Next</span>
            </a>
          </div>
        </div>
      </div>
   
      <div class="row">
        <div class="col-12">
          <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand" href="#">Navbar</a>
            <button
              class="navbar-toggler"
              type="button"
              data-toggle="collapse"
              data-target="#navbarSupportedContent"
              aria-controls="navbarSupportedContent"
              aria-expanded="false"
              aria-label="Toggle navigation"
            >
              <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                  <a class="nav-link" href="#"
                    >Home <span class="sr-only">(current)</span></a
                  >
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#">Link</a>
                </li>
                <li class="nav-item dropdown">
                  <a
                    class="nav-link dropdown-toggle"
                    href="#"
                    id="navbarDropdown"
                    role="button"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                  >
                    Dropdown
                  </a>
                  <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="#">Action</a>
                    <a class="dropdown-item" href="#">Another action</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#">Something else here</a>
                  </div>
                </li>
                <li class="nav-item">
                  <a
                    class="nav-link disabled"
                    href="#"
                    tabindex="-1"
                    aria-disabled="true"
                    >Disabled</a
                  >
                </li>
              </ul>
              <form class="form-inline my-2 my-lg-0">
                <input
                  class="form-control mr-sm-2"
                  type="search"
                  placeholder="Search"
                  aria-label="Search"
                />
                <button
                  class="btn btn-outline-success my-2 my-sm-0"
                  type="submit"
                >
                  Search
                </button>
              </form>
            </div>
          </nav>
        </div>
      </div>
   
          <fieldset>
            <legend>Formulário</legend>
            <form action="#" id="frm" name="frm">
              <div class="form-group">
                <label>Nome</label>
                <input type="text" class="form-control" />
              </div>
              <button type="button" class="btn btn-primary mt-2">Click</button>

             

                <table
                  class="table table-striped table-success table-bordered"
                  style="display: block; margin: auto"
                >
                 
             
    <script src="bootstrap-4.5.3-dist/js/jquery-3.7.1.js"></script>
    <script src="bootstrap-4.5.3-dist/js/bootstrap.js"></script>
    <script src="bootstrap-4.5.3-dist/js/bootstrap.bundle.js"></script>
  </body>
</html>

<?php
$data = '{
  "draw": 0,
  "iTotalRecords": "4",
  "iTotalDisplayRecords": "4",
  "aaData": [
    {"idlogin": "1", "nome": "Baltazar Ferreira", "email": "baltazar.ferreira@gmail.com", "senha": "Balta"},
    {"idlogin": "2", "nome": "Belchior Oliveira", "email": "belchior.oliveira@gmail.com", "senha": "Belch"},
    {"idlogin": "3", "nome": "Gaspar Dias", "email": "gaspar.dias@gmail.com", "senha": "Gasp"},
    {"idlogin": "4", "nome": "Ze Manel", "email": "ze.manel@gmail.com", "senha": "Zeman"}
  ]
}';

$dataArray = json_decode($data, true);

if ($dataArray && isset($dataArray['aaData']) && is_array($dataArray['aaData'])) {
    $users = $dataArray['aaData'];
} else {

    $users = [];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tabela de Usuários</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.3/css/bootstrap.min.css" rel="stylesheet" />
    <style>
      .table-container {
        margin-top: 50px;
      }
    </style>
  </head>
  <body>
    <div class="container table-container">
      <h2 class="text-center">Tabela de Login</h2>
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>ID Login</th>
            <th>Nome</th>
            <th>Email</th>
            <th>Senha</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($users)): ?>
            <?php foreach ($users as $user): ?>
              <tr>
                <td><?php echo htmlspecialchars($user['idlogin']); ?></td>
                <td><?php echo htmlspecialchars($user['nome']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['senha']); ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="4" class="text-center">Nenhum usuário encontrado.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </body>
</html>

<?php

$data = '{
  "draw": 0,
  "iTotalRecords": "4",
  "iTotalDisplayRecords": "4",
  "aaData": [
    {"idlogin": "1", "nome": "Baltazar Ferreira", "email": "baltazar.ferreira@gmail.com", "senha": "Balta"},
    {"idlogin": "2", "nome": "Belchior Oliveira", "email": "belchior.oliveira@gmail.com", "senha": "Belch"},
    {"idlogin": "3", "nome": "Gaspar Dias", "email": "gaspar.dias@gmail.com", "senha": "Gasp"},
    {"idlogin": "4", "nome": "Ze Manel", "email": "ze.manel@gmail.com", "senha": "Zeman"}
  ]
}';


$dataArray = json_decode($data, true);


if($dataArray && isset($dataArray['aaData'])) {
    $users = $dataArray['aaData'];
} else {
    $users = [];
}
?>
