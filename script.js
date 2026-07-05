// Global variables
let currentUser = null;
let priceTrendChart = null;

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    checkSession();
    
    // Only load stats and trends if elements exist on current page
    if (document.getElementById('supplierCount')) loadStats();
    if (document.getElementById('priceTrendChart')) loadTrends();
    
    // Setup event listeners
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const regRole = document.getElementById('regRole');
    
    if (loginForm) loginForm.addEventListener('submit', handleLogin);
    if (registerForm) registerForm.addEventListener('submit', handleRegister);
    if (regRole) regRole.addEventListener('change', toggleSupplierFields);
    
    // Initial search on compare page
    if (window.location.pathname.includes('compare.html')) {
        searchMaterials();
    }
});



// Session Management
async function checkSession() {
    try {
        const response = await fetch('php/auth/session.php');
        const data = await response.json();
        
        if (data.logged_in) {
            currentUser = data.user;
            showUserUI();
            
        }
    } catch (error) {
        console.error('Session check failed:', error);
    }
}

function showUserUI() {
    const navButtons = document.getElementById('navButtons');
    const userMenu = document.getElementById('userMenu');
    const userName = document.getElementById('userName');
    
    if (navButtons) navButtons.style.display = 'none';
    if (userMenu) userMenu.style.display = 'flex';
    if (userName && currentUser) userName.textContent = currentUser.name;
    
    // Show supplier specific elements
    if (currentUser && currentUser.role === 'supplier') {
        showSupplierPanel();
    }
}

function logout() {
    window.location.href = 'php/auth/logout.php';
}

// Modal Functions
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) modal.style.display = 'block';
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) modal.style.display = 'none';
}

function switchModal(targetModal) {
    document.querySelectorAll('.modal').forEach(modal => {
        modal.style.display = 'none';
    });
    openModal(targetModal);
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}

// Authentication Handlers
async function handleLogin(e) {
    e.preventDefault();
    
    const email = document.getElementById('loginEmail').value;
    const password = document.getElementById('loginPassword').value;
    
    try {
        const response = await fetch('php/api/login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, password })
        });
        
        const data = await response.json();
        
        if (data.success) {
            closeModal('loginModal');
            showNotification('Login successful!', 'success');
            
            
            // Redirect based on user role
            if (data.user.role === 'admin') {
                // Admin goes to admin dashboard
                window.location.href = 'admin.html';

            } else if (data.user.role === 'supplier') {
                // Supplier goes to compare page (where they can add prices)
                window.location.href = 'supplier.html';
            } else {
                // Customer goes to home page
                window.location.href = 'index.html';
            }
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        showNotification('Login failed', 'error');
    }
}

async function handleRegister(e) {
    e.preventDefault();
    
    const userData = {
        name: document.getElementById('regName').value,
        email: document.getElementById('regEmail').value,
        password: document.getElementById('regPassword').value,
        role: document.getElementById('regRole').value
    };
    
    if (userData.role === 'supplier') {
        userData.business_name = document.getElementById('businessName').value;
        userData.location = document.getElementById('businessLocation').value;
        userData.phone = document.getElementById('businessPhone').value;
    }
    
    try {
        const response = await fetch('php/api/register.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(userData)
        });
        
        const data = await response.json();
        
        if (data.success) {
            closeModal('registerModal');
            showNotification('Registration successful! Please login.', 'success');
            // Reset form
            document.getElementById('registerForm').reset();
            document.getElementById('supplierFields').style.display = 'none';
            setTimeout(() => {
                switchModal('loginModal');
            }, 1500);
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        showNotification('Registration failed', 'error');
    }
}

function toggleSupplierFields() {
    const role = document.getElementById('regRole').value;
    const supplierFields = document.getElementById('supplierFields');
    
    if (role === 'supplier') {
        supplierFields.style.display = 'block';
        // Make fields required
        document.getElementById('businessName').required = true;
        document.getElementById('businessLocation').required = true;
        document.getElementById('businessPhone').required = true;
    } else {
        supplierFields.style.display = 'none';
        // Remove required
        document.getElementById('businessName').required = false;
        document.getElementById('businessLocation').required = false;
        document.getElementById('businessPhone').required = false;
    }
}

// Search and Compare Functions
async function searchMaterials() {
    const query = document.getElementById('searchInput')?.value || '';
    const category = document.getElementById('categoryFilter')?.value || '';
    const location = document.getElementById('locationFilter')?.value || '';
    
    const container = document.getElementById('resultsContainer');
    if (!container) return;
    
    if (!query && !category) {
        container.innerHTML = '<div class="loading">🔍 Enter a material name to search (e.g., cement, bricks)</div>';
        return;
    }
    
    container.innerHTML = '<div class="loading">⏳ Searching for prices...</div>';
    
    try {
        const url = `php/api/search.php?query=${encodeURIComponent(query)}&category=${encodeURIComponent(category)}&location=${encodeURIComponent(location)}`;
        console.log('Fetching:', url);
        
        const response = await fetch(url);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const results = await response.json();
        console.log('Results:', results);
        
        // Check for message or error
        if (results.error) {
            container.innerHTML = `<div class="loading">❌ ${results.error}</div>`;
            return;
        }
        
        if (results.message) {
            container.innerHTML = `<div class="loading">⚠️ ${results.message}<br><br>Try adding sample data in phpMyAdmin.</div>`;
            return;
        }
        
        if (!results || results.length === 0) {
            container.innerHTML = '<div class="loading">😕 No results found. Try "cement", "bricks", or "steel"</div>';
            return;
        }
        
        container.innerHTML = results.map(result => `
            <div class="result-card">
                <div class="supplier-name">🏪 ${escapeHtml(result.supplier_name)}</div>
                <div class="material-name">📦 ${escapeHtml(result.material_name)}</div>
                <div class="price">💰 $${parseFloat(result.price).toFixed(2)}</div>
                <div class="location">📍 ${escapeHtml(result.location || 'Unknown')}</div>
                ${result.supplier_phone ? `<div class="phone">📞 ${escapeHtml(result.supplier_phone)}</div>` : ''}
                <div class="last-updated">🕐 Updated: ${new Date(result.date_recorded).toLocaleDateString()}</div>
            </div>
        `).join('');
        
    } catch (error) {
        console.error('Search error:', error);
        container.innerHTML = `<div class="loading">❌ Error: ${error.message}<br><br>Make sure XAMPP is running and you're using http://localhost/</div>`;
    }
}

// calculate estimate prices
function calculateEstimatee() {
    
    const houseSize = parseFloat(document.getElementById('houseSize')?.value);
    const roomCount = parseFloat(document.getElementById('roomCount')?.value);
    const buildingType = document.getElementById('buildingType')?.value || 'economy';
    const floors = parseFloat(document.getElementById('floors')?.value || 1);
   
    if  (houseSize < roomCount) {
        showNotification(`Too many rooms for the ${houseSize} Sqm`, 'error');
        
        return;
        }
    if (roomCount <= 0){
        showNotification(`minimum room number is 1`, 'error');
        return;

    }
  
    
    
    if  (houseSize < 1) {
        alert('checked 2');
        showNotification('House Size too tiny', 'error');
        
        return;   
        }

    if (!houseSize || !roomCount) {
        showNotification('Please fill in house size and number of rooms', 'error');
        return;
    }
    
    // Material ratios (simplified for demo)
    const ratios = {
        cement: houseSize * 2 * floors * roomCount,
        bricks: houseSize * 100 * floors * roomCount,
        sand: houseSize * 0.5 * floors * roomCount,
        steel: houseSize * 1.8 * floors
    };
    
    // Price multipliers based on building type
    const multipliers = {
        economy: 0.8,
        standard: 1,
        premium: 1.5
    };

  
    
    const multiplier = multipliers[buildingType];
    
    // Current average prices 
    const prices = {
        cement: 12,
        bricks: 0.30,
        sand: 9,
        steel: 3
    };
    
    const materials = [
        { name: 'Cement (50kg bags)', quantity: Math.ceil(ratios.cement), unit: 'bags', price: prices.cement },
        { name: 'Bricks', quantity: Math.ceil(ratios.bricks), unit: 'pieces', price: prices.bricks },
        { name: 'Sand', quantity: Math.ceil(ratios.sand), unit: 'cubic meters', price: prices.sand },
        { name: 'Steel (12mm)', quantity: Math.ceil(ratios.steel), unit: 'lengths', price: prices.steel }
    ];

    
    
      let total = 0;

    if (buildingType === 'standard' && houseSize <= 4 ){
       
        const resultsDiv = document.getElementById('estimatorResults');
        const totalDiv = document.getElementById('totalCost');
    
        let Total = 0; 
   
        if (totalDiv) totalDiv.innerHTML = `Total Estimated Cost: $${Math.round(Total).toLocaleString()}`;
        if (resultsDiv) resultsDiv.style.display = 'block';

        showNotification('minimum standard size 5 sqm', 'error');
        
        return total;

    }

    if (buildingType === 'premium' && houseSize <= 9 ){
       
        const resultsDiv = document.getElementById('estimatorResults');
        const totalDiv = document.getElementById('totalCost');
    
        let Total = 0; 
   
        if (totalDiv) totalDiv.innerHTML = `Total Estimated Cost: $${Math.round(Total).toLocaleString()}`;
        if (resultsDiv) resultsDiv.style.display = 'block';

        showNotification('minimum standard size 10 sqm', 'error');
        
        return total;

    }

    const breakdown = materials.filter (m => m && m.quantity != null).map(m =>  {

   
       
        let cost = m.quantity * m.price * multiplier;
        total += cost;
        
        return `
            <div class="estimate-item">
                <span>${m.name}</span>
                <span>${m.quantity.toLocaleString()} ${m.unit} × $${m.price} = $${Math.round(cost).toLocaleString()}</span>
            </div>
        `;
    }).join('');
    
    
    
    const resultsDiv = document.getElementById('estimatorResults');
    const breakdownDiv = document.getElementById('estimateBreakdown');
    const totalDiv = document.getElementById('totalCost');
    
    let Total = total; 
   
    if (breakdownDiv)  breakdownDiv.innerHTML = breakdown;
    if (totalDiv) totalDiv.innerHTML = `Total Estimated Cost: $${Math.round(Total).toLocaleString()}`;
    if (resultsDiv) resultsDiv.style.display = 'block';
}

// Market Trends
async function loadTrends() {
    const material = document.getElementById('trendMaterial')?.value || 'cement';
    const months = document.getElementById('trendPeriod')?.value || 3;
    
    try {
        const response = await fetch(`php/api/get_trends.php?material=${material}&months=${months}`);
        const data = await response.json();

        if (data.error) {
            console.error('Error loading trends:', data.error);
            return;
        }
        
        if (data.dates && data.dates.length > 0) {
            updateChart(data.dates, data.prices);
            updatePrediction(data.prediction);
        } else {
            // Shows demo data if no real data exists
            showDemoTrends();
        }
       
    } catch (error) {
        console.error('Failed to load trends:', error);
        showDemoTrends();
    }
}

function showDemoTrends() {
    const demoDates = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
    const demoPrices = [11.50, 11.80, 12.00, 12.30, 12.50, 12.75];
    updateChart(demoDates, demoPrices);
    updatePrediction({
        next_month: 13.00,
        trend: 'increasing',
        change: 2.0
    });
}

function updateChart(dates, prices) {
    const ctx = document.getElementById('priceTrendChart')?.getContext('2d');
    if (!ctx) return;
    
    // Destroy existing chart if it exists
    if (window.priceTrendChart) {
        window.priceTrendChart.destroy();
    }
    
    window.priceTrendChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                label: 'Average Price ($)',
                data: prices,
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top'
                }
            }
        }
    });
}

function updatePrediction(prediction) {
    const predictionText = document.getElementById('predictionText');
    if (!predictionText) return;
    
    if (prediction) {
        const trendIcon = prediction.trend === 'increasing' ? '📈' : 
                         (prediction.trend === 'decreasing' ? '📉' : '➡️');
        predictionText.innerHTML = `
            ${trendIcon} Next month's predicted price: <strong>$${prediction.next_month}</strong><br>
            Trend: ${prediction.trend} (${prediction.change}% change)
        `;
    } else {
        predictionText.innerHTML = 'Insufficient data for prediction. Add more price records.';
    }
}

// Load Dashboard Stats
async function loadStats() {
    try {
        const response = await fetch('php/api/get_stats.php');
        const stats = await response.json();
        
        const supplierCountEl = document.getElementById('supplierCount');
        const materialCountEl = document.getElementById('materialCount');
        const priceUpdateEl = document.getElementById('priceUpdateCount');
        
        if (supplierCountEl) supplierCountEl.textContent = stats.supplier_count || 0;
        if (materialCountEl) materialCountEl.textContent = stats.material_count || 0;
        if (priceUpdateEl) priceUpdateEl.textContent = stats.price_updates || 0;
    } catch (error) {
        console.error('Failed to load stats:', error);
    }
}

// Supplier Panels
function showSupplierPanel() {
    const compareSection = document.getElementById('compare');
    if (compareSection) {
        const existingPanel = document.querySelector('.supplier-panel');
        if (!existingPanel) {
            const addPriceBtn = document.createElement('div');
            addPriceBtn.className = 'supplier-panel';
            addPriceBtn.innerHTML = `
                <div class="supplier-actions" style="margin-top: 2rem; padding: 1.5rem; background: var(--light); border-radius: 0.5rem;">
                    <h3>📊 Supplier Dashboard</h3>
                    <p>Welcome back, ${currentUser?.name || 'Supplier'}</p>
                    <button class="btn btn-primary" onclick="openAddPriceModal()">➕ Add New Price</button>
                </div>
            `;
            compareSection.appendChild(addPriceBtn);
        }
    }
}

function openAddPriceModal() {
    const material = prompt('Enter material name (e.g., "Portland Cement 50kg"):');
    if (!material) return;
    
    const price = prompt('Enter price in USD (e.g., 12.50):');
    if (!price) return;
    
    const priceNum = parseFloat(price);
    if (isNaN(priceNum) || priceNum <= 0) {
        showNotification('Please enter a valid price', 'error');
        return;
    }
    
    const stock = prompt('Enter quantity in stock (e.g., "100 bags", "500 pieces", "50 units"):');
    
    addSupplierPrice(material, priceNum, stock);
}


async function addSupplierPrice(material, price, stock) {
    try {
        const response = await fetch('php/api/add_price.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ material, price, stock })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('✅ Price and stock added successfully!', 'success');
            searchMaterials(); // Refresh search results
        } else {
            showNotification('❌ ' + data.message, 'error');
        }
    } catch (error) {
        console.error('Add price error:', error);
        showNotification('❌ Failed to add price', 'error');
    }
}

async function addSupplierPrice(material, price) {
    try {
        const response = await fetch('php/api/add_price.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ material, price })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('Price added successfully!', 'success');
            searchMaterials(); // Refresh search results
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        showNotification('Failed to add price', 'error');
    }
}

// Utility Functions
function escapeHtml(str) {
    if (!str) return '';
    return str
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function formatPrice(price) {
    return parseFloat(price).toFixed(2);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-ZA');
}

function showNotification(message, type) {
    // Remove existing notification
    const existing = document.querySelector('.notification');
    if (existing) existing.remove();
    
    // Create new notification
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        padding: 12px 24px;
        background: ${type === 'success' ? '#10b981' : '#ef4444'};
        color: white;
        border-radius: 8px;
        z-index: 2000;
        animation: slideIn 0.3s ease;
        font-family: 'Inter', sans-serif;
        font-weight: 500;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

function scrollToCompare() {
    const compareSection = document.getElementById('compare');
    if (compareSection) {
        compareSection.scrollIntoView({ behavior: 'smooth' });
    }
}

function toggleMobileMenu() {
    const navMenu = document.getElementById('navMenu');
    if (navMenu) {
        navMenu.style.display = navMenu.style.display === 'flex' ? 'none' : 'flex';
    }
}

//  animation keyframes
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);