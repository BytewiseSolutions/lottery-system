// Authentication handlers
document.addEventListener('DOMContentLoaded', function() {
    // Simple login form handler
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;
            const errorDiv = document.getElementById('loginError');
            
            try {
                const result = await login(email, password);
                $('#loginModal').modal('hide');
                alert('Login successful!');
                location.reload();
            } catch (error) {
                errorDiv.textContent = error.message;
                errorDiv.style.display = 'block';
            }
        });
    }
    
    // Main login form handler
    const loginFormMain = document.getElementById('loginFormMain');
    if (loginFormMain) {
        loginFormMain.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = document.getElementById('loginEmailMain').value;
            const password = document.getElementById('loginPasswordMain').value;
            const errorDiv = document.getElementById('loginErrorMain');
            
            try {
                const result = await login(email, password);
                $('#loginModal').modal('hide');
                alert('Login successful!');
                location.reload();
            } catch (error) {
                errorDiv.textContent = error.message;
                errorDiv.style.display = 'block';
            }
        });
    }
    
    // Simple register form handler
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = document.getElementById('registerEmail').value;
            const password = document.getElementById('registerPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const errorDiv = document.getElementById('registerError');
            
            try {
                const result = await register(email, password, confirmPassword);
                $('#registerModal').modal('hide');
                alert('Registration successful!');
                location.reload();
            } catch (error) {
                errorDiv.textContent = error.message;
                errorDiv.style.display = 'block';
            }
        });
    }
    
    // Main register form handler
    const registerFormMain = document.getElementById('registerFormMain');
    if (registerFormMain) {
        registerFormMain.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = document.getElementById('registerEmailMain').value;
            const password = document.getElementById('registerPasswordMain').value;
            const confirmPassword = document.getElementById('confirmPasswordMain').value;
            const errorDiv = document.getElementById('registerErrorMain');
            
            try {
                const result = await register(email, password, confirmPassword);
                $('#registerModal').modal('hide');
                alert('Registration successful!');
                location.reload();
            } catch (error) {
                errorDiv.textContent = error.message;
                errorDiv.style.display = 'block';
            }
        });
    }
    
    // Check if user is logged in and update UI
    if (isLoggedIn()) {
        const user = JSON.parse(localStorage.getItem('user'));
        updateAuthUI(true, user);
    }
});