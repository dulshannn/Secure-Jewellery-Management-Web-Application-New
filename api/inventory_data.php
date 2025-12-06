<?php
include __DIR__ . '/../db_pg.php';
header('Content-Type: application/json');

$type = $_GET['type'] ?? '';

if ($type === 'stock') {
    $res = pg_query($db, "SELECT * FROM jewellery ORDER BY type ASC");
    echo json_encode(pg_fetch_all($res) ?: []);
} elseif ($type === 'deliveries') {
    $q = "SELECT d.*, s.company_name FROM deliveries d 
          LEFT JOIN suppliers s ON d.supplier_id = s.supplier_id 
          ORDER BY d.delivery_date DESC";
    $res = pg_query($db, $q);
    echo json_encode(pg_fetch_all($res) ?: []);
}
?>