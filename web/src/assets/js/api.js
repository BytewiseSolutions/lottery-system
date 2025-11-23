const API_BASE = 'http://localhost:3002/api';

// Fetch lottery draws and update homepage
async function fetchDraws() {
    try {
        const response = await fetch(`${API_BASE}/draws`);
        const draws = await response.json();
        updateHomepageDraws(draws);
    } catch (error) {
        console.error('Error fetching draws:', error);
    }
}

// Fetch lottery results
async function fetchResults() {
    try {
        const response = await fetch(`${API_BASE}/results`);
        const results = await response.json();
        updateResultsPage(results);
    } catch (error) {
        console.error('Error fetching results:', error);
    }
}

// Update homepage and lotteries page lottery cards
function updateHomepageDraws(draws) {
    const drawItems = document.querySelectorAll('.single-draw');
    draws.forEach((draw, index) => {
        if (drawItems[index]) {
            const h4 = drawItems[index].querySelector('h4');
            const timeElement = drawItems[index].querySelector('.time h6');
            
            if (h4) {
                h4.innerHTML = `${draw.name}<br>For ${formatDate(draw.nextDraw)}<br>${draw.jackpot}`;
            }
            
            if (timeElement) {
                updateCountdown(timeElement, draw.nextDraw);
            }
        }
    });
}

// Update results page
function updateResultsPage(results) {
    const resultItems = document.querySelectorAll('.single-list');
    
    // Hide all result items first
    resultItems.forEach(item => item.style.display = 'none');
    
    // Show and update only available results
    results.forEach((result, index) => {
        if (resultItems[index]) {
            const item = resultItems[index];
            item.style.display = 'block';
            
            // Update game name
            const gameName = item.querySelector('.left h4');
            if (gameName) gameName.textContent = result.game;
            
            // Update date
            const dateElement = item.querySelector('.right h6');
            if (dateElement) dateElement.textContent = formatDate(result.date);
            
            // Update numbers
            const numbersContainer = item.querySelector('.numbers');
            if (numbersContainer) {
                const allNumbers = [...result.numbers, ...result.bonusNumbers];
                numbersContainer.innerHTML = allNumbers.map(num => `<span>${num}</span>`).join('');
            }
            
            // Update pool money
            const poolMoney = item.querySelector('.light-area-bottom .right h6');
            if (poolMoney) poolMoney.textContent = result.poolMoney;
        }
    });
    
    // Show message if no results
    if (results.length === 0) {
        const resultBox = document.querySelector('.result-box');
        if (resultBox) {
            resultBox.innerHTML = '<h4 class="box-header">No Lottery Results Yet</h4><p>Results will appear after users submit entries.</p>';
        }
    }
}

// Format date helper
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
}

// Update countdown timer
function updateCountdown(element, targetDate) {
    function updateTimer() {
        const now = new Date().getTime();
        const target = new Date(targetDate).getTime();
        const distance = target - now;
        
        if (distance > 0) {
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            element.textContent = `${days.toString().padStart(2, '0')} Days ${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        } else {
            element.textContent = '00 Days 00:00:00';
        }
    }
    
    updateTimer();
    setInterval(updateTimer, 1000);
}

// Test API connection
async function testAPI() {
    try {
        const response = await fetch(`${API_BASE}/health`);
        const data = await response.json();
        console.log('API Status:', data.status);
        return true;
    } catch (error) {
        console.error('API Connection Failed:', error);
        return false;
    }
}

// Submit lottery entry (requires authentication)
async function submitEntry(lottery, numbers, bonusNumbers) {
    const token = getAuthToken();
    
    if (!token) {
        throw new Error('Please login first to submit entry');
    }
    
    try {
        const response = await fetch(`${API_BASE}/play`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify({
                lottery,
                numbers,
                bonusNumbers
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            console.log('✅ Entry submitted:', result);
            await fetchDraws();
            return result;
        } else {
            throw new Error(result.error);
        }
    } catch (error) {
        throw error;
    }
}

// Authentication functions
async function register(email, password, confirmPassword) {
    try {
        const response = await fetch(`${API_BASE}/register`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, password, confirmPassword })
        });
        
        const result = await response.json();
        
        if (result.success) {
            localStorage.setItem('token', result.token);
            localStorage.setItem('user', JSON.stringify(result.user));
            updateAuthUI(true, result.user);
            return result;
        } else {
            throw new Error(result.error);
        }
    } catch (error) {
        throw error;
    }
}

async function login(email, password) {
    try {
        const response = await fetch(`${API_BASE}/login`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, password })
        });
        
        const result = await response.json();
        
        if (result.success) {
            localStorage.setItem('token', result.token);
            localStorage.setItem('user', JSON.stringify(result.user));
            updateAuthUI(true, result.user);
            return result;
        } else {
            throw new Error(result.error);
        }
    } catch (error) {
        throw error;
    }
}

function logout() {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    updateAuthUI(false);
}

function isLoggedIn() {
    return !!localStorage.getItem('token');
}

function getAuthToken() {
    return localStorage.getItem('token');
}

function updateAuthUI(loggedIn, user = null) {
    const loginBtns = document.querySelectorAll('[data-target="#loginModal"]');
    const registerBtns = document.querySelectorAll('[data-target="#registerModal"]');
    
    if (loggedIn && user) {
        loginBtns.forEach(btn => {
            btn.textContent = 'Logout';
            btn.onclick = (e) => {
                e.preventDefault();
                logout();
                location.reload();
            };
        });
        registerBtns.forEach(btn => btn.style.display = 'none');
    } else {
        loginBtns.forEach(btn => {
            btn.textContent = 'Login In';
            btn.onclick = null;
        });
        registerBtns.forEach(btn => btn.style.display = 'inline-block');
    }
}

// Update header money display
async function updateHeaderMoney() {
    try {
        const response = await fetch(`${API_BASE}/pool`);
        const poolData = await response.json();
        
        const totalMoney = Object.values(poolData).reduce((sum, amount) => sum + amount, 0);
        const moneyElements = document.querySelectorAll('.mony');
        
        moneyElements.forEach(element => {
            element.textContent = `$ ${totalMoney.toFixed(3)}`;
        });
    } catch (error) {
        console.error('Error updating header money:', error);
    }
}

// Make functions available globally
window.submitLotteryEntry = submitEntry;
window.register = register;
window.login = login;
window.logout = logout;
window.isLoggedIn = isLoggedIn;

// Initialize immediately when page loads
(async function() {
    console.log('🚀 Starting API integration...');
    
    // Load data immediately
    try {
        await fetchDraws();
        await fetchResults();
        await updateHeaderMoney();
        console.log('✅ Data loaded from API');
        
        // Update data every 30 seconds
        setInterval(async () => {
            await fetchDraws();
            await updateHeaderMoney();
        }, 30000);
    } catch (error) {
        console.log('❌ API not available:', error);
    }
})();

// Also run when DOM is ready as backup
document.addEventListener('DOMContentLoaded', async () => {
    // Initialize static countdown timers
    document.querySelectorAll('.countdown-timer').forEach(timer => {
        const targetDate = timer.getAttribute('data-target');
        if (targetDate) {
            updateCountdown(timer, targetDate);
        }
    });
    
    // Double-check data is loaded
    setTimeout(async () => {
        await fetchDraws();
        await updateHeaderMoney();
    }, 100);
});