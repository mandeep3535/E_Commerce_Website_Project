document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('forgotPasswordForm');
    const statusMessage = document.getElementById('statusMessage');
  
    form.addEventListener('submit', function(e) {
      e.preventDefault();
  
      const emailInput = document.getElementById('email');
      const email = emailInput.value.trim();
  
      if (!email) {
        displayStatus('Please enter a valid email address.', 'danger');
        return;
      }
  
      fetch('forgot-password.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email })
      })
      .then(response => response.json())
      .then(data => {
        
        if (data.success) {
          displayStatus(data.message, 'success');
          emailInput.value = ''; 
        } else {
          displayStatus(data.message, 'danger');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        displayStatus('An error occurred. Please try again later.', 'danger');
      });
    });
  
    function displayStatus(message, type) {
      statusMessage.textContent = message;
      statusMessage.className = 'alert alert-' + type;
      statusMessage.style.display = 'block';
    }
  });
  