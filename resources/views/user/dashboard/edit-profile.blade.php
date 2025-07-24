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
                    <label for="phone">Phone</label>
                    <input type="number" id="phone" name="phone" placeholder="+2345678902" value="{{old('phone')}}">
                </div>
                
                <div class="input-group">
                    <label for="postal_code">Postal Code</label>
                    <input type="text" id="postal_code" name="postal_code" placeholder="4000001" value="{{old('postal_code')}}">
                </div>
                
                <div class="input-group">
                    <label for="gender">Gender</label>
                    <select name="gender" id="gender">
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="others">others</option>
                    </select>
                </div>
                <div class="input-group">
                    <label for="city">City</label>
                    <input type="text" id="city" name="city" placeholder="Enugu" value="{{old('city')}}">
                </div>

                <div class="input-group">
                    <label for="state">State</label>
                    <input type="text" id="state" name="state" placeholder="Enugu" value="{{old('state')}}">
                </div>

                <div class="input-group">
                    <label for="country">Country</label>
                    <input type="text" id="country" name="country" placeholder="Nigeria" value="{{old('country')}}">
                </div>

                <div class="input-group span2">
                    <label for="address">Address</label>
                    <input type="text" name="address" id="address" placeholder="14 Osmos Street" value="{{old('address')}}">
                </div>

                {{-- <div class="input-group span2">
                    <label for="date_of_birth">DOB</label>
                    <div class="dob-picker" style="display: flex; gap: 8px;">
                        <select id="dob-day" class="dob-select" style="width: 70px;">
                            <option value="">Day</option>
                            @for ($d = 1; $d <= 31; $d++)
                                <option value="{{ sprintf('%02d', $d) }}">{{ $d }}</option>
                            @endfor
                        </select>
                        <select id="dob-month" class="dob-select" style="width: 100px;">
                            <option value="">Month</option>
                            @foreach ([
                                '01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr',
                                '05' => 'May', '06' => 'Jun', '07' => 'Jul', '08' => 'Aug',
                                '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec'
                            ] as $num => $name)
                                <option value="{{ $num }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        <select id="dob-year" class="dob-select" style="width: 90px;">
                            <option value="">Year</option>
                            @for ($y = date('Y'); $y >= 1900; $y--)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <input type="text" id="date_of_birth" name="date_of_birth" readonly style="margin-top:8px;" value="{{ old('date_of_birth') }}" placeholder="YYYY-MM-DD">
                </div> --}}

                <div class="input-group span2">
                    <label for="date_of_birth">DOB</label>
                    <div class="dob-picker" id="dob-picker" style="display: flex; gap: 12px;">
                        <div class="dob-dropdown" id="dob-day-dropdown" tabindex="0">
                            <span id="dob-day-label">Day</span>
                            <div class="dob-options" id="dob-day-options" style="display:none;">
                                @for ($d = 1; $d <= 31; $d++)
                                    <div class="dob-option" data-value="{{ sprintf('%02d', $d) }}">{{ $d }}</div>
                                @endfor
                            </div>
                        </div>
                        <div class="dob-dropdown" id="dob-month-dropdown" tabindex="0">
                            <span id="dob-month-label">Month</span>
                            <div class="dob-options" id="dob-month-options" style="display:none;">
                                @foreach ([
                                    '01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr',
                                    '05' => 'May', '06' => 'Jun', '07' => 'Jul', '08' => 'Aug',
                                    '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec'
                                ] as $num => $name)
                                    <div class="dob-option" data-value="{{ $num }}">{{ $name }}</div>
                                @endforeach
                            </div>
                        </div>
                        <div class="dob-dropdown" id="dob-year-dropdown" tabindex="0">
                            <span id="dob-year-label">Year</span>
                            <div class="dob-options" id="dob-year-options" style="display:none; max-height: 200px; overflow-y: auto;">
                                @for ($y = date('Y'); $y >= 1900; $y--)
                                    <div class="dob-option" data-value="{{ $y }}">{{ $y }}</div>
                                @endfor
                            </div>
                        </div>
                    </div>
                    <input type="text" id="date_of_birth" name="date_of_birth" readonly style="margin-top:8px;" value="{{ old('date_of_birth') }}" placeholder="YYYY-MM-DD">
                </div>

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