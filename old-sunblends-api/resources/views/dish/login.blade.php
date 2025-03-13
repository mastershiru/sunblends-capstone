@extends('dish.layout')


<button onclick="showLoginPopup();">pre</button>
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
            <div class="profile-account" id="profile-account" >
                <p id= "AccName" style="font-weight: 300; color: #000; font-size: 25px;">Name</p>
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
                    <label for="email">Email</label>
                    <input type="text" id="email" name="" placeholder="Email">
                </div>
                <div class="form-element">
                    <label for="password">Password</label>
                    <input type="password" id="password" placeholder="Passowrd">
                </div>
                <div class="form-element">
                    <input type="checkbox" id="remember-me">
                    <label for="remember-me">Remember me</label>
                </div>
                <div class="form-element">
                    <button onclick="login()">Login</button>
                    <button id="popup-register-form" class="signup-btn">Register</button>
                    <div class="form-element">
                        <a href="#">Forgot password?</a>
                    </div>
                </div>
            </div>

            <!-- regiter -->
            <div class="back-btn" id="back-btn">
                <i class="uil uil-angle-left-b"></i>
            </div>

            <div class="form" id="register-form" style="display: none;">
                <h2 class="h2-register">Register</h2>
                <div class="form-element register-form-element">
                    <label for="username1">Username</label>
                    <input type="text" id="username1" placeholder="Username">
                </div>
                <div class="form-element register-form-element">
                    <label for="email1">Email</label>
                    <input type="text" id="email1" placeholder="Email">
                </div>
                <div class="form-element register-form-element">
                    <label for="number">Phone Number</label>
                    <input type="text" id="number" placeholder="Phone Number">
                </div>
                <div class="form-element register-form-element">
                    <label for="password1">Password</label>
                    <input type="password" id="password1" placeholder="Password">
                </div>
                <div class="form-element register-form-element">
                    <label for="confirm-password">Confirm Password</label>
                    <input type="password" id="confirm-password" placeholder="Confirm Password">
                </div>
                <div class="form-element register-form-element">
                    <button type="submit" onclick="validateForm()">Sign up</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {        
            // Call the function when the page loads
            showLoginPopup()
        });
    </script>
    