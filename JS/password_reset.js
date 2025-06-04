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
                
                const result = await response.json();
                
                if (!response.ok) {
                    throw new Error(result.message || 'Request failed');
                }
                
                messageEl.textContent = 'Reset link sent! Check your email';
                messageEl.className = 'message success';
                
            } catch (error) {
                messageEl.textContent = errorMessages[error.message] || error.message;
                messageEl.className = 'message error';
                console.error('Password Reset Error:', error);
            }
        });
    }

    // Password update
    const resetPasswordForm = document.getElementById('resetPasswordForm');
    if (resetPasswordForm) {
        const urlParams = new URLSearchParams(window.location.search);
        const token = urlParams.get('token');
        const statusEl = document.getElementById('resetStatus');

        const verifyToken = async () => {
            if (!token) {
                statusEl.textContent = 'Missing reset token';
                statusEl.className = 'message error';
                resetPasswordForm.style.display = 'none';
                return;
            }
            
            try {
                statusEl.textContent = 'Verifying token...';
                statusEl.className = 'message info';

                const response = await fetch(baseURL + `verify_token.php?token=${token}`);
                if (!response.ok) throw new Error('Token verification failed');
                
                const data = await response.json();
                if (!data.success) {
                    throw new Error(data.message);
                }
                
                document.getElementById('token').value = token;
                statusEl.textContent = '';
                
            } catch (error) {
                statusEl.textContent = errorMessages[error.message] || error.message;
                statusEl.className = 'message error';
                resetPasswordForm.style.display = 'none';
            }
        };

        verifyToken();

        resetPasswordForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = {
                token: document.getElementById('token').value,
                password: document.getElementById('new_password').value,
                confirm_password: document.getElementById('confirm_password').value
            };

            if (formData.password !== formData.confirm_password) {
                alert('Passwords do not match!');
                return;
            }

            try {
                statusEl.textContent = 'Updating password...';
                statusEl.className = 'message info';

                const response = await fetch(baseURL + 'update_password.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        token: formData.token,
                        password: formData.password 
                    })
                });

                const result = await response.json();
                
                statusEl.textContent = result.success 
                    ? 'Password updated successfully!' 
                    : result.message;
                statusEl.className = `message ${result.success ? 'success' : 'error'}`;

                if (result.success) {
                    setTimeout(() => {
                        window.location.href = 'login.html';
                    }, 2000);
                }
            } catch (error) {
                statusEl.textContent = 'Password update failed. Please try again.';
                statusEl.className = 'message error';
                console.error('Password Update Error:', error);
            }
        });
    }
});