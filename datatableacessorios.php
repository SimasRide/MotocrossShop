
<?php

header('Content-Type: application/json; charset=utf-8');

require_once 'cnn.php';

try {
    // Validar parâmetros do DataTables
    $draw = isset($_POST['draw']) ? max(0, (int) $_POST['draw']) : 0;

    $start = isset($_POST['start']) ? (int) $_POST['start'] : 0;
    $start = max(0, $start);

    $length = isset($_POST['length']) ? (int) $_POST['length'] : 10;

    // Impedir paginações excessivas
    if ($length < 1) {
        $length = 10;
    }

    $length = min($length, 100);

    // Colunas que podem ser utilizadas na ordenação
    $validColumns = [
        'id_acessorio',
        'equipamentos',
        'marcas',
        'preço'
    ];

    $columnIndex = isset($_POST['order'][0]['column'])
        ? (int) $_POST['order'][0]['column']
        : 0;

    $columnName = $validColumns[0];

    if (
        isset($_POST['columns'][$columnIndex]['data']) &&
        in_array($_POST['columns'][$columnIndex]['data'], $validColumns, true)
    ) {
        $columnName = $_POST['columns'][$columnIndex]['data'];
    }

    // Validar direção da ordenação
    $columnSortOrder = isset($_POST['order'][0]['dir'])
        ? strtolower($_POST['order'][0]['dir'])
        : 'asc';

    if (!in_array($columnSortOrder, ['asc', 'desc'], true)) {
        $columnSortOrder = 'asc';
    }

    // Validar pesquisa
    $searchValue = isset($_POST['search']['value'])
        ? trim((string) $_POST['search']['value'])
        : '';

    // Limitar o tamanho da pesquisa
    $searchValue = mb_substr($searchValue, 0, 100, 'UTF-8');

    $searchQuery = '';
    $searchArray = [];

    if ($searchValue !== '') {
        $searchQuery = "
            AND (
                id_acessorio LIKE :id_acessorio OR
                equipamentos LIKE :equipamentos OR
                marcas LIKE :marcas OR
                preço LIKE :preço       
            )
        ";

        $searchTerm = '%' . $searchValue . '%';

        $searchArray = [
            ':id_acessorio' => $searchTerm,
            ':equipamentos' => $searchTerm,
            ':marcas' => $searchTerm,
            ':preço' => $searchTerm
        ];
    }

    // Total de registos
    $stmt = $pdo->prepare(
        "SELECT COUNT(*) AS allcount FROM acessorios"
    );

    $stmt->execute();
    $totalRecords = (int) $stmt->fetchColumn();

    // Total de registos após a pesquisa
    $stmt = $pdo->prepare(
        "SELECT COUNT(*) FROM acessorios WHERE 1=1 $searchQuery"
    );

    $stmt->execute($searchArray);
    $totalRecordwithFilter = (int) $stmt->fetchColumn();

    // Obter os registos
    // A coluna e a direção são seguras porque foram validadas por listas permitidas.
    $sql = "
        SELECT *
        FROM acessorios
        WHERE 1=1 $searchQuery
        ORDER BY `$columnName` $columnSortOrder
        LIMIT :limit OFFSET :offset
    ";

    $stmt = $pdo->prepare($sql);

    foreach ($searchArray as $key => $value) {
        $stmt->bindValue($key, $value, PDO::PARAM_STR);
    }

    $stmt->bindValue(':limit', $length, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $start, PDO::PARAM_INT);

    $stmt->execute();

    $empRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Preparar resposta
    $data = [];

    foreach ($empRecords as $row) {
        $data[] = [
            'id_acessorio' => $row['id_acessorio'],
            'equipamentos' => $row['equipamentos'],
            'marcas' => $row['marcas'],
            'preço' => $row['preço'] 
        ];
    }

$draw = isset($_POST['draw'])
    ? (int) $_POST['draw']
    : 0;

foreach ($data as &$row) {
    foreach ($row as &$value) {
        if (is_string($value)) {
            $value = htmlspecialchars(
                $value,
                ENT_QUOTES | ENT_SUBSTITUTE,
                'UTF-8'
            );
        }
    }
    unset($value);
}
unset($row);

header('Content-Type: application/json; charset=utf-8');

echo json_encode([
    'draw' => $draw,
    'iTotalRecords' => (int) $totalRecords,
    'iTotalDisplayRecords' => (int) $totalRecordwithFilter,
    'aaData' => $data
], JSON_INVALID_UTF8_SUBSTITUTE);

} catch (Throwable $e) {
    error_log('Erro em datatableacessorios.php: ' . $e->getMessage());

    http_response_code(500);

    echo json_encode([
        'error' => 'Ocorreu um erro ao consultar os acessórios.'
    ]);
}
