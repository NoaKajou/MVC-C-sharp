// Utility functions
function showMessage(message, type = 'error') {
    const existingMessages = document.querySelectorAll('.error-message, .success-message');
    existingMessages.forEach(msg => msg.remove());
    
    const div = document.createElement('div');
    div.className = type === 'error' ? 'error-message' : 'success-message';
    div.textContent = message;
    
    const container = document.querySelector('.auth-container, .edit-container, .page-container');
    if (container) {
        container.insertBefore(div, container.firstChild);
    }
    
    setTimeout(() => div.remove(), 5000);
}

// Form validation
document.addEventListener('DOMContentLoaded', () => {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', (e) => {
            const requiredFields = form.querySelectorAll('[required]');
            let valid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.style.borderColor = '#E74C3C';
                    valid = false;
                } else {
                    field.style.borderColor = '#444';
                }
            });
            
            if (!valid) {
                e.preventDefault();
                showMessage('Veuillez remplir tous les champs obligatoires');
            }
        });
    });
    
    // Password confirmation
    const confirmPassword = document.querySelector('input[name="confirm_mdp"]');
    if (confirmPassword) {
        confirmPassword.addEventListener('input', () => {
            const password = document.querySelector('input[name="mdp"]');
            if (password.value !== confirmPassword.value) {
                confirmPassword.style.borderColor = '#E74C3C';
            } else {
                confirmPassword.style.borderColor = '#27AE60';
            }
        });
    }
});

// API helper
async function apiCall(url, method = 'GET', data = null) {
    const options = {
        method,
        headers: {
            'Content-Type': 'application/json'
        }
    };
    
    if (data) {
        options.body = JSON.stringify(data);
    }
    
    try {
        const response = await fetch(url, options);
        return await response.json();
    } catch (error) {
        console.error('API Error:', error);
        return { success: false, message: 'Erreur de connexion' };
    }
}
