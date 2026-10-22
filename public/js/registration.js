const form = document.querySelector('#registration-form');

if (form) {
    form.addEventListener('submit', () => {
        const button = form.querySelector('button[type="submit"]');
        button.disabled = true;
        button.setAttribute('aria-busy', 'true');
        button.querySelector('.button-label').textContent = 'Menyimpan pendaftaran…';
    });

    window.addEventListener('pageshow', () => {
        const button = form.querySelector('button[type="submit"]');
        button.disabled = false;
        button.removeAttribute('aria-busy');
        button.querySelector('.button-label').textContent = 'Kirim pendaftaran saya';
    });
}

document.querySelector('.error-summary')?.focus();
