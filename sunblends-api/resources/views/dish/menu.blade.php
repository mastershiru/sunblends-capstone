@extends('layouts.layout')


<head>
<meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<style>
    .view-cart {
    max-height: 375px; /* Maximum height of the popup */
    overflow-y: auto; /* Enable vertical scrollbar if needed */
    padding: 20px; /* Add padding for better appearance */
    box-sizing: border-box; /* Ensure padding is included in the height calculation */
}

    #popup{
        max-height: 500px;
    }

</style>


<body>

    @livewire('customer-navbar')


    <br><br><br><br><br><br><br>
    



    <section class="our-menu section bg-light repeat-img" id="menu">
    <div class="sec-wp" style="margin-top: -50px;">
        <div class="container">
            <div id="category-filter-buttons" class="category-filter" style="padding-bottom: 110px;">
                <span><h3>Category</h3></span>
                <a href="{{ url('/dish/create') }}" class="btn btn-success btn-sm" title="Add New Dish">
                    <i class="fa fa-plus" aria-hidden="true"></i> Add New
                </a>
            </div>

            <!-- Container for the menu items -->
            <div class="menu-list-row">
                <div class="row g-xxl-5 bydefault_show" id="menu-dish">
                    @foreach($dish as $item)
                        <div class="col-lg-4 col-sm-6 dish-box">
                            <form method="POST" action="{{ url('/dish' . '/' . $item->id) }}" accept-charset="UTF-8" style="display:inline">
                                {{ method_field('DELETE') }}
                                {{ csrf_field() }}
                                <button type="submit" class="btn btn-danger btn-sm" title="Delete Dish" onclick="return confirm(&quot;Confirm delete?&quot;)">
                                    <i class="fa fa-trash-o" aria-hidden="true"></i> Delete
                                </button>
                            </form>
                            <div class="dish-box text-center">
                                <div class="dist-img">
                                    <img src="{{ asset('images/dish/dish' . $item->id . '.png') }}" alt="{{ $item->Dish }}">
                                </div>
                                <div class="dish-title">
                                    <h3 class="h3-title">{{ $item->Dish }}</h3>
                                    <p>{{ $item->Calories }} Calories</p>
                                </div>
                                <div class="dish-info">
                                    <ul>
                                        <li>
                                            <p>Type</p>
                                            <b>{{ $item->Category }}</b>
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
                                            <b>{{ $item->Price }}$</b>
                                        </li>
                                        <li>
                                            <button class="dish-add-btn" data-dish-id="{{ $item->id }}">
                                                <i class="uil uil-plus"></i>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>



</body>





