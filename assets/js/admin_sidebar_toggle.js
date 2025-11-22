document.addEventListener('DOMContentLoaded', function() {
    
    // Get Elements
    const btn = document.getElementById('sidebarToggleBtn');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    const overlay = document.querySelector('.sidebar-overlay'); // Might be null on desktop

    if (btn && sidebar && mainContent) {
        
        // Function to Toggle
        function toggleSidebar() {
            sidebar.classList.toggle('toggled');
            mainContent.classList.toggle('toggled');
            
            // Handle Overlay for Mobile
            if (overlay) {
                if (sidebar.classList.contains('toggled') && window.innerWidth <= 900) {
                    overlay.classList.add('active');
                } else {
                    overlay.classList.remove('active');
                }
            }
        }

        // Button Click
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleSidebar();
        });

        // Overlay Click (Close on Mobile)
        if(overlay) {
            overlay.addEventListener('click', function() {
                toggleSidebar();
            });
        }
    }
});