<?php

include 'cnn.php';

$draw = isset($_POST['draw']) ? (int) $_POST['draw'] : 0;
$row = isset($_POST['start']) ? (int) $_POST['start'] : 0;
$rowperpage = isset($_POST['length']) ? (int) $_POST['length'] : 10;

$columnIndex = isset($_POST['order'][0]['column'])
    ? (int) $_POST['order'][0]['column'] : 0;

$columnName = isset($_POST['columns'][$columnIndex]['data'])
    ? $_POST['columns'][$columnIndex]['data'] : 'idmota';

// Colunas permitidas para ordenação
$validColumns = [
    'idmota',
    'matricula',
    'marca',
    'modelo',
    'preco',
    'estado'
];

if (!in_array($columnName, $validColumns, true)) {
    $columnName = 'idmota';
}

$columnSortOrder = isset($_POST['order'][0]['dir'])
    ? strtoupper($_POST['order'][0]['dir']) : 'ASC';

$columnSortOrder = $columnSortOrder === 'DESC' ? 'DESC' : 'ASC';

$searchValue = isset($_POST['search']['value'])
    ? trim($_POST['search']['value']) : '';

$searchArray = [];
$searchQuery = '';

if ($searchValue !== '') {
    $searchQuery = " AND (
        motas.idmota LIKE :idmota OR
        motas.matricula LIKE :matricula OR
        marca.marca LIKE :marca OR
        motas.modelo LIKE :modelo OR
        motas.preco LIKE :preco OR
        motas.estado LIKE :estado
    )";

    $searchArray = [
        ':idmota' => "%$searchValue%",
        ':matricula' => "%$searchValue%",
        ':marca' => "%$searchValue%",
        ':modelo' => "%$searchValue%",
        ':preco' => "%$searchValue%",
        ':estado' => "%$searchValue%"
    ];
}

// Total de registos sem pesquisa
/** @var PDO $pdo */
$stmt = $pdo->query(
    "SELECT COUNT(*) AS allcount FROM motas"
);

$records = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRecords = (int) $records['allcount'];

// Total de registos com pesquisa
$stmt = $pdo->prepare(
    "SELECT COUNT(*)
     FROM motas
     INNER JOIN marca
        ON motas.idmarca = marca.idmarca
     WHERE 1=1 " . 
     
     $searchQuery
);

$stmt->execute($searchArray);
$totalRecordwithFilter = (int) $stmt->fetchColumn();

// Obter as motas com a respetiva marca
$stmt = $pdo->prepare(
    "SELECT
        motas.idmota,
        motas.matricula,
        marca.marca AS marca,
        motas.modelo,
        motas.preco,
        motas.foto,
        motas.estado
     FROM motas
     INNER JOIN marca
        ON motas.idmarca = marca.idmarca
     WHERE 1=1 " . $searchQuery . "
     ORDER BY " . $columnName . " " . $columnSortOrder . "
     LIMIT :limit OFFSET :offset"
);

// Parâmetros de pesquisa
foreach ($searchArray as $key => $search) {
    $stmt -> bindValue($key, $search, PDO::PARAM_STR);
}

$stmt->bindValue(':limit', $rowperpage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $row, PDO::PARAM_INT);

$stmt->execute();
$empRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Preparar dados para o DataTables
$data = [];

foreach ($empRecords as $row) {
    $data[] = [
        'idmota' => $row['idmota'],
        'matricula' => $row['matricula'],
        'marca' => $row['marca'],
        'modelo' => $row['modelo'],
        'preco' => $row['preco'],
        'foto' => $row['foto'],
        'estado' => $row['estado']
    ];
}

// Resposta JSON
$response = [
    'draw' => $draw,
    'recordsTotal' => $totalRecords,
    'recordsFiltered' => $totalRecordwithFilter,
    'data' => $data
];
header('Content-Type: application/json; charset=utf-8');

echo json_encode($response);
