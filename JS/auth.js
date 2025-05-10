function checkSession() {
    fetch('../PHP/sessions.php')
        .then(response => response.json())
        .then(data => {
            const nav = document.getElementById('nav-links');
            const loginBtn = document.getElementById('login-btn');

            if (data.success) {
                // Remove login button if exists
                if (loginBtn) loginBtn.remove();
                // Inject dropdown
                const userMenu = document.createElement('div');
                userMenu.innerHTML = `
                    <div class="user-dropdown">
                        <a href="#">Hi, ${data.username}</a>
                        <div class="dropdown-content">
                            <a href="guest_dashboard.html">Profile</a>
                            <a href="#" id="headerLogout">Log out</a>
                        </div>
                    </div>
                `;
                nav.appendChild(userMenu);

                // Add logout functionality
                document.getElementById('headerLogout').addEventListener('click', logoutUser);
            } else {
                // Ensure login button is there if not logged in
                if (!loginBtn) {
                    const loginLink = document.createElement('a');
                    loginLink.href = "login.html";
                    loginLink.id = "login-btn";
                    loginLink.textContent = "Login";
                    nav.appendChild(loginLink);
                }
            }
        })
        .catch(err => console.error("Session Check Error:", err));
}

function logoutUser(e) {
    e.preventDefault();
    fetch('../PHP/logout.php', { method: 'POST' })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '../HTML/login.html';
            }
        })
        .catch(error => console.error("Logout Error:", error));
}
