// Globální proměnné
let cenikData = [];
let draggedItem = null;
let isEditMode = false;

// Inicializace po načtení stránky
document.addEventListener('DOMContentLoaded', function() {
    loadCenik();
    initializeEventListeners();
});

// Event listenery
function initializeEventListeners() {
    // Vyhledávání
    document.getElementById('searchInput').addEventListener('input', filterCenik);
    
    // Řazení
    document.getElementById('sortBy').addEventListener('change', sortCenik);
    
    // Modal form
    document.getElementById('itemForm').addEventListener('submit', saveItem);
    
    // Zavření modalu při kliknutí mimo obsah
    document.getElementById('itemModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
    
    // ESC pro zavření modalu
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && document.getElementById('itemModal').style.display === 'block') {
            closeModal();
        }
    });
}

// API volání
async function apiCall(action, data = {}) {
    try {
        const response = await fetch('../api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ action, ...data })
        });
        return await response.json();
    } catch (error) {
        console.error('API Error:', error);
        showToast('Chyba připojení', 'error');
        return { success: false, message: 'Chyba připojení' };
    }
}

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
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    
    // Filtrování
    let filteredData = cenikData.filter(item => 
        item.sluzba.toLowerCase().includes(searchTerm) ||
        item.cena.toLowerCase().includes(searchTerm)
    );
    
    // Řazení
    const sortBy = document.getElementById('sortBy').value;
    filteredData = sortData(filteredData, sortBy);
    
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

// Vytvoření HTML elementu pro položku
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

// Inline editace
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

// Uložení inline editace
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

// Zrušení inline editace
function cancelInlineEdit(id) {
    loadCenik(); // Znovu načte a vykreslí původní data
}

// Přidání nové položky
function addNewItem() {
    document.getElementById('modalTitle').textContent = 'Přidat položku';
    document.getElementById('itemId').value = '';
    document.getElementById('itemService').value = '';
    document.getElementById('itemPrice').value = '';
    document.getElementById('itemModal').style.display = 'block';
}

// Uložení položky z modalu
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

// Smazání položky
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

// Zavření modalu
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
        case 'name':
            return sorted.sort((a, b) => a.sluzba.localeCompare(b.sluzba));
        case 'name_desc':
            return sorted.sort((a, b) => b.sluzba.localeCompare(a.sluzba));
        case 'price':
            return sorted.sort((a, b) => extractPrice(a.cena) - extractPrice(b.cena));
        case 'price_desc':
            return sorted.sort((a, b) => extractPrice(b.cena) - extractPrice(a.cena));
        default:
            return sorted.sort((a, b) => a.id - b.id);
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

// Aktualizace statistik
function updateStats() {
    document.getElementById('totalItems').textContent = cenikData.length;
    
    if (cenikData.length > 0) {
        const lastUpdate = cenikData.reduce((latest, item) => {
            const itemDate = new Date(item.updated_at || item.created_at);
            return itemDate > latest ? itemDate : latest;
        }, new Date(0));
        
        document.getElementById('lastUpdate').textContent = formatDate(lastUpdate);
    } else {
        document.getElementById('lastUpdate').textContent = '-';
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

// === DRAG & DROP FUNKCIONALITA ===

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
    
    const draggedElement = document.querySelector('.dragging');
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
    
    // Zde byste měli zavolat API pro uložení nového pořadí
    // Pro jednoduchost nyní pouze aktualizujeme lokální data
    newOrder.forEach(orderItem => {
        const item = cenikData.find(i => i.id === orderItem.id);
        if (item) {
            item.order = orderItem.order;
        }
    });
    
    showToast('Pořadí bylo aktualizováno', 'success');
}