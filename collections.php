<?php
require_once 'config.php';
require_once 'includes/api.php';
require_once 'includes/auth.php';
require_once 'includes/collections.php';

// Check if user is logged in
$isLoggedIn = Auth::isLoggedIn();
$userCollections = [];

if ($isLoggedIn) {
    $userCollections = Collections::getUserCollections();
}

// Page meta data
$pageTitle = 'Style Collections';
$pageDescription = 'Explore curated collections of interior decoration items organized by style and theme.';

include 'includes/header.php';
?>

<!-- Collections Page -->
<div class="min-h-screen bg-neutral pb-16">
    <!-- Hero Section -->
    <div class="bg-gradient-to-br from-primary/10 to-secondary/10 py-16">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-3xl md:text-5xl font-display font-bold mb-4">Style Collections</h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Discover curated collections of interior pieces organized by style, theme, and aesthetic. 
                Find inspiration for your next design project.
            </p>
            
            <?php if ($isLoggedIn): ?>
            <button type="button" onclick="showCreateCollectionModal()" class="mt-8 inline-flex items-center px-6 py-3 bg-primary hover:bg-primary/90 text-white rounded-xl font-medium transition">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mr-2">
                    <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Create New Collection
            </button>
            <?php else: ?>
            <p class="mt-8 text-sm text-gray-500">
                <a href="/login.php" class="text-primary hover:underline">Sign in</a> to create and save your own collections
            </p>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="container mx-auto px-4 py-12">
        <?php if ($isLoggedIn && !empty($userCollections)): ?>
        <!-- User's Collections -->
        <div class="mb-16">
            <h2 class="text-2xl font-display font-bold mb-6">My Collections</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($userCollections as $collection): ?>
                <div class="bg-white rounded-xl shadow-soft overflow-hidden hover-card">
                    <div class="aspect-video bg-neutral relative">
                        <?php if (!empty($collection['products'])): ?>
                        <!-- Show first 4 product images -->
                        <div class="grid grid-cols-2 gap-1 h-full p-1">
                            <?php 
                            $api = new MeiliSearchAPI();
                            $displayProducts = array_slice($collection['products'], 0, 4);
                            foreach ($displayProducts as $index => $productId):
                                $product = $api->getProduct($productId);
                                if ($product && !empty($product['product']['imageUrls'])):
                            ?>
                            <div class="bg-gray-200 rounded overflow-hidden">
                                <img src="<?php echo $product['product']['imageUrls'][0]; ?>" 
                                     alt="Collection item" 
                                     class="w-full h-full object-cover">
                            </div>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </div>
                        <?php else: ?>
                        <div class="flex items-center justify-center h-full text-gray-400">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19 11H13M13 11H7M13 11V5M13 11V17M9 3H4C3.44772 3 3 3.44772 3 4V9M15 3H20C20.5523 3 21 3.44772 21 4V9M15 21H20C20.5523 21 21 20.5523 21 20V15M9 21H4C3.44772 21 3 20.5523 3 20V15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="p-4">
                        <h3 class="font-bold text-lg mb-1"><?php echo htmlspecialchars($collection['name']); ?></h3>
                        <?php if (!empty($collection['description'])): ?>
                        <p class="text-sm text-gray-600 mb-3 line-clamp-2"><?php echo htmlspecialchars($collection['description']); ?></p>
                        <?php endif; ?>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">
                                <?php echo count($collection['products']); ?> items
                            </span>
                            <div class="flex space-x-2">
                                <a href="/collection.php?id=<?php echo $collection['id']; ?>" 
                                   class="text-primary hover:text-primary/80 text-sm font-medium">
                                    View
                                </a>
                                <button type="button" 
                                        onclick="editCollection('<?php echo $collection['id']; ?>')"
                                        class="text-gray-600 hover:text-primary text-sm font-medium">
                                    Edit
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Pre-made Collections -->
        <div>
            <h2 class="text-2xl font-display font-bold mb-6">Curated Collections</h2>
            
            <!-- Featured Collections Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Scandinavian Collection -->
                <a href="/search.php?q=scandinavian" class="group">
                    <div class="bg-white rounded-xl shadow-soft overflow-hidden hover-card">
                        <div class="aspect-video relative overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1586023492125-27b2c045efd7?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" 
                                 alt="Scandinavian style" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-dark/60 to-transparent"></div>
                            <div class="absolute bottom-4 left-4 text-white">
                                <h3 class="text-xl font-bold mb-1">Scandinavian Minimalism</h3>
                                <p class="text-sm text-white/90">Clean lines, natural materials</p>
                            </div>
                        </div>
                        
                        <div class="p-4">
                            <p class="text-sm text-gray-600 mb-3">
                                Embrace simplicity with our Scandinavian collection featuring light woods, 
                                neutral colors, and functional design.
                            </p>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">156 items</span>
                                <span class="text-primary group-hover:text-primary/80 transition">
                                    Explore →
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
                
                <!-- Mid-Century Modern Collection -->
                <a href="/search.php?q=mid+century+modern" class="group">
                    <div class="bg-white rounded-xl shadow-soft overflow-hidden hover-card">
                        <div class="aspect-video relative overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1556228453-efd6c1ff04f6?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" 
                                 alt="Mid-Century Modern style" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-dark/60 to-transparent"></div>
                            <div class="absolute bottom-4 left-4 text-white">
                                <h3 class="text-xl font-bold mb-1">Mid-Century Modern</h3>
                                <p class="text-sm text-white/90">Retro elegance meets functionality</p>
                            </div>
                        </div>
                        
                        <div class="p-4">
                            <p class="text-sm text-gray-600 mb-3">
                                Iconic designs from the 1950s and 60s with sleek lines, organic curves, 
                                and a mix of materials.
                            </p>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">203 items</span>
                                <span class="text-primary group-hover:text-primary/80 transition">
                                    Explore →
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
                
                <!-- Industrial Collection -->
                <a href="/search.php?q=industrial" class="group">
                    <div class="bg-white rounded-xl shadow-soft overflow-hidden hover-card">
                        <div class="aspect-video relative overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" 
                                 alt="Industrial style" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-dark/60 to-transparent"></div>
                            <div class="absolute bottom-4 left-4 text-white">
                                <h3 class="text-xl font-bold mb-1">Industrial Loft</h3>
                                <p class="text-sm text-white/90">Raw materials, urban edge</p>
                            </div>
                        </div>
                        
                        <div class="p-4">
                            <p class="text-sm text-gray-600 mb-3">
                                Exposed brick, metal fixtures, and reclaimed wood define this urban-inspired 
                                collection.
                            </p>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">127 items</span>
                                <span class="text-primary group-hover:text-primary/80 transition">
                                    Explore →
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
                
                <!-- Bohemian Collection -->
                <a href="/search.php?q=bohemian" class="group">
                    <div class="bg-white rounded-xl shadow-soft overflow-hidden hover-card">
                        <div class="aspect-video relative overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" 
                                 alt="Bohemian style" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-dark/60 to-transparent"></div>
                            <div class="absolute bottom-4 left-4 text-white">
                                <h3 class="text-xl font-bold mb-1">Bohemian Eclectic</h3>
                                <p class="text-sm text-white/90">Free-spirited and colorful</p>
                            </div>
                        </div>
                        
                        <div class="p-4">
                            <p class="text-sm text-gray-600 mb-3">
                                Mix patterns, textures, and colors with our boho collection featuring 
                                global-inspired pieces.
                            </p>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">189 items</span>
                                <span class="text-primary group-hover:text-primary/80 transition">
                                    Explore →
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
                
                <!-- Minimalist Collection -->
                <a href="/search.php?q=minimalist" class="group">
                    <div class="bg-white rounded-xl shadow-soft overflow-hidden hover-card">
                        <div class="aspect-video relative overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1588854337221-4cf9fa96059c?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" 
                                 alt="Minimalist style" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-dark/60 to-transparent"></div>
                            <div class="absolute bottom-4 left-4 text-white">
                                <h3 class="text-xl font-bold mb-1">Modern Minimalist</h3>
                                <p class="text-sm text-white/90">Less is more</p>
                            </div>
                        </div>
                        
                        <div class="p-4">
                            <p class="text-sm text-gray-600 mb-3">
                                Essential pieces that combine form and function with a focus on simplicity 
                                and quality.
                            </p>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">98 items</span>
                                <span class="text-primary group-hover:text-primary/80 transition">
                                    Explore →
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
                
                <!-- Rustic Collection -->
                <a href="/search.php?q=rustic" class="group">
                    <div class="bg-white rounded-xl shadow-soft overflow-hidden hover-card">
                        <div class="aspect-video relative overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1600210492493-0946911123ea?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" 
                                 alt="Rustic style" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-dark/60 to-transparent"></div>
                            <div class="absolute bottom-4 left-4 text-white">
                                <h3 class="text-xl font-bold mb-1">Rustic Farmhouse</h3>
                                <p class="text-sm text-white/90">Warm and welcoming</p>
                            </div>
                        </div>
                        
                        <div class="p-4">
                            <p class="text-sm text-gray-600 mb-3">
                                Bring countryside charm home with weathered woods, vintage accents, 
                                and cozy textiles.
                            </p>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">145 items</span>
                                <span class="text-primary group-hover:text-primary/80 transition">
                                    Explore →
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        
        <!-- Seasonal Collections -->
        <div class="mt-16">
            <h2 class="text-2xl font-display font-bold mb-6">Seasonal Collections</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Summer Collection -->
                <div class="bg-gradient-to-br from-yellow-100 to-orange-100 rounded-xl p-8">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-2xl font-bold mb-2">Summer Refresh</h3>
                            <p class="text-gray-700 mb-4">
                                Light, airy pieces perfect for warm weather living. Think natural materials, 
                                bright accents, and outdoor-friendly designs.
                            </p>
                            <a href="/search.php?q=summer+outdoor" class="inline-flex items-center text-primary hover:text-primary/80 font-medium">
                                Shop Summer Collection
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="ml-2">
                                    <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        </div>
                        <div class="text-6xl">☀️</div>
                    </div>
                </div>
                
                <!-- Winter Collection -->
                <div class="bg-gradient-to-br from-blue-100 to-purple-100 rounded-xl p-8">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-2xl font-bold mb-2">Winter Cozy</h3>
                            <p class="text-gray-700 mb-4">
                                Warm up your space with plush textiles, rich colors, and comfortable 
                                furniture perfect for cold nights.
                            </p>
                            <a href="/search.php?q=cozy+winter" class="inline-flex items-center text-primary hover:text-primary/80 font-medium">
                                Shop Winter Collection
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="ml-2">
                                    <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        </div>
                        <div class="text-6xl">❄️</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Collection Modal -->
<?php if ($isLoggedIn): ?>
<div id="createCollectionModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center invisible opacity-0 transition-all duration-300">
    <div class="bg-white rounded-2xl w-full max-w-md p-6 transform scale-95 transition-transform duration-300" id="createCollectionContent">
        <h3 class="text-xl font-bold mb-4">Create New Collection</h3>
        
        <form id="createCollectionForm" onsubmit="handleCreateCollection(event)">
            <div class="mb-4">
                <label for="collectionName" class="block text-sm font-medium text-gray-700 mb-1">Collection Name</label>
                <input type="text" id="collectionName" name="name" required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                       placeholder="e.g., My Living Room Ideas">
            </div>
            
            <div class="mb-6">
                <label for="collectionDescription" class="block text-sm font-medium text-gray-700 mb-1">Description (optional)</label>
                <textarea id="collectionDescription" name="description" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                          placeholder="Describe your collection..."></textarea>
            </div>
            
            <div class="flex space-x-3">
                <button type="submit" class="flex-1 py-2 bg-primary hover:bg-primary/90 text-white rounded-lg font-medium transition">
                    Create Collection
                </button>
                <button type="button" onclick="closeCreateCollectionModal()" class="flex-1 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-medium transition">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<script>
<?php if ($isLoggedIn): ?>
function showCreateCollectionModal() {
    const modal = document.getElementById('createCollectionModal');
    const content = document.getElementById('createCollectionContent');
    
    modal.classList.remove('invisible', 'opacity-0');
    content.classList.remove('scale-95');
    content.classList.add('scale-100');
    document.body.classList.add('overflow-hidden');
}

function closeCreateCollectionModal() {
    const modal = document.getElementById('createCollectionModal');
    const content = document.getElementById('createCollectionContent');
    
    modal.classList.add('opacity-0');
    content.classList.remove('scale-100');
    content.classList.add('scale-95');
    
    setTimeout(() => {
        modal.classList.add('invisible');
        document.body.classList.remove('overflow-hidden');
    }, 300);
}

function handleCreateCollection(event) {
    event.preventDefault();
    
    const name = document.getElementById('collectionName').value;
    const description = document.getElementById('collectionDescription').value;
    
    // Make AJAX request to create collection
    fetch('/api/collection.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'create',
            name: name,
            description: description
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = `/collection.php?id=${data.collectionId}`;
        } else {
            alert(data.message || 'Failed to create collection');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to create collection');
    });
}

function editCollection(collectionId) {
    // In a real app, this would open an edit modal
    window.location.href = `/collection.php?id=${collectionId}&edit=true`;
}
<?php endif; ?>
</script>

<?php include 'includes/footer.php'; ?>