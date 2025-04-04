<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;
use Carbon\Carbon;

class ActivityLogsView extends Component
{
    use WithPagination;

    // Filter properties
    public $search = '';
    public $logType = '';
    public $dateRange = 'today';
    public $startDate;
    public $endDate;
    public $causer = '';
    public $perPage = 15;
    public $isDetailModalOpen = false;
    public $selectedLog = null;

    protected $listeners = ['refreshLogs' => '$refresh'];

    public function mount()
    {
        // Set default date range
        $this->setDateRange('today');
    }

    /**
     * Set date range based on selection
     */
    public function setDateRange($range)
    {
        $this->dateRange = $range;
        
        switch ($range) {
            case 'today':
                $this->startDate = now()->startOfDay()->format('Y-m-d');
                $this->endDate = now()->endOfDay()->format('Y-m-d');
                break;
            case 'yesterday':
                $this->startDate = now()->subDay()->startOfDay()->format('Y-m-d');
                $this->endDate = now()->subDay()->endOfDay()->format('Y-m-d');
                break;
            case 'this_week':
                $this->startDate = now()->startOfWeek()->format('Y-m-d');
                $this->endDate = now()->endOfWeek()->format('Y-m-d');
                break;
            case 'this_month':
                $this->startDate = now()->startOfMonth()->format('Y-m-d');
                $this->endDate = now()->endOfMonth()->format('Y-m-d');
                break;
            case 'last_month':
                $this->startDate = now()->subMonth()->startOfMonth()->format('Y-m-d');
                $this->endDate = now()->subMonth()->endOfMonth()->format('Y-m-d');
                break;
            case 'custom':
                // Keep current dates if already set, otherwise set to last 7 days
                if (!$this->startDate) {
                    $this->startDate = now()->subDays(7)->format('Y-m-d');
                }
                if (!$this->endDate) {
                    $this->endDate = now()->format('Y-m-d');
                }
                break;
            default:
                $this->startDate = now()->startOfDay()->format('Y-m-d');
                $this->endDate = now()->endOfDay()->format('Y-m-d');
                break;
        }
    }

    /**
     * Handle date range change
     */
    public function updatedDateRange()
    {
        $this->resetPage();
        $this->setDateRange($this->dateRange);
    }

    /**
     * Reset pagination on filter change
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingLogType()
    {
        $this->resetPage();
    }

    public function updatingCauser()
    {
        $this->resetPage();
    }

    /**
     * Get activity logs based on filters
     */
    public function getActivityLogsProperty()
    {
        return Activity::query()
            ->with(['causer', 'subject'])
            ->when($this->search, function ($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                      ->orWhere('log_name', 'like', "%{$search}%")
                      ->orWhere('subject_type', 'like', "%{$search}%");
                });
            })
            ->when($this->logType, function ($query, $logType) {
                return $query->where('log_name', $logType);
            })
            ->when($this->causer, function ($query, $causer) {
                return $query->where('causer_type', 'like', "%{$causer}%");
            })
            ->when($this->startDate && $this->endDate, function ($query) {
                return $query->whereBetween('created_at', [
                    Carbon::parse($this->startDate)->startOfDay(),
                    Carbon::parse($this->endDate)->endOfDay(),
                ]);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);
    }

    /**
     * Get unique log types/names for filtering
     */
    public function getLogTypesProperty()
    {
        return Activity::distinct()->pluck('log_name')->filter()->toArray();
    }

    /**
     * Get unique causer types for filtering
     */
    public function getCauserTypesProperty()
    {
        return Activity::distinct()->pluck('causer_type')
            ->map(function($type) {
                // Extract class name from namespace
                $parts = explode('\\', $type);
                return end($parts);
            })
            ->filter()
            ->toArray();
    }

    public function getDetailedDescription($log)
    {
        $subjectType = $log->subject_type ? class_basename($log->subject_type) : null;
        $action = $this->getEventName($log->description);
        $properties = $log->properties;
        
        // If no subject type, return base description with enhancements
        if (!$subjectType) {
            if (str_contains($log->description, 'logged in')) {
                $ipAddress = $properties['ip_address'] ?? 'unknown IP';
                return "User logged in from {$ipAddress}";
            }
            
            if (str_contains($log->description, 'logged out')) {
                $ipAddress = $properties['ip_address'] ?? 'unknown IP';
                return "User logged out from {$ipAddress}";
            }
            
            return $log->description;
        }
        
        // Get entity name if available
        $entityName = $this->getEntityName($subjectType, $log->subject_id, $properties);
        
        // Build description based on subject type and action
        switch (strtolower($subjectType)) {
            case 'dish':
                if ($action === 'Created') {
                    return "Added new dish \"{$entityName}\" to the menu";
                } elseif ($action === 'Updated') {
                    return "Updated dish \"{$entityName}\"" . $this->getUpdateSummary($properties);
                } elseif ($action === 'Deleted') {
                    return "Removed dish \"{$entityName}\" from the menu";
                }
                break;
                
            case 'menu':
                if ($action === 'Created') {
                    return "Created new menu \"{$entityName}\"";
                } elseif ($action === 'Updated') {
                    return "Updated menu \"{$entityName}\"" . $this->getUpdateSummary($properties);
                } elseif ($action === 'Deleted') {
                    return "Deleted menu \"{$entityName}\"";
                }
                break;
                
            case 'employee':
                if ($action === 'Created') {
                    $role = $this->getEmployeeRole($properties);
                    return "Created new {$role} account for \"{$entityName}\"";
                } elseif ($action === 'Updated') {
                    return "Updated employee \"{$entityName}\"" . $this->getUpdateSummary($properties);
                } elseif ($action === 'Deleted') {
                    return "Deactivated employee account \"{$entityName}\"";
                }
                break;
                
            case 'order':
                if ($action === 'Created') {
                    $orderType = $properties['attributes']['type_order'] ?? 'unknown type';
                    $amount = isset($properties['attributes']['total_price']) ? 
                        '₱' . number_format($properties['attributes']['total_price'], 2) : '';
                    return "New {$orderType} order #{$log->subject_id} created {$amount}";
                } elseif ($action === 'Updated') {
                    $status = $this->getOrderStatus($properties);
                    return "Order #{$log->subject_id} status changed to {$status}";
                }
                break;
                
            case 'reservation':
                if ($action === 'Created') {
                    $people = $properties['attributes']['reservation_people'] ?? '';
                    $date = isset($properties['attributes']['reservation_date']) ? 
                        Carbon::parse($properties['attributes']['reservation_date'])->format('M d, Y') : '';
                    return "New reservation #{$log->subject_id} created for {$people} people on {$date}";
                } elseif ($action === 'Updated') {
                    $status = $this->getReservationStatus($properties);
                    return "Reservation #{$log->subject_id} status changed to {$status}";
                }
                break;
        }
        
        // Default enhanced description if no specific format matches
        return ucfirst($action) . " " . $subjectType . " #" . $log->subject_id . 
               ($entityName ? " ({$entityName})" : "");
    }
    
    /**
     * Get entity name from properties
     */
    private function getEntityName($subjectType, $subjectId, $properties)
    {
        if (!$properties || !isset($properties['attributes'])) {
            return "#{$subjectId}";
        }
        
        $attributes = $properties['attributes'];
        
        switch (strtolower($subjectType)) {
            case 'dish':
                return $attributes['dishes_name'] ?? "#{$subjectId}";
                
            case 'menu':
                return $attributes['menu_name'] ?? "#{$subjectId}";
                
            case 'employee':
                return $attributes['name'] ?? $attributes['employee_name'] ?? "#{$subjectId}";
                
            case 'customer':
                return $attributes['customer_name'] ?? "#{$subjectId}";
                
            default:
                return "#{$subjectId}";
        }
    }
    
    /**
     * Get a summary of what was updated
     */
    private function getUpdateSummary($properties)
    {
        if (!$properties || !isset($properties['old'])) {
            return "";
        }
        
        $changedFields = array_keys($properties['old']);
        
        // Filter out timestamps and other common non-descriptive fields
        $changedFields = array_filter($changedFields, function($field) {
            $nonDescriptiveFields = ['updated_at', 'created_at', 'id', 'remember_token'];
            return !in_array($field, $nonDescriptiveFields);
        });
        
        if (count($changedFields) === 0) {
            return "";
        }
        
        if (count($changedFields) === 1) {
            $field = $this->formatFieldName($changedFields[0]);
            return " - changed {$field}";
        }
        
        return " - changed " . count($changedFields) . " fields";
    }
    
    /**
     * Get the employee role from properties
     */
    private function getEmployeeRole($properties)
    {
        if (!$properties || !isset($properties['attributes'])) {
            return "employee";
        }
        
        // This is a simplification - you might need to adapt based on how roles are stored
        $attributes = $properties['attributes'];
        
        if (isset($attributes['role'])) {
            return $attributes['role'];
        }
        
        return "employee";
    }
    
    /**
     * Get order status from properties
     */
    private function getOrderStatus($properties)
    {
        if (!$properties) {
            return "unknown";
        }
        
        if (isset($properties['attributes']['status_order'])) {
            return ucfirst($properties['attributes']['status_order']);
        }
        
        return "updated";
    }
    
    /**
     * Get reservation status from properties
     */
    private function getReservationStatus($properties)
    {
        if (!$properties) {
            return "unknown";
        }
        
        if (isset($properties['attributes']['reservation_status'])) {
            return ucfirst($properties['attributes']['reservation_status']);
        }
        
        return "updated";
    }
    
    /**
     * Format database field names for better readability
     */
    private function formatFieldName($field)
    {
        return ucfirst(str_replace('_', ' ', $field));
    }

    /**
     * View detailed log entry
     */
    public function viewLogDetails($logId)
    {
        $this->selectedLog = Activity::with(['causer', 'subject'])->find($logId);
        $this->isDetailModalOpen = true;
    }

    /**
     * Close detail modal
     */
    public function closeDetailModal()
    {
        $this->isDetailModalOpen = false;
        $this->selectedLog = null;
    }

    /**
     * Format properties for display
     */
    public function formatProperties($properties)
    {
        if (!$properties) {
            return [];
        }

        $formatted = [];
        
        // Handle attributes
        if (isset($properties['attributes'])) {
            $formatted['Current Values'] = $properties['attributes'];
        }
        
        // Handle old attributes
        if (isset($properties['old'])) {
            $formatted['Previous Values'] = $properties['old'];
        }
        
        return $formatted;
    }

    /**
     * Get log event name in readable format
     */
    public function getEventName($description)
    {
        $events = [
            'created' => 'Created',
            'updated' => 'Updated',
            'deleted' => 'Deleted',
            'restored' => 'Restored',
            'logged in' => 'Logged In',
            'logged out' => 'Logged Out',
        ];
        
        foreach ($events as $key => $value) {
            if (str_contains($description, $key)) {
                return $value;
            }
        }
        
        return 'Action';
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.activity-logs-view', [
            'activityLogs' => $this->activityLogs,
            'logTypes' => $this->logTypes,
            'causerTypes' => $this->causerTypes
        ]);
    }
}