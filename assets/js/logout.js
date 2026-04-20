document.addEventListener('DOMContentLoaded', function() {
    
    // Select all logout links
    const logoutLinks = document.querySelectorAll('.logout-link a');

    if (logoutLinks.length > 0) {
        logoutLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault(); 
                
                const targetUrl = this.getAttribute('href'); 

                // Trigger SweetAlert2
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Confirm Log Out',
                        text: "Are you sure you want to log out of the system?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#800000', 
                        cancelButtonColor: '#f0f0f0',
                        confirmButtonText: 'Yes, Log Out',
                        cancelButtonText: '<span style="color: black; font-weight: 600;">Cancel</span>',
                        customClass: {
                            cancelButton: 'swal2-cancel-custom' 
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = targetUrl;
                        }
                    });
                } else {
                    // Fallback if SweetAlert isn't loaded
                    if(confirm("Are you sure you want to log out?")) {
                        window.location.href = targetUrl;
                    }
                }
            });
        });
    }

    // Inject custom CSS for the cancel button text color
    const style = document.createElement('style');
    style.innerHTML = `
        .swal2-cancel-custom {
            color: black !important;
        }
    `;
    document.head.appendChild(style);
});