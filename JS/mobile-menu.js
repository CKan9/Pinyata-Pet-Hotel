function setupMobileMenu() {
    const hamburger = document.querySelector('.hamburger');
    const navLinks = document.getElementById('nav-links');

    // Toggle main menu
    hamburger.addEventListener('click', (e) => {
        e.stopPropagation();
        navLinks.classList.toggle('active');
        hamburger.classList.toggle('active');
    });

    // Handle user dropdown toggle
    document.addEventListener('click', (e) => {
        const userDropdown = e.target.closest('.user-dropdown');
        const dropdownToggle = e.target.closest('.user-dropdown > a');

        // Toggle dropdown
        if (dropdownToggle) {
            e.preventDefault();
            userDropdown.classList.toggle('active');
            
            // Close other dropdowns
            document.querySelectorAll('.user-dropdown')
                .forEach(d => d !== userDropdown && d.classList.remove('active'));
        }
        
        // Close menus when clicking outside
        if (!e.target.closest('#navbar')) {
            navLinks.classList.remove('active');
            hamburger.classList.remove('active');
            document.querySelectorAll('.user-dropdown').forEach(d => {
                d.classList.remove('active');
            });
        }
    });
}

// Initialize after all content loads
document.addEventListener('DOMContentLoaded', () => {
    includeHTML(() => {
        setupMobileMenu();
        
        // Only check session if the function exists (not on login page)
        if (typeof checkSession === 'function') {
            checkSession();
        }
    });
});