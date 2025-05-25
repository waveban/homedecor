<?php
require_once 'config.php';
require_once 'includes/auth.php';
require_once 'includes/wishlist.php';
require_once 'includes/collections.php';
require_once 'includes/moodboard.php';

// Check if user is logged in
if (!Auth::isLoggedIn()) {
    header('Location: /login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$user = Auth::getCurrentUser();
$wishlistItems = Wishlist::getItems();
$collections = Collections::getUserCollections();
$moodboards = MoodBoard::getUserBoards();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $name = $_POST['name'] ?? '';
    
    Auth::updateUser($user['email'], ['name' => $name]);
    $success = 'Profile updated successfully!';
    $user = Auth::getCurrentUser(); // Refresh user data
}

// Page meta data
$pageTitle = 'My Account';
$pageDescription = 'Manage your Interior Mosaic account, view saved items, and track your design projects.';

include 'includes/header.php';
?>

<!-- Account Page -->
<div class="min-h-screen bg-neutral pb-16">
    <!-- Account Header -->
    <div class="bg-white border-b border-gray-200">
        <div class="container mx-auto px-4 py-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl md:text-3xl font-display font-bold mb-2">My Account</h1>
                    <p class="text-gray-600">Welcome back, <?php echo htmlspecialchars($user['name']); ?>!</p>
                </div>
                <a href="/logout.php" class="px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                    Sign Out
                </a>
            </div>
        </div>
    </div>
    
    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar Navigation -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-soft p-4">
                    <nav class="space-y-1">
                        <button type="button" onclick="showSection('profile')" class="w-full text-left px-4 py-3 rounded-lg hover:bg-neutral transition flex items-center nav-item active" data-section="profile">
                            <i class="fas fa-user w-5 mr-3"></i>
                            Profile
                        </button>
                        <button type="button" onclick="showSection('wishlist')" class="w-full text-left px-4 py-3 rounded-lg hover:bg-neutral transition flex items-center nav-item" data-section="wishlist">
                            <i class="fas fa-heart w-5 mr-3"></i>
                            Wishlist
                            <?php if (count($wishlistItems) > 0): ?>
                            <span class="ml-auto bg-primary text-white text-xs rounded-full px-2 py-0.5"><?php echo count($wishlistItems); ?></span>
                            <?php endif; ?>
                        </button>
                        <button type="button" onclick="showSection('collections')" class="w-full text-left px-4 py-3 rounded-lg hover:bg-neutral transition flex items-center nav-item" data-section="collections">
                            <i class="fas fa-layer-group w-5 mr-3"></i>
                            Collections
                            <?php if (count($collections) > 0): ?>
                            <span class="ml-auto bg-gray-200 text-gray-700 text-xs rounded-full px-2 py-0.5"><?php echo count($collections); ?></span>
                            <?php endif; ?>
                        </button>
                        <button type="button" onclick="showSection('moodboards')" class="w-full text-left px-4 py-3 rounded-lg hover:bg-neutral transition flex items-center nav-item" data-section="moodboards">
                            <i class="fas fa-palette w-5 mr-3"></i>
                            Mood Boards
                            <?php if (count($moodboards) > 0): ?>
                            <span class="ml-auto bg-gray-200 text-gray-700 text-xs rounded-full px-2 py-0.5"><?php echo count($moodboards); ?></span>
                            <?php endif; ?>
                        </button>
                        <button type="button" onclick="showSection('settings')" class="w-full text-left px-4 py-3 rounded-lg hover:bg-neutral transition flex items-center nav-item" data-section="settings">
                            <i class="fas fa-cog w-5 mr-3"></i>
                            Settings
                        </button>
                    </nav>
                </div>
            </div>
            
            <!-- Main Content Area -->
            <div class="lg:col-span-3">
                <!-- Profile Section -->
                <div id="profile-section" class="content-section">
                    <div class="bg-white rounded-xl shadow-soft p-6">
                        <h2 class="text-xl font-bold mb-6">Profile Information</h2>
                        
                        <?php if (isset($success)): ?>
                        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-600 text-sm">
                            <?php echo htmlspecialchars($success); ?>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="update_profile">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                                </div>
                                
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                    <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled 
                                           class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-500">
                                </div>
                            </div>
                            
                            <div class="mt-6">
                                <button type="submit" class="px-6 py-2 bg-primary hover:bg-primary/90 text-white rounded-lg font-medium transition">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                        
                        <div class="mt-8 pt-8 border-t border-gray-200">
                            <h3 class="font-medium mb-4">Account Statistics</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="bg-neutral rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-primary"><?php echo count($wishlistItems); ?></div>
                                    <div class="text-sm text-gray-600">Wishlist Items</div>
                                </div>
                                <div class="bg-neutral rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-primary"><?php echo count($collections); ?></div>
                                    <div class="text-sm text-gray-600">Collections</div>
                                </div>
                                <div class="bg-neutral rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-primary"><?php echo count($moodboards); ?></div>
                                    <div class="text-sm text-gray-600">Mood Boards</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Wishlist Section -->
                <div id="wishlist-section" class="content-section hidden">
                    <div class="bg-white rounded-xl shadow-soft p-6">
                        <h2 class="text-xl font-bold mb-6">My Wishlist</h2>
                        
                        <?php if (empty($wishlistItems)): ?>
                        <div class="text-center py-12">
                            <i class="far fa-heart text-6xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 mb-4">Your wishlist is empty</p>
                            <a href="/search.php" class="inline-flex items-center px-4 py-2 bg-primary hover:bg-primary/90 text-white rounded-lg transition">
                                Start Shopping
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="ml-2">
                                    <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        </div>
                        <?php else: ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <?php 
                            require_once 'includes/api.php';
                            $api = new MeiliSearchAPI();
                            foreach ($wishlistItems as $productId):
                                $productData = $api->getProduct($productId);
                                if ($productData && !empty($productData)):
                                    $product = $productData['product'];
                            ?>
                            <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition">
                                <div class="aspect-square bg-gray-100">
                                    <img src="<?php echo !empty($product['imageUrls']) ? $product['imageUrls'][0] : 'https://placehold.co/400x400/f3f4f6/1f2937?text=No+Image'; ?>" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                         class="w-full h-full object-cover">
                                </div>
                                <div class="p-4">
                                    <h3 class="font-medium text-sm mb-2 line-clamp-2"><?php echo htmlspecialchars($product['name']); ?></h3>
                                    <div class="flex items-center justify-between">
                                        <span class="text-lg font-bold text-primary">
                                            <?php echo formatPrice($product['priceDetails']['lowest'] ?? 0); ?>
                                        </span>
                                        <button type="button" onclick="removeFromWishlist('<?php echo $productId; ?>')" class="text-red-500 hover:text-red-700">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Collections Section -->
                <div id="collections-section" class="content-section hidden">
                    <div class="bg-white rounded-xl shadow-soft p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-bold">My Collections</h2>
                            <a href="/collections.php" class="text-primary hover:text-primary/80 text-sm font-medium">
                                Create New
                            </a>
                        </div>
                        
                        <?php if (empty($collections)): ?>
                        <div class="text-center py-12">
                            <i class="fas fa-layer-group text-6xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 mb-4">You haven't created any collections yet</p>
                            <a href="/collections.php" class="inline-flex items-center px-4 py-2 bg-primary hover:bg-primary/90 text-white rounded-lg transition">
                                Create Collection
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="ml-2">
                                    <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        </div>
                        <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($collections as $collection): ?>
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="font-medium"><?php echo htmlspecialchars($collection['name']); ?></h3>
                                        <p class="text-sm text-gray-500 mt-1"><?php echo count($collection['products']); ?> items</p>
                                    </div>
                                    <a href="/collection.php?id=<?php echo $collection['id']; ?>" class="text-primary hover:text-primary/80 text-sm font-medium">
                                        View →
                                    </a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Mood Boards Section -->
                <div id="moodboards-section" class="content-section hidden">
                    <div class="bg-white rounded-xl shadow-soft p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-bold">My Mood Boards</h2>
                            <a href="/moodboard.php" class="text-primary hover:text-primary/80 text-sm font-medium">
                                Create New
                            </a>
                        </div>
                        
                        <?php if (empty($moodboards)): ?>
                        <div class="text-center py-12">
                            <i class="fas fa-palette text-6xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 mb-4">You haven't created any mood boards yet</p>
                            <a href="/moodboard.php" class="inline-flex items-center px-4 py-2 bg-primary hover:bg-primary/90 text-white rounded-lg transition">
                                Create Mood Board
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="ml-2">
                                    <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        </div>
                        <?php else: ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <?php foreach ($moodboards as $board): ?>
                            <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition">
                                <div class="aspect-video bg-gray-100 relative">
                                    <!-- Preview of mood board items would go here -->
                                    <div class="absolute inset-0 flex items-center justify-center text-gray-400">
                                        <i class="fas fa-palette text-4xl"></i>
                                    </div>
                                </div>
                                <div class="p-4">
                                    <h3 class="font-medium"><?php echo htmlspecialchars($board['title']); ?></h3>
                                    <div class="flex items-center justify-between mt-2">
                                        <span class="text-sm text-gray-500">
                                            <?php echo count($board['items']); ?> items
                                        </span>
                                        <a href="/moodboard.php?id=<?php echo $board['id']; ?>" class="text-primary hover:text-primary/80 text-sm font-medium">
                                            Edit →
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Settings Section -->
                <div id="settings-section" class="content-section hidden">
                    <div class="bg-white rounded-xl shadow-soft p-6">
                        <h2 class="text-xl font-bold mb-6">Account Settings</h2>
                        
                        <div class="space-y-6">
                            <!-- Email Preferences -->
                            <div>
                                <h3 class="font-medium mb-4">Email Preferences</h3>
                                <div class="space-y-3">
                                    <label class="flex items-center">
                                        <input type="checkbox" checked class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                        <span class="ml-2 text-sm">Newsletter & product updates</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" checked class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                        <span class="ml-2 text-sm">Price drop alerts for wishlist items</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                        <span class="ml-2 text-sm">Design tips and inspiration</span>
                                    </label>
                                </div>
                            </div>
                            
                            <!-- Privacy Settings -->
                            <div>
                                <h3 class="font-medium mb-4">Privacy Settings</h3>
                                <div class="space-y-3">
                                    <label class="flex items-center">
                                        <input type="checkbox" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                        <span class="ml-2 text-sm">Make my collections public</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                        <span class="ml-2 text-sm">Allow others to see my wishlist</span>
                                    </label>
                                </div>
                            </div>
                            
                            <!-- Danger Zone -->
                            <div class="pt-6 border-t border-gray-200">
                                <h3 class="font-medium mb-4 text-red-600">Danger Zone</h3>
                                <button type="button" class="px-4 py-2 border border-red-600 text-red-600 hover:bg-red-50 rounded-lg transition">
                                    Delete Account
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showSection(section) {
    // Hide all sections
    document.querySelectorAll('.content-section').forEach(el => {
        el.classList.add('hidden');
    });
    
    // Remove active state from all nav items
    document.querySelectorAll('.nav-item').forEach(el => {
        el.classList.remove('active', 'bg-neutral');
    });
    
    // Show selected section
    document.getElementById(section + '-section').classList.remove('hidden');
    
    // Add active state to selected nav item
    document.querySelector(`[data-section="${section}"]`).classList.add('active', 'bg-neutral');
}

function removeFromWishlist(productId) {
    if (confirm('Remove this item from your wishlist?')) {
        // Make AJAX request to remove item
        fetch('/api/wishlist.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'remove',
                productId: productId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}
</script>

<?php include 'includes/footer.php'; ?>