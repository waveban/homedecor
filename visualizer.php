<?php
// Set current page for navigation highlighting
$currentPage = 'visualizer';

// SEO information
$pageTitle = 'Room Visualizer | See Products in Your Space';
$pageDescription = 'Try our Room Visualizer to see how furniture and decor will look in your space before purchasing. Upload a photo of your room and place products to preview the perfect fit.';

// Include API client
require_once 'includes/api-client.php';

// Fetch some sample products for the sidebar
$sampleProducts = fetchFeaturedProducts(12);

// Include header
include 'includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <!-- Visualizer Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Room Visualizer</h1>
            <p class="text-gray-600 max-w-2xl">
                See how furniture and decor will look in your space before committing to a purchase. Upload a photo of your room and place products to preview the perfect fit.
            </p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button id="upload-room" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Upload Room Photo
            </button>
            <button id="capture-room" class="px-4 py-2 bg-white text-indigo-600 border border-indigo-600 rounded-lg hover:bg-indigo-50 transition-colors flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Use Camera
            </button>
        </div>
    </div>
    
    <!-- Visualizer Interface -->
    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Sidebar -->
        <div class="lg:w-1/4 bg-white rounded-2xl shadow-lg p-4 h-[700px] flex flex-col">
            <!-- Search Box -->
            <div class="mb-4">
                <div class="relative">
                    <input type="text" id="visualizer-search" placeholder="Search for items" class="w-full px-4 py-2 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>
            
            <!-- Category Filters -->
            <div class="flex overflow-x-auto space-x-2 py-2 mb-4">
                <button class="px-3 py-1 bg-indigo-600 text-white text-sm rounded-full whitespace-nowrap">All</button>
                <button class="px-3 py-1 bg-white text-gray-700 text-sm rounded-full whitespace-nowrap border border-gray-300 hover:bg-gray-100 transition-colors">Sofas</button>
                <button class="px-3 py-1 bg-white text-gray-700 text-sm rounded-full whitespace-nowrap border border-gray-300 hover:bg-gray-100 transition-colors">Chairs</button>
                <button class="px-3 py-1 bg-white text-gray-700 text-sm rounded-full whitespace-nowrap border border-gray-300 hover:bg-gray-100 transition-colors">Tables</button>
                <button class="px-3 py-1 bg-white text-gray-700 text-sm rounded-full whitespace-nowrap border border-gray-300 hover:bg-gray-100 transition-colors">Lighting</button>
                <button class="px-3 py-1 bg-white text-gray-700 text-sm rounded-full whitespace-nowrap border border-gray-300 hover:bg-gray-100 transition-colors">Decor</button>
            </div>
            
            <!-- Items Grid -->
            <div class="flex-grow overflow-y-auto">
                <div class="grid grid-cols-2 gap-3">
                    <?php foreach ($sampleProducts as $product): ?>
                    <div class="visualizer-item cursor-move bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow" data-id="<?php echo isset($product['id']) ? $product['id'] : ''; ?>">
                        <img src="<?php echo !empty($product['imageUrls']) ? $product['imageUrls'][0] : '/assets/images/placeholder.jpg'; ?>" alt="<?php echo isset($product['name']) ? htmlspecialchars($product['name']) : 'Product'; ?>" class="w-full h-24 object-contain p-2">
                        <div class="p-2 bg-gray-50">
                            <div class="text-xs font-medium text-gray-900 truncate"><?php echo isset($product['name']) ? htmlspecialchars($product['name']) : htmlspecialchars($product['product']['name'] ?? 'Product Name'); ?></div>
                            <?php if (isset($product['priceDetails']) && isset($product['priceDetails']['lowest'])): ?>
                            <div class="text-xs text-gray-600">$<?php echo number_format($product['priceDetails']['lowest'], 2); ?></div>
                            <?php elseif (isset($product['product']['priceDetails']) && isset($product['product']['priceDetails']['lowest'])): ?>
                            <div class="text-xs text-gray-600">$<?php echo number_format($product['product']['priceDetails']['lowest'], 2); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <!-- Visualizer Canvas -->
        <div class="lg:w-3/4 bg-white rounded-2xl shadow-lg h-[700px] relative overflow-hidden">
            <div class="absolute inset-0 flex items-center justify-center bg-gray-100 z-0">
                <!-- Sample room image -->
                <img id="room-image" src="/assets/images/living-room.jpg" alt="Living room" class="w-full h-full object-contain">
                
                <!-- Empty state when no image is uploaded -->
                <div id="empty-state" class="text-center hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Upload a photo of your room</h3>
                    <p class="text-gray-600 max-w-md mx-auto mb-6">
                        Take a photo or upload an image of your space to visualize how products will look in your home.
                    </p>
                    <div class="flex justify-center space-x-3">
                        <button id="empty-state-upload" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            Upload Photo
                        </button>
                        <button id="empty-state-camera" class="px-4 py-2 bg-white text-indigo-600 border border-indigo-600 rounded-lg hover:bg-indigo-50 transition-colors">
                            Use Camera
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Placed Items Container -->
            <div id="visualizer-items" class="absolute inset-0 z-10">
                <!-- Sample placed item -->
                <div class="placed-item absolute" style="top: 55%; left: 50%; transform: translate(-50%, -50%); z-index: 20;">
                    <img src="https://assets.wfcdn.com/im/06900104/resize-h800-w800%5Ecompr-r85/2246/224606489/Modway+Celebrate+Channel+Tufted+Performance+Velvet+Ottoman%2C+Mint+Velvet.jpg" alt="Mint Ottoman" class="h-32 max-w-[200px] object-contain filter drop-shadow-lg">
                    <div class="absolute -top-3 -right-3 flex space-x-1">
                        <button class="w-6 h-6 bg-white rounded-full shadow-md flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <button class="remove-item w-6 h-6 bg-white rounded-full shadow-md flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Toolbar -->
            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-white/90 backdrop-blur-sm rounded-xl shadow-lg px-4 py-2 flex items-center space-x-3 z-20">
                <button class="w-8 h-8 flex items-center justify-center text-gray-700 hover:text-indigo-600 transition-colors" title="Zoom In">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                    </svg>
                </button>
                <button class="w-8 h-8 flex items-center justify-center text-gray-700 hover:text-indigo-600 transition-colors" title="Zoom Out">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7" />
                    </svg>
                </button>
                <div class="h-6 border-l border-gray-300 mx-1"></div>
                <button class="w-8 h-8 flex items-center justify-center text-gray-700 hover:text-indigo-600 transition-colors" title="Reset View">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </button>
                <button class="w-8 h-8 flex items-center justify-center text-gray-700 hover:text-indigo-600 transition-colors" title="Remove All Items">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
                <div class="h-6 border-l border-gray-300 mx-1"></div>
                <button id="save-visualization" class="w-8 h-8 flex items-center justify-center text-gray-700 hover:text-indigo-600 transition-colors" title="Save Visualization">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                </button>
                <button id="share-visualization" class="w-8 h-8 flex items-center justify-center text-gray-700 hover:text-indigo-600 transition-colors" title="Share Visualization">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Sample Rooms Section -->
<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Try Sample Rooms</h2>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Living Room -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                <img src="/assets/images/living-room.jpg" alt="Living Room" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h3 class="font-medium text-gray-900">Modern Living Room</h3>
                    <button class="mt-2 px-3 py-1.5 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition-colors">
                        Try This Room
                    </button>
                </div>
            </div>
            
            <!-- Bedroom -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                <img src="/assets/images/bedroom.jpg" alt="Bedroom" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h3 class="font-medium text-gray-900">Cozy Bedroom</h3>
                    <button class="mt-2 px-3 py-1.5 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition-colors">
                        Try This Room
                    </button>
                </div>
            </div>
            
            <!-- Kitchen -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                <img src="/assets/images/kitchen.jpg" alt="Kitchen" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h3 class="font-medium text-gray-900">Contemporary Kitchen</h3>
                    <button class="mt-2 px-3 py-1.5 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition-colors">
                        Try This Room
                    </button>
                </div>
            </div>
            
            <!-- Home Office -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                <img src="/assets/images/office.jpg" alt="Home Office" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h3 class="font-medium text-gray-900">Minimal Home Office</h3>
                    <button class="mt-2 px-3 py-1.5 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition-colors">
                        Try This Room
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Visualizer Tips -->
<section class="py-12">
    <div class="container mx-auto px-4">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Tips for Better Results</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Good Lighting</h3>
                <p class="text-gray-600">
                    Take photos in well-lit rooms with natural light for the most accurate results. Avoid harsh shadows and direct sunlight.
                </p>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Frame the Space</h3>
                <p class="text-gray-600">
                    Capture the entire area where you plan to place furniture. Include floors and walls for perspective.
                </p>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Keep It Clear</h3>
                <p class="text-gray-600">
                    Clear the area of clutter and existing furniture. This gives you a clean canvas to work with.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- File Upload Modal -->
<div id="upload-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 m-4">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-gray-900">Upload Room Photo</h3>
            <button id="close-upload-modal" class="text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <form id="upload-form" class="space-y-6">
            <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <div id="upload-label">
                    <p class="text-base text-gray-700 mb-1">Drag and drop your photo here</p>
                    <p class="text-sm text-gray-500 mb-4">or click to browse files</p>
                </div>
                <div id="upload-preview" class="hidden">
                    <img id="preview-image" src="#" alt="Room Preview" class="max-h-48 mx-auto mb-3">
                    <p class="text-sm text-gray-700 font-medium" id="file-name">filename.jpg</p>
                </div>
                <input type="file" id="file-input" accept="image/*" class="hidden">
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" id="cancel-upload" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                    Cancel
                </button>
                <button type="submit" id="confirm-upload" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    Upload & Continue
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Camera Capture Modal -->
<div id="camera-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 m-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-900">Take a Photo</h3>
            <button id="close-camera-modal" class="text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <div class="space-y-4">
            <div id="camera-container" class="bg-black rounded-xl overflow-hidden h-80 flex items-center justify-center">
                <video id="camera-feed" class="w-full h-full hidden" autoplay></video>
                <div id="camera-placeholder" class="text-white text-center p-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <p>Camera access required</p>
                </div>
                <canvas id="camera-canvas" class="hidden"></canvas>
            </div>
            
            <div class="flex justify-center space-x-4">
                <button id="switch-camera" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                    Switch Camera
                </button>
                <button id="capture-photo" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    </svg>
                    Take Photo
                </button>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include 'includes/footer.php';
?>

<!-- JavaScript code moved outside of PHP block -->
<script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // File Upload Modal
    const uploadModal = document.getElementById('upload-modal');
    const uploadRoomBtn = document.getElementById('upload-room');
    const emptyStateUploadBtn = document.getElementById('empty-state-upload');
    const closeUploadModalBtn = document.getElementById('close-upload-modal');
    const cancelUploadBtn = document.getElementById('cancel-upload');
    const fileInput = document.getElementById('file-input');
    const uploadLabel = document.getElementById('upload-label');
    const uploadPreview = document.getElementById('upload-preview');
    const previewImage = document.getElementById('preview-image');
    const fileName = document.getElementById('file-name');
    const uploadForm = document.getElementById('upload-form');

    // Camera Modal
    const cameraModal = document.getElementById('camera-modal');
    const captureRoomBtn = document.getElementById('capture-room');
    const emptyStateCameraBtn = document.getElementById('empty-state-camera');
    const closeCameraModalBtn = document.getElementById('close-camera-modal');
    const cameraFeed = document.getElementById('camera-feed');
    const cameraPlaceholder = document.getElementById('camera-placeholder');
    const cameraCanvas = document.getElementById('camera-canvas');
    const switchCameraBtn = document.getElementById('switch-camera');
    const capturePhotoBtn = document.getElementById('capture-photo');

    // Room and Visualizer
    const roomImage = document.getElementById('room-image');
    const emptyState = document.getElementById('empty-state');
    const visualizerItems = document.getElementById('visualizer-items');
    const saveVisualizationBtn = document.getElementById('save-visualization');
    const shareVisualizationBtn = document.getElementById('share-visualization');

    // Check if a room image is already loaded
    if (!roomImage.getAttribute('src') || roomImage.getAttribute('src') === '') {
        roomImage.classList.add('hidden');
        emptyState.classList.remove('hidden');
    }

    // Upload Room Button Click
    function openUploadModal() {
        uploadModal.classList.remove('hidden');
        // Reset the upload form
        uploadLabel.classList.remove('hidden');
        uploadPreview.classList.add('hidden');
        uploadForm.reset();
    }
    
    uploadRoomBtn.addEventListener('click', openUploadModal);
    if (emptyStateUploadBtn) {
        emptyStateUploadBtn.addEventListener('click', openUploadModal);
    }

    // Close Upload Modal
    function closeUploadModal() {
        uploadModal.classList.add('hidden');
    }
    
    closeUploadModalBtn.addEventListener('click', closeUploadModal);
    cancelUploadBtn.addEventListener('click', closeUploadModal);

    // Handle File Selection
    const uploadContainer = uploadForm.querySelector('.border-dashed');
    uploadContainer.addEventListener('click', function() {
        fileInput.click();
    });

    uploadContainer.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadContainer.classList.add('border-indigo-500');
    });

    uploadContainer.addEventListener('dragleave', function() {
        uploadContainer.classList.remove('border-indigo-500');
    });

    uploadContainer.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadContainer.classList.remove('border-indigo-500');
        
        if (e.dataTransfer.files.length) {
            handleFileSelect(e.dataTransfer.files[0]);
        }
    });

    fileInput.addEventListener('change', function() {
        if (fileInput.files.length) {
            handleFileSelect(fileInput.files[0]);
        }
    });

    function handleFileSelect(file) {
        if (file.type.match('image.*')) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                fileName.textContent = file.name;
                uploadLabel.classList.add('hidden');
                uploadPreview.classList.remove('hidden');
            };
            
            reader.readAsDataURL(file);
        } else {
            alert('Please select an image file.');
        }
    }

    // Handle Form Submit
    uploadForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (previewImage.src) {
            roomImage.src = previewImage.src;
            roomImage.classList.remove('hidden');
            emptyState.classList.add('hidden');
            closeUploadModal();
        }
    });

    // Camera Capture Button Click
    function openCameraModal() {
        cameraModal.classList.remove('hidden');
        startCamera();
    }
    
    captureRoomBtn.addEventListener('click', openCameraModal);
    if (emptyStateCameraBtn) {
        emptyStateCameraBtn.addEventListener('click', openCameraModal);
    }

    // Close Camera Modal
    function closeCameraModal() {
        cameraModal.classList.add('hidden');
        stopCamera();
    }
    
    closeCameraModalBtn.addEventListener('click', closeCameraModal);

    // Camera Handling
    let stream = null;
    
    function startCamera() {
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(function(mediaStream) {
                stream = mediaStream;
                cameraFeed.srcObject = stream;
                cameraFeed.classList.remove('hidden');
                cameraPlaceholder.classList.add('hidden');
            })
            .catch(function(error) {
                console.error('Error accessing camera:', error);
                cameraPlaceholder.textContent = 'Camera access denied or not available.';
            });
    }
    
    function stopCamera() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
        cameraFeed.classList.add('hidden');
        cameraPlaceholder.classList.remove('hidden');
    }

    // Switch Camera
    switchCameraBtn.addEventListener('click', function() {
        if (stream) {
            stopCamera();
            // Toggle between front and back camera (simple implementation)
            // In a real app, you'd enumerate devices and select accordingly
            const constraints = { 
                video: { 
                    facingMode: cameraFeed.style.transform ? undefined : 'environment'
                } 
            };
            
            cameraFeed.style.transform = cameraFeed.style.transform ? '' : 'scaleX(-1)';
            
            navigator.mediaDevices.getUserMedia(constraints)
                .then(function(mediaStream) {
                    stream = mediaStream;
                    cameraFeed.srcObject = stream;
                    cameraFeed.classList.remove('hidden');
                    cameraPlaceholder.classList.add('hidden');
                });
        }
    });

    // Capture Photo
    capturePhotoBtn.addEventListener('click', function() {
        if (stream) {
            // Set canvas dimensions to match video
            const width = cameraFeed.videoWidth;
            const height = cameraFeed.videoHeight;
            cameraCanvas.width = width;
            cameraCanvas.height = height;
            
            // Draw the video frame to the canvas
            const context = cameraCanvas.getContext('2d');
            context.drawImage(cameraFeed, 0, 0, width, height);
            
            // Get image data from canvas
            const imageData = cameraCanvas.toDataURL('image/png');
            
            // Set the room image
            roomImage.src = imageData;
            roomImage.classList.remove('hidden');
            emptyState.classList.add('hidden');
            
            // Close the camera modal
            closeCameraModal();
        }
    });

    // Make visualizer items draggable
    interact('.visualizer-item')
        .draggable({
            inertia: true,
            modifiers: [
                interact.modifiers.restrictRect({
                    restriction: 'parent',
                    endOnly: true
                })
            ],
            autoScroll: true,
            listeners: {
                move: dragMoveListener,
                end: function(event) {
                    // Clone the dragged item and add it to the visualizer
                    const canvas = document.getElementById('visualizer-items');
                    const clone = event.target.cloneNode(true);
                    
                    // Position the clone at the drop position
                    const rect = canvas.getBoundingClientRect();
                    const x = event.clientX - rect.left - (clone.offsetWidth / 2);
                    const y = event.clientY - rect.top - (clone.offsetHeight / 2);
                    
                    clone.classList.remove('visualizer-item');
                    clone.classList.add('placed-item');
                    clone.style.position = 'absolute';
                    clone.style.left = Math.max(0, x) + 'px';
                    clone.style.top = Math.max(0, y) + 'px';
                    clone.style.zIndex = 20;
                    clone.style.transform = 'none';
                    
                    // Adjust appearance
                    const imgSrc = clone.querySelector('img').src;
                    clone.innerHTML = ''; // Clear the HTML
                    
                    // Add the image
                    const img = document.createElement('img');
                    img.src = imgSrc;
                    img.alt = 'Placed item';
                    img.className = 'h-32 max-w-[200px] object-contain filter drop-shadow-lg';
                    clone.appendChild(img);
                    
                    // Add control buttons
                    const controls = document.createElement('div');
                    controls.className = 'absolute -top-3 -right-3 flex space-x-1';
                    controls.innerHTML = `
                        <button class="w-6 h-6 bg-white rounded-full shadow-md flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <button class="remove-item w-6 h-6 bg-white rounded-full shadow-md flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    `;
                    clone.appendChild(controls);
                    
                    canvas.appendChild(clone);
                    
                    // Make the new item draggable
                    makePlacedItemDraggable(clone);
                    
                    // Add event listener for the remove button
                    const removeBtn = clone.querySelector('.remove-item');
                    if (removeBtn) {
                        removeBtn.addEventListener('click', function() {
                            clone.remove();
                        });
                    }
                }
            }
        });

    function dragMoveListener(event) {
        const target = event.target;
        // Keep the dragged position in the data-x/data-y attributes
        const x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx;
        const y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;

        // Update the element's position
        target.style.transform = `translate(${x}px, ${y}px)`;

        // Update the position attributes
        target.setAttribute('data-x', x);
        target.setAttribute('data-y', y);
    }
    
    function makePlacedItemDraggable(element) {
        interact(element)
            .draggable({
                inertia: true,
                modifiers: [
                    interact.modifiers.restrictRect({
                        restriction: 'parent',
                        endOnly: true
                    })
                ],
                autoScroll: true,
                listeners: {
                    move: dragMoveListener
                }
            })
            .resizable({
                edges: { left: true, right: true, bottom: true, top: true },
                invert: 'reposition',
                listeners: {
                    move: function(event) {
                        const target = event.target;
                        let x = parseFloat(target.getAttribute('data-x')) || 0;
                        let y = parseFloat(target.getAttribute('data-y')) || 0;

                        // Update the element's dimensions
                        const img = target.querySelector('img');
                        if (img) {
                            img.style.height = `${event.rect.height}px`;
                        }

                        // Translate when resizing from top or left edges
                        x += event.deltaRect.left;
                        y += event.deltaRect.top;

                        // Update the element's position
                        target.style.transform = `translate(${x}px, ${y}px)`;

                        // Update the position attributes
                        target.setAttribute('data-x', x);
                        target.setAttribute('data-y', y);
                    }
                }
            });
    }
    
    // Make existing placed items draggable
    document.querySelectorAll('.placed-item').forEach(item => {
        makePlacedItemDraggable(item);
    });
    
    // Make existing remove buttons functional
    document.querySelectorAll('.remove-item').forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.placed-item').remove();
        });
    });
    
    // Save Visualization
    saveVisualizationBtn.addEventListener('click', function() {
        // In a real app, this would save the visualization to a database
        alert('Your visualization has been saved!');
    });
    
    // Share Visualization
    shareVisualizationBtn.addEventListener('click', function() {
        // In a real app, this would generate a shareable link
        alert('Share link copied to clipboard!');
    });
});
</script>
