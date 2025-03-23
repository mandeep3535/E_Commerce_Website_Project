document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('forgotPasswordForm');
  const statusMessage = document.getElementById('statusMessage');

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const email = document.getElementById('email').value.trim();
    if (!email) {
      showMessage('Please enter a valid email address.', 'danger');
      return;
    }

    try {
      const response = await fetch('forgot_password.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `email=${encodeURIComponent(email)}`
      });

      if (!response.ok) {
        throw new Error(`Server error: ${response.status} ${response.statusText}`);
      }

      const data = await response.json();
      showMessage(data.message, data.success ? 'success' : 'danger');
      if (data.success) form.reset();
    } catch (error) {
      console.error('Error:', error);
      showMessage('An error occurred. Please try again later.', 'danger');
    }
  });

  function showMessage(message, type) {
    statusMessage.style.display = 'block';
    statusMessage.className = `alert alert-${type}`;
    statusMessage.textContent = message;
  }
});