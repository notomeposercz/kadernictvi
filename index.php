<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kadeřnictví Tereza Dvořáčková</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header class="main-header">
        <div class="header-overlay"></div>
        <img src="image1.jpg" alt="Kadeřnictví Tereza Dvořáčková" class="header-photo">
        <div class="header-content">
            <div class="header-logo">
                <i class="fas fa-cut"></i>
            </div>
            <h1>Kadeřnictví Tereza Dvořáčková</h1>
            <div class="header-info">
                <div class="info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Měnín 475</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-phone"></i>
                    <a href="tel:792350545">792 350 545</a>
                </div>
                <div class="info-item">
                    <i class="fas fa-id-card"></i>
                    <span>IČ: 04652240</span>
                </div>
            </div>
        </div>
    </header>

    <nav class="main-nav">
        <div class="nav-container">
            <ul>
                <li><a href="#o-nas"><i class="fas fa-heart"></i> O nás</a></li>
                <li><a href="#cenik"><i class="fas fa-tags"></i> Ceník</a></li>
                <li><a href="#doba"><i class="fas fa-clock"></i> Pracovní doba</a></li>
                <li><a href="#objednani"><i class="fas fa-calendar-plus"></i> Objednání</a></li>
                <li><a href="#mapa"><i class="fas fa-map-marker-alt"></i> Kde nás najdete</a></li>
            </ul>
        </div>
    </nav>

    <main class="main-content">
        <section id="o-nas" class="section">
            <div class="section-container">
                <div class="section-header">
                    <h2><i class="fas fa-heart"></i> O nás</h2>
                    <div class="section-line"></div>
                </div>
                <div class="about-content">
                    <div class="about-text">
                        <p>Moderní kadeřnické služby v příjemném prostředí. Sledujeme trendy, pečujeme o každého zákazníka individuálně. Přijďte se nechat hýčkat do našeho salonu v Měníně!</p>
                        <div class="features">
                            <div class="feature">
                                <i class="fas fa-star"></i>
                                <span>Moderní trendy</span>
                            </div>
                            <div class="feature">
                                <i class="fas fa-user-friends"></i>
                                <span>Individuální přístup</span>
                            </div>
                            <div class="feature">
                                <i class="fas fa-spa"></i>
                                <span>Příjemné prostředí</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="cenik" class="section section-alt">
            <div class="section-container">
                <div class="section-header">
                    <h2><i class="fas fa-tags"></i> Ceník služeb</h2>
                    <div class="section-line"></div>
                </div>
                <div class="cenik-grid" id="cenikGrid">
                    <!-- Ceník se načte z databáze -->
                </div>
            </div>
        </section>

        <section id="doba" class="section">
            <div class="section-container">
                <div class="section-header">
                    <h2><i class="fas fa-clock"></i> Pracovní doba</h2>
                    <div class="section-line"></div>
                </div>
                <div class="doba-grid">
                    <div class="doba-item">
                        <div class="day">Pondělí</div>
                        <div class="time">8:00 – 17:00</div>
                    </div>
                    <div class="doba-item">
                        <div class="day">Úterý</div>
                        <div class="time">8:00 – 17:00</div>
                    </div>
                    <div class="doba-item">
                        <div class="day">Středa</div>
                        <div class="time">8:00 – 17:00</div>
                    </div>
                    <div class="doba-item">
                        <div class="day">Čtvrtek</div>
                        <div class="time">8:00 – 17:00</div>
                    </div>
                    <div class="doba-item">
                        <div class="day">Pátek</div>
                        <div class="time">8:00 – 15:00</div>
                    </div>
                    <div class="doba-item closed">
                        <div class="day">Víkend</div>
                        <div class="time">Zavřeno</div>
                    </div>
                </div>
            </div>
        </section>

        <section id="objednani" class="section section-alt">
            <div class="section-container">
                <div class="section-header">
                    <h2><i class="fas fa-calendar-plus"></i> Objednání termínu</h2>
                    <div class="section-line"></div>
                </div>
                <div class="form-container">
                    <form class="booking-form" id="bookingForm">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name"><i class="fas fa-user"></i> Jméno a příjmení</label>
                                <input type="text" id="name" name="name" required>
                            </div>
                            <div class="form-group">
                                <label for="phone"><i class="fas fa-phone"></i> Telefon</label>
                                <input type="tel" id="phone" name="phone" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email"><i class="fas fa-envelope"></i> E-mail</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="msg"><i class="fas fa-comment"></i> Požadovaný termín a služba</label>
                            <textarea id="msg" name="msg" required placeholder="Napište nám, kdy byste chtěli přijít a jakou službu preferujete..."></textarea>
                        </div>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-paper-plane"></i>
                            Odeslat objednávku
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <section id="mapa" class="section">
            <div class="section-container">
                <div class="section-header">
                    <h2><i class="fas fa-map-marker-alt"></i> Kde nás najdete</h2>
                    <div class="section-line"></div>
                </div>
                <div class="map-container">
                    <iframe src="https://www.google.com/maps?q=Měnín+475&amp;output=embed" 
                            width="100%" 
                            height="400" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy">
                    </iframe>
                </div>
            </div>
        </section>
    </main>

    <footer class="main-footer">
        <div class="footer-content">
            <div class="footer-info">
                <h3>Kadeřnictví Tereza Dvořáčková</h3>
                <p>Profesionální kadeřnické služby v Měníně</p>
            </div>
            <div class="footer-contact">
                <div class="contact-item">
                    <i class="fas fa-phone"></i>
                    <a href="tel:792350545">792 350 545</a>
                </div>
                <div class="contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Měnín 475</span>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <small>&copy; 2025 Kadeřnictví Tereza Dvořáčková | Vytvořeno s láskou</small>
        </div>
    </footer>

    <script>
        // Načtení ceníku při načtení stránky
        document.addEventListener('DOMContentLoaded', function() {
            loadCenik();
            initSmoothScrolling();
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
                updateCenikGrid(result.data);
            }
        }

        // Aktualizace ceníku - nový grid design
        function updateCenikGrid(data) {
            const grid = document.getElementById('cenikGrid');
            grid.innerHTML = '';
            
            data.forEach(item => {
                const cardElement = document.createElement('div');
                cardElement.className = 'cenik-card';
                cardElement.innerHTML = `
                    <div class="cenik-icon">
                        <i class="fas fa-cut"></i>
                    </div>
                    <h3>${escapeHtml(item.sluzba)}</h3>
                    <div class="cenik-price">${escapeHtml(item.cena)}</div>
                `;
                grid.appendChild(cardElement);
            });
        }

        // Smooth scrolling pro navigaci
        function initSmoothScrolling() {
            const links = document.querySelectorAll('nav a[href^="#"]');
            links.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    const targetElement = document.querySelector(targetId);
                    if (targetElement) {
                        targetElement.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        }

        // Escape HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Formulář objednávky
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // Zde by byla logika pro odeslání formuláře
            alert('Děkujeme za Vaši objednávku! Brzy se Vám ozveme.');
        });
    </script>
</body>
</html>