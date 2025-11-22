document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Inject CSS for the Modal (Reusing your theme styles)
    const style = document.createElement('style');
    style.innerHTML = `
        /* Modal Overlay */
        .delete-modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1001; /* Higher than other elements */
            justify-content: center;
            align-items: center;
            animation: fadeIn 0.3s ease;
        }

        /* Modal Box */
        .delete-modal {
            background: white;
            padding: 25px 30px;
            border-radius: 12px;
            width: 380px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            font-family: 'Poppins', sans-serif;
            transform: translateY(-20px);
            animation: slideIn 0.3s forwards;
            border-top: 5px solid #c92a2a; /* Red border for danger/delete */
        }

        /* Modal Header */
        .delete-modal h3 {
            margin: 0 0 10px 0;
            color: #c92a2a; /* Red color for delete title */
            font-size: 20px;
            font-weight: 600;
        }

        /* Modal Text */
        .delete-modal p {
            margin: 0 0 25px 0;
            color: #555;
            font-size: 14px;
            line-height: 1.5;
        }

        /* Button Container */
        .delete-modal-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        /* Buttons */
        .del-modal-btn {
            padding: 10px 25px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            font-family: 'Poppins', sans-serif;
        }

        .btn-del-confirm {
            background-color: #c92a2a; /* Red */
            color: white;
        }
        
        .btn-del-confirm:hover {
            background-color: #a61e1e;
        }

        .btn-del-cancel {
            background-color: #f0f0f0;
            color: #333;
        }

        .btn-del-cancel:hover {
            background-color: #e0e0e0;
        }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideIn { from { transform: translateY(-20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    `;
    document.head.appendChild(style);

    // 2. Inject HTML for the Modal
    const modalHtml = `
        <div id="deleteModal" class="delete-modal-overlay">
            <div class="delete-modal">
                <h3>Confirm Deletion</h3>
                <p>Are you sure you want to delete this record?<br>This action cannot be undone.</p>
                <div class="delete-modal-buttons">
                    <button id="cancelDelete" class="del-modal-btn btn-del-cancel">Cancel</button>
                    <button id="confirmDelete" class="del-modal-btn btn-del-confirm">Yes, Delete</button>
                </div>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', modalHtml);

    // 3. Logic to Handle Clicks
    const modal = document.getElementById('deleteModal');
    const confirmBtn = document.getElementById('confirmDelete');
    const cancelBtn = document.getElementById('cancelDelete');
    let targetUrl = '';

    // Find all links with class .btn-delete
    const deleteButtons = document.querySelectorAll('.btn-delete');

    if(deleteButtons.length > 0) {
        deleteButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault(); // Stop immediate deletion
                targetUrl = this.getAttribute('href'); // Capture the delete link
                modal.style.display = 'flex'; // Show modal
            });
        });
    }

    // Handle "Yes" click
    confirmBtn.addEventListener('click', function() {
        if(targetUrl) {
            window.location.href = targetUrl;
        }
    });

    // Handle "Cancel" click
    cancelBtn.addEventListener('click', function() {
        modal.style.display = 'none';
        targetUrl = '';
    });

    // Close if clicked outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.style.display = 'none';
            targetUrl = '';
        }
    });
});