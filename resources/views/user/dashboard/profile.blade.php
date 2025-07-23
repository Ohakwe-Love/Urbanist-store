{{-- @props([
    
]) --}}

<x-dashboard-layout>
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
                <img src="{{asset('assets/images/avatar/testing.jpg')}}" alt="">
            </div>

            <div class="profile-name-bio profile-content-data">
                <h2>Chidinma</h2>

                <div class="bio">
                    Bio: <span>let it prevail</span>
                </div>
            </div>

            <div class="profile-location profile-content-data">
                <h3>Location</h3>
                <p>
                    Address: <span>chidi@gmail.com</span>
                </p>

                <p>
                    City: <span>093898329</span>
                </p>
                <p>
                    State: <span>chidi@gmail.com</span>
                </p>

                <p>
                    Country: <span>093898329</span>
                </p>
            </div>

            <div class="profile-contact profile-content-data">
                <h3>contact</h3>
                <p>
                    Email: <span>chidi@gmail.com</span>
                </p>

                <p>
                    Phone: <span>093898329</span>
                </p>
            </div>

            <div class="profile-personal profile-content-data">
                <h3>Personal</h3>
                <p>
                    DOB: <span>27/09/2006</span>
                </p>

                <p>
                    Gender: <span>Female</span>
                </p>
            </div>

            <a href="{{route('profileEdit')}}" class="edit-profile-btn">Edit Profile</a>
        </div>


    </div>
</x-dashboard-layout>