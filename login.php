<?php
require_once 'config.php';
require_once 'includes/auth.php';

// Redirect if already logged in
if (Auth::isLoggedIn()) {
    header('Location: /account.php');
    exit;
}

$error = '';
$success = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $result = Auth::login($email, $password);
    
    if ($result['success']) {
        // Redirect to the page they were trying to access, or home
        $redirect = $_GET['redirect'] ?? '/';
        header('Location: ' . $redirect);
        exit;
    } else {
        $error = $result['message'];
    }
}

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $name = $_POST['name'] ?? '';
    
    $result = Auth::register($email, $password, $name);
    
    if ($result['success']) {
        $success = 'Registration successful! You can now login.';
    } else {
        $error = $result['message'];
    }
}

// Page meta data
$pageTitle = 'Sign In';
$pageDescription = 'Sign in to your Interior Mosaic account to save collections, create mood boards, and more.';

include 'includes/header.php';
?>

<!-- Login/Register Page -->
<div class="min-h-screen bg-neutral flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full">
        <!-- Logo -->
        <div class="text-center mb-8">
            <a href="/" class="inline-flex items-center space-x-2">
                <div class="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2L5 7.5M12 2L19 7.5M12 2V5M5 7.5V16.5L12 22L19 16.5V7.5L12 12L5 7.5Z" stroke="<?php echo PRIMARY_COLOR; ?>" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <span class="font-display font-bold text-2xl">Interior<span class="text-primary">Mosaic</span></span>
            </a>
        </div>
        
        <!-- Tab Navigation -->
        <div class="bg-white rounded-2xl shadow-soft overflow-hidden">
            <div class="flex border-b border-gray-200">
                <button type="button" class="flex-1 py-4 text-center font-medium text-primary border-b-2 border-primary" id="loginTab" onclick="showLoginForm()">
                    Sign In
                </button>
                <button type="button" class="flex-1 py-4 text-center font-medium text-gray-600 hover:text-primary transition" id="registerTab" onclick="showRegisterForm()">
                    Create Account
                </button>
            </div>
            
            <div class="p-8">
                <?php if ($error): ?>
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-600 text-sm">
                    <?php echo htmlspecialchars($error); ?>
                </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-600 text-sm">
                    <?php echo htmlspecialchars($success); ?>
                </div>
                <?php endif; ?>
                
                <!-- Login Form -->
                <form id="loginForm" method="POST" action="">
                    <input type="hidden" name="action" value="login">
                    
                    <div class="mb-6">
                        <label for="loginEmail" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input type="email" id="loginEmail" name="email" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                               placeholder="you@example.com">
                    </div>
                    
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-2">
                            <label for="loginPassword" class="block text-sm font-medium text-gray-700">Password</label>
                            <a href="#" class="text-sm text-primary hover:underline">Forgot password?</a>
                        </div>
                        <input type="password" id="loginPassword" name="password" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                               placeholder="••••••••">
                    </div>
                    
                    <div class="mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-600">Remember me</span>
                        </label>
                    </div>
                    
                    <button type="submit" class="w-full py-3 px-6 bg-primary hover:bg-primary/90 text-white rounded-xl font-medium transition">
                        Sign In
                    </button>
                </form>
                
                <!-- Register Form -->
                <form id="registerForm" method="POST" action="" class="hidden">
                    <input type="hidden" name="action" value="register">
                    
                    <div class="mb-6">
                        <label for="registerName" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                        <input type="text" id="registerName" name="name" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                               placeholder="John Doe">
                    </div>
                    
                    <div class="mb-6">
                        <label for="registerEmail" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input type="email" id="registerEmail" name="email" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                               placeholder="you@example.com">
                    </div>
                    
                    <div class="mb-6">
                        <label for="registerPassword" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input type="password" id="registerPassword" name="password" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                               placeholder="••••••••">
                        <p class="mt-2 text-xs text-gray-500">Must be at least 8 characters</p>
                    </div>
                    
                    <div class="mb-6">
                        <label class="flex items-start">
                            <input type="checkbox" name="terms" required class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded mt-0.5">
                            <span class="ml-2 text-sm text-gray-600">
                                I agree to the <a href="#" class="text-primary hover:underline">Terms of Service</a> 
                                and <a href="#" class="text-primary hover:underline">Privacy Policy</a>
                            </span>
                        </label>
                    </div>
                    
                    <button type="submit" class="w-full py-3 px-6 bg-primary hover:bg-primary/90 text-white rounded-xl font-medium transition">
                        Create Account
                    </button>
                </form>
                
                <!-- Social Login -->
                <div class="mt-8">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-3 bg-white text-gray-500">Or continue with</span>
                        </div>
                    </div>
                    
                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <button type="button" class="w-full py-3 px-4 border border-gray-300 rounded-xl hover:bg-gray-50 transition flex items-center justify-center">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mr-2">
                                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                            </svg>
                            Google
                        </button>
                        
                        <button type="button" class="w-full py-3 px-4 border border-gray-300 rounded-xl hover:bg-gray-50 transition flex items-center justify-center">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mr-2">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" fill="#1877F2"/>
                            </svg>
                            Facebook
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Guest Checkout Option -->
        <div class="mt-6 text-center text-sm text-gray-600">
            <p>Just browsing? You can still save items to your wishlist as a guest.</p>
        </div>
    </div>
</div>

<script>
function showLoginForm() {
    document.getElementById('loginForm').classList.remove('hidden');
    document.getElementById('registerForm').classList.add('hidden');
    document.getElementById('loginTab').classList.add('text-primary', 'border-b-2', 'border-primary');
    document.getElementById('loginTab').classList.remove('text-gray-600');
    document.getElementById('registerTab').classList.add('text-gray-600');
    document.getElementById('registerTab').classList.remove('text-primary', 'border-b-2', 'border-primary');
}

function showRegisterForm() {
    document.getElementById('registerForm').classList.remove('hidden');
    document.getElementById('loginForm').classList.add('hidden');
    document.getElementById('registerTab').classList.add('text-primary', 'border-b-2', 'border-primary');
    document.getElementById('registerTab').classList.remove('text-gray-600');
    document.getElementById('loginTab').classList.add('text-gray-600');
    document.getElementById('loginTab').classList.remove('text-primary', 'border-b-2', 'border-primary');
}
</script>

<?php include 'includes/footer.php'; ?>