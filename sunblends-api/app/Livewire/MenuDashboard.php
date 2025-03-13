<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Dish;
use Livewire\WithFileUploads;



class MenuDashboard extends Component
{
    use withFileUploads;

    public $isDishModalOpen = false;

    public $dishes;
    public $dish_id;
    public $dish_name;
    public $dish_picture;
    public $old_picture;
    public $category;
    public $dish_available;
    public $Price;
    public $dishDropDown;

    public function openDishModal()
    {
        $this->isDishModalOpen = true;
    }

    public function closeDishModal()
    {
        $this->isDishModalOpen = false;
        $this->resetInputFields();
    }

    public function updateAvailability($id, $value)
    {
        $dish = Dish::findOrFail($id);

        if ($dish) {
            $dish->update([
                'dish_available' => $value
            ]);
            
            $this->loadDishes();
        }
    }

    public function resetInputFields()
    {
        $this->reset([
            'dish_name', 
            'dish_picture', 
            'category', 
            'dish_available', 
            'Price'
        ]);
        
    }

    public function mount()
    {
        $this->loadDishes();
    }

    public function loadDishes()
    {
        $this->dishes = Dish::all();
    }

    public function store()
    {
        $validationRules = [
            'dish_name' => 'required|string|min:3',
            'category' => 'required|string',
            'dish_available' => 'required|boolean',
            'Price' => 'required|numeric'
        ];

        // Add dish_picture validation only for new dishes
        if (!$this->dish_id) {
            $validationRules['dish_picture'] = 'required|image|max:1024';
        } 
            

        $validatedData = $this->validate($validationRules);

        if ($this->dish_id) {
            // Update existing dish
            $dish = Dish::find($this->dish_id);
            
            // Store the new image if provided
            if ($this->dish_picture) {
                $imagePath = $this->dish_picture->store('dishes', 'public');
                $dish->dish_picture = 'storage/' . $imagePath;
            }

            $dish->update([
                'dish_name' => $this->dish_name,
                'category' => $this->category,
                'dish_available' => $this->dish_available,
                'Price' => $this->Price,
            ]);

            session()->flash('message', 'Dish Updated Successfully.');
        } else {
            // Create new dish
            $imagePath = $this->dish_picture->store('dishes', 'public');
            
            Dish::create([
                'dish_name' => $this->dish_name,
                'dish_picture' => 'storage/' . $imagePath,
                'category' => $this->category,
                'dish_available' => $this->dish_available,
                'Price' => $this->Price,
            ]);

            session()->flash('message', 'Dish Created Successfully.');
        }

        $this->loadDishes();
        $this->closeDishModal();
        $this->resetInputFields();
    }
    public function edit($id)
    {
        $dish = Dish::findOrFail($id);
        $this->dish_id = $id;
        $this->dish_name = $dish->dish_name;
        $this->old_picture = $dish->dish_picture; // Store the old picture path
        $this->dish_picture = null; // Reset picture upload
        $this->category = $dish->category;
        $this->dish_available = $dish->dish_available;
        $this->Price = $dish->Price;

        $this->openDishModal();
    }
    


    public function render()
    {
        return view('livewire.menu-dashboard',
        ['dishes' => $this->dishes]);
    }
}
