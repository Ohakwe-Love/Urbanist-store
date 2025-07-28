<x-dashboard-layout :user=$user>
    <div class="profile-container">
        <div class="profile-header">
            <div>
                <h1 class="">Profile Info</h1>
                <p class="welcome-subtext">Here's your information, you can edit</p>
            </div>

            <x-dashboard-sidebar-toggle />
        </div>

        <div class="profile-content">
            <div class="profile-img">
                @if ($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{$user->username}}">
                @else 
                    <img src="{{ asset('storage/avatars/default-avatar.png')}}" alt="{{$user->username}}">
                @endif
            </div>

            <div class="profile-name-bio profile-content-data">
                <h2>{{$user->username}}</h2>

                {{-- <div class="bio">
                    Bio: <span>let it prevail</span>
                </div> --}}
            </div>

            <div class="profile-location profile-content-data">
                <h3>Location</h3>
                <p>
                    Address: 
                    @if ($user->address)
                        <span>{{ucwords($user->address)}}</span>
                    @else
                        <span>N/A</span>
                    @endif
                </p>

                <p>
                    City: 
                    @if ($user->city)
                        <span>{{ucwords($user->city)}}</span>
                    @else
                        <span>N/A</span>
                    @endif
                </p>
                <p>
                    State: 
                    @if($user->state)
                        <span>{{ucwords($user->state)}}</span>
                    @else
                        <span>N/A</span>
                    @endif
                </p>

                <p>
                    Country:
                    @if ($user->country)
                        <span>{{ucwords($user->country)}}</span>                        
                    @else
                        <span>N/A</span>
                    @endif
                </p>
            </div>

            <div class="profile-contact profile-content-data">
                <h3>contact</h3>
                <p>
                    Email: 
                    @if ($user->email)
                        <span>{{$user->email}}</span>                        
                    @else
                        <span>N/A</span>
                    @endif
                </p>

                <p>
                    Phone: 
                    @if ($user->phone)
                        <span>{{$user->phone}}</span>                        
                    @else
                        <span>N/A</span>
                    @endif
                </p>
            </div>

            <div class="profile-personal profile-content-data">
                <h3>Personal</h3>
                <p>
                    DOB: 
                    @if ($user->date_of_birth)
                        <span>{{$user->date_of_birth}}</span>                        
                    @else
                        <span>N/A</span>
                    @endif
                </p>

                <p>
                    Gender: 
                    @if ($user->gender)
                        <span>{{$user->gender}}</span>                        
                    @else
                        <span>N/A</span>
                    @endif
                </p>
            </div>

            <a href="{{route('profileEdit')}}" class="edit-profile-btn" target="_blank" rel="noopener noreferrer">Edit Profile</a>
        </div>


    </div>
</x-dashboard-layout>