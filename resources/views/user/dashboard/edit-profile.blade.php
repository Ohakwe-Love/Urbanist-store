<x-form-layout>
    <x-slot name="title">Edit Profile  | Urbanist Store</x-slot>

    <div class="form-container editProfileFormContainer">
        <form action="" method="POST">
            @csrf

            <a href="{{route('home')}}" class="auth-logo">
                <img src="{{asset('assets/images/logo/logo-dark.webp')}}" alt="">    
            </a>  
            <h2>Edit Your Profile</h2> 

            <div class="input-row">
                <div class="input-group">
                    <label for="name">Full name</label>
                    <input type="text" id="name" name="name" placeholder="Sam Peters" value="{{old('name')}}">
                </div>
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" placeholder="SammyPete" value="{{old('username')}}">
                </div>
                <div class="input-group span2">
                    <label for="email">email</label>
                    <input type="email" name="email" id="email" placeholder="sampeter@google.com" value="{{old('email')}}">
                </div>

                <div class="input-group">
                    <label for="city">City</label>
                    <input type="text" id="city" name="city" placeholder="Enugu" value="{{old('city')}}">
                </div>

                <div class="input-group">
                    <label for="state">State</label>
                    <input type="text" id="state" name="state" placeholder="Enugu" value="{{old('state')}}">
                </div>

                <div class="input-group span2">
                    <label for="country">Country</label>
                    <input type="text" id="country" name="country" placeholder="Nigeria" value="{{old('country')}}">
                </div>

                <div class="input-group span2">
                    <label for="address">Address</label>
                    <input type="text" name="address" id="address" placeholder="14 Osmos Street" value="{{old('address')}}">
                </div>

                <div class="input-group">
                    <label for="phone">Phone</label>
                    <input type="number" id="phone" name="phone" placeholder="+2345678902" value="{{old('phone')}}">
                </div>

                <div class="input-group">
                    <label for="postal_code">Postal Code</label>
                    <input type="text" id="postal_code" name="postal_code" placeholder="4000001" value="{{old('postal_code')}}">
                </div>





                {{-- <div class="input-group span2">
                    <label for="avatar">Avatar</label>
                    <input type="file" name="avatar" id="avatar" placeholder="SammyPete" value="{{old('avatar')}}">
                    <div class="file-input">

                    </div>
                </div> --}}

                <div class="input-group span2">
                    <label for="avatar">Avatar</label>
                    <div class="custom-file-input-wrapper">
                        <input 
                            type="file" 
                            name="avatar" 
                            id="avatar" 
                            class="custom-file-input" 
                            accept="image/*"
                            style="display: none;"
                        >
                        <label for="avatar" class="custom-file-label" id="avatar-label">
                            <span id="avatar-filename">Choose an image...</span>
                        </label>
                        <div class="avatar-preview" id="avatar-preview" style="display:none;">
                            <img src="" alt="Avatar Preview" id="avatar-img" >
                        </div>
                    </div>
                </div>

                <div class="input-group span2">
                    <label for="password">password</label>
                    <div class="password">
                        <input type="password" id="password" name="password" class="password-input" placeholder="********">
                        <span class="passwordToggle"><i class="fa-regular fa-eye-slash"></i></span>
                    </div>
                </div>
            </div>

            <button class="submit-btn">Save</button>

            <p class="login-link">Don't want to Edit? <a href="{{route('profile')}}">Go back</a></p>
        </form>
    </div> 
</x-form-layout>

{{-- // responsive header
const header = document.querySelector("header");

if (header) {
    const headerMenuBtn = document.querySelector("header .show_menu");
    const closeMenuBtn = document.querySelector(".close_small_screen_nav");
    const smallScreenNav = document.querySelector(".small_screen_nav");

    headerMenuBtn.addEventListener("click", () => {
        smallScreenNav.style.left = "0";
    });

    closeMenuBtn.addEventListener("click", () => {
        smallScreenNav.style.left = "-1000px";
    });


    // header scroll event
    window.addEventListener('scroll', (event)=>{
        const height = window.scrollY;

        if (height > 500){
            header.style.position = "fixed";
            header.style.top = "0";
            header.style.zIndex = "999";
        } else {
            header.style.position = "relative";
        }
    });
}


// hero slides
const hero_wrapper = document.querySelector('.hero_wrapper')

if (hero_wrapper) {
    const hero_slides = document.querySelectorAll(".hero_slide");
    const hero_pagination_dots = document.querySelectorAll(".hero_pagination_dots span");
    let currentSlide = 0;
    const totalSlides = hero_slides.length;
    let interval;

    function showSlide(index) {
        hero_slides.forEach((slide, i) => {
            slide.classList.toggle("active", i === index);
        });
        hero_pagination_dots.forEach((dot, i) => {
            dot.classList.toggle("active", i === index);
        });
        currentSlide = index;
    }

    function nextSlide() {
        currentSlide = (currentSlide + 1) % totalSlides;
        showSlide(currentSlide);
    }

    function autoSlide() {
        clearInterval(interval);
        interval = setInterval(nextSlide, 5000);
    }

    hero_pagination_dots.forEach((dot, index) => {
        dot.addEventListener("click", function () {
            clearInterval(interval); 
            showSlide(index);
            autoSlide();
        });
    });

    showSlide(currentSlide);
    autoSlide();
}



// core statements filter function
const about_us_section = document.querySelector(".about_us");

if (about_us_section){
    const core_statement_btn_con = document.querySelector(".core_statement_filter_btns");
    const core_statement_btns = document.querySelectorAll(".core_statement_filter_btns span");

    function filterStatement(statementBtn) {
        const btn_category = statementBtn.getAttribute("data-category").trim();
        const core_statements_wrapper = document.querySelector(".core_statements_wrapper");
        const core_statements = core_statements_wrapper.querySelectorAll(".core_statements");

        core_statements.forEach(core_statement => {
            if (core_statement.classList.contains(btn_category)) {
                core_statement.style.display = "block";
                core_statement.style.opacity = "1";
            } else {
                core_statement.style.display = "none";
                core_statement.style.opacity = "0";
            }
        })

        core_statement_btn_con.querySelectorAll("span").forEach(btn => btn.classList.remove("active_statement_btn"));
        statementBtn.classList.add("active_statement_btn");
    }

    window.addEventListener("DOMContentLoaded", () => {
        let defaultBtn = document.querySelector(".core_statement_filter_btns span.active_statement_btn");
        
        if (!defaultBtn) {
            defaultBtn = core_statement_btns[0];
            defaultBtn.classList.add("active_statement_btn");
        }

        filterStatement(defaultBtn);
    })

    core_statement_btns.forEach(statementBtn => {
        statementBtn.addEventListener("click", () => {
            filterStatement(statementBtn);
        })
    })
}


// counting function
const counter_wrapper = document.querySelector(".counter_wrapper");

if (counter_wrapper) {
    const counters = document.querySelectorAll(".counter");

    function runCounter() {
        counters.forEach(counter => {
            const counterUpdate = () => {
                const target = +counter.getAttribute("data-target");
                let count = +counter.innerText;
                const increment = target / 1000;
                
                if (count < target) {
                    count += increment;
                    counter.innerText = Math.ceil(count);
                    setTimeout(counterUpdate, 50);
                } else {
                    counter.innerText = target.toString();
                }
            }
            counterUpdate();
        });
    }

    // intersection observer for counting
    const countObserver = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                runCounter(entry.target);
                countObserver.unobserve(entry.target);
            }
        });
    });
    counters.forEach(counter => countObserver.observe(counter));
}


// date_picker function
const date_time_container = document.querySelector(".date_time_container");
if (date_time_container){
    const dateInput =  date_time_container.querySelector(".dateInput");
    const calendar = date_time_container.querySelector(".calendar");
    const monthYear = date_time_container.querySelector(".monthYear");
    const prevMonth = date_time_container.querySelector(".prevMonth");
    const nextMonth = date_time_container.querySelector(".nextMonth");
    const calendarGrid = date_time_container.querySelector(".calendar_grid");
    
    let currentDate = new Date();
    let selectedDate = null;
    
    function generateCalendar(year, month) {
        calendarGrid.innerHTML = '';    
    
        let firstDay = new Date(year, month, 1).getDay(); 
        let totalDays = new Date(year, month + 1, 0).getDate();
    
        monthYear.textContent = currentDate.toLocaleDateString("en-US", {
            month: "long",
            year: "numeric",
        });
    
        for (let i = 0; i < firstDay; i++) {
            let emptyCell = document.createElement("div");
            calendarGrid.appendChild(emptyCell);
        }
    
        for (let day = 1; day <= totalDays; day++) {
            let dayElement = document.createElement("div");
            dayElement.textContent = day;
    
            if (selectedDate && selectedDate.getFullYear() === year && 
                selectedDate.getMonth() === month && selectedDate.getDate() === day) {
                dayElement.classList.add("selected_date");
            }
    
            dayElement.onclick = function () {
                selectedDate = new Date(year, month, day);
                dateInput.value = selectedDate.toLocaleDateString("en-US");
    
                document.querySelectorAll(".selected_date").forEach(el => el.classList.remove("selected_date"));
                
                dayElement.classList.add("selected_date");
    
                closeCalendar();
            };
    
            calendarGrid.appendChild(dayElement);
        }
    }
    
    function openCalendar() {
        generateCalendar(currentDate.getFullYear(), currentDate.getMonth());
        calendar.style.display = "block";
    }
    
    function closeCalendar() {
        calendar.style.display = "none";
    }
    
    dateInput.addEventListener("click", openCalendar);
    
    prevMonth.addEventListener("click", function () {
        currentDate.setMonth(currentDate.getMonth() - 1);
        generateCalendar(currentDate.getFullYear(), currentDate.getMonth());
    });
    
    nextMonth.addEventListener("click", function () {
        currentDate.setMonth(currentDate.getMonth() + 1);
        generateCalendar(currentDate.getFullYear(), currentDate.getMonth());
    });
    
    document.addEventListener("click", function (event) {
        if (!dateInput.contains(event.target) && !calendar.contains(event.target)) {
            closeCalendar();
        }
    });
    
    
    // time picker
    const timeInput = date_time_container.querySelector(".timeInput");
    const timeDropdown = date_time_container.querySelector(".time_dropdown");
    let selectedTime = null;
    
    function generateTimeOptions() {
        const times = [];
        let hour = 0;
        let minute = 0;
    
        while (hour < 24) {
            let formattedTime = formatTime(hour, minute);
            times.push(formattedTime);
            minute += 30;
            if (minute === 60) {
                minute = 0;
                hour++;
            }
        }
    
        times.forEach(time => {
            let timeOption = document.createElement("div");
            timeOption.classList.add("time_option");
            timeOption.textContent = time;
    
            if (selectedTime === time) {
                timeOption.classList.add("selected_time");
            }
    
            timeOption.onclick = function () {
                selectedTime = time;
                timeInput.value = time;
    
                document.querySelectorAll(".selected_time").forEach(el => el.classList.remove("selected_time"));
    
                timeOption.classList.add("selected_time");
    
                closeTimeDropdown();
            };
    
            timeDropdown.appendChild(timeOption);
        });
    }
    
    function formatTime(hour, minute) {
        let ampm = hour < 12 ? "AM" : "PM";
        let displayHour = hour % 12 || 12;
        let displayMinute = minute < 10 ? "0" + minute : minute;
        return `${displayHour}:${displayMinute} ${ampm}`;
    }
    
    function openTimeDropdown() {
        timeDropdown.style.display = "block";
    }
    
    function closeTimeDropdown() {
        timeDropdown.style.display = "none";
    }
    
    timeInput.addEventListener("click", function () {
        timeDropdown.innerHTML = "";
        generateTimeOptions();
        openTimeDropdown();
    });
    
    document.addEventListener("click", function (event) {
        if (!timeInput.contains(event.target) && !timeDropdown.contains(event.target)) {
            closeTimeDropdown();
        }
    });

    // footer year
    const footer_date = document.querySelector(".footer_date").innerHTML = currentDate.getFullYear();
}



// swiper animation styles
const testimonies = document.querySelector(".testimonies");

if (testimonies) {
    new Swiper('.testimonies_wrapper', {
        loop: true,
        spaceBetween: 30,
        pagination: {
        el: '.swiper-pagination',
        clickable: true,
        dynamicBullets: true
        },
    
        breakpoints: {
            0:{
                slidesPerView: 1
            },
            768:{
                slidesPerView: 2
            },
            1024:{
                slidesPerView: 3
            }
        }
    });
}

// gallery lightbox
const projects_wrapper = document.querySelector(".projects");

if (projects_wrapper) {
    const galleryImgs = document.querySelectorAll(".project_img");
    const enlargeImgBtns = document.querySelectorAll(".enlarge_img");
    const projectLightbox = document.querySelector(".project_lightbox");
    const lightboxImg = projectLightbox.querySelector(".lightbox_img");
    const prevLightboxImg = projectLightbox.querySelector(".prev_lightbox_img");
    const nextLightboxImg = projectLightbox.querySelector(".next_lightbox_img");
    const closeLightbox = projectLightbox.querySelector(".close_lightbox");
    let currentIndex = 0;

    function openLightbox(index){
        currentIndex = index;
        lightboxImg.src = galleryImgs[index].src;
        projectLightbox.style.display = "flex";
    }

    enlargeImgBtns.forEach((btn, index) => {
        btn.addEventListener("click", () => openLightbox(index));
    })

    closeLightbox.addEventListener("click", ()=>{
        projectLightbox.style.display = "none";
    })

    prevLightboxImg.addEventListener("click", ()=>{
        currentIndex = (currentIndex - 1 + galleryImgs.length) % galleryImgs.length;
        lightboxImg.src = galleryImgs[currentIndex].src;
    })

    nextLightboxImg.addEventListener("click", ()=>{
        currentIndex = (currentIndex + 1) % galleryImgs.length;
        lightboxImg.src = galleryImgs[currentIndex].src;
    })
} --}}