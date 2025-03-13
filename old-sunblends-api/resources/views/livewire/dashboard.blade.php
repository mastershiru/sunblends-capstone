<div>
<div class="bg-white p-6 rounded-lg shadow-lg relative ">
        <div class="flex justify-between items-center mb-6">
            <div>
            <h2 class="text-2xl font-bold text-gray-800">
                    Welcome, {{ Auth::guard('employee')->user()->name }}!
            </h2> 
            </div>
              
            @if(Auth::guard('employee')->user() && Auth::guard('employee')->user()->hasRole(['admin', 'manager']))  
            <div>  
            <a href="{{ url('/projects?open_modal=1') }}" 
                class="inline-block px-6 py-2 h-14 w-48 text-white font-semibold rounded-lg bg-gray-600 hover:bg-blue-600 transition duration-300 ease-in-out text-center leading-[2.5rem]">
                <i class="fas fa-plus mr-2"></i> Add Projects
                </a>
            </div>
            @endif
        </div>
        
        <p class="text-gray-600 mb-6 leading-relaxed">
            You are logged in as: {{ Auth::guard('employee')->user()->hasRole('admin') ? 'Admin' : (Auth::guard('employee')->user()->hasRole('manager') ? 'Manager' : 'Employee') }}.
            <span class="font-semibold text-gray-800">
                
            </span>
        </p>
        
        <p class="text-gray-600 mb-6 mt-3 leading-relaxed">
            This is the home page 
        </p>
      
        <div class="deadline-modal">
           
        </div>

    </div>      
</div>
