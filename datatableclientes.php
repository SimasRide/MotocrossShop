<?php
// Habilitar exibição de erros
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database Connection
include 'cnn.php';

// Verificar se a conexão com o banco foi bem-sucedida
if (!$pdo) {
    die("Erro na conexão com o banco de dados.");
}

// Reading value
$draw = isset($_POST['draw']) ? $_POST['draw'] : 0; 
$row = isset($_POST['start']) ? $_POST['start'] : 0; 
$rowperpage = isset($_POST['length']) ? $_POST['length'] : 10; 
$columnIndex = isset($_POST['order'][0]['column']) ? $_POST['order'][0]['column'] : 0; 
$columnName = isset($_POST['columns'][$columnIndex]['data']) ? $_POST['columns'][$columnIndex]['data'] : 'idcli'; 

// Lista de colunas válidas para ORDER BY
$validColumns = ['idcli', 'nome', 'datanasc', 'idade'];

// Verifique se o valor de $columnName é válido
if (!in_array($columnName, $validColumns)) {
    $columnName = 'idcli'; // Valor padrão em caso de falha
}

$columnSortOrder = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc'; 
$searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : ''; 
$searchArray = array();

// Search
$searchQuery = " ";
if($searchValue != '') {
    $searchQuery = " AND (
           idcli LIKE :idcli OR 
           nome LIKE :nome OR
           datanasc LIKE :datanasc OR 
           idade LIKE :idade)";

    $searchArray = array(
        'idcli' => "%$searchValue%",
        'nome' => "%$searchValue%",
        'datanasc' => "%$searchValue%",
        'idade' => "%$searchValue%"
    );
}

// Verificando a consulta para contar os registros
$stmt = $pdo->prepare("SELECT COUNT(*) AS allcount FROM clientes");
$stmt->execute();
$records = $stmt->fetch();
$totalRecords = $records['allcount'];

// Verificando a consulta para contar os registros filtrados
$stmt = $pdo->prepare("SELECT COUNT(*) AS allcount FROM clientes WHERE 1 ".$searchQuery);
$stmt->execute($searchArray);
$records = $stmt->fetch();
$totalRecordwithFilter = $records['allcount'];

// Fetch records
$stmt = $pdo->prepare("SELECT * FROM clientes WHERE 1 ".$searchQuery." ORDER BY ".$columnName." ".$columnSortOrder." LIMIT :limit OFFSET :offset");

// Bind values
foreach ($searchArray as $key => $search) {
    $stmt->bindValue(':'.$key, $search, PDO::PARAM_STR);
}

$stmt->bindValue(':limit', (int)$rowperpage, PDO::PARAM_INT);  
$stmt->bindValue(':offset', (int)$row, PDO::PARAM_INT);  
$stmt->execute();
$empRecords = $stmt->fetchAll();

// Depuração
// var_dump($empRecords);  // Imprimir os registros retornados

$data = array();

foreach ($empRecords as $row) {
    $data[] = array(
        "idcli" => $row['idcli'],
        "nome" => $row['nome'],
        "datanasc" => $row['datanasc'],
        "idade" => $row['idade']
    );
}

// Response
$response = array(
    "draw" => intval($draw),
    "iTotalRecords" => $totalRecords,
    "iTotalDisplayRecords" => $totalRecordwithFilter,
    "aaData" => $data
);

echo json_encode($response);
?>
