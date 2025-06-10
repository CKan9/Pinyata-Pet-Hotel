document.addEventListener('DOMContentLoaded', () => {
    const baseURL = 'http://localhost/PT/PHP/';
    const errorMessages = {
        'Email not found': 'No account found with this email address',
        'Invalid token': 'Password reset link has expired or is invalid'
    };

    // Password reset request
    const resetRequestForm = document.getElementById('resetRequestForm');
    if (resetRequestForm) {
        resetRequestForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = document.getElementById('email').value;
            const messageEl = document.getElementById('resetMessage');
            
            try {
                messageEl.textContent = 'Sending reset link...';
                messageEl.className = 'message info';

                const response = await fetch(baseURL + 'send_reset.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email })
                });
                
                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const text = await response.text();
                    throw new Error(`Invalid response: ${text}`);
                }
                
                const result = await response.json();
                
                if (!response.ok || !result.success) {
                    throw new Error(result.message || 'Request failed');
                }
                
                messageEl.textContent = result.message || 'Reset link sent! Check your email';
                messageEl.className = 'message success';
                
            } catch (error) {
                messageEl.textContent = errorMessages[error.message] || error.message;
                messageEl.className = 'message error';
                console.error('Password Reset Error:', error);
            }
        });
    }

    const resetPasswordForm = document.getElementById('resetPasswordForm');
    if (resetPasswordForm) {
        resetPasswordForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const urlParams = new URLSearchParams(window.location.search);
            const token = urlParams.get('token');
            const password = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const statusEl = document.getElementById('resetStatus');

            if (!token) {
                statusEl.textContent = 'Missing reset token in URL';
                statusEl.className = 'message error';
                return;
            }

            if (password.length < 8) {
                statusEl.textContent = 'Password must be at least 8 characters';
                statusEl.className = 'message error';
                return;
            }

            // Validate password match
            if (password !== confirmPassword) {
                statusEl.textContent = 'Passwords do not match!';
                statusEl.className = 'message error';
                return;
            }

            try {
                statusEl.textContent = 'Updating password...';
                statusEl.className = 'message info';

                const response = await fetch(baseURL + 'update_password.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ token, password })
                });

                // Handle non-JSON responses
                const contentType = response.headers.get('content-type');
                let result;
                
                if (contentType && contentType.includes('application/json')) {
                    result = await response.json();
                } else {
                    const text = await response.text();
                    throw new Error(`Server responded with: ${text}`);
                }

                if (!response.ok || !result.success) {
                    throw new Error(result.message || 'Password update failed');
                }
                
                statusEl.textContent = 'Password updated successfully! Redirecting...';
                statusEl.className = 'message success';
                
                setTimeout(() => {
                    window.location.href = 'login.html';
                }, 2000);
                
            } catch(error) {
                statusEl.textContent = error.message || 'Error updating password';
                statusEl.className = 'message error';
                console.error('Password Update Error:', error);
            }
        });
    }
});