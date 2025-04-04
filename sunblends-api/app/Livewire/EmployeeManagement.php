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
use Illuminate\Support\Facades\Auth;

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
    public $currentUser;
    
    // Check if current user can edit a specific employee
    public function canEdit($employeeId)
    {
        // Check if currentUser is set
        if (!$this->currentUser) {
            return false;
        }

        // Super Admin can edit anyone
        if ($this->currentUser->role_id == 1 || ($this->currentUser->role && $this->currentUser->role->name == 'Super Admin')) {
            return true;
        }
        
        // Manager can edit themselves and employees (but not Super Admins)
        if ($this->currentUser->role_id == 2 || ($this->currentUser->role && $this->currentUser->role->name == 'Manager')) {
            if ($employeeId == $this->currentUser->employee_id) {
                return true; // Manager can edit themselves
            }
            
            try {
                // Check if target is not Super Admin or Manager
                $employee = Employee::findOrFail($employeeId);
                if (($employee->role_id != 1 && $employee->role_id != 2) || 
                    ($employee->role && $employee->role->name != 'Super Admin' && $employee->role->name != 'Manager')) {
                    return true; // Manager can edit regular employees
                }
            } catch (\Exception $e) {
                return false; // Employee not found
            }
            
            return false;
        }
        
        // Employees cannot edit anyone
        return false;
    }
    
    // Check if current user can delete a specific employee
    public function canDelete($employeeId)
    {
        // Check if currentUser is set
        if (!$this->currentUser) {
            return false;
        }

        // Super Admin can delete anyone except themselves
        if ($this->currentUser->role_id == 1 || ($this->currentUser->role && $this->currentUser->role->name == 'Super Admin')) {
            return $employeeId != $this->currentUser->employee_id;
        }
        
        // Manager can delete regular employees only
        if ($this->currentUser->role_id == 2 || ($this->currentUser->role && $this->currentUser->role->name == 'Manager')) {
            try {
                $employee = Employee::findOrFail($employeeId);
                return ($employee->role_id != 1 && $employee->role_id != 2) &&
                       !($employee->role && ($employee->role->name == 'Super Admin' || $employee->role->name == 'Manager'));
            } catch (\Exception $e) {
                return false; // Employee not found
            }
        }
        
        // Employees cannot delete anyone
        return false;
    }
    
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
            'role_id' => [
                'required',
                'exists:roles,id',
                function ($attribute, $value, $fail) {
                    // Prevent Managers from creating Super Admins
                    if (($this->currentUser->role_id == 2 || 
                        ($this->currentUser->role && $this->currentUser->role->name == 'Manager')) && 
                        $value == Role::where('name', 'Super Admin')->first()->id) {
                        $fail('You do not have permission to assign Super Admin role.');
                    }
                    
                    // Prevent Managers from changing role of other Managers
                    if ($this->editMode && ($this->currentUser->role_id == 2 || 
                        ($this->currentUser->role && $this->currentUser->role->name == 'Manager'))) {
                        $employee = Employee::findOrFail($this->employee_id);
                        if (($employee->role_id == 2 || 
                            ($employee->role && $employee->role->name == 'Manager')) && 
                            $employee->employee_id != $this->currentUser->employee_id) {
                            $fail('You do not have permission to change another manager\'s role.');
                        }
                    }
                },
            ],
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
        // Get current authenticated employee from multiple possible sources
        $this->currentUser = Auth::guard('web')->user() ?? 
                             Auth::guard('employee')->user() ?? 
                             session('logged_in_employee');
        
        // Redirect if user is not authenticated or not authorized
        if (!$this->currentUser || 
            !(($this->currentUser->role_id == 1 || $this->currentUser->role_id == 2) || 
            ($this->currentUser->role && in_array($this->currentUser->role->name, ['Super Admin', 'Manager'])))) {
            // Add a flash message
            session()->flash('error', 'You do not have permission to access Employee Management.');
            
            // Redirect to dashboard
            return redirect()->to('/dashboard');
        }
        
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
        // Only Super Admin and Manager can create employees
        if (!$this->canCreateEmployee()) {
            session()->flash('error', 'You do not have permission to create employees.');
            return;
        }
        
        $this->resetInputFields();
        $this->isModalOpen = true;
    }

    // Check if current user can create employees
    public function canCreateEmployee()
    {
        if (!$this->currentUser) {
            return false;
        }
        
        return ($this->currentUser->role_id == 1 || $this->currentUser->role_id == 2) || 
               ($this->currentUser->role && in_array($this->currentUser->role->name, ['Super Admin', 'Manager']));
    }

    // Open modal for editing an employee
    public function edit($id)
    {
        // Check if user can edit this employee
        if (!$this->canEdit($id)) {
            session()->flash('error', 'You do not have permission to edit this employee.');
            return;
        }
        
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
        // Check if user can delete this employee
        if (!$this->canDelete($id)) {
            session()->flash('error', 'You do not have permission to delete this employee.');
            return;
        }
        
        $this->employee_id = $id;
        $this->isDeleteModalOpen = true;
    }

    // Delete an employee
    public function deleteEmployee()
    {
        // Double-check permissions before deleting
        if (!$this->canDelete($this->employee_id)) {
            session()->flash('error', 'You do not have permission to delete this employee.');
            $this->isDeleteModalOpen = false;
            return;
        }
        
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
                // Verify permission again before updating
                if (!$this->canEdit($this->employee_id)) {
                    session()->flash('error', 'You do not have permission to edit this employee.');
                    return;
                }
                
                $employee = Employee::findOrFail($this->employee_id);
                
                // Update employee data
                $employee->name = $this->name;
                $employee->employee_email = $this->employee_email;
                $employee->employee_number = $this->employee_number;
                
                // Only update role if user has permission
                if (($this->currentUser->role_id == 1 || ($this->currentUser->role && $this->currentUser->role->name == 'Super Admin')) || 
                    (($this->currentUser->role_id == 2 || ($this->currentUser->role && $this->currentUser->role->name == 'Manager')) && 
                     (($employee->role_id != 1 && $employee->role_id != 2) || 
                      ($employee->role && $employee->role->name != 'Super Admin' && $employee->role->name != 'Manager') ||
                     $employee->employee_id == $this->currentUser->employee_id))) {
                    $employee->role_id = $this->role_id;
                }
                
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
                
                // Sync roles with Spatie if role changed
                if (($this->currentUser->role_id == 1 || ($this->currentUser->role && $this->currentUser->role->name == 'Super Admin')) || 
                    (($this->currentUser->role_id == 2 || ($this->currentUser->role && $this->currentUser->role->name == 'Manager')) && 
                     (($employee->role_id != 1 && $employee->role_id != 2) || 
                      ($employee->role && $employee->role->name != 'Super Admin' && $employee->role->name != 'Manager') ||
                     $employee->employee_id == $this->currentUser->employee_id))) {
                    $role = SpatieRole::findById($this->role_id);
                    if ($role) {
                        $employee->syncRoles([$role->name]);
                    }
                }
                
                session()->flash('success', 'Employee updated successfully.');
            } else {
                // Verify permission before creating
                if (!$this->canCreateEmployee()) {
                    session()->flash('error', 'You do not have permission to create employees.');
                    return;
                }
                
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
                if ($role) {
                    $employee->assignRole($role->name);
                }
                
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
        // For Managers, filter out Super Admins from the list
        $employeesQuery = Employee::with('role');
        
        if ($this->currentUser->role_id == 2 || ($this->currentUser->role && $this->currentUser->role->name == 'Manager')) {
            // Get Super Admin role ID
            $superAdminRoleId = Role::where('name', 'Super Admin')->first()->id;
            
            // Exclude Super Admins from the list for Managers
            $employeesQuery->where(function($query) use ($superAdminRoleId) {
                $query->where('role_id', '!=', $superAdminRoleId)
                      ->orWhere('employee_id', $this->currentUser->employee_id); // But include themselves
            });
        }
        
        // Apply search filters
        $employees = $employeesQuery
            ->when($this->search, function ($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('employee_email', 'like', "%{$search}%")
                      ->orWhere('employee_number', 'like', "%{$search}%");
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
        
        // Limit role options for managers
        if ($this->currentUser->role_id == 1 || ($this->currentUser->role && $this->currentUser->role->name == 'Super Admin')) {
            $roles = Role::all();
        } else {
            // Managers can't assign Super Admin role
            $roles = Role::where('name', '!=', 'Super Admin')->get();
        }
        
        return view('livewire.employee-management', [
            'employees' => $employees,
            'roles' => $roles,
            'currentUser' => $this->currentUser
        ]);
    }
}