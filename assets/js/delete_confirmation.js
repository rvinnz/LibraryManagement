document.addEventListener('DOMContentLoaded', function() {
    
    // Select all delete buttons with the specific class
    const deleteButtons = document.querySelectorAll('.btn-delete-swal');

    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const deleteUrl = this.getAttribute('href');

            // Trigger SweetAlert2
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#800000',
                cancelButtonColor: '#f0f0f0', 
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: '<span style="color: black; font-weight: 600;">Cancel</span>',
            }).then((result) => {
                if (result.isConfirmed) {
                    // If confirmed, navigate to the delete URL
                    window.location.href = deleteUrl;
                }
            });
        });
    });
});