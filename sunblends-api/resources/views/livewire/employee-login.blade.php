<div>
<div class="fixed inset-0 bg-orange-900 backdrop-blur-sm z-50 flex items-center justify-center">
                <div class="bg-white rounded-lg shadow-xl w-[500px] max-w-[95%] relative">
                
                <div class="p-6">
                    <h2 class="text-2xl font-medium mb-6 text-center">Employee Log in</h2>
                    <form wire:submit.prevent="login" class="space-y-4">
                    <div class="flex flex-col ">
                        <label for="login-email" class="text-sm mb-1">Email</label>
                        <input type="email" id="login-email" wire:model="email" 
                        class="rounded-lg border-gray-200 shadow-inner p-2 " required>
                        @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <label for="login-password" class="text-sm mb-1">Password</label>
                        <input type="password" id="login-password" wire:model="password" 
                        class="rounded-lg border-gray-200 shadow-inner p-2" required>
                        @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="space-y-3">
                        <button type="submit" class="w-full bg-black text-white py-2 rounded-lg hover:bg-gray-800">Login</button>
                        
                        
                    </div>
                    </form>
                </div>
                </div>
            </div>
</div>
