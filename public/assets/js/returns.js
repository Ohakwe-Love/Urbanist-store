// Accordion functionality
document.querySelectorAll('.accordion-header').forEach(button => {
    button.addEventListener('click', () => {
        const content = button.nextElementSibling;
        
        button.classList.toggle('active');
        content.classList.toggle('active');
    });
});

// File upload preview functionality
document.querySelector('.file-upload').addEventListener('click', () => {
    document.getElementById('returnImages').click();
});

document.getElementById('returnImages').addEventListener('change', function() {
    const fileUploadText = document.querySelector('.file-upload-text');
    if (this.files.length > 0) {
        fileUploadText.textContent = `${this.files.length} file(s) selected`;
    } else {
        fileUploadText.textContent = 'Drop images here or click to upload';
    }
});

// Form submission prevention (for demo)
document.getElementById('returnForm').addEventListener('submit', function(e) {
    e.preventDefault();
    alert('Return request submitted successfully! We\'ll process your request and get back to you soon.');
    this.reset();
    document.querySelector('.file-upload-text').textContent = 'Drop images here or click to upload';
});