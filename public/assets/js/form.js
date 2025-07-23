const passwordInputs = document.querySelectorAll('.password-input');
console.log(passwordInputs);

passwordInputs.forEach(input => {
    const passwordToggle = input.nextElementSibling;
    passwordToggle.addEventListener('click', ()=>{
        if (input.type == "password") {
            input.focus()
            input.type = 'text'
            passwordToggle.innerHTML = `<i class="fa-regular fa-eye"></i>`
        } else {
            input.focus()
            input.type = "password"
            passwordToggle.innerHTML = `<i class="fa-regular fa-eye-slash"></i>`
        }
    })
})

document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('avatar');
    const label = document.getElementById('avatar-label');
    const filename = document.getElementById('avatar-filename');
    const preview = document.getElementById('avatar-preview');
    const img = document.getElementById('avatar-img');

    input.addEventListener('change', function () {
        if (input.files && input.files[0]) {
            filename.textContent = input.files[0].name;

            // Show image preview
            const reader = new FileReader();
            reader.onload = function (e) {
                img.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            filename.textContent = 'Choose an image...';
            preview.style.display = 'none';
        }
    });
});