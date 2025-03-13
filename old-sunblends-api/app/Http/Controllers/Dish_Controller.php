<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Dish;

class Dish_Controller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index():View
    {
        $dish = Dish::all();
        return view ('menu-customer-page', compact('dish'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dish.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $input = $request->all();
        Dish::create($input);
        return redirect('dish')->with('flash_message', 'dish Addedd!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $dish = Dish::find($id);
        return view('dish.show')->with('dish', $dish);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $dish = Dish::find($id);
        return view('dish.edit')->with('dish', $dish);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $dish = Student::find($id);
        $input = $request->all();
        $dish->update($input);
        return redirect('dish')->with('flash_message', 'Dish Updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Dish::destroy($id);
        return redirect('dish')->with('flash_message', 'Dish deleted!');
    }
}
