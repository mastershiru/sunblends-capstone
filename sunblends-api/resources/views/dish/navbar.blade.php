

<div>
<nav class="scale-in-ver-top">
        <a href="{{ url('/home') }}">
            <img class="logo" src="{{ asset('images/logo.png') }}" alt="Sunblends Logo" />
        </a>

        <div style="display: flex; ">
            <ul id="navbar">
                <li>
                    <a href="{{ url('/dish') }}">Menu</a>
                </li>
                <li>
                    <a href="#about">About</a>
                </li>
                <li>
                    <a href="#contact">Contact</a>
                </li>

                <li class="for-mobile">
                    <button style="display: none;" id="mobile-account-button">Account</button>
                </li>
                <li class="for-mobile">
                    <a id="mobile-show-login" href="{{ url('/login') }}">Login</a>
                    
                </li>


                <form action="#" class="header-search-form for-des">
                    <input type="search" class="form-input" placeholder="Search Here...">
                    <button type="submit">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </form>
                <a  class="header-btn header-cart" id="show-cart" style="text-decoration: none; color: #000;">
                    <i class="uil uil-shopping-bag"></i>
                    <span class="cart-number"></span>
                </a>
            </ul>

            <div id="user">
                <a>
                    <i class="fa-solid fa-circle-user header-btn"></i>
                </a>
                <div id="username-display"></div>
                <div class="dropdown-content">
                    <button id="show-account" style="display: none;">Account</button>
                    <button id="show-login">Login</button>
                    <button id="logout-button" style="display: none;" onclick="logout()">Logout</button>
                    

                </div>
            </div>

            <div id="mobile">
                <a>
                    <i id="bar" class="fas fa-bars"></i>
                </a>
            </div>
        </div>
    </nav>



    <!-- popup login / register -->
    <div class="center" id="center">
        <div class="popup">

            <div class="close-btn" id="close-btn">
                <i class="uil uil-times"></i>
            </div>

            <!-- Cart -->
            <div class="view-cart" id="view-cart">
                <h2 style="font-weight: 500; color: #000; text-align: center;">Cart</h2>
                <button class="checkout-btn">Checkout</button>
            </div>

            <!-- Account -->
            <div class="profile-account" id="profile-account">
                <p id="AccName" style="font-weight: 300; color: #000; font-size: 25px;">Name</p>
                <p id="AccEmail"></p>
                <img src="{{ asset('images/profile.png') }}" alt=""
                    style="height: 100px; width: 100px; border-radius: 50%; margin-bottom: 30px;">
                <button id="edit-profile"
                    style="display: block; padding: 12px; cursor: pointer; margin: 10px auto;">Edit Profile</button>
            </div>

            <!-- Login Form -->
            <div class="form" id="login-form">
                <h2>Log in</h2>
                <div class="form-element">
                    <label for="login-email">Email</label>
                    <input type="text" id="login-email" placeholder="Email">
                </div>
                <div class="form-element">
                    <label for="login-password">Password</label>
                    <input type="password" id="login-password" placeholder="Password">
                </div>
                <div class="form-element">
                    <input type="checkbox" id="remember-me">
                    <label for="remember-me">Remember me</label>
                </div>
                <div class="form-element">
                    <button id="login-button">Login</button>
                    <button id="popup-register-form" class="signup-btn">Register</button>
                    <div class="form-element">
                        <a href="#">Forgot password?</a>
                    </div>
                </div>
            </div>

                            <!-- Register Form -->
            <div class="form" id="register-form" style="display: none;">
                <form id="registration-form" action="{{ url('/register') }}" method="POST">
                    @csrf <!-- Include CSRF token -->
                    <h2 class="h2-register">Register</h2>
                    <div class="form-element register-form-element">
                        <label for="register-name">Name</label>
                        <input type="text" id="register-name" name="name" placeholder="Name" class="form-control" required>
                    </div>
                    <div class="form-element register-form-element">
                        <label for="register-email">Email</label>
                        <input type="email" id="register-email" name="email" placeholder="Email" class="form-control" required>
                    </div>
                    <div class="form-element register-form-element">
                        <label for="register-number">Phone Number</label>
                        <input type="text" id="register-number" name="number" placeholder="Phone Number" class="form-control" required>
                    </div>
                    <div class="form-element register-form-element">
                        <label for="register-password">Password</label>
                        <input type="password" id="register-password" name="password" placeholder="Password" class="form-control" required>
                    </div>
                    <div class="form-element register-form-element">
                        <label for="register-confirm-password">Confirm Password</label>
                        <input type="password" id="register-confirm-password" name="password_confirmation" placeholder="Confirm Password" class="form-control" required>
                    </div>
                    <div class="form-element register-form-element">
                        <button type="submit">Sign up</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
</div>