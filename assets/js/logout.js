document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Inject CSS for the Modal
    const style = document.createElement('style');
    style.innerHTML = `
        /* Modal Overlay */
        .logout-modal-overlay {
            display: none; /* Hidden by default */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent black */
            z-index: 1000;
            justify-content: center;
            align-items: center;
            animation: fadeIn 0.3s ease;
        }

        /* Modal Box */
        .logout-modal {
            background: white;
            padding: 25px 30px;
            border-radius: 12px;
            width: 350px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            font-family: 'Poppins', sans-serif;
            transform: translateY(-20px);
            animation: slideIn 0.3s forwards;
        }

        /* Modal Header */
        .logout-modal h3 {
            margin: 0 0 10px 0;
            color: #800000; /* ZPPSU Maroon */
            font-size: 20px;
            font-weight: 600;
        }

        /* Modal Text */
        .logout-modal p {
            margin: 0 0 25px 0;
            color: #555;
            font-size: 14px;
        }

        /* Button Container */
        .logout-modal-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        /* Buttons */
        .logout-btn {
            padding: 10px 25px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            font-family: 'Poppins', sans-serif;
        }

        .btn-confirm {
            background-color: #800000; /* ZPPSU Maroon */
            color: white;
        }
        
        .btn-confirm:hover {
            background-color: #570000;
        }

        .btn-cancel {
            background-color: #f0f0f0;
            color: #333;
        }

        .btn-cancel:hover {
            background-color: #e0e0e0;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    `;
    document.head.appendChild(style);

    // 2. Inject HTML for the Modal
    const modalHtml = `
        <div id="logoutModal" class="logout-modal-overlay">
            <div class="logout-modal">
                <h3>Confirm Log Out</h3>
                <p>Are you sure you want to log out of the system?</p>
                <div class="logout-modal-buttons">
                    <button id="cancelLogout" class="logout-btn btn-cancel">Cancel</button>
                    <button id="confirmLogout" class="logout-btn btn-confirm">Yes, Log Out</button>
                </div>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', modalHtml);

    // 3. Logic to Handle Clicks
    const modal = document.getElementById('logoutModal');
    const confirmBtn = document.getElementById('confirmLogout');
    const cancelBtn = document.getElementById('cancelLogout');
    let targetUrl = '';

    // Find all links inside the .logout-link class
    const logoutLinks = document.querySelectorAll('.logout-link a');

    if(logoutLinks.length > 0) {
        logoutLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault(); // Stop immediate redirection
                targetUrl = this.getAttribute('href'); // Capture the existing href (e.g., ../../login.php)
                modal.style.display = 'flex'; // Show modal
            });
        });
    }

    // Handle "Yes" click
    confirmBtn.addEventListener('click', function() {
        if(targetUrl) {
            // This redirects the user to whatever page was in the <a href="...">
            // Based on your files, this is usually "../../login.php"
            window.location.href = targetUrl;
        } else {
            // Fallback mostly for debugging, usually won't be reached if the HTML is correct
            console.warn("No target URL found.");
        }
    });

    // Handle "Cancel" click
    cancelBtn.addEventListener('click', function() {
        modal.style.display = 'none';
        targetUrl = ''; // Reset
    });

    // Close if clicked outside the box
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.style.display = 'none';
            targetUrl = '';
        }
    });
});