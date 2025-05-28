<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';

    switch ($action) {
        case 'login':
            $password = $input['password'] ?? '';
            if (verifyPassword($password)) {
                $_SESSION['admin_logged_in'] = true;
                echo json_encode(['success' => true, 'message' => 'Úspěšně přihlášen']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Nesprávné heslo']);
            }
            break;

        case 'logout':
            session_destroy();
            echo json_encode(['success' => true, 'message' => 'Úspěšně odhlášen']);
            break;

        case 'get_cenik':
            try {
                $stmt = $pdo->query("SELECT * FROM cenik ORDER BY id");
                $cenik = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'data' => $cenik]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Chyba při načítání ceníku']);
            }
            break;

        case 'add_item':
            if (!isLoggedIn()) {
                echo json_encode(['success' => false, 'message' => 'Nejste přihlášen']);
                break;
            }
            
            $sluzba = $input['sluzba'] ?? '';
            $cena = $input['cena'] ?? '';
            
            if (empty($sluzba) || empty($cena)) {
                echo json_encode(['success' => false, 'message' => 'Vyplňte všechna pole']);
                break;
            }
            
            try {
                $stmt = $pdo->prepare("INSERT INTO cenik (sluzba, cena) VALUES (?, ?)");
                $stmt->execute([$sluzba, $cena]);
                $id = $pdo->lastInsertId();
                echo json_encode(['success' => true, 'message' => 'Položka přidána', 'id' => $id]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Chyba při přidávání položky']);
            }
            break;

        case 'update_item':
            if (!isLoggedIn()) {
                echo json_encode(['success' => false, 'message' => 'Nejste přihlášen']);
                break;
            }
            
            $id = $input['id'] ?? 0;
            $sluzba = $input['sluzba'] ?? '';
            $cena = $input['cena'] ?? '';
            
            if (empty($sluzba) || empty($cena) || $id <= 0) {
                echo json_encode(['success' => false, 'message' => 'Neplatná data']);
                break;
            }
            
            try {
                $stmt = $pdo->prepare("UPDATE cenik SET sluzba = ?, cena = ? WHERE id = ?");
                $stmt->execute([$sluzba, $cena, $id]);
                echo json_encode(['success' => true, 'message' => 'Položka upravena']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Chyba při úpravě položky']);
            }
            break;

        case 'delete_item':
            if (!isLoggedIn()) {
                echo json_encode(['success' => false, 'message' => 'Nejste přihlášen']);
                break;
            }
            
            $id = $input['id'] ?? 0;
            
            if ($id <= 0) {
                echo json_encode(['success' => false, 'message' => 'Neplatné ID']);
                break;
            }
            
            try {
                $stmt = $pdo->prepare("DELETE FROM cenik WHERE id = ?");
                $stmt->execute([$id]);
                echo json_encode(['success' => true, 'message' => 'Položka smazána']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Chyba při mazání položky']);
            }
            break;
            
            case 'update_order':
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Nejste přihlášen']);
        break;
    }
    
    $items = $input['items'] ?? [];
    
    if (empty($items)) {
        echo json_encode(['success' => false, 'message' => 'Žádná data']);
        break;
    }
    
    try {
        $pdo->beginTransaction();
        
        foreach ($items as $item) {
            $stmt = $pdo->prepare("UPDATE cenik SET display_order = ? WHERE id = ?");
            $stmt->execute([$item['order'], $item['id']]);
        }
        
        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Pořadí aktualizováno']);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Chyba při aktualizaci pořadí']);
    }
    break;

        default:
            echo json_encode(['success' => false, 'message' => 'Neplatná akce']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Nepodporovaná metoda']);
}
?>