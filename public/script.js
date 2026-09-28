function getCurrentBSDate() {
    const bsMonths = [
        'बैशाख', 'जेठ', 'असार', 'साउन', 'भदौ', 'असोज',
        'कार्तिक', 'मंसिर', 'पुस', 'माघ', 'फागुन', 'चैत'
    ];

    // Accurate days-per-month for BS years 2070-2090
    // Source: Nepal Panchanga Nirnayak Samiti via sonill/nepali-dates
    const bsData = {
        2070: [31,31,31,32,31,31,29,30,30,29,30,30],
        2071: [31,31,32,31,31,31,30,29,30,29,30,30],
        2072: [31,32,31,32,31,30,30,29,30,29,30,30],
        2073: [31,32,31,32,31,30,30,30,29,29,30,31],
        2074: [31,31,31,32,31,31,30,29,30,29,30,30],
        2075: [31,31,32,31,31,31,30,29,30,29,30,30],
        2076: [31,32,31,32,31,30,30,30,29,29,30,30],
        2077: [31,32,31,32,31,30,30,30,29,30,29,31],
        2078: [31,31,31,32,31,31,30,29,30,29,30,30],
        2079: [31,31,32,31,31,31,30,29,30,29,30,30],
        2080: [31,32,31,32,31,30,30,30,29,29,30,30],
        2081: [31,31,32,32,31,30,30,30,29,30,30,30],
        2082: [30,32,31,32,31,30,30,30,29,30,30,30],
        2083: [31,31,32,31,31,30,30,30,29,30,30,30],
        2084: [31,31,32,31,31,30,30,30,29,30,30,30],
        2085: [31,32,31,32,30,31,30,30,29,30,30,30],
        2086: [30,32,31,32,31,30,30,30,29,30,30,30],
        2087: [31,31,32,31,31,31,30,30,29,30,30,30],
        2088: [30,31,32,32,30,31,30,30,29,30,30,30],
        2089: [30,32,31,32,31,30,30,30,29,30,30,30],
        2090: [30,32,31,32,31,30,30,30,29,30,30,30],
    };

    const now = new Date();
    const refDate = new Date(2023, 3, 14);
    const refYear = 2080;

    const diffDays = Math.floor((now - refDate) / (1000 * 60 * 60 * 24));

    if (diffDays < 0) {
        return { year: 2079, month: 12, day: 30, monthName: 'चैत' };
    }

    let remaining = diffDays;
    let bsYear = refYear;

    while (true) {
        const yearMonths = bsData[bsYear];
        if (!yearMonths) break;
        const yearDays = yearMonths.reduce(function (a, b) { return a + b; }, 0);
        if (remaining < yearDays) break;
        remaining -= yearDays;
        bsYear++;
    }

    const months = bsData[bsYear] || bsData[2083];
    let bsMonthIndex = 0;
    while (bsMonthIndex < 12 && remaining >= months[bsMonthIndex]) {
        remaining -= months[bsMonthIndex];
        bsMonthIndex++;
    }

    return {
        year: bsYear,
        month: bsMonthIndex + 1,
        day: remaining + 1,
        monthName: bsMonths[bsMonthIndex]
    };
}

function formatDate(date) {
    const options = { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        weekday: 'long'
    };
    return date.toLocaleDateString('ne-NP', options);
}

function toNepaliDigits(num) {
    const nepaliDigits = ['\u0966', '\u0967', '\u0968', '\u0969', '\u096A', '\u096B', '\u096C', '\u096D', '\u096E', '\u096F'];
    return String(num).replace(/\d/g, function (d) { return nepaliDigits[d]; });
}

function updateDateDisplay() {
    const bsDate = getCurrentBSDate();
    const adDate = new Date();
    
    const bsDateString = toNepaliDigits(bsDate.year) + ' \u0938\u093E\u0932 ' + bsDate.monthName + ' ' + toNepaliDigits(bsDate.day) + ' \u0917\u0924\u0947';
    const adDateString = formatDate(adDate);
    
    const bsElement = document.getElementById('current-date-bs');
    const adElement = document.getElementById('current-date-ad');
    
    if (bsElement) {
        bsElement.textContent = bsDateString;
    }
    
    if (adElement) {
        adElement.textContent = adDateString;
    }
}

// Mobile navigation toggle
function initMobileNav() {
    const navToggle = document.querySelector('.nav-toggle');
    const navMenu = document.querySelector('.nav-menu');
    
    if (navToggle && navMenu) {
        navToggle.addEventListener('click', () => {
            navMenu.classList.toggle('active');
            
            // Animate hamburger menu
            const spans = navToggle.querySelectorAll('span');
            spans.forEach((span, index) => {
                if (navMenu.classList.contains('active')) {
                    if (index === 0) span.style.transform = 'rotate(45deg) translate(5px, 5px)';
                    if (index === 1) span.style.opacity = '0';
                    if (index === 2) span.style.transform = 'rotate(-45deg) translate(7px, -6px)';
                } else {
                    span.style.transform = 'none';
                    span.style.opacity = '1';
                }
            });
        });
    }
}

// Smooth scrolling for anchor links
function initSmoothScrolling() {
    const links = document.querySelectorAll('a[href^="#"]');
    
    links.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const targetId = link.getAttribute('href');
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

// News loading animation
function initNewsLoading() {
    const newsItems = document.querySelectorAll('.news-item, .trending-item, .story-item');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, {
        threshold: 0.1
    });
    
    newsItems.forEach(item => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(30px)';
        item.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(item);
    });
}

// Search functionality
function initSearch() {
    const searchInput = document.querySelector('.search-input');
    const searchResults = document.querySelector('.search-results');
    
    if (searchInput) {
        let searchTimeout;
        
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            const query = e.target.value.trim();
            
            if (query.length > 2) {
                searchTimeout = setTimeout(() => {
                    performSearch(query);
                }, 300);
            } else {
                hideSearchResults();
            }
        });
    }
}

function performSearch(query) {
    // This would typically make an API call
    // For now, we'll just show a loading state
    console.log('Searching for:', query);
    
    // Simulate search results
    const mockResults = [
        { title: 'समाचार १', category: 'राजनीति', time: '१ घण्टा अगाडि' },
        { title: 'समाचार २', category: 'बिजनेस', time: '२ घण्टा अगाडि' },
        { title: 'समाचार ३', category: 'खेलकुद', time: '३ घण्टा अगाडि' }
    ];
    
    displaySearchResults(mockResults);
}

function displaySearchResults(results) {
    const searchResults = document.querySelector('.search-results');
    if (searchResults) {
        searchResults.innerHTML = results.map(result => `
            <div class="search-result-item">
                <h5>${result.title}</h5>
                <span class="search-category">${result.category}</span>
                <span class="search-time">${result.time}</span>
            </div>
        `).join('');
        searchResults.style.display = 'block';
    }
}

function hideSearchResults() {
    const searchResults = document.querySelector('.search-results');
    if (searchResults) {
        searchResults.style.display = 'none';
    }
}

// Form validation for news upload
function initFormValidation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            
            const formData = new FormData(form);
            const title = formData.get('title');
            const content = formData.get('content');
            const category = formData.get('category');
            
            if (!title || !content || !category) {
                showNotification('कृपया सबै फिल्डहरू भर्नुहोस्', 'error');
                return;
            }
            
            // Simulate form submission
            showNotification('समाचार सफलतापूर्वक अपलोड भयो', 'success');
            form.reset();
        });
    });
}

// Notification system
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#17a2b8'};
        color: white;
        padding: 15px 20px;
        border-radius: 5px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        z-index: 10000;
        animation: slideInRight 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Add CSS for animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);

// Initialize everything when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    try { updateDateDisplay(); } catch (e) { console.warn('updateDateDisplay failed:', e); }
    try { initMobileNav(); } catch (e) { console.warn('initMobileNav failed:', e); }
    try { initSmoothScrolling(); } catch (e) { console.warn('initSmoothScrolling failed:', e); }
    try { initNewsLoading(); } catch (e) { console.warn('initNewsLoading failed:', e); }
    try { initSearch(); } catch (e) { console.warn('initSearch failed:', e); }
    try { initFormValidation(); } catch (e) { console.warn('initFormValidation failed:', e); }
    
    // Update date every minute
    setInterval(() => { try { updateDateDisplay(); } catch (e) {} }, 60000);
});

// Handle page visibility change
document.addEventListener('visibilitychange', () => {
    if (!document.hidden) {
        updateDateDisplay();
    }
});

// Export functions for use in other scripts
window.SankalpKhabar = {
    updateDateDisplay,
    showNotification,
    performSearch
};
