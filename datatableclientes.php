
<?php
require_once __DIR__ . '/cnn.php';

function jsonResponse($data) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function clientData() {
    global $pdo;

    $name = trim($_POST['nome'] ?? '');
    $birth = $_POST['datanasc'] ?? '';

    $date = DateTime::createFromFormat('!Y-m-d', $birth);

    if ($name === '' || mb_strlen($name) > 100 ||
        !$date || $date->format('Y-m-d') !== $birth ||
        $date > new DateTime('today')) {
        jsonResponse(['success' => false, 'message' => 'Invalid name or date of birth.']);
    }

    return [
        'nome' => $name,
        'datanasc' => $birth,
        'idade' => $date->diff(new DateTime('today'))->y
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $action = $_POST['action'] ?? '';

        if ($action === 'add' || $action === 'edit') {
            $client = clientData();

            if ($action === 'add') {
                $sql = 'INSERT INTO clientes (nome, datanasc, idade)
                        VALUES (:nome, :datanasc, :idade)';
            } else {
                $client['idcli'] = (int)($_POST['idcli'] ?? 0);

                if ($client['idcli'] < 1) {
                    jsonResponse(['success' => false, 'message' => 'Invalid client ID.']);
                }

                $sql = 'UPDATE clientes
                        SET nome = :nome, datanasc = :datanasc, idade = :idade
                        WHERE idcli = :idcli';
            }
/** @var PDO $pdo */
$stmt = $pdo->prepare($sql); 
$stmt->execute($client);

            $stmt = $pdo->prepare($sql);
            $stmt->execute($client);

            jsonResponse(['success' => true, 'message' => 'Client saved successfully.']);
        }

        if ($action === 'delete') {
            /** @var PDO $pdo */
            $stmt = $pdo->prepare('DELETE FROM clientes WHERE idcli = ?');
            $stmt->execute([(int)($_POST['idcli'] ?? 0)]);

            jsonResponse(['success' => true, 'message' => 'Client deleted successfully.']);
        }

        // DataTables
        $columns = ['idcli', 'nome', 'datanasc', 'idade'];
        $index = (int)($_POST['order'][0]['column'] ?? 0);
        $column = $columns[$index] ?? 'idcli';
        $direction = strtolower($_POST['order'][0]['dir'] ?? 'asc') === 'desc' ? 'DESC' : 'ASC';
        $search = trim($_POST['search']['value'] ?? '');

        $where = '';
        $params = [];

        if ($search !== '') {
            $where = " WHERE CAST(idcli AS CHAR) LIKE ?
                       OR nome LIKE ? OR datanasc LIKE ?
                       OR CAST(idade AS CHAR) LIKE ?";
            $params = array_fill(0, 4, "%$search%");
        }
/** @var PDO $pdo */
        $total = (int)$pdo->query('SELECT COUNT(*) FROM clientes')->fetchColumn();

        $stmt = $pdo->prepare('SELECT COUNT(*) FROM clientes' . $where);
        $stmt->execute($params);
        $filtered = (int)$stmt->fetchColumn();

        $stmt = $pdo->prepare(
            "SELECT idcli, nome, datanasc, idade FROM clientes$where
             ORDER BY $column $direction LIMIT ? OFFSET ?"
        );

        foreach ($params as $i => $value) {
            $stmt->bindValue($i + 1, $value, PDO::PARAM_STR);
        }

        $stmt->bindValue(count($params) + 1, max(1, min(100, (int)($_POST['length'] ?? 10))), PDO::PARAM_INT);
        $stmt->bindValue(count($params) + 2, max(0, (int)($_POST['start'] ?? 0)), PDO::PARAM_INT);
        $stmt->execute();

        jsonResponse([
            'draw' => (int)($_POST['draw'] ?? 0),
            'iTotalRecords' => $total,
            'iTotalDisplayRecords' => $filtered,
            'aaData' => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ]);

    } catch (Throwable $e) {
        error_log($e->getMessage());
        jsonResponse(['success' => false, 'message' => 'A server error occurred.']);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Clients Management</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">
    

    <style>
        body {
            min-height: 100vh;
            background: url('enduro.png') center center / cover fixed no-repeat;
        }

        .container { padding-top: 35px; padding-bottom: 35px; }

      /* Panel slightly transparent */
/* Move the panel slightly down */
.panel {
    margin-top: 45px !important;
    padding: 30px !important;
    background: rgba(255, 255, 255, 0.55) !important;
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
}

/* Make table text larger and easier to read */
#clientsTable th,
#clientsTable td {
    font-size: 16px !important;
    color: #111 !important;
    padding: 14px 12px !important;
    font-weight: 500;
    border-color: rgba(0, 0, 0, 0.25) !important;
}

/* Make column headings stand out */
#clientsTable thead th {
    font-size: 17px !important;
    font-weight: 700 !important;
}

/* Improve the title */
.page-header h2 {
    font-size: 30px !important;
    color: #111 !important;
    font-weight: 700;
    margin-bottom: 25px;
}

/* Improve search and pagination readability */
.dataTables_wrapper {
    font-size: 15px !important;
    color: #111 !important;
}

.dataTables_wrapper input,
.dataTables_wrapper select {
    font-size: 15px !important;
    padding: 7px !important;
    background: rgba(255, 255, 255, 0.9) !important;
    color: #111 !important;
    border: 1px solid #888 !important;
}

/* Add space above the table */
#clientsTable {
    margin-top: 15px !important;
}
    </style>
</head>
<body>

<div class="container">
    <div class="panel">

        <div class="page-header">
            <a href="welcome.php" class="btn btn-secondary">← Back</a>
            <h2>Clients Management</h2>
            <button class="btn btn-primary" id="addBtn">+ Add Client</button>
        </div>

        <div id="message"></div>

        <div class="table-responsive">
            <table id="clientsTable" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Date of Birth</th>
                        <th>Age</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="clientModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="clientForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add Client</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="idcli">

                    <div class="form-group">
                        <label for="nome">Name</label>
                        <input class="form-control" id="nome" maxlength="100" required>
                    </div>

                    <div class="form-group">
                        <label for="datanasc">Date of Birth</label>
                        <input type="date" class="form-control" id="datanasc" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>

<script>
$(function () {
    const table = $('#clientsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: { url: 'datatableclientes.php', type: 'POST', dataSrc: 'aaData' },
        columns: [
            { data: 'idcli' },
            { data: 'nome' },
            { data: 'datanasc' },
            { data: 'idade' },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: (data, type, row) =>
                    `<button class="btn btn-sm btn-warning edit" data-id="${row.idcli}">Edit</button>
                     <button class="btn btn-sm btn-danger delete" data-id="${row.idcli}">Delete</button>`
            }
        ]
    });

    function message(text, type = 'success') {
        $('#message').html(`<div class="alert alert-${type}">${text}</div>`);
    }

    function request(data) {
        $.post('datatableclientes.php', data, function (res) {
            $('#clientModal').modal('hide');
            $('#clientForm')[0].reset();
            table.ajax.reload(null, false);
            message(res.message, res.success ? 'success' : 'danger');
        }, 'json').fail(function (xhr) {
            message(xhr.responseJSON?.message || 'Request failed.', 'danger');
        });
    }

    $('#addBtn').click(function () {
        $('#clientForm')[0].reset();
        $('#idcli').val('');
        $('#modalTitle').text('Add Client');
        $('#datanasc').attr('max', new Date().toISOString().slice(0, 10));
        $('#clientModal').modal('show');
    });

    $('#clientForm').submit(function (e) {
        e.preventDefault();

        const id = $('#idcli').val();

        request({
            action: id ? 'edit' : 'add',
            idcli: id,
            nome: $('#nome').val(),
            datanasc: $('#datanasc').val()
        });
    });

    $('#clientsTable').on('click', '.edit', function () {
        const row = table.row($(this).closest('tr')).data();

        $('#idcli').val(row.idcli);
        $('#nome').val(row.nome);
        $('#datanasc').val(row.datanasc).attr('max', new Date().toISOString().slice(0, 10));
        $('#modalTitle').text('Edit Client');
        $('#clientModal').modal('show');
    });

    $('#clientsTable').on('click', '.delete', function () {
        if (confirm('Are you sure you want to delete this client?')) {
            request({ action: 'delete', idcli: $(this).data('id') });
        }
    });
});
</script>

</body>
</html>
