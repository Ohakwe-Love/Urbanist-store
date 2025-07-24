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

// document.addEventListener('DOMContentLoaded', function () {
//     function updateDOBInput() {
//         const day = document.getElementById('dob-day').value;
//         const month = document.getElementById('dob-month').value;
//         const year = document.getElementById('dob-year').value;
//         const dobInput = document.getElementById('date_of_birth');
//         if (day && month && year) {
//             dobInput.value = `${year}-${month}-${day}`;
//         } else {
//             dobInput.value = '';
//         }
//     }

//     ['dob-day', 'dob-month', 'dob-year'].forEach(id => {
//         document.getElementById(id).addEventListener('change', updateDOBInput);
//     });
// });

document.addEventListener('DOMContentLoaded', function () {
    function setupDropdown(dropdownId, labelId, optionsId, onSelect) {
        const dropdown = document.getElementById(dropdownId);
        const label = document.getElementById(labelId);
        const options = document.getElementById(optionsId);

        dropdown.addEventListener('click', function (e) {
            options.style.display = options.style.display === 'block' ? 'none' : 'block';
        });

        options.querySelectorAll('.dob-option').forEach(option => {
            option.addEventListener('click', function (e) {
                label.textContent = option.textContent;
                label.dataset.value = option.dataset.value;
                options.style.display = 'none';
                options.querySelectorAll('.dob-option').forEach(opt => opt.classList.remove('selected'));
                option.classList.add('selected');
                onSelect(option.dataset.value);
            });
        });

        // Hide dropdown when clicking outside
        document.addEventListener('click', function (e) {
            if (!dropdown.contains(e.target)) {
                options.style.display = 'none';
            }
        });
    }

    let selectedDay = '', selectedMonth = '', selectedYear = '';
    const dobInput = document.getElementById('date_of_birth');

    function updateDOB() {
        if (selectedDay && selectedMonth && selectedYear) {
            dobInput.value = `${selectedYear}-${selectedMonth}-${selectedDay}`;
        } else {
            dobInput.value = '';
        }
    }

    setupDropdown('dob-day-dropdown', 'dob-day-label', 'dob-day-options', function(val) {
        selectedDay = val;
        updateDOB();
    });
    setupDropdown('dob-month-dropdown', 'dob-month-label', 'dob-month-options', function(val) {
        selectedMonth = val;
        updateDOB();
    });
    setupDropdown('dob-year-dropdown', 'dob-year-label', 'dob-year-options', function(val) {
        selectedYear = val;
        updateDOB();
    });
});