<?php 
require_once '../config.php';

// Kontrola přihlášení - pokud není přihlášen, přesměruj na login
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrace - Kadeřnictví Tereza Dvořáčková</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="admin-style.css">
    <link href="https://fonts.googleapis.com/css?family=Inter:400,600,700,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="admin-body">
    <div class="admin-container">
        <header class="admin-header">
            <div class="admin-header-content">
                <h1><i class="fas fa-cogs"></i> Administrace kadeřnictví</h1>
                <div class="admin-header-actions">
                    <a href="../index.php" class="btn btn-secondary">
                        <i class="fas fa-eye"></i> Zobrazit web
                    </a>
                    <button onclick="logout()" class="btn btn-danger">
                        <i class="fas fa-sign-out-alt"></i> Odhlásit se
                    </button>
                </div>
            </div>
        </header>

        <main class="admin-main">
            <div class="admin-section">
                <div class="section-header">
                    <h2><i class="fas fa-list"></i> Správa ceníku</h2>
                    <button onclick="addNewItem()" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Přidat položku
                    </button>
                </div>

                <div class="cenik-stats">
                    <div class="stat-card">
                        <i class="fas fa-clipboard-list"></i>
                        <div>
                            <span class="stat-number" id="totalItems">0</span>
                            <span class="stat-label">Celkem položek</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-clock"></i>
                        <div>
                            <span class="stat-number" id="lastUpdate">-</span>
                            <span class="stat-label">Poslední úprava</span>
                        </div>
                    </div>
                </div>

                <div class="cenik-container">
                    <div class="cenik-toolbar">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="searchInput" placeholder="Vyhledat službu...">
                        </div>
                        <div class="sort-options">
                            <select id="sortBy">
                                <option value="order">Vlastní pořadí</option>
                                <option value="name">Název A-Z</option>
                                <option value="name_desc">Název Z-A</option>
                                <option value="price">Cena vzestupně</option>
                                <option value="price_desc">Cena sestupně</option>
                            </select>
                        </div>
                    </div>

                    <div class="cenik-list" id="cenikList">
                        <!-- Položky se načtou zde -->
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal pro přidání/úpravu položky -->
    <div id="itemModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Přidat položku</h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <form id="itemForm">
                <input type="hidden" id="itemId" value="">
                <div class="form-group">
                    <label for="itemService">Název služby</label>
                    <input type="text" id="itemService" required placeholder="např. Dámský střih">
                </div>
                <div class="form-group">
                    <label for="itemPrice">Cena</label>
                    <input type="text" id="itemPrice" required placeholder="např. 350 Kč">
                </div>
                <div class="modal-actions">
                    <button type="button" onclick="closeModal()" class="btn btn-secondary">Zrušit</button>
                    <button type="submit" class="btn btn-primary">Uložit</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast notifikace -->
    <div id="toast" class="toast"></div>

    <script src="admin-script.js"></script>
</body>
</html>