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