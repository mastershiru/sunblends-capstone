<div>
<div class="fixed inset-0 bg-orange-900 backdrop-blur-sm z-50 flex items-center justify-center">
                <div class="bg-white rounded-lg shadow-xl w-[500px] max-w-[95%] relative">
                <button wire:click="closeModal" class="absolute right-4 top-4 text-gray-500 hover:text-gray-700">
                    <i class="uil uil-times text-xl"></i>
                </button>
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
                    <div class="flex items-center">
                        <input type="checkbox" id="remember-me" class="mr-2" wire:model="remember">
                        <label for="remember-me" class="text-sm">Remember me</label>
                    </div>
                    <div class="space-y-3">
                        <button type="submit" class="w-full bg-black text-white py-2 rounded-lg hover:bg-gray-800">Login</button>
                        
                        <a href="#" class="block text-center text-sm text-gray-600 hover:text-gray-800">Forgot password?</a>
                    </div>
                    </form>
                </div>
                </div>
            </div>
</div>
