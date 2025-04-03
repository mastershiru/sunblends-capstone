<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Employee;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role as SpatieRole;

class EmployeeManagement extends Component
{
    use WithPagination, WithFileUploads;
    
    // Table properties
    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $perPage = 10;
    
    // Form properties
    public $employee_id;
    public $name;
    public $employee_email;
    public $employee_number;
    public $password;
    public $password_confirmation;
    public $employee_picture;
    public $role_id;
    public $temp_image;
    
    // UI state
    public $isModalOpen = false;
    public $isDetailsModalOpen = false;
    public $isDeleteModalOpen = false;
    public $confirmingPassword = false;
    public $editMode = false;
    public $selectedEmployee = null;
    
    // Validation rules - defined as a method to handle conditional rules
    protected function rules()
    {
        return [
            'name' => 'required|string|min:3|max:255',
            'employee_email' => [
                'required', 
                'email',
                Rule::unique('employee', 'employee_email')->ignore($this->employee_id, 'employee_id')
            ],
            'employee_number' => [
                'required',
                'string',
                Rule::unique('employee', 'employee_number')->ignore($this->employee_id, 'employee_id')
            ],
            'role_id' => 'required|exists:roles,id',
            'temp_image' => $this->editMode ? 'nullable|image|max:1024' : 'required|image|max:1024',
            'password' => $this->editMode ? 'nullable|min:8|confirmed' : 'required|min:8|confirmed',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function mount()
    {
        $this->resetInputFields();
    }

    // Reset form fields
    public function resetInputFields()
    {
        $this->reset([
            'employee_id', 'name', 'employee_email', 'employee_number', 
            'password', 'password_confirmation', 'role_id', 'temp_image'
        ]);
        $this->editMode = false;
    }

    // Open modal for creating a new employee
    public function openModal()
    {
        $this->resetInputFields();
        $this->isModalOpen = true;
    }

    // Open modal for editing an employee
    public function edit($id)
    {
        $this->editMode = true;
        $this->employee_id = $id;
        
        $employee = Employee::findOrFail($id);
        $this->name = $employee->name;
        $this->employee_email = $employee->employee_email;
        $this->employee_number = $employee->employee_number;
        $this->role_id = $employee->role_id;
        
        $this->isModalOpen = true;
    }

    // View employee details
    public function viewDetails($id)
    {
        $this->selectedEmployee = Employee::with('role')->findOrFail($id);
        $this->isDetailsModalOpen = true;
    }

    // Confirm delete operation
    public function confirmDelete($id)
    {
        $this->employee_id = $id;
        $this->isDeleteModalOpen = true;
    }

    // Delete an employee
    public function deleteEmployee()
    {
        try {
            $employee = Employee::findOrFail($this->employee_id);
            
            // Delete employee picture if exists
            if ($employee->employee_picture) {
                Storage::disk('public')->delete($employee->employee_picture);
            }
            
            $employee->delete();
            
            session()->flash('success', 'Employee deleted successfully.');
            $this->isDeleteModalOpen = false;
            
        } catch (\Exception $e) {
            Log::error('Error deleting employee: ' . $e->getMessage());
            session()->flash('error', 'Failed to delete employee: ' . $e->getMessage());
        }
    }

    // Store/Update employee
    public function store()
    {
        $this->validate();
        
        try {
            if ($this->editMode) {
                $employee = Employee::findOrFail($this->employee_id);
                
                // Update employee data
                $employee->name = $this->name;
                $employee->employee_email = $this->employee_email;
                $employee->employee_number = $this->employee_number;
                $employee->role_id = $this->role_id;
                
                // Update password if provided
                if ($this->password) {
                    $employee->employee_password = Hash::make($this->password);
                }
                
                // Update image if provided
                if ($this->temp_image) {
                    // Delete old image
                    if ($employee->employee_picture) {
                        Storage::disk('public')->delete($employee->employee_picture);
                    }
                    
                    // Store new image
                    $imagePath = $this->temp_image->store('employee_pictures', 'public');
                    $employee->employee_picture = $imagePath;
                }
                
                $employee->save();
                
                // Sync roles with Spatie
                $role = SpatieRole::findById($this->role_id);
                $employee->syncRoles([$role->name]);
                
                session()->flash('success', 'Employee updated successfully.');
            } else {
                // Create new employee
                $employee = new Employee();
                $employee->name = $this->name;
                $employee->employee_email = $this->employee_email;
                $employee->employee_number = $this->employee_number;
                $employee->employee_password = Hash::make($this->password);
                $employee->role_id = $this->role_id;
                
                // Store image
                if ($this->temp_image) {
                    $imagePath = $this->temp_image->store('employee_pictures', 'public');
                    $employee->employee_picture = $imagePath;
                }
                
                $employee->save();
                
                // Assign role with Spatie
                $role = SpatieRole::findById($this->role_id);
                $employee->assignRole($role->name);
                
                session()->flash('success', 'Employee created successfully.');
            }
            
            $this->resetInputFields();
            $this->isModalOpen = false;
            
        } catch (\Exception $e) {
            Log::error('Error saving employee: ' . $e->getMessage());
            session()->flash('error', 'Failed to save employee: ' . $e->getMessage());
        }
    }

    // Close modal
    public function closeModal()
    {
        $this->resetInputFields();
        $this->isModalOpen = false;
        $this->isDetailsModalOpen = false;
        $this->isDeleteModalOpen = false;
    }

    // Sort table
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        
        $this->sortField = $field;
    }

    public function render()
    {
        $employees = Employee::with('role')
            ->when($this->search, function ($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('employee_email', 'like', "%{$search}%")
                      ->orWhere('employee_number', 'like', "%{$search}%");
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
            
        $roles = Role::all();
            
        return view('livewire.employee-management', [
            'employees' => $employees,
            'roles' => $roles
        ]);
    }
}