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
            @if ($user->avatar)
                <div class="profile-img">
                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{$user->username}}">
                </div>
            @endif

            <div class="profile-name-bio profile-content-data">
                <h2>{{$user->username}}</h2>

                {{-- <div class="bio">
                    Bio: <span>let it prevail</span>
                </div> --}}
            </div>

            <div class="profile-location profile-content-data">
                <h3>Location</h3>
                <p>
                    Address: <span>{{ucwords($user->address)}}</span>
                </p>

                <p>
                    City: <span>{{ucwords($user->city)}}</span>
                </p>
                <p>
                    State: <span>{{ucwords($user->state)}}</span>
                </p>

                <p>
                    Country: <span>{{ucwords($user->country)}}</span>
                </p>
            </div>

            <div class="profile-contact profile-content-data">
                <h3>contact</h3>
                <p>
                    Email: <span>{{$user->email}}</span>
                </p>

                <p>
                    Phone: <span>{{$user->phone}}</span>
                </p>
            </div>

            <div class="profile-personal profile-content-data">
                <h3>Personal</h3>
                <p>
                    DOB: <span>{{$user->date_of_birth}}</span>
                </p>

                <p>
                    Gender: <span>{{$user->gender}}</span>
                </p>
            </div>

            <a href="{{route('profileEdit')}}" class="edit-profile-btn" target="_blank" rel="noopener noreferrer">Edit Profile</a>
        </div>


    </div>
</x-dashboard-layout>