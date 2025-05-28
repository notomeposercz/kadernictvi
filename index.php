<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kadeřnictví Tereza Dvořáčková</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css?family=Inter:400,600,700,800&display=swap" rel="stylesheet">
</head>
<body>
    <header class="main-header">
        <img src="image1.jpg" alt="Kadeřnictví Tereza Dvořáčková" class="header-photo">
        <div class="header-content">
            <h1>Kadeřnictví Tereza Dvořáčková</h1>
            <p>Měnín 475</p>
            <p>Tel.: <a href="tel:792350545">792 350 545</a></p>
            <p>IČ: 04652240</p>
        </div>
    </header>
    <nav>
        <ul>
            <li><a href="#cenik">Ceník</a></li>
            <li><a href="#doba">Pracovní doba</a></li>
            <li><a href="#objednani">Objednání</a></li>
            <li><a href="#mapa">Kde nás najdete</a></li>
            <li><a href="#admin-login">Administrace</a></li>
        </ul>
    </nav>
    <main>
        <section id="o-nas" class="card">
            <h2>O nás</h2>
            <p>Moderní kadeřnické služby v příjemném prostředí. Sledujeme trendy, pečujeme o každého zákazníka individuálně. Přijďte se nechat hýčkat do našeho salonu v Měníně!</p>
        </section>

        <section id="cenik" class="card">
            <h2>Ceník</h2>
            <table id="cenikTable">
                <tr><th>Služba</th><th>Cena</th></tr>
                <!-- Ceník se načte z databáze -->
            </table>
        </section>

        <section id="doba" class="card">
            <h2>Pracovní doba</h2>
            <table>
                <tr><td>Pondělí</td><td>8:00 – 17:00</td></tr>
                <tr><td>Úterý</td><td>8:00 – 17:00</td></tr>
                <tr><td>Středa</td><td>8:00 – 17:00</td></tr>
                <tr><td>Čtvrtek</td><td>8:00 – 17:00</td></tr>
                <tr><td>Pátek</td><td>8:00 – 15:00</td></tr>
                <tr><td>Sobota</td><td>Zavřeno</td></tr>
                <tr><td>Neděle</td><td>Zavřeno</td></tr>
            </table>
        </section>

        <section id="objednani" class="card">
            <h2>Formulář pro objednání</h2>
            <form>
                <label for="name">Jméno:</label>
                <input type="text" id="name" name="name" required>
                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" required>
                <label for="phone">Telefon:</label>
                <input type="tel" id="phone" name="phone">
                <label for="msg">Požadovaný termín a služba:</label>
                <textarea id="msg" name="msg" required></textarea>
                <button type="submit">Odeslat objednávku</button>
            </form>
        </section>

        <section id="mapa" class="card">
            <h2>Kde nás najdete</h2>
            <div class="mapouter">
                <iframe src="https://www.google.com/maps?q=Měnín+475&amp;output=embed" width="100%" height="270" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </section>

        <!-- Přihlášení do administrace -->
        <section id="admin-login" class="admin-panel" style="display:block;">
            <h2>Přihlášení do administrace</h2>
            <form id="loginForm">
                <label for="adminPassword">Heslo:</label>
                <input type="password" id="adminPassword" required>
                <button type="submit">Přihlásit se</button>
            </form>
            <div id="loginError" style="color:#b34d1e; display:none; margin-top:1em;">Nesprávné heslo!</div>
        </section>

        <!-- Administrace ceníku -->
        <section id="admin" class="admin-panel" style="display:none;">
            <h2>Administrace ceníku</h2>
            <form id="cenikForm">
                <input type="hidden" id="editId" value="">
                <label for="service">Služba:</label>
                <input type="text" id="service" name="service" required>
                <label for="price">Cena:</label>
                <input type="text" id="price" name="price" required>
                <button type="submit" id="submitBtn">Přidat položku</button>
                <button type="button" id="cancelBtn" style="display:none;" onclick="cancelEdit()">Zrušit úpravu</button>
            </form>
            <div class="admin-cenik-list">
                <h3>Aktuální ceník</h3>
                <table id="adminCenikTable">
                    <tr>
                        <th>Služba</th>
                        <th>Cena</th>
                        <th>Akce</th>
                    </tr>
                    <!-- Položky se načtou z databáze -->
                </table>
            </div>
            <button onclick="logout()" style="background:#ffeb3b;color:#613636;margin-top:1em;">Odhlásit se</button>
        </section>
    </main>
    <footer>
        <small>&copy; 2025 Kadeřnictví Tereza Dvořáčková | Vytvořeno s láskou</small>
    </footer>

    <script>
        // Globální proměnné
        let isLoggedIn = <?php echo isLoggedIn() ? 'true' : 'false'; ?>;
        const adminSection = document.getElementById('admin');
        const adminLoginSection = document.getElementById('admin-login');
        const loginForm = document.getElementById('loginForm');
        const loginError = document.getElementById('loginError');

        // Načtení ceníku při načtení stránky
        document.addEventListener('DOMContentLoaded', function() {
            loadCenik();
            if (isLoggedIn) {
                showAdminPanel();
            }
        });

        // API volání
        async function apiCall(action, data = {}) {
            try {
                const response = await fetch('api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ action, ...data })
                });
                return await response.json();
            } catch (error) {
                console.error('API Error:', error);
                return { success: false, message: 'Chyba připojení' };
            }
        }

        // Načtení ceníku z databáze
        async function loadCenik() {
            const result = await apiCall('get_cenik');
            if (result.success) {
                updateCenikTable(result.data);
                if (isLoggedIn) {
                    updateAdminCenikTable(result.data);
                }
            }
        }

        // Aktualizace veřejného ceníku
        function updateCenikTable(data) {
            const table = document.getElementById('cenikTable');
            // Zachovej header
            table.innerHTML = '<tr><th>Služba</th><th>Cena</th></tr>';
            data.forEach(item => {
                const row = table.insertRow(-1);
                row.innerHTML = `<td>${item.sluzba}</td><td>${item.cena}</td>`;
            });
        }

        // Aktualizace admin ceníku
        function updateAdminCenikTable(data) {
            const table = document.getElementById('adminCenikTable');
            // Zachovej header
            table.innerHTML = '<tr><th>Služba</th><th>Cena</th><th>Akce</th></tr>';
            data.forEach(item => {
                const row = table.insertRow(-1);
                row.innerHTML = `
                    <td>${item.sluzba}</td>
                    <td>${item.cena}</td>
                    <td class="admin-cenik-actions">
                        <button onclick="editItem(${item.id}, '${item.sluzba}', '${item.cena}')">Upravit</button>
                        <button onclick="deleteItem(${item.id})">Smazat</button>
                    </td>
                `;
            });
        }

        // Přihlášení
        loginForm.onsubmit = async function(e) {
            e.preventDefault();
            const password = document.getElementById('adminPassword').value;
            const result = await apiCall('login', { password });
            
            if (result.success) {
                isLoggedIn = true;
                showAdminPanel();
                loginError.style.display = 'none';
                loadCenik();
            } else {
                loginError.style.display = 'block';
                loginError.textContent = result.message;
            }
        };

        // Zobrazení admin panelu
        function showAdminPanel() {
            adminSection.style.display = 'block';
            adminLoginSection.style.display = 'none';
            window.location.hash = '#admin';
        }

        // Odhlášení
        async function logout() {
            await apiCall('logout');
            isLoggedIn = false;
            adminSection.style.display = 'none';
            adminLoginSection.style.display = 'block';
            loginForm.reset();
            window.location.hash = '#admin-login';
        }

        // Přidání/úprava položky
        document.getElementById('cenikForm').onsubmit = async function(e) {
            e.preventDefault();
            const sluzba = document.getElementById('service').value;
            const cena = document.getElementById('price').value;
            const editId = document.getElementById('editId').value;
            
            if (!sluzba || !cena) return;
            
            let result;
            if (editId) {
                result = await apiCall('update_item', { id: editId, sluzba, cena });
            } else {
                result = await apiCall('add_item', { sluzba, cena });
            }
            
            if (result.success) {
                document.getElementById('service').value = '';
                document.getElementById('price').value = '';
                document.getElementById('editId').value = '';
                document.getElementById('submitBtn').textContent = 'Přidat položku';
                document.getElementById('cancelBtn').style.display = 'none';
                loadCenik();
            } else {
                alert(result.message);
            }
        };

        // Úprava položky
        function editItem(id, sluzba, cena) {
            document.getElementById('editId').value = id;
            document.getElementById('service').value = sluzba;
            document.getElementById('price').value = cena;
            document.getElementById('submitBtn').textContent = 'Upravit položku';
            document.getElementById('cancelBtn').style.display = 'inline-block';
            window.scrollTo({top: document.getElementById('admin').offsetTop - 80, behavior: 'smooth'});
        }

        // Zrušení úpravy
        function cancelEdit() {
            document.getElementById('editId').value = '';
            document.getElementById('service').value = '';
            document.getElementById('price').value = '';
            document.getElementById('submitBtn').textContent = 'Přidat položku';
            document.getElementById('cancelBtn').style.display = 'none';
        }

        // Smazání položky
        async function deleteItem(id) {
            if (confirm('Opravdu chcete smazat tuto položku?')) {
                const result = await apiCall('delete_item', { id });
                if (result.success) {
                    loadCenik();
                } else {
                    alert(result.message);
                }
            }
        }
    </script>
</body>
</html>