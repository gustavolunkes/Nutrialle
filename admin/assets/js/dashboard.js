
document.querySelectorAll('.menu-item > a').forEach(item => {
    item.addEventListener('click', function(e) {
        const parent = this.parentElement;
        const hasSubmenu = parent.querySelector('.submenu');
                
        if (hasSubmenu) {
            e.preventDefault();
            parent.classList.toggle('active');
        }
    });
});