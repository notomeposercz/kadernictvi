<?php
require_once 'config.php';

header('Content-Type: application/json');

// Kontrola připojení k databázi
if (!isDatabaseConnected()) {
    echo json_encode(['success' => false, 'message' => 'Databáze není dostupná']);
    exit;
}

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

        // === SPRÁVA SEKCE "O NÁS" ===
        
        case 'get_about':
            try {
                $stmt = $pdo->prepare("SELECT * FROM about_section LIMIT 1");
                $stmt->execute();
                $about = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$about) {
                    // Vytvoř výchozí záznam
                    $stmt = $pdo->prepare("INSERT INTO about_section (text, feature1_icon, feature1_text, feature2_icon, feature2_text, feature3_icon, feature3_text) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        'Moderní kadeřnické služby v příjemném prostředí. Sledujeme trendy, pečujeme o každého zákazníka individuálně.',
                        'fas fa-star',
                        'Moderní trendy',
                        'fas fa-user-friends',
                        'Individuální přístup',
                        'fas fa-spa',
                        'Příjemné prostředí'
                    ]);
                    $about = [
                        'text' => 'Moderní kadeřnické služby v příjemném prostředí. Sledujeme trendy, pečujeme o každého zákazníka individuálně.',
                        'feature1_icon' => 'fas fa-star',
                        'feature1_text' => 'Moderní trendy',
                        'feature2_icon' => 'fas fa-user-friends',
                        'feature2_text' => 'Individuální přístup',
                        'feature3_icon' => 'fas fa-spa',
                        'feature3_text' => 'Příjemné prostředí'
                    ];
                }
                
                echo json_encode(['success' => true, 'data' => $about]);
            } catch (Exception $e) {
                error_log("API Error - get_about: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Chyba při načítání sekce O nás']);
            }
            break;

        case 'save_about':
            if (!isLoggedIn()) {
                echo json_encode(['success' => false, 'message' => 'Nejste přihlášen']);
                break;
            }
            
            try {
                $text = trim($input['text'] ?? '');
                $feature1_icon = trim($input['feature1_icon'] ?? '');
                $feature1_text = trim($input['feature1_text'] ?? '');
                $feature2_icon = trim($input['feature2_icon'] ?? '');
                $feature2_text = trim($input['feature2_text'] ?? '');
                $feature3_icon = trim($input['feature3_icon'] ?? '');
                $feature3_text = trim($input['feature3_text'] ?? '');
                
                // Zkontroluj, zda záznam existuje
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM about_section");
                $stmt->execute();
                $exists = $stmt->fetchColumn() > 0;
                
                if ($exists) {
                    $stmt = $pdo->prepare("UPDATE about_section SET text = ?, feature1_icon = ?, feature1_text = ?, feature2_icon = ?, feature2_text = ?, feature3_icon = ?, feature3_text = ?, updated_at = NOW()");
                    $stmt->execute([$text, $feature1_icon, $feature1_text, $feature2_icon, $feature2_text, $feature3_icon, $feature3_text]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO about_section (text, feature1_icon, feature1_text, feature2_icon, feature2_text, feature3_icon, feature3_text) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$text, $feature1_icon, $feature1_text, $feature2_icon, $feature2_text, $feature3_icon, $feature3_text]);
                }
                
                echo json_encode(['success' => true, 'message' => 'Sekce O nás byla uložena']);
            } catch (Exception $e) {
                error_log("API Error - save_about: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Chyba při ukládání sekce O nás']);
            }
            break;

        // === SPRÁVA PRACOVNÍ DOBY ===

        case 'get_hours':
            try {
                $stmt = $pdo->prepare("SELECT * FROM working_hours ORDER BY display_order, id");
                $stmt->execute();
                $hours = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'data' => $hours]);
            } catch (Exception $e) {
                error_log("API Error - get_hours: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Chyba při načítání pracovní doby']);
            }
            break;

        case 'add_hours':
            if (!isLoggedIn()) {
                echo json_encode(['success' => false, 'message' => 'Nejste přihlášen']);
                break;
            }
            
            $day_name = trim($input['day_name'] ?? '');
            $time_range = trim($input['time_range'] ?? '');
            $is_closed = (int)($input['is_closed'] ?? 0);
            
            if (empty($day_name) || empty($time_range)) {
                echo json_encode(['success' => false, 'message' => 'Vyplňte všechna pole']);
                break;
            }
            
            try {
                $stmt = $pdo->prepare("INSERT INTO working_hours (day_name, time_range, is_closed) VALUES (?, ?, ?)");
                $stmt->execute([$day_name, $time_range, $is_closed]);
                $id = $pdo->lastInsertId();
                echo json_encode(['success' => true, 'message' => 'Pracovní doba přidána', 'id' => $id]);
            } catch (Exception $e) {
                error_log("API Error - add_hours: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Chyba při přidávání pracovní doby']);
            }
            break;

        case 'update_hours':
            if (!isLoggedIn()) {
                echo json_encode(['success' => false, 'message' => 'Nejste přihlášen']);
                break;
            }
            
            $id = (int)($input['id'] ?? 0);
            $day_name = trim($input['day_name'] ?? '');
            $time_range = trim($input['time_range'] ?? '');
            $is_closed = (int)($input['is_closed'] ?? 0);
            
            if (empty($day_name) || empty($time_range) || $id <= 0) {
                echo json_encode(['success' => false, 'message' => 'Neplatná data']);
                break;
            }
            
            try {
                $stmt = $pdo->prepare("UPDATE working_hours SET day_name = ?, time_range = ?, is_closed = ? WHERE id = ?");
                $stmt->execute([$day_name, $time_range, $is_closed, $id]);
                echo json_encode(['success' => true, 'message' => 'Pracovní doba upravena']);
            } catch (Exception $e) {
                error_log("API Error - update_hours: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Chyba při úpravě pracovní doby']);
            }
            break;

        case 'delete_hours':
            if (!isLoggedIn()) {
                echo json_encode(['success' => false, 'message' => 'Nejste přihlášen']);
                break;
            }
            
            $id = (int)($input['id'] ?? 0);
            
            if ($id <= 0) {
                echo json_encode(['success' => false, 'message' => 'Neplatné ID']);
                break;
            }
            
            try {
                $stmt = $pdo->prepare("DELETE FROM working_hours WHERE id = ?");
                $stmt->execute([$id]);
                echo json_encode(['success' => true, 'message' => 'Pracovní doba smazána']);
            } catch (Exception $e) {
                error_log("API Error - delete_hours: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Chyba při mazání pracovní doby']);
            }
            break;
            
        case 'update_hours_order':
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
                    if (isset($item['id']) && isset($item['order'])) {
                        $stmt = $pdo->prepare("UPDATE working_hours SET display_order = ? WHERE id = ?");
                        $stmt->execute([$item['order'], $item['id']]);
                    }
                }
                
                $pdo->commit();
                echo json_encode(['success' => true, 'message' => 'Pořadí pracovní doby aktualizováno']);
            } catch (Exception $e) {
                $pdo->rollBack();
                error_log("API Error - update_hours_order: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Chyba při aktualizaci pořadí']);
            }
            break;

        // === SPRÁVA NASTAVENÍ OBJEDNÁVEK ===

        case 'get_booking_settings':
            try {
                $stmt = $pdo->prepare("SELECT * FROM booking_settings LIMIT 1");
                $stmt->execute();
                $settings = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$settings) {
                    // Vytvoř výchozí nastavení
                    $stmt = $pdo->prepare("INSERT INTO booking_settings (email, subject, active) VALUES (?, ?, ?)");
                    $stmt->execute(['', 'Nová objednávka z webu', 1]);
                    $settings = [
                        'email' => '',
                        'subject' => 'Nová objednávka z webu',
                        'active' => 1
                    ];
                }
                
                echo json_encode(['success' => true, 'data' => $settings]);
            } catch (Exception $e) {
                error_log("API Error - get_booking_settings: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Chyba při načítání nastavení objednávek']);
            }
            break;

        case 'save_booking_settings':
            if (!isLoggedIn()) {
                echo json_encode(['success' => false, 'message' => 'Nejste přihlášen']);
                break;
            }
            
            try {
                $email = trim($input['email'] ?? '');
                $subject = trim($input['subject'] ?? '');
                $active = (int)($input['active'] ?? 1);
                
                // Zkontroluj, zda záznam existuje
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM booking_settings");
                $stmt->execute();
                $exists = $stmt->fetchColumn() > 0;
                
                if ($exists) {
                    $stmt = $pdo->prepare("UPDATE booking_settings SET email = ?, subject = ?, active = ?, updated_at = NOW()");
                    $stmt->execute([$email, $subject, $active]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO booking_settings (email, subject, active) VALUES (?, ?, ?)");
                    $stmt->execute([$email, $subject, $active]);
                }
                
                echo json_encode(['success' => true, 'message' => 'Nastavení objednávek bylo uloženo']);
            } catch (Exception $e) {
                error_log("API Error - save_booking_settings: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Chyba při ukládání nastavení objednávek']);
            }
            break;

        // === SPRÁVA KONTAKTŮ ===

        case 'get_contact':
            try {
                $stmt = $pdo->prepare("SELECT * FROM contact_info LIMIT 1");
                $stmt->execute();
                $contact = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$contact) {
                    // Vytvoř výchozí kontakt
                    $stmt = $pdo->prepare("INSERT INTO contact_info (business_name, address, phone, ico, map_url) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([
                        'Kadeřnictví Tereza Dvořáčková',
                        'Měnín 475',
                        '792 350 545',
                        '04652240',
                        'https://www.google.com/maps?q=Měnín+475&output=embed'
                    ]);
                    $contact = [
                        'business_name' => 'Kadeřnictví Tereza Dvořáčková',
                        'address' => 'Měnín 475',
                        'phone' => '792 350 545',
                        'ico' => '04652240',
                        'map_url' => 'https://www.google.com/maps?q=Měnín+475&output=embed'
                    ];
                }
                
                echo json_encode(['success' => true, 'data' => $contact]);
            } catch (Exception $e) {
                error_log("API Error - get_contact: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Chyba při načítání kontaktních údajů']);
            }
            break;

        case 'save_contact':
            if (!isLoggedIn()) {
                echo json_encode(['success' => false, 'message' => 'Nejste přihlášen']);
                break;
            }
            
            try {
                $business_name = trim($input['business_name'] ?? '');
                $address = trim($input['address'] ?? '');
                $phone = trim($input['phone'] ?? '');
                $ico = trim($input['ico'] ?? '');
                $map_url = trim($input['map_url'] ?? '');
                
                // Zkontroluj, zda záznam existuje
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM contact_info");
                $stmt->execute();
                $exists = $stmt->fetchColumn() > 0;
                
                if ($exists) {
                    $stmt = $pdo->prepare("UPDATE contact_info SET business_name = ?, address = ?, phone = ?, ico = ?, map_url = ?, updated_at = NOW()");
                    $stmt->execute([$business_name, $address, $phone, $ico, $map_url]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO contact_info (business_name, address, phone, ico, map_url) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$business_name, $address, $phone, $ico, $map_url]);
                }
                
                echo json_encode(['success' => true, 'message' => 'Kontaktní údaje byly uloženy']);
            } catch (Exception $e) {
                error_log("API Error - save_contact: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Chyba při ukládání kontaktních údajů']);
            }
            break;

        // === PŮVODNÍ SPRÁVA CENÍKU ===

        case 'get_cenik':
            try {
                $stmt = $pdo->prepare("SELECT * FROM cenik ORDER BY display_order, id LIMIT 50");
                $stmt->execute();
                $cenik = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'data' => $cenik]);
            } catch (Exception $e) {
                error_log("API Error - get_cenik: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Chyba při načítání ceníku']);
            }
            break;

        case 'add_item':
            if (!isLoggedIn()) {
                echo json_encode(['success' => false, 'message' => 'Nejste přihlášen']);
                break;
            }
            
            $sluzba = trim($input['sluzba'] ?? '');
            $cena = trim($input['cena'] ?? '');
            
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
                error_log("API Error - add_item: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Chyba při přidávání položky']);
            }
            break;

        case 'update_item':
            if (!isLoggedIn()) {
                echo json_encode(['success' => false, 'message' => 'Nejste přihlášen']);
                break;
            }
            
            $id = (int)($input['id'] ?? 0);
            $sluzba = trim($input['sluzba'] ?? '');
            $cena = trim($input['cena'] ?? '');
            
            if (empty($sluzba) || empty($cena) || $id <= 0) {
                echo json_encode(['success' => false, 'message' => 'Neplatná data']);
                break;
            }
            
            try {
                $stmt = $pdo->prepare("UPDATE cenik SET sluzba = ?, cena = ? WHERE id = ?");
                $stmt->execute([$sluzba, $cena, $id]);
                echo json_encode(['success' => true, 'message' => 'Položka upravena']);
            } catch (Exception $e) {
                error_log("API Error - update_item: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Chyba při úpravě položky']);
            }
            break;

        case 'delete_item':
            if (!isLoggedIn()) {
                echo json_encode(['success' => false, 'message' => 'Nejste přihlášen']);
                break;
            }
            
            $id = (int)($input['id'] ?? 0);
            
            if ($id <= 0) {
                echo json_encode(['success' => false, 'message' => 'Neplatné ID']);
                break;
            }
            
            try {
                $stmt = $pdo->prepare("DELETE FROM cenik WHERE id = ?");
                $stmt->execute([$id]);
                echo json_encode(['success' => true, 'message' => 'Položka smazána']);
            } catch (Exception $e) {
                error_log("API Error - delete_item: " . $e->getMessage());
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
                    if (isset($item['id']) && isset($item['order'])) {
                        $stmt = $pdo->prepare("UPDATE cenik SET display_order = ? WHERE id = ?");
                        $stmt->execute([$item['order'], $item['id']]);
                    }
                }
                
                $pdo->commit();
                echo json_encode(['success' => true, 'message' => 'Pořadí aktualizováno']);
            } catch (Exception $e) {
                $pdo->rollBack();
                error_log("API Error - update_order: " . $e->getMessage());
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