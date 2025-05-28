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
    <!-- TinyMCE Editor -->
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
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

        <div class="admin-layout">
            <!-- Levé menu -->
            <aside class="admin-sidebar">
                <nav class="admin-nav">
                    <h3><i class="fas fa-sitemap"></i> Správa webu</h3>
                    <div class="nav-list">
                        <div class="nav-item active" onclick="showSection('o-nas')">
                            <div class="nav-item-content">
                                <i class="fas fa-heart"></i>
                                <span class="nav-item-name">O nás</span>
                            </div>
                        </div>
                        <div class="nav-item" onclick="showSection('cenik')">
                            <div class="nav-item-content">
                                <i class="fas fa-tags"></i>
                                <span class="nav-item-name">Ceník</span>
                            </div>
                        </div>
                        <div class="nav-item" onclick="showSection('pracovni-doba')">
                            <div class="nav-item-content">
                                <i class="fas fa-clock"></i>
                                <span class="nav-item-name">Pracovní doba</span>
                            </div>
                        </div>
                        <div class="nav-item" onclick="showSection('objednani')">
                            <div class="nav-item-content">
                                <i class="fas fa-calendar-plus"></i>
                                <span class="nav-item-name">Objednání</span>
                            </div>
                        </div>
                        <div class="nav-item" onclick="showSection('kontakt')">
                            <div class="nav-item-content">
                                <i class="fas fa-map-marker-alt"></i>
                                <span class="nav-item-name">Kde nás najdete</span>
                            </div>
                        </div>
                    </div>
                </nav>
            </aside>

            <!-- Hlavní obsah -->
            <main class="admin-main">
                <!-- Sekce O nás -->
                <div class="admin-section" id="o-nas-section">
                    <div class="section-header">
                        <h2><i class="fas fa-heart"></i> Správa sekce "O nás"</h2>
                        <div class="section-actions">
                            <button onclick="saveAboutContent()" class="btn btn-success">
                                <i class="fas fa-save"></i> Uložit změny
                            </button>
                        </div>
                    </div>

                    <div class="content-editor">
                        <div class="form-group">
                            <label for="aboutText">Hlavní text sekce</label>
                            <textarea id="aboutText" rows="6"></textarea>
                        </div>
                        
                        <h3>Výhody (3 boxy)</h3>
                        <div class="features-editor">
                            <div class="feature-item">
                                <div class="form-group">
                                    <label for="feature1Icon">Ikona 1 (Font Awesome)</label>
                                    <input type="text" id="feature1Icon" placeholder="fas fa-star">
                                </div>
                                <div class="form-group">
                                    <label for="feature1Text">Text 1</label>
                                    <input type="text" id="feature1Text" placeholder="Moderní trendy">
                                </div>
                            </div>
                            <div class="feature-item">
                                <div class="form-group">
                                    <label for="feature2Icon">Ikona 2 (Font Awesome)</label>
                                    <input type="text" id="feature2Icon" placeholder="fas fa-user-friends">
                                </div>
                                <div class="form-group">
                                    <label for="feature2Text">Text 2</label>
                                    <input type="text" id="feature2Text" placeholder="Individuální přístup">
                                </div>
                            </div>
                            <div class="feature-item">
                                <div class="form-group">
                                    <label for="feature3Icon">Ikona 3 (Font Awesome)</label>
                                    <input type="text" id="feature3Icon" placeholder="fas fa-spa">
                                </div>
                                <div class="form-group">
                                    <label for="feature3Text">Text 3</label>
                                    <input type="text" id="feature3Text" placeholder="Příjemné prostředí">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sekce Ceník (původní) -->
                <div class="admin-section" id="cenik-section" style="display: none;">
                    <div class="section-header">
                        <h2><i class="fas fa-tags"></i> Správa ceníku</h2>
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

                <!-- Sekce Pracovní doba -->
                <div class="admin-section" id="pracovni-doba-section" style="display: none;">
                    <div class="section-header">
                        <h2><i class="fas fa-clock"></i> Správa pracovní doby</h2>
                        <button onclick="addNewHour()" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Přidat den/čas
                        </button>
                    </div>

                    <div class="hours-stats">
                        <div class="stat-card">
                            <i class="fas fa-calendar-alt"></i>
                            <div>
                                <span class="stat-number" id="totalHours">0</span>
                                <span class="stat-label">Celkem položek</span>
                            </div>
                        </div>
                        <div class="stat-card">
                            <i class="fas fa-clock"></i>
                            <div>
                                <span class="stat-number" id="lastHoursUpdate">-</span>
                                <span class="stat-label">Poslední úprava</span>
                            </div>
                        </div>
                    </div>

                    <div class="hours-list" id="hoursList">
                        <!-- Položky se načtou zde -->
                    </div>
                </div>

                <!-- Sekce Objednání -->
                <div class="admin-section" id="objednani-section" style="display: none;">
                    <div class="section-header">
                        <h2><i class="fas fa-calendar-plus"></i> Nastavení objednávek</h2>
                        <div class="section-actions">
                            <button onclick="saveBookingSettings()" class="btn btn-success">
                                <i class="fas fa-save"></i> Uložit nastavení
                            </button>
                        </div>
                    </div>

                    <div class="content-editor">
                        <div class="form-group">
                            <label for="bookingEmail">E-mail pro objednávky</label>
                            <input type="email" id="bookingEmail" placeholder="objednavky@kadernictvi.cz">
                            <small>Na tento e-mail budou chodit objednávky</small>
                        </div>
                        <div class="form-group">
                            <label for="bookingSubject">Předmět e-mailu</label>
                            <input type="text" id="bookingSubject" placeholder="Nová objednávka z webu">
                        </div>
                        <div class="form-group">
                            <label for="bookingActive">Stav objednávek</label>
                            <select id="bookingActive">
                                <option value="1">Aktivní - formulář funguje</option>
                                <option value="0">Neaktivní - formulář je skrytý</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Sekce Kontakt/Mapa -->
                <div class="admin-section" id="kontakt-section" style="display: none;">
                    <div class="section-header">
                        <h2><i class="fas fa-map-marker-alt"></i> Správa kontaktů a mapy</h2>
                        <div class="section-actions">
                            <button onclick="saveContactInfo()" class="btn btn-success">
                                <i class="fas fa-save"></i> Uložit kontakty
                            </button>
                        </div>
                    </div>

                    <div class="content-editor">
                        <div class="form-group">
                            <label for="businessName">Název firmy</label>
                            <input type="text" id="businessName" placeholder="Kadeřnictví Tereza Dvořáčková">
                        </div>
                        <div class="form-group">
                            <label for="address">Adresa</label>
                            <input type="text" id="address" placeholder="Měnín 475">
                        </div>
                        <div class="form-group">
                            <label for="phone">Telefon</label>
                            <input type="tel" id="phone" placeholder="792 350 545">
                        </div>
                        <div class="form-group">
                            <label for="ico">IČ</label>
                            <input type="text" id="ico" placeholder="04652240">
                        </div>
                        <div class="form-group">
                            <label for="mapUrl">URL mapy (Google Maps embed)</label>
                            <textarea id="mapUrl" rows="3" placeholder="https://www.google.com/maps?q=Měnín+475&output=embed"></textarea>
                            <small>Vložte embed URL z Google Maps</small>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal pro přidání/úpravu položky ceníku -->
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

    <!-- Modal pro přidání/úpravu pracovní doby -->
    <div id="hoursModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="hoursModalTitle">Přidat pracovní dobu</h3>
                <button class="modal-close" onclick="closeHoursModal()">&times;</button>
            </div>
            <form id="hoursForm">
                <input type="hidden" id="hoursId" value="">
                <div class="form-group">
                    <label for="dayName">Den/Období</label>
                    <input type="text" id="dayName" required placeholder="např. Pondělí, Víkend">
                </div>
                <div class="form-group">
                    <label for="timeRange">Čas</label>
                    <input type="text" id="timeRange" required placeholder="např. 8:00 – 17:00, Zavřeno">
                </div>
                <div class="form-group">
                    <label for="isClosed">Stav</label>
                    <select id="isClosed">
                        <option value="0">Otevřeno</option>
                        <option value="1">Zavřeno</option>
                    </select>
                </div>
                <div class="modal-actions">
                    <button type="button" onclick="closeHoursModal()" class="btn btn-secondary">Zrušit</button>
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