@extends('layouts.layout')


@section('content')


    
<section class="main-banner" id="home">
        <div class="sec-wp">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 ">
                        <div class="banner-text">
                            <h1 class="h1-title">
                                SHE LOVED
                                <span>TEA,</span>
                                I AM COFFEE. WE BLEND.
                            </h1>
                            <p class="tagline">We offer a wonderful range of uniquely delectable food and beverages.</p>
                            <div class="banner-btn mt-4">
                                <a href="#menu" class="sec-btn">Order Now</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="banner-img-wp">
                            <div class="banner-img" style="background-image: url('{{ asset('images/coffee.jpg') }}');">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="about-sec section" id="about">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="sec-title text-center mb-5">
                        <p class="sec-sub-title mb-3">About Us</p>
                        <h2 class="h2-title">Discover our <span>restaurant story</span></h2>
                        <div class="sec-title-shape mb-4">
                            <img src="'{{ asset('images/title-shape.svg') }}'" alt="">
                        </div>
                        <p class="about-text">
                            Welcome to The SunBlends Café, where every cup tells a story of love, remembrance, and dedication.
                        </p>
                        
                        <p class="about-text">
                            Our journey began in December 2019, with the opening of our first branch in Montalban, Rizal. Founded as a tribute to a cherished daughter who adored tea, and her mother who cherished coffee, SunBlends Café is more than just a place to enjoy delicious beverages and mouthwatering treats. It's a living memory, a celebration of a life well-lived, and a testament to the enduring power of love.
                        </p>
                        
                        <p class="about-text">
                            In 2022, we expanded our family with the opening of our second branch in Quezon City, nestled in the heart of Trinity University of Asia. Here, we proudly collaborate with the College of Hospitality and Tourism Management, offering students invaluable hands-on experience and instilling in them the essence of excellent customer service.
                        </p>
                        
                        <p class="about-text">
                            At SunBlends Café, we offer more than just a menu; we offer an experience. Step into our vibrant and trendy space, where every corner exudes warmth and hospitality. Whether you're craving a comforting cup of coffee, a refreshing milk tea, or a delectable pastry to satisfy your sweet tooth, we have something to tantalize every palate.
                        </p>
                        
                        <p class="about-text">
                            But SunBlends Café is more than just a place to eat and drink; it's a community hub, a gathering place for friends and family to connect, unwind, and create lasting memories. Our unlimited promos, diverse menu offerings, and welcoming atmosphere ensure that every visit is a delightful experience.
                        </p>
                        
                        <p class="about-text">
                            So come, bask in the sunshine of our smiles, and let us brighten up your day at The SunBlends Café, where every moment is infused with love and warmth.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8 m-auto">
                <div class="about-video">
                    <div class="about-video-img" style="background-image: url('{{ asset('images/Sundblends-img.jpg') }}');">
                    </div>
                    <div class="play-btn-wp">
                        <a href="'{{ asset('images/video.mp4') }}'" data-fancybox="video" class="play-btn">
                            <i class="uil uil-play"></i>

                        </a>
                        <span>Watch The Recipe</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section style="background-image: url('{{ asset('images/menu-bg.png') }}');" class="our-menu section bg-light repeat-img" id="menu">
        <div class="sec-wp">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="sec-title text-center mb-5">
                            <p class="sec-sub-title mb-3">our menu</p>
                            <h2 class="h2-title">wake up early, <span>eat fresh & healthy</span></h2>
                            <div class="sec-title-shape mb-4">
                                <img src="{{ asset('images/title-shape.svg') }}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="best-selling-tab-wp">
                    <div class="row">
                        <div class="col-lg-12 m-auto">
                            <div class="best-selling-tab text-center">
                                <img src="{{ asset('images/menu-1.png') }}" alt="">
                                BEST SELLING
                            </div>
                        </div>
                    </div>
                </div>
                <div class="menu-list-row">
                    <div class="row g-xxl-5 bydefault_show" id="menu-dish">
                        <div class="col-lg-4 col-sm-6 dish-box-wp">
                            <div class="dish-box text-center">
                                <div class="dist-img">
                                    <img src="assets/images/dish/dish1.png" alt="">
                                </div>
                                <div class="dish-rating">
                                    5
                                    <i class="uil uil-star"></i>
                                </div>
                                <div class="dish-title">
                                    <h3 class="h3-title">Breakfast <br> egg & bacon</h3>
                                    <p>120 calories</p>
                                </div>
                                <div class="dish-info">
                                    <ul>
                                        <li>
                                            <p>Type</p>
                                            <b>Non Veg</b>
                                        </li>
                                        <li>
                                            <p>Persons</p>
                                            <b>2</b>
                                        </li>
                                    </ul>
                                </div>
                                <div class="dist-bottom-row">
                                    <ul>
                                        <li>
                                            <b>99</b>
                                        </li>
                                        <li>
                                            <button class="dish-add-btn">
                                                <i class="uil uil-plus"></i>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- 2 -->
                        <div class="col-lg-4 col-sm-6 dish-box-wp">
                            <div class="dish-box text-center">
                                <div class="dist-img">
                                    <img src="assets/images/dish/dish2.png" alt="">
                                </div>
                                <div class="dish-rating">
                                    4.3
                                    <i class="uil uil-star"></i>
                                </div>
                                <div class="dish-title">
                                    <h3 class="h3-title">Fish & Chips</h3>
                                    <p>100 calories</p>
                                </div>
                                <div class="dish-info">
                                    <ul>
                                        <li>
                                            <p>Type</p>
                                            <b>Fish</b>
                                        </li>
                                        <li>
                                            <p>Persons</p>
                                            <b>1</b>
                                        </li>
                                    </ul>
                                </div>
                                <div class="dist-bottom-row">
                                    <ul>
                                        <li>
                                            <b>₱359</b>
                                        </li>
                                        <li>
                                            <button class="dish-add-btn">
                                                <i class="uil uil-plus"></i>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- 3 -->
                        <div class="col-lg-4 col-sm-6 dish-box-wp">
                            <div class="dish-box text-center">
                                <div class="dist-img">
                                    <img src="assets/images/dish/dish3.png" alt="">
                                </div>
                                <div class="dish-rating">
                                    4
                                    <i class="uil uil-star"></i>
                                </div>
                                <div class="dish-title">
                                    <h3 class="h3-title">Spaghetti w/ Feta Cheese</h3>
                                    <p>161.88 calories</p>
                                </div>
                                <div class="dish-info">
                                    <ul>
                                        <li>
                                            <p>Type</p>
                                            <b>Pasta</b>
                                        </li>
                                        <li>
                                            <p>Persons</p>
                                            <b>1</b>
                                        </li>
                                    </ul>
                                </div>
                                <div class="dist-bottom-row">
                                    <ul>
                                        <li>
                                            <b>₱399</b>
                                        </li>
                                        <li>
                                            <button class="dish-add-btn">
                                                <i class="uil uil-plus"></i>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- 4 -->
                        <div class="col-lg-4 col-sm-6 dish-box-wp lunch" data-cat="lunch">
                            <div class="dish-box text-center">
                                <div class="dist-img">
                                    <img src="assets/images/dish/dish4.png" alt="">
                                </div>
                                <div class="dish-rating">
                                    4.5
                                    <i class="uil uil-star"></i>
                                </div>
                                <div class="dish-title">
                                    <h3 class="h3-title">Ramen</h3>
                                    <p>436 calories</p>
                                </div>
                                <div class="dish-info">
                                    <ul>
                                        <li>
                                            <p>Type</p>
                                            <b>Noodles</b>
                                        </li>
                                        <li>
                                            <p>Persons</p>
                                            <b>1</b>
                                        </li>
                                    </ul>
                                </div>
                                <div class="dist-bottom-row">
                                    <ul>
                                        <li>
                                            <b>₱379</b>
                                        </li>
                                        <li>
                                            <button class="dish-add-btn">
                                                <i class="uil uil-plus"></i>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- 5 -->
                        <div class="col-lg-4 col-sm-6 dish-box-wp dinner" data-cat="dinner">
                            <div class="dish-box text-center">
                                <div class="dist-img">
                                    <img src="assets/images/dish/dish5.png" alt="">
                                </div>
                                <div class="dish-rating">
                                    5
                                    <i class="uil uil-star"></i>
                                </div>
                                <div class="dish-title">
                                    <h3 class="h3-title">Ground Beef Kebabs</h3>
                                    <p>322.3 calories</p>
                                </div>
                                <div class="dish-info">
                                    <ul>
                                        <li>
                                            <p>Type</p>
                                            <b>Meat</b>
                                        </li>
                                        <li>
                                            <p>Persons</p>
                                            <b>1</b>
                                        </li>
                                    </ul>
                                </div>
                                <div class="dist-bottom-row">
                                    <ul>
                                        <li>
                                            <b>₱99</b>
                                        </li>
                                        <li>
                                            <button class="dish-add-btn">
                                                <i class="uil uil-plus"></i>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- 6 -->
                        <div class="col-lg-4 col-sm-6 dish-box-wp dinner" data-cat="dinner">
                            <div class="dish-box text-center">
                                <div class="dist-img">
                                    <img src="assets/images/dish/dish6.png" alt="">
                                </div>
                                <div class="dish-rating">
                                    5
                                    <i class="uil uil-star"></i>
                                </div>
                                <div class="dish-title">
                                    <h3 class="h3-title">Tomato Basil <br> Penne Pasta </h3>
                                    <p>502 calories</p>
                                </div>
                                <div class="dish-info">
                                    <ul>
                                        <li>
                                            <p>Type</p>
                                            <b>Pasta</b>
                                        </li>
                                        <li>
                                            <p>Persons</p>
                                            <b>1</b>
                                        </li>
                                    </ul>
                                </div>
                                <div class="dist-bottom-row">
                                    <ul>
                                        <li>
                                            <b>₱159</b>
                                        </li>
                                        <li>
                                            <button class="dish-add-btn">
                                                <i class="uil uil-plus"></i>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="two-col-sec section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-5">
                    <div class="sec-img img1 mt-5">
                        <img src="{{ asset('images/Peppermint Mocha Espresso Martini.png') }}" alt="">
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="sec-text text1">
                        <h2 class="xxl-title">Peppermint Mocha Espresso Martini</h2>
                        <p>This is Lorem ipsum dolor sit amet consectetur adipisicing elit. Amet dolores
                            eligendi earum eveniet soluta officiis asperiores repellat, eum praesentium nihil
                            totam. Non ipsa expedita repellat atque mollitia praesentium assumenda quo
                            distinctio excepturi nobis tenetur, cum ab vitae fugiat hic aspernatur? Quos
                            laboriosam, repudiandae exercitationem atque a excepturi vel. Voluptas, ipsa.</p>
                        <p>This is Lorem ipsum dolor sit amet consectetur adipisicing elit. At fugit laborum
                            voluptas magnam sed ad illum? Minus officiis quod deserunt.</p>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="two-col-sec section pt-0">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 order-lg-1 order-2">
                    <div class="sec-text text2">
                        <h2 class="xxl-title">Vanilla Ice Cream</h2>
                        <p>This is Lorem ipsum dolor sit amet consectetur adipisicing elit. Amet dolores
                            eligendi earum eveniet soluta officiis asperiores repellat, eum praesentium nihil
                            totam. Non ipsa expedita repellat atque mollitia praesentium assumenda quo
                            distinctio excepturi nobis tenetur, cum ab vitae fugiat hic aspernatur? Quos
                            laboriosam, repudiandae exercitationem atque a excepturi vel. Voluptas, ipsa.</p>
                        <p>This is Lorem ipsum dolor sit amet consectetur adipisicing elit. At fugit laborum
                            voluptas magnam sed ad illum? Minus officiis quod deserunt.</p>
                    </div>
                </div>
                <div class="col-lg-6 order-lg-2 order-1">
                    <div class="sec-img img2">
                        <img src="{{ asset('images/Vanilla Ice Cream.png') }}" alt="">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="book-table section bg-light">
        <div class="book-table-shape">
            <img src="{{ asset('images/table-leaves-shape.png') }}" alt="">
        </div>

        <div class="book-table-shape book-table-shape2">
            <img src="{{ asset('images/table-leaves-shape.png') }}" alt="">
        </div>

        <div class="sec-wp">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="sec-title text-center mb-5">
                            <p class="sec-sub-title mb-3">Book Table</p>
                            <h2 class="h2-title">Opening Table</h2>
                            <div class="sec-title-shape mb-4">
                                <img src="'{{ asset('images/title-shape.svg') }}'" alt="">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="book-table-info">
                    <div class="row align-items-center">
                        <div class="col-lg-4 info">
                            <div class="table-title table-title-1 text-center">
                                <h3>Monday to Friday</h3>
                                <p>7:00 am - 5:00 pm</p>
                            </div>
                        </div>
                        <div class="col-lg-4 info">
                            <div class="call-now text-center">
                                <a href="mailto:thesunblendscafe.ph@gmail.com"><i class="fa-regular fa-envelope"></i></a>
                                <h5>thesunblendscafe.ph@gmail.com</h5>
                            </div>
                        </div>
                        <div class="col-lg-4 info">
                            <div class="table-title table-title-2 text-center">
                                <h3>Saturday to Sunday</h3>
                                <p>CLOSE</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
    
    <!-- footer starts  -->
    <footer class="site-footer" id="contact">
        <div class="top-footer section">
            <div class="sec-wp">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="footer-info">
                                <div class="footer-logo">
                                    <a href="{{ url('/home') }}">
                                        <img src="{{ asset('images/logo.png') }}" alt="" style="height: 70px; width: 130px;">
                                    </a>
                                </div>
                                <p class="footer-text">Lorem ipsum, dolor sit amet consectetur adipisicing elit. Mollitia, tenetur.
                                </p>
                                <div class="social-icon">
                                    <ul>
                                        <li>
                                            <a href="#">
                                                <i class="uil uil-facebook-f"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">
                                                <i class="uil uil-instagram"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">
                                                <i class="uil uil-github-alt"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">
                                                <i class="uil uil-youtube"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8 right-content">
                            <div class="footer-flex-box">
                                <div class="footer-table-info">
                                    <h3 class="h3-title">open hours</h3>
                                    <ul>
                                        <li><i class="uil uil-clock"></i> Mon-Fri : 7am - 5pm</li>
                                    </ul>
                                </div>
                                <div class="footer-menu food-nav-menu">
                                    <h3 class="h3-title">Links</h3>
                                    <ul class="column-2">
                                        <li>
                                            <a href="{{ url('/home') }}" class="footer-active-menu">Home</a>
                                        </li>
                                        <li><a href="#about">About</a></li>
                                        <li><a href="{{ url('/dish') }}">Menu</a></li>
                                        <li><a href="#contact">Contact</a></li>
                                    </ul>
                                </div>
                                <div class="footer-menu">
                                    <h3 class="h3-title">Company</h3>
                                    <ul>
                                        <li><a href="#">Terms & Conditions</a></li>
                                        <li><a href="#">Privacy Policy</a></li>
                                        <li><a href="#">Cookie Policy</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="bottom-footer">
            <div class="container">
                <button class="scrolltop"><a href="#" style="text-decoration: none; color:#ff8243 ;"><i class="uil uil-angle-up"></i></a></button>
            </div>
        </div>
    </footer>
    @endsection

    