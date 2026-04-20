document.addEventListener('DOMContentLoaded', function() {
    
    // Select all buttons that trigger the return action
    const returnButtons = document.querySelectorAll('.btn-return-trigger');

    returnButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault(); 
            
            const currentForm = this.closest('form'); 

            // Trigger SweetAlert2
            Swal.fire({
                title: 'Confirm Return',
                text: "Are you sure you want to mark this book as returned?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#800000', 
                cancelButtonColor: '#f0f0f0', 
                confirmButtonText: 'Yes, return it!',
                cancelButtonText: '<span style="color: black; font-weight: 600;">Cancel</span>', 
                customClass: {
                    cancelButton: 'swal2-cancel-custom' 
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // If user clicks "Yes", submit the form manually
                    currentForm.submit();
                }
            });
        });
    });
});