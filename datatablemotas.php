<?php
// Database Connection
include 'cnn.php';

// Reading value
$draw = isset($_POST['draw']) ? $_POST['draw'] : 0; // valor padrão 0 caso 'draw' não exista
$row = isset($_POST['start']) ? $_POST['start'] : 0; // valor padrão 0
$rowperpage = isset($_POST['length']) ? $_POST['length'] : 10; // valor padrão 10
$columnIndex = isset($_POST['order'][0]['column']) ? $_POST['order'][0]['column'] : 0; // valor padrão 0
$columnName = isset($_POST['columns'][$columnIndex]['data']) ? $_POST['columns'][$columnIndex]['data'] : 'idmota'; // valor padrão

// Lista de colunas válidas para ORDER BY
$validColumns = ['idmota', 'matricula', 'idmarca', 'modelo', 'preco'];

// Verifique se o valor de $columnName é válido
if (!in_array($columnName, $validColumns)) {
    $columnName = 'idmota'; // Valor padrão em caso de falha
}

$columnSortOrder = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc'; // valor padrão 'asc'
$searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : ''; // valor padrão vazio
$searchArray = array();

// Search
$searchQuery = " ";
if($searchValue != '') {
    $searchQuery = " AND (
           idmota LIKE :idmota OR 
           matricula LIKE :matricula OR
           idmarca LIKE :idmarca OR 
           modelo LIKE :modelo OR
           preco LIKE :preco)";

    $searchArray = array(
        'idmota' => "%$searchValue%",
        'matricula' => "%$searchValue%",
        'idmarca' => "%$searchValue%",
        'modelo' => "%$searchValue%",
        'preco' => "%$searchValue%"
    );
}

// Total number of records without filtering
$stmt = $pdo->prepare("SELECT COUNT(*) AS allcount FROM motas");
$stmt->execute();
$records = $stmt->fetch();
$totalRecords = $records['allcount'];

// Total number of records with filtering
$stmt = $pdo->prepare("SELECT COUNT(*) AS allcount FROM motas WHERE 1 ".$searchQuery);
$stmt->execute($searchArray);
$records = $stmt->fetch();
$totalRecordwithFilter = $records['allcount'];

// Fetch records
$stmt = $pdo->prepare("SELECT * FROM motas WHERE 1 ".$searchQuery." ORDER BY ".$columnName." ".$columnSortOrder." LIMIT :limit OFFSET :offset");

// Bind values
foreach ($searchArray as $key => $search) {
    $stmt->bindValue(':'.$key, $search, PDO::PARAM_STR);
}

$stmt->bindValue(':limit', (int)$rowperpage, PDO::PARAM_INT);  
$stmt->bindValue(':offset', (int)$row, PDO::PARAM_INT);  
$stmt->execute();
$empRecords = $stmt->fetchAll();

$data = array();

foreach ($empRecords as $row) {
    $data[] = array(
        "idmota" => $row['idmota'],
        "matricula" => $row['matricula'],
        "idmarca" => $row['idmarca'],
        "modelo" => $row['modelo'],  
        "preco" => $row['preco'],
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
