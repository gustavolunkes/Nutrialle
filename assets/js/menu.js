// ===== MENU MOBILE =====
const mobileMenuToggle = document.getElementById('mobileMenuToggle');
const mainNav = document.getElementById('mainNav');
const mobileOverlay = document.getElementById('mobileOverlay');
const body = document.body;

// Abrir/Fechar menu mobile
mobileMenuToggle.addEventListener('click', () => {
    const isActive = mainNav.classList.contains('active');
    
    if (isActive) {
        closeMenu();
    } else {
        openMenu();
    }
});

function openMenu() {
    mainNav.classList.add('active');
    mobileOverlay.classList.add('active');
    mobileMenuToggle.classList.add('active');
    body.classList.add('menu-open');
}

function closeMenu() {
    mainNav.classList.remove('active');
    mobileOverlay.classList.remove('active');
    mobileMenuToggle.classList.remove('active');
    body.classList.remove('menu-open');
    
    // Remove todas as classes active ao fechar
    document.querySelectorAll('.has-submenu').forEach(item => {
        item.classList.remove('active');
    });
    document.querySelectorAll('.dropdown, .dropdown-sub').forEach(dropdown => {
        dropdown.classList.remove('active');
    });
}

// Fechar menu ao clicar no overlay
mobileOverlay.addEventListener('click', closeMenu);

// ===== SUBMENU MOBILE (Accordion) =====
function initMobileMenu() {
    const hasSubmenu = document.querySelectorAll('.has-submenu');
    
    hasSubmenu.forEach(item => {
        const link = item.querySelector(':scope > a');
        const dropdown = item.querySelector('.dropdown, .dropdown-sub');
        
        // Remove eventos anteriores para evitar duplicação
        const newLink = link.cloneNode(true);
        link.parentNode.replaceChild(newLink, link);
        
        newLink.addEventListener('click', (e) => {
            // No mobile, se tem submenu, apenas abre/fecha (não navega)
            if (window.innerWidth <= 768 && dropdown) {
                e.preventDefault();
                e.stopPropagation();
                
                // Fecha outros submenus do mesmo nível
                const parent = item.parentElement;
                const siblings = Array.from(parent.children).filter(child => 
                    child !== item && child.classList.contains('has-submenu')
                );
                
                siblings.forEach(sibling => {
                    sibling.classList.remove('active');
                    const siblingDropdown = sibling.querySelector('.dropdown, .dropdown-sub');
                    if (siblingDropdown) {
                        siblingDropdown.classList.remove('active');
                    }
                });
                
                // Toggle do item atual
                item.classList.toggle('active');
                if (dropdown) {
                    dropdown.classList.toggle('active');
                }
            }
        });
    });
}

// ===== FECHA MENU AO CLICAR EM LINK FINAL =====
function initLinkCloseMenu() {
    const allLinks = document.querySelectorAll('.main-nav a');
    
    allLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            const parent = link.parentElement;
            const hasDropdown = parent.classList.contains('has-submenu');
            
            // Se NÃO tem submenu e está no mobile, fecha o menu
            if (!hasDropdown && window.innerWidth <= 768) {
                // Pequeno delay para permitir a navegação antes de fechar
                setTimeout(() => {
                    closeMenu();
                }, 100);
            }
        });
    });
}

// ===== INICIALIZAÇÃO =====
initMobileMenu();
initLinkCloseMenu();

// ===== REINICIA MENU AO REDIMENSIONAR =====
let resizeTimer;
window.addEventListener('resize', () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(() => {
        if (window.innerWidth > 768) {
            closeMenu();
        } else {
            initMobileMenu();
            initLinkCloseMenu();
        }
    }, 250);
});