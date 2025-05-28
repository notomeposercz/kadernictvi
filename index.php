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
            <table>
                <tr><th>Služba</th><th>Cena</th></tr>
                <tr><td>Dámský střih</td><td>350 Kč</td></tr>
                <tr><td>Pánský střih</td><td>250 Kč</td></tr>
                <tr><td>Dětský střih</td><td>200 Kč</td></tr>
                <tr><td>Barvení</td><td>od 600 Kč</td></tr>
                <tr><td>Melír</td><td>od 700 Kč</td></tr>
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

        <!-- Administrace ceníku, skrytá dokud není přihlášeno -->
        <section id="admin" class="admin-panel" style="display:none;">
            <h2>Administrace ceníku</h2>
            <form id="cenikForm">
                <label for="service">Služba:</label>
                <input type="text" id="service" name="service" required>
                <label for="price">Cena:</label>
                <input type="text" id="price" name="price" required>
                <button type="submit">Přidat položku</button>
            </form>
            <div class="admin-cenik-list">
                <h3>Aktuální ceník</h3>
                <table id="adminCenikTable">
                    <tr>
                        <th>Služba</th>
                        <th>Cena</th>
                        <th>Akce</th>
                    </tr>
                    <tr>
                        <td>Dámský střih</td>
                        <td>350 Kč</td>
                        <td class="admin-cenik-actions">
                            <button onclick="editRow(this)">Upravit</button>
                            <button onclick="deleteRow(this)">Smazat</button>
                        </td>
                    </tr>
                    <tr>
                        <td>Pánský střih</td>
                        <td>250 Kč</td>
                        <td class="admin-cenik-actions">
                            <button onclick="editRow(this)">Upravit</button>
                            <button onclick="deleteRow(this)">Smazat</button>
                        </td>
                    </tr>
                    <tr>
                        <td>Dětský střih</td>
                        <td>200 Kč</td>
                        <td class="admin-cenik-actions">
                            <button onclick="editRow(this)">Upravit</button>
                            <button onclick="deleteRow(this)">Smazat</button>
                        </td>
                    </tr>
                    <tr>
                        <td>Barvení</td>
                        <td>od 600 Kč</td>
                        <td class="admin-cenik-actions">
                            <button onclick="editRow(this)">Upravit</button>
                            <button onclick="deleteRow(this)">Smazat</button>
                        </td>
                    </tr>
                    <tr>
                        <td>Melír</td>
                        <td>od 700 Kč</td>
                        <td class="admin-cenik-actions">
                            <button onclick="editRow(this)">Upravit</button>
                            <button onclick="deleteRow(this)">Smazat</button>
                        </td>
                    </tr>
                </table>
            </div>
            <button onclick="logout()" style="background:#ffeb3b;color:#613636;margin-top:1em;">Odhlásit se</button>
        </section>
    </main>
    <footer>
        <small>&copy; 2025 Kadeřnictví Tereza Dvořáčková | Vytvořeno s láskou</small>
    </footer>
    <script>
        // --- ADMIN LOGIN ---
        const adminSection = document.getElementById('admin');
        const adminLoginSection = document.getElementById('admin-login');
        const loginForm = document.getElementById('loginForm');
        const loginError = document.getElementById('loginError');
        const ADMIN_PASS = 'kadernice2025'; // změňte si dle potřeby

        loginForm.onsubmit = function(e) {
            e.preventDefault();
            const pass = document.getElementById('adminPassword').value;
            if(pass === ADMIN_PASS) {
                adminSection.style.display = 'block';
                adminLoginSection.style.display = 'none';
                loginError.style.display = 'none';
                window.location.hash = '#admin';
            } else {
                loginError.style.display = 'block';
            }
        };

        window.logout = function() {
            adminSection.style.display = 'none';
            adminLoginSection.style.display = 'block';
            loginForm.reset();
            window.location.hash = '#admin-login';
        };

        // --- ADMIN PANEL: Add, edit, delete rows (demo only, no persistence) ---
        document.getElementById('cenikForm').onsubmit = function(e) {
            e.preventDefault();
            var service = document.getElementById('service').value;
            var price = document.getElementById('price').value;
            if(service && price) {
                var table = document.getElementById('adminCenikTable');
                var row = table.insertRow(-1);
                row.innerHTML = '<td>' + service + '</td><td>' + price + '</td><td class="admin-cenik-actions"><button onclick="editRow(this)">Upravit</button><button onclick="deleteRow(this)">Smazat</button></td>';
                document.getElementById('service').value = '';
                document.getElementById('price').value = '';
            }
        };
        window.deleteRow = function(btn) {
            var row = btn.parentNode.parentNode;
            row.parentNode.removeChild(row);
        };
        window.editRow = function(btn) {
            var row = btn.parentNode.parentNode;
            var service = row.cells[0].innerText;
            var price = row.cells[1].innerText;
            document.getElementById('service').value = service;
            document.getElementById('price').value = price;
            row.parentNode.removeChild(row);
            window.scrollTo({top: document.getElementById('admin').offsetTop - 80, behavior: 'smooth'});
        };
    </script>
</body>
</html>