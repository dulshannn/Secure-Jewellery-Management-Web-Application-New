<?php
include __DIR__ . '/../db_pg.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_id = $_POST['supplier_id'];
    $jewellery_type = $_POST['jewellery_type'];
    $qty = intval($_POST['quantity']);
    
    // File Upload Logic
    $file_path = null;
    if (isset($_FILES['invoice']) && $_FILES['invoice']['error'] == 0) {
        $upload_dir = __DIR__ . '/../uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $file_name = time() . '_' . basename($_FILES['invoice']['name']);
        if (move_uploaded_file($_FILES['invoice']['tmp_name'], $upload_dir . $file_name)) {
            $file_path = 'uploads/' . $file_name;
        }
    }

    // BEGIN TRANSACTION
    pg_query($db, "BEGIN");

    try {
        // 1. Create Delivery Record
        $q1 = "INSERT INTO deliveries (supplier_id, jewellery_type, quantity_received, invoice_path) VALUES ($1, $2, $3, $4)";
        $r1 = pg_query_params($db, $q1, [$supplier_id, $jewellery_type, $qty, $file_path]);
        if (!$r1) throw new Exception("Failed to save delivery.");

        // 2. Update Stock (Task 2.3.1 & 2.3.2)
        $q2 = "UPDATE jewellery SET current_quantity = current_quantity + $1 WHERE type = $2";
        $r2 = pg_query_params($db, $q2, [$qty, $jewellery_type]);
        // If item doesn't exist, handle error or insert new (Assuming exists for now)
        if (pg_affected_rows($r2) == 0) throw new Exception("Jewellery Type not found in Inventory.");

        // 3. Update Supplier Score (Bonus)
        pg_query_params($db, "UPDATE suppliers SET supplier_score = supplier_score + 10 WHERE supplier_id = $1", [$supplier_id]);

        // 4. Generate Stock Log (Task 2.3.3)
        $q3 = "INSERT INTO stock_logs (jewellery_type, change_amount, action_type) VALUES ($1, $2, 'DELIVERY')";
        pg_query_params($db, $q3, [$jewellery_type, $qty]);

        pg_query($db, "COMMIT");
        echo json_encode(["status" => "success", "message" => "Stock Updated Successfully!"]);

    } catch (Exception $e) {
        pg_query($db, "ROLLBACK");
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}
?>