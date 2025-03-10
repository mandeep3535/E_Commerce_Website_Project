document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('adminLoginForm');
    
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form inputs
        const userId = document.getElementById('adminUserId').value;
        const password = document.getElementById('adminPassword').value;
        
        // Create form data
        const formData = new FormData();
        formData.append('userId', userId);
        formData.append('password', password);
        
        // Send AJAX request
        fetch('admin_login.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showMessage(data.message, 'success');
                
                // Redirect to admin dashboard after a short delay
                setTimeout(() => {
                    window.location.href = 'admin.php';
                }, 1500);
            } else {
                // Show error message
                showMessage(data.message, 'danger');
            }
        })
        .catch(error => {
            showMessage('An error occurred. Please try again later.', 'danger');
            console.error('Login error:', error);
        });
    });
    
    // Function to show messages
    function showMessage(message, type) {
        // Check if there's already a message element
        let alertElement = document.querySelector('.alert');
        if (alertElement) {
            alertElement.remove();
        }
        
        // Create new alert element
        alertElement = document.createElement('div');
        alertElement.className = `alert alert-${type} mt-3`;
        alertElement.textContent = message;
        
        // Insert before the button
        const submitButton = document.querySelector('button[type="submit"]');
        submitButton.parentNode.insertBefore(alertElement, submitButton);
        
        // Auto remove after 5 seconds
        if (type !== 'success') {
            setTimeout(() => {
                alertElement.remove();
            }, 5000);
        }
    }
});