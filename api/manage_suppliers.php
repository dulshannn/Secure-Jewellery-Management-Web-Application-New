<?php
include __DIR__ . '/../db_pg.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

// LIST (GET)
if ($method === 'GET') {
    $result = pg_query($db, "SELECT * FROM suppliers ORDER BY supplier_id DESC");
    echo json_encode(pg_fetch_all($result) ?: []);
}

// ADD (POST)
if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $query = "INSERT INTO suppliers (company_name, contact_person, email, phone) VALUES ($1, $2, $3, $4)";
    $result = pg_query_params($db, $query, [$data['name'], $data['contact'], $data['email'], $data['phone']]);
    echo json_encode(["status" => $result ? "success" : "error"]);
}

// UPDATE (PUT) - Task 1.1.3
if ($method === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);
    $query = "UPDATE suppliers SET company_name=$1, contact_person=$2, email=$3, phone=$4 WHERE supplier_id=$5";
    $result = pg_query_params($db, $query, [$data['name'], $data['contact'], $data['email'], $data['phone'], $data['id']]);
    echo json_encode(["status" => $result ? "success" : "error"]);
}

// DELETE (DELETE) - Task 1.1.4
if ($method === 'DELETE') {
    $id = $_GET['id'];
    $query = "DELETE FROM suppliers WHERE supplier_id = $1";
    $result = pg_query_params($db, $query, [$id]);
    echo json_encode(["status" => $result ? "success" : "error"]);
}
?>