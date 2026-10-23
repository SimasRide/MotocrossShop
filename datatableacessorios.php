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
$columnName = isset($_POST['columns'][$columnIndex]['data']) ? $_POST['columns'][$columnIndex]['data'] : 'id_acessorio'; 

// Lista de colunas válidas para ORDER BY
$validColumns = ['id_acessorio', 'equipamentos', 'marcas', 'preco']; // renomeado de 'preço' para 'preco'

// Verifique se o valor de $columnName é válido
if (!in_array($columnName, $validColumns)) {
    $columnName = 'id_acessorio'; // Valor padrão em caso de falha
}

$columnSortOrder = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc'; 
$searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : ''; 
$searchArray = array();

// Search
$searchQuery = " ";
if($searchValue != '') {
    $searchQuery = " AND (
           id_acessorio LIKE :id_acessorio OR 
           equipamentos LIKE :equipamentos OR
           marcas LIKE :marcas OR 
           preco LIKE :preco)"; 
           
    $searchArray = array(
        'id_acessorio' => "%$searchValue%",
        'equipamentos' => "%$searchValue%",
        'marcas' => "%$searchValue%",
        'preco' => "%$searchValue%" // atualizado para 'preco'
    );
}

// Verificando a consulta para contar os registros
$stmt = $pdo->prepare("SELECT COUNT(*) AS allcount FROM acessorios");
$stmt->execute();
$records = $stmt->fetch();
$totalRecords = $records['allcount'];

// Verificando a consulta para contar os registros filtrados
$stmt = $pdo->prepare("SELECT COUNT(*) AS allcount FROM acessorios WHERE 1 ".$searchQuery);
$stmt->execute($searchArray);
$records = $stmt->fetch();
$totalRecordwithFilter = $records['allcount'];

// Fetch records
$stmt = $pdo->prepare("SELECT * FROM acessorios WHERE 1 ".$searchQuery." ORDER BY `".$columnName."` ".$columnSortOrder." LIMIT :limit OFFSET :offset"); // uso de aspas invertidas para a coluna com nome variável

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
        "id_acessorio" => $row['id_acessorio'],
        "equipamentos" => $row['equipamentos'],
        "marcas" => $row['marcas'],
        "preco" => $row['preco'] 
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
    