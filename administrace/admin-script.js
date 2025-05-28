// Globální proměnné
let cenikData = [];
let hoursData = [];
let aboutData = {};
let bookingSettings = {};
let contactData = {};
let draggedItem = null;
let draggedHoursItem = null;
let currentSection = 'o-nas';

// Inicializace po načtení stránky
document.addEventListener('DOMContentLoaded', function() {
    initializeEventListeners();
    loadAllData();
    showSection('o-nas');
});

// Načtení všech dat
async function loadAllData() {
    await Promise.all([
        loadAboutData(),
        loadCenik(),
        loadHoursData(),
        loadBookingSettings(),
        loadContactData()
    ]);
}

// Event listenery
function initializeEventListeners() {
    // Vyhledávání ceníku
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', filterCenik);
    }
    
    // Řazení ceníku
    const sortBy = document.getElementById('sortBy');
    if (sortBy) {
        sortBy.addEventListener('change', sortCenik);
    }
    
    // Modal formy
    const itemForm = document.getElementById('itemForm');
    if (itemForm) {
        itemForm.addEventListener('submit', saveItem);
    }
    
    const hoursForm = document.getElementById('hoursForm');
    if (hoursForm) {
        hoursForm.addEventListener('submit', saveHours);
    }
    
    // Zavření modalů při kliknutí mimo obsah
    document.getElementById('itemModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
    
    document.getElementById('hoursModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeHoursModal();
        }
    });
    
    // ESC pro zavření modalů
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (document.getElementById('itemModal').style.display === 'block') {
                closeModal();
            }
            if (document.getElementById('hoursModal').style.display === 'block') {
                closeHoursModal();
            }
        }
    });
}

// API volání
async function apiCall(action, data = {}) {
    try {
        console.time(`API Call - ${action}`);
        const response = await fetch('../api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ action, ...data })
        });
        const result = await response.json();
        console.timeEnd(`API Call - ${action}`);
        return result;
    } catch (error) {
        console.error('API Error:', error);
        showToast('Chyba připojení', 'error');
        return { success: false, message: 'Chyba připojení' };
    }
}

// === SPRÁVA SEKCÍ UI ===

// Zobrazení konkrétní sekce administrace
function showSection(sectionName) {
    // Skryj všechny sekce
    document.querySelectorAll('.admin-section').forEach(section => {
        section.style.display = 'none';
    });
    
    // Zobraz vybranou sekci
    const targetSection = document.getElementById(`${sectionName}-section`);
    if (targetSection) {
        targetSection.style.display = 'block';
    }
    
    currentSection = sectionName;
    
    // Aktualizuj navigaci vlevo
    document.querySelectorAll('.nav-item').forEach(item => {
        item.classList.remove('active');
    });
    
    // Najdi a aktivuj správnou navigační položku
    const navItems = document.querySelectorAll('.nav-item');
    navItems.forEach(item => {
        const onclick = item.getAttribute('onclick');
        if (onclick && onclick.includes(`'${sectionName}'`)) {
            item.classList.add('active');
        }
    });
}

// === SPRÁVA SEKCE "O NÁS" ===

// Načtení dat sekce O nás
async function loadAboutData() {
    const result = await apiCall('get_about');
    if (result.success) {
        aboutData = result.data;
        populateAboutForm();
    } else {
        showToast('Chyba při načítání sekce O nás', 'error');
    }
}

// Naplnění formuláře sekce O nás
function populateAboutForm() {
    document.getElementById('aboutText').value = aboutData.text || '';
    document.getElementById('feature1Icon').value = aboutData.feature1_icon || 'fas fa-star';
    document.getElementById('feature1Text').value = aboutData.feature1_text || '';
    document.getElementById('feature2Icon').value = aboutData.feature2_icon || 'fas fa-user-friends';
    document.getElementById('feature2Text').value = aboutData.feature2_text || '';
    document.getElementById('feature3Icon').value = aboutData.feature3_icon || 'fas fa-spa';
    document.getElementById('feature3Text').value = aboutData.feature3_text || '';
}

// Uložení obsahu sekce O nás
async function saveAboutContent() {
    const data = {
        text: document.getElementById('aboutText').value.trim(),
        feature1_icon: document.getElementById('feature1Icon').value.trim(),
        feature1_text: document.getElementById('feature1Text').value.trim(),
        feature2_icon: document.getElementById('feature2Icon').value.trim(),
        feature2_text: document.getElementById('feature2Text').value.trim(),
        feature3_icon: document.getElementById('feature3Icon').value.trim(),
        feature3_text: document.getElementById('feature3Text').value.trim()
    };
    
    if (!data.text || !data.feature1_text || !data.feature2_text || !data.feature3_text) {
        showToast('Vyplňte všechna textová pole', 'error');
        return;
    }
    
    const result = await apiCall('save_about', data);
    
    if (result.success) {
        showToast('Sekce "O nás" byla uložena', 'success');
        aboutData = data;
    } else {
        showToast(result.message || 'Chyba při ukládání', 'error');
    }
}

// === SPRÁVA PRACOVNÍ DOBY ===

// Načtení pracovní doby
async function loadHoursData() {
    const result = await apiCall('get_hours');
    if (result.success) {
        hoursData = result.data;
        renderHours();
        updateHoursStats();
    } else {
        showToast('Chyba při načítání pracovní doby', 'error');
    }
}

// Vykreslení pracovní doby
function renderHours() {
    const container = document.getElementById('hoursList');
    if (!container) return;
    
    container.innerHTML = '';
    
    if (hoursData.length === 0) {
        container.innerHTML = `
            <div style="text-align: center; padding: 3rem; color: #a0aec0;">
                <i class="fas fa-clock" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                <p>Žádné položky pracovní doby</p>
            </div>
        `;
        return;
    }
    
    hoursData.forEach(item => {
        const itemElement = createHoursItem(item);
        container.appendChild(itemElement);
    });
}

// Vytvoření HTML elementu pro pracovní dobu
function createHoursItem(item) {
    const div = document.createElement('div');
    div.className = `hours-item ${item.is_closed == 1 ? 'closed' : ''}`;
    div.dataset.id = item.id;
    div.draggable = true;
    
    div.innerHTML = `
        <div class="drag-handle">
            <i class="fas fa-grip-vertical"></i>
        </div>
        <div class="hours-content">
            <div class="hours-day" data-field="day_name">${escapeHtml(item.day_name)}</div>
            <div class="hours-time" data-field="time_range">${escapeHtml(item.time_range)}</div>
        </div>
        <div class="hours-actions">
            <button class="btn btn-warning btn-sm" onclick="editHoursInline(${item.id})">
                <i class="fas fa-edit"></i>
            </button>
            <button class="btn btn-danger btn-sm" onclick="deleteHours(${item.id})">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    
    // Drag & Drop event listenery
    div.addEventListener('dragstart', handleHoursDragStart);
    div.addEventListener('dragover', handleHoursDragOver);
    div.addEventListener('drop', handleHoursDrop);
    div.addEventListener('dragend', handleHoursDragEnd);
    
    return div;
}

// Inline editace pracovní doby
function editHoursInline(id) {
    const item = hoursData.find(i => i.id == id);
    if (!item) return;
    
    const itemElement = document.querySelector(`[data-id="${id}"]`);
    if (!itemElement) return;
    
    itemElement.classList.add('edit-mode');
    
    const dayElement = itemElement.querySelector('[data-field="day_name"]');
    const timeElement = itemElement.querySelector('[data-field="time_range"]');
    const actionsElement = itemElement.querySelector('.hours-actions');
    
    // Vytvoř input fieldy
    dayElement.innerHTML = `<input type="text" class="edit-input" value="${escapeHtml(item.day_name)}" data-field="day_name">`;
    timeElement.innerHTML = `<input type="text" class="edit-input" value="${escapeHtml(item.time_range)}" data-field="time_range">`;
    
    // Změň akce
    actionsElement.innerHTML = `
        <div class="edit-actions">
            <button class="btn btn-success btn-sm" onclick="saveHoursInlineEdit(${id})">
                <i class="fas fa-check"></i>
            </button>
            <button class="btn btn-secondary btn-sm" onclick="cancelHoursInlineEdit(${id})">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    // Focus na první input
    dayElement.querySelector('input').focus();
    
    // Enter pro uložení, ESC pro zrušení
    itemElement.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            saveHoursInlineEdit(id);
        } else if (e.key === 'Escape') {
            cancelHoursInlineEdit(id);
        }
    });
}

// Uložení inline editace pracovní doby
async function saveHoursInlineEdit(id) {
    const itemElement = document.querySelector(`[data-id="${id}"]`);
    const dayInput = itemElement.querySelector('[data-field="day_name"] input');
    const timeInput = itemElement.querySelector('[data-field="time_range"] input');
    
    const day_name = dayInput.value.trim();
    const time_range = timeInput.value.trim();
    
    if (!day_name || !time_range) {
        showToast('Vyplňte všechna pole', 'error');
        return;
    }
    
    const is_closed = time_range.toLowerCase().includes('zavřeno') ? 1 : 0;
    
    const result = await apiCall('update_hours', { id, day_name, time_range, is_closed });
    
    if (result.success) {
        showToast('Pracovní doba byla upravena', 'success');
        loadHoursData();
    } else {
        showToast(result.message, 'error');
    }
}

// Zrušení inline editace pracovní doby
function cancelHoursInlineEdit(id) {
    loadHoursData(); // Znovu načte a vykreslí původní data
}

// Přidání nové pracovní doby
function addNewHour() {
    document.getElementById('hoursModalTitle').textContent = 'Přidat pracovní dobu';
    document.getElementById('hoursId').value = '';
    document.getElementById('dayName').value = '';
    document.getElementById('timeRange').value = '';
    document.getElementById('isClosed').value = '0';
    document.getElementById('hoursModal').style.display = 'block';
}

// Uložení pracovní doby z modalu
async function saveHours(e) {
    e.preventDefault();
    
    const id = document.getElementById('hoursId').value;
    const day_name = document.getElementById('dayName').value.trim();
    const time_range = document.getElementById('timeRange').value.trim();
    const is_closed = document.getElementById('isClosed').value;
    
    if (!day_name || !time_range) {
        showToast('Vyplňte všechna pole', 'error');
        return;
    }
    
    let result;
    if (id) {
        result = await apiCall('update_hours', { id, day_name, time_range, is_closed });
    } else {
        result = await apiCall('add_hours', { day_name, time_range, is_closed });
    }
    
    if (result.success) {
        showToast(id ? 'Pracovní doba byla upravena' : 'Pracovní doba byla přidána', 'success');
        closeHoursModal();
        loadHoursData();
    } else {
        showToast(result.message, 'error');
    }
}

// Smazání pracovní doby
async function deleteHours(id) {
    const item = hoursData.find(i => i.id == id);
    if (!item) return;
    
    if (!confirm(`Opravdu chcete smazat "${item.day_name}"?`)) {
        return;
    }
    
    const result = await apiCall('delete_hours', { id });
    
    if (result.success) {
        showToast('Pracovní doba byla smazána', 'success');
        loadHoursData();
    } else {
        showToast(result.message, 'error');
    }
}

// Zavření modalu pro pracovní dobu
function closeHoursModal() {
    document.getElementById('hoursModal').style.display = 'none';
}

// Aktualizace statistik pracovní doby
function updateHoursStats() {
    const totalHoursElement = document.getElementById('totalHours');
    const lastHoursUpdateElement = document.getElementById('lastHoursUpdate');
    
    if (totalHoursElement) {
        totalHoursElement.textContent = hoursData.length;
    }
    
    if (lastHoursUpdateElement) {
        if (hoursData.length > 0) {
            const lastUpdate = hoursData.reduce((latest, item) => {
                const itemDate = new Date(item.updated_at || item.created_at);
                return itemDate > latest ? itemDate : latest;
            }, new Date(0));
            
            lastHoursUpdateElement.textContent = formatDate(lastUpdate);
        } else {
            lastHoursUpdateElement.textContent = '-';
        }
    }
}

// === DRAG & DROP PRACOVNÍ DOBA ===

function handleHoursDragStart(e) {
    draggedHoursItem = this;
    this.classList.add('dragging');
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/html', this.outerHTML);
}

function handleHoursDragOver(e) {
    if (e.preventDefault) {
        e.preventDefault();
    }
    
    e.dataTransfer.dropEffect = 'move';
    
    const draggedElement = document.querySelector('.hours-item.dragging');
    if (!draggedElement || draggedElement === this) return;
    
    const container = document.getElementById('hoursList');
    const items = Array.from(container.children);
    const draggedIndex = items.indexOf(draggedElement);
    const targetIndex = items.indexOf(this);
    
    if (draggedIndex < targetIndex) {
        this.parentNode.insertBefore(draggedElement, this.nextSibling);
    } else {
        this.parentNode.insertBefore(draggedElement, this);
    }
    
    return false;
}

function handleHoursDrop(e) {
    if (e.stopPropagation) {
        e.stopPropagation();
    }
    
    updateHoursOrder();
    
    return false;
}

function handleHoursDragEnd(e) {
    this.classList.remove('dragging');
    draggedHoursItem = null;
}

// Aktualizace pořadí pracovní doby po drag & drop
async function updateHoursOrder() {
    const container = document.getElementById('hoursList');
    const items = Array.from(container.children);
    const newOrder = items.map((item, index) => ({
        id: parseInt(item.dataset.id),
        order: index
    }));
    
    const result = await apiCall('update_hours_order', { items: newOrder });
    
    if (result.success) {
        // Aktualizace lokálních dat
        newOrder.forEach(orderItem => {
            const item = hoursData.find(i => i.id === orderItem.id);
            if (item) {
                item.display_order = orderItem.order;
            }
        });
        showToast('Pořadí bylo aktualizováno', 'success');
    } else {
        showToast(result.message || 'Chyba při aktualizaci pořadí', 'error');
        loadHoursData(); // Obnovit při chybě
    }
}

// === SPRÁVA NASTAVENÍ OBJEDNÁVEK ===

// Načtení nastavení objednávek
async function loadBookingSettings() {
    const result = await apiCall('get_booking_settings');
    if (result.success) {
        bookingSettings = result.data;
        populateBookingForm();
    } else {
        showToast('Chyba při načítání nastavení objednávek', 'error');
    }
}

// Naplnění formuláře nastavení objednávek
function populateBookingForm() {
    document.getElementById('bookingEmail').value = bookingSettings.email || '';
    document.getElementById('bookingSubject').value = bookingSettings.subject || 'Nová objednávka z webu';
    document.getElementById('bookingActive').value = bookingSettings.active || '1';
}

// Uložení nastavení objednávek
async function saveBookingSettings() {
    const data = {
        email: document.getElementById('bookingEmail').value.trim(),
        subject: document.getElementById('bookingSubject').value.trim(),
        active: document.getElementById('bookingActive').value
    };
    
    if (!data.email) {
        showToast('Vyplňte e-mail pro objednávky', 'error');
        return;
    }
    
    const result = await apiCall('save_booking_settings', data);
    
    if (result.success) {
        showToast('Nastavení objednávek bylo uloženo', 'success');
        bookingSettings = data;
    } else {
        showToast(result.message || 'Chyba při ukládání', 'error');
    }
}

// === SPRÁVA KONTAKTNÍCH ÚDAJŮ ===

// Načtení kontaktních údajů
async function loadContactData() {
    const result = await apiCall('get_contact');
    if (result.success) {
        contactData = result.data;
        populateContactForm();
    } else {
        showToast('Chyba při načítání kontaktních údajů', 'error');
    }
}

// Naplnění formuláře kontaktních údajů
function populateContactForm() {
    document.getElementById('businessName').value = contactData.business_name || '';
    document.getElementById('address').value = contactData.address || '';
    document.getElementById('phone').value = contactData.phone || '';
    document.getElementById('ico').value = contactData.ico || '';
    document.getElementById('mapUrl').value = contactData.map_url || '';
}

// Uložení kontaktních údajů
async function saveContactInfo() {
    const data = {
        business_name: document.getElementById('businessName').value.trim(),
        address: document.getElementById('address').value.trim(),
        phone: document.getElementById('phone').value.trim(),
        ico: document.getElementById('ico').value.trim(),
        map_url: document.getElementById('mapUrl').value.trim()
    };
    
    if (!data.business_name || !data.address || !data.phone) {
        showToast('Vyplňte název firmy, adresu a telefon', 'error');
        return;
    }
    
    const result = await apiCall('save_contact', data);
    
    if (result.success) {
        showToast('Kontaktní údaje byly uloženy', 'success');
        contactData = data;
    } else {
        showToast(result.message || 'Chyba při ukládání', 'error');
    }
}

// === PŮVODNÍ FUNKCE PRO CENÍK ===

// Načtení ceníku
async function loadCenik() {
    const result = await apiCall('get_cenik');
    if (result.success) {
        cenikData = result.data;
        renderCenik();
        updateStats();
    } else {
        showToast('Chyba při načítání ceníku', 'error');
    }
}

// Vykreslení ceníku
function renderCenik() {
    const container = document.getElementById('cenikList');
    if (!container) return;
    
    const searchInput = document.getElementById('searchInput');
    const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
    
    // Filtrování
    let filteredData = cenikData.filter(item => 
        item.sluzba.toLowerCase().includes(searchTerm) ||
        item.cena.toLowerCase().includes(searchTerm)
    );
    
    // Řazení
    const sortBy = document.getElementById('sortBy');
    const sortValue = sortBy ? sortBy.value : 'order';
    filteredData = sortData(filteredData, sortValue);
    
    container.innerHTML = '';
    
    if (filteredData.length === 0) {
        container.innerHTML = `
            <div style="text-align: center; padding: 3rem; color: #a0aec0;">
                <i class="fas fa-search" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                <p>Žádné položky nenalezeny</p>
            </div>
        `;
        return;
    }
    
    filteredData.forEach(item => {
        const itemElement = createCenikItem(item);
        container.appendChild(itemElement);
    });
}

// Vytvoření HTML elementu pro položku ceníku
function createCenikItem(item) {
    const div = document.createElement('div');
    div.className = 'cenik-item';
    div.dataset.id = item.id;
    div.draggable = true;
    
    div.innerHTML = `
        <div class="drag-handle">
            <i class="fas fa-grip-vertical"></i>
        </div>
        <div class="item-content">
            <div class="item-service" data-field="sluzba">${escapeHtml(item.sluzba)}</div>
            <div class="item-price" data-field="cena">${escapeHtml(item.cena)}</div>
        </div>
        <div class="item-actions">
            <button class="btn btn-warning btn-sm" onclick="editItemInline(${item.id})">
                <i class="fas fa-edit"></i>
            </button>
            <button class="btn btn-danger btn-sm" onclick="deleteItem(${item.id})">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    
    // Drag & Drop event listenery
    div.addEventListener('dragstart', handleDragStart);
    div.addEventListener('dragover', handleDragOver);
    div.addEventListener('drop', handleDrop);
    div.addEventListener('dragend', handleDragEnd);
    
    return div;
}

// Inline editace ceníku
function editItemInline(id) {
    const item = cenikData.find(i => i.id == id);
    if (!item) return;
    
    const itemElement = document.querySelector(`[data-id="${id}"]`);
    if (!itemElement) return;
    
    itemElement.classList.add('edit-mode');
    
    const serviceElement = itemElement.querySelector('[data-field="sluzba"]');
    const priceElement = itemElement.querySelector('[data-field="cena"]');
    const actionsElement = itemElement.querySelector('.item-actions');
    
    // Vytvoř input fieldy
    serviceElement.innerHTML = `<input type="text" class="edit-input" value="${escapeHtml(item.sluzba)}" data-field="sluzba">`;
    priceElement.innerHTML = `<input type="text" class="edit-input" value="${escapeHtml(item.cena)}" data-field="cena">`;
    
    // Změň akce
    actionsElement.innerHTML = `
        <div class="edit-actions">
            <button class="btn btn-success btn-sm" onclick="saveInlineEdit(${id})">
                <i class="fas fa-check"></i>
            </button>
            <button class="btn btn-secondary btn-sm" onclick="cancelInlineEdit(${id})">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    // Focus na první input
    serviceElement.querySelector('input').focus();
    
    // Enter pro uložení, ESC pro zrušení
    itemElement.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            saveInlineEdit(id);
        } else if (e.key === 'Escape') {
            cancelInlineEdit(id);
        }
    });
}

// Uložení inline editace ceníku
async function saveInlineEdit(id) {
    const itemElement = document.querySelector(`[data-id="${id}"]`);
    const serviceInput = itemElement.querySelector('[data-field="sluzba"] input');
    const priceInput = itemElement.querySelector('[data-field="cena"] input');
    
    const sluzba = serviceInput.value.trim();
    const cena = priceInput.value.trim();
    
    if (!sluzba || !cena) {
        showToast('Vyplňte všechna pole', 'error');
        return;
    }
    
    const result = await apiCall('update_item', { id, sluzba, cena });
    
    if (result.success) {
        showToast('Položka byla upravena', 'success');
        loadCenik();
    } else {
        showToast(result.message, 'error');
    }
}

// Zrušení inline editace ceníku
function cancelInlineEdit(id) {
    loadCenik();
}

// Přidání nové položky ceníku
function addNewItem() {
    document.getElementById('modalTitle').textContent = 'Přidat položku';
    document.getElementById('itemId').value = '';
    document.getElementById('itemService').value = '';
    document.getElementById('itemPrice').value = '';
    document.getElementById('itemModal').style.display = 'block';
}

// Uložení položky ceníku z modalu
async function saveItem(e) {
    e.preventDefault();
    
    const id = document.getElementById('itemId').value;
    const sluzba = document.getElementById('itemService').value.trim();
    const cena = document.getElementById('itemPrice').value.trim();
    
    if (!sluzba || !cena) {
        showToast('Vyplňte všechna pole', 'error');
        return;
    }
    
    let result;
    if (id) {
        result = await apiCall('update_item', { id, sluzba, cena });
    } else {
        result = await apiCall('add_item', { sluzba, cena });
    }
    
    if (result.success) {
        showToast(id ? 'Položka byla upravena' : 'Položka byla přidána', 'success');
        closeModal();
        loadCenik();
    } else {
        showToast(result.message, 'error');
    }
}

// Smazání položky ceníku
async function deleteItem(id) {
    const item = cenikData.find(i => i.id == id);
    if (!item) return;
    
    if (!confirm(`Opravdu chcete smazat službu "${item.sluzba}"?`)) {
        return;
    }
    
    const result = await apiCall('delete_item', { id });
    
    if (result.success) {
        showToast('Položka byla smazána', 'success');
        loadCenik();
    } else {
        showToast(result.message, 'error');
    }
}

// Zavření modalu pro ceník
function closeModal() {
    document.getElementById('itemModal').style.display = 'none';
}

// Odhlášení
async function logout() {
    if (confirm('Opravdu se chcete odhlásit?')) {
        await apiCall('logout');
        window.location.href = 'login.php';
    }
}

// Filtrování ceníku
function filterCenik() {
    renderCenik();
}

// Řazení dat
function sortData(data, sortBy) {
    const sorted = [...data];
    
    switch (sortBy) {
        case 'order':
            return sorted.sort((a, b) => {
                const orderA = a.display_order !== null ? a.display_order : 999999;
                const orderB = b.display_order !== null ? b.display_order : 999999;
                return orderA - orderB;
            });
        case 'name':
            return sorted.sort((a, b) => a.sluzba.localeCompare(b.sluzba));
        case 'name_desc':
            return sorted.sort((a, b) => b.sluzba.localeCompare(a.sluzba));
        case 'price':
            return sorted.sort((a, b) => extractPrice(a.cena) - extractPrice(b.cena));
        case 'price_desc':
            return sorted.sort((a, b) => extractPrice(b.cena) - extractPrice(a.cena));
        default:
            return sorted.sort((a, b) => {
                const orderA = a.display_order !== null ? a.display_order : 999999;
                const orderB = b.display_order !== null ? b.display_order : 999999;
                return orderA - orderB;
            });
    }
}

// Extrakce čísla z ceny pro řazení
function extractPrice(priceString) {
    const match = priceString.match(/\d+/);
    return match ? parseInt(match[0]) : 0;
}

// Řazení ceníku
function sortCenik() {
    renderCenik();
}

// Aktualizace statistik ceníku
function updateStats() {
    const totalItemsElement = document.getElementById('totalItems');
    const lastUpdateElement = document.getElementById('lastUpdate');
    
    if (totalItemsElement) {
        totalItemsElement.textContent = cenikData.length;
    }
    
    if (lastUpdateElement) {
        if (cenikData.length > 0) {
            const lastUpdate = cenikData.reduce((latest, item) => {
                const itemDate = new Date(item.updated_at || item.created_at);
                return itemDate > latest ? itemDate : latest;
            }, new Date(0));
            
            lastUpdateElement.textContent = formatDate(lastUpdate);
        } else {
            lastUpdateElement.textContent = '-';
        }
    }
}

// Formátování data
function formatDate(date) {
    const now = new Date();
    const diffMs = now - date;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMs / 3600000);
    const diffDays = Math.floor(diffMs / 86400000);
    
    if (diffMins < 1) return 'Právě teď';
    if (diffMins < 60) return `před ${diffMins} min`;
    if (diffHours < 24) return `před ${diffHours} h`;
    if (diffDays < 7) return `před ${diffDays} dny`;
    
    return date.toLocaleDateString('cs-CZ');
}

// Zobrazení toast notifikace
function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    toast.className = `toast ${type}`;
    
    const icon = type === 'success' ? 'check' : type === 'error' ? 'exclamation-triangle' : 'info';
    toast.innerHTML = `<i class="fas fa-${icon}"></i> ${message}`;
    
    toast.classList.add('show');
    
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

// Escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// === DRAG & DROP PRO CENÍK ===

function handleDragStart(e) {
    draggedItem = this;
    this.classList.add('dragging');
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/html', this.outerHTML);
}

function handleDragOver(e) {
    if (e.preventDefault) {
        e.preventDefault();
    }
    
    e.dataTransfer.dropEffect = 'move';
    
    const draggedElement = document.querySelector('.cenik-item.dragging');
    if (!draggedElement || draggedElement === this) return;
    
    const container = document.getElementById('cenikList');
    const items = Array.from(container.children);
    const draggedIndex = items.indexOf(draggedElement);
    const targetIndex = items.indexOf(this);
    
    if (draggedIndex < targetIndex) {
        this.parentNode.insertBefore(draggedElement, this.nextSibling);
    } else {
        this.parentNode.insertBefore(draggedElement, this);
    }
    
    return false;
}

function handleDrop(e) {
    if (e.stopPropagation) {
        e.stopPropagation();
    }
    
    updateItemOrder();
    
    return false;
}

function handleDragEnd(e) {
    this.classList.remove('dragging');
    draggedItem = null;
}

// Aktualizace pořadí položek po drag & drop
async function updateItemOrder() {
    const container = document.getElementById('cenikList');
    const items = Array.from(container.children);
    const newOrder = items.map((item, index) => ({
        id: parseInt(item.dataset.id),
        order: index
    }));
    
    const result = await apiCall('update_order', { items: newOrder });
    
    if (result.success) {
        // Aktualizace lokálních dat
        newOrder.forEach(orderItem => {
            const item = cenikData.find(i => i.id === orderItem.id);
            if (item) {
                item.display_order = orderItem.order;
            }
        });
        showToast('Pořadí bylo aktualizováno', 'success');
    } else {
        showToast(result.message || 'Chyba při aktualizaci pořadí', 'error');
        loadCenik();
    }
}