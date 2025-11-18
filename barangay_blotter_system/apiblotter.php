<?php
// api/blotter.php
require_once __DIR__.'connect.php';
require_once __DIR__.'helpers.php';

// expects authenticated session; you can also check role
if(empty($_SESSION['user_id'])){
    http_response_code(401);
    echo json_encode(['error'=>'Not logged in']);
    exit;
}

$action = $_REQUEST['action'] ?? '';

if($action === 'create'){
    // create complainant if not exist, respondent, then blotter
    $pdo->beginTransaction();
    try {
        // complainant
        $stmt = $pdo->prepare("INSERT INTO tbl_complainant (fullname,address,contact,age,sex) VALUES (?,?,?,?,?)");
        $stmt->execute([$_POST['complainant_name'], $_POST['complainant_address'], $_POST['complainant_contact'], $_POST['complainant_age'] ?: null, $_POST['complainant_sex']]);
        $complainant_id = $pdo->lastInsertId();

        // respondent
        $stmt = $pdo->prepare("INSERT INTO tbl_respondent (fullname,address,contact,age,sex) VALUES (?,?,?,?,?)");
        $stmt->execute([$_POST['respondent_name'], $_POST['respondent_address'], $_POST['respondent_contact'], $_POST['respondent_age'] ?: null, $_POST['respondent_sex']]);
        $respondent_id = $pdo->lastInsertId();

        // generate blotter_no (BB-YYYYmmdd-XXXX)
        $blotter_no = 'BB-'.date('Ymd').'-'.strtoupper(substr(bin2hex(random_bytes(3)),0,4));

        $stmt = $pdo->prepare("INSERT INTO tbl_blotter (blotter_no, reported_at, incident_type_id, location, complainant_id, respondent_id, incident_statement, action_taken, status, official_in_charge) VALUES (?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([
            $blotter_no,
            $_POST['reported_at'],
            $_POST['incident_type_id'] ?: null,
            $_POST['location'],
            $complainant_id,
            $respondent_id,
            $_POST['incident_statement'],
            $_POST['action_taken'],
            $_POST['status'] ?: 'ongoing',
            $_POST['official_in_charge'] ?: null
        ]);

        $pdo->commit();
        audit($pdo, $_SESSION['user_id'], "Created blotter: $blotter_no");
        echo json_encode(['success'=>true, 'blotter_no'=>$blotter_no]);
    } catch(Exception $e){
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
    }
    exit;
}

if($action === 'list'){
    // return list with joins
    $stmt = $pdo->query("SELECT b.*, it.type_name, c.fullname as complainant, r.fullname as respondent, u.fullname as official
        FROM tbl_blotter b
        LEFT JOIN incident_types it ON b.incident_type_id = it.id
        LEFT JOIN tbl_complainant c ON b.complainant_id = c.id
        LEFT JOIN tbl_respondent r ON b.respondent_id = r.id
        LEFT JOIN users u ON b.official_in_charge = u.id
        ORDER BY b.reported_at DESC
    ");
    $rows = $stmt->fetchAll();
    echo json_encode($rows);
    exit;
}

if($action === 'get' && isset($_GET['id'])){
    $stmt = $pdo->prepare("SELECT b.*, it.type_name, c.*, r.* FROM tbl_blotter b
        LEFT JOIN incident_types it ON b.incident_type_id = it.id
        LEFT JOIN tbl_complainant c ON b.complainant_id = c.id
        LEFT JOIN tbl_respondent r ON b.respondent_id = r.id
        WHERE b.id = ?");
    $stmt->execute([$_GET['id']]);
    echo json_encode($stmt->fetch());
    exit;
}

// Update and Delete would be similar - use prepared statements and check role
