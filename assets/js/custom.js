// Ensure DOM is fully loaded before running scripts
document.addEventListener('DOMContentLoaded', function () {
    console.log('Custom JS Loaded');

    // Highlight active navigation link
    const currentPage = window.location.pathname.split('/').pop();
    const navLinks = document.querySelectorAll('.navbar a');

    navLinks.forEach(link => {
        if (link.getAttribute('href') === currentPage) {
            link.style.backgroundColor = '#0056b3';
        }
    });

    // Confirmation dialog for critical actions
    const deleteButtons = document.querySelectorAll('.btn-danger');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function (event) {
            if (!confirm('Are you sure you want to perform this action?')) {
                event.preventDefault();
            }
        });
    });

    // Example: Show password toggle
    const passwordFields = document.querySelectorAll('.toggle-password');
    passwordFields.forEach(toggle => {
        toggle.addEventListener('click', function () {
            const input = document.querySelector(`#${this.dataset.target}`);
            if (input.type === 'password') {
                input.type = 'text';
                this.textContent = 'Hide';
            } else {
                input.type = 'password';
                this.textContent = 'Show';
            }
        });
    });
});
