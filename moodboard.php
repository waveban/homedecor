<?php
// Set current page for navigation highlighting
$currentPage = 'moodboard';

// SEO information
$pageTitle = 'Moodboard Creator | Design Your Space';
$pageDescription = 'Create beautiful moodboards with our easy-to-use tool. Mix and match furniture, decor, and colors to visualize your dream space.';

// Include API client
require_once 'includes/api-client.php';

// Fetch some sample products for the sidebar
$sampleProducts = fetchFeaturedProducts(12);

// Include header
include 'includes/header.php';
?>
<!-- Include InteractJS correctly -->
<script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>

<div class="container mx-auto px-4 py-8">
    <!-- Moodboard Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Moodboard Creator</h1>
            <p class="text-gray-600 max-w-2xl">
                Create beautiful moodboards by dragging and dropping items. Mix and match furniture, decor, and colors to visualize your dream space.
            </p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button id="create-new-board" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                New Board
            </button>
            <button id="save-board" class="px-4 py-2 bg-white text-indigo-600 border border-indigo-600 rounded-lg hover:bg-indigo-50 transition-colors flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                </svg>
                Save Board
            </button>
        </div>
    </div>
    
    <!-- Moodboard Interface -->
    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Sidebar -->
        <div class="lg:w-1/4 bg-white rounded-2xl shadow-lg p-4 h-[700px] flex flex-col">
            <!-- Search Box -->
            <div class="mb-4">
                <div class="relative">
                    <input type="text" id="moodboard-search" placeholder="Search for items" class="w-full px-4 py-2 bg-gray-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>
            
            <!-- Tabs -->
            <div class="flex border-b border-gray-200 mb-4">
                <button class="tab-button flex-1 py-2 text-indigo-600 border-b-2 border-indigo-600 font-medium" data-tab="items-tab">Items</button>
                <button class="tab-button flex-1 py-2 text-gray-600 hover:text-indigo-600 transition-colors" data-tab="colors-tab">Colors</button>
                <button class="tab-button flex-1 py-2 text-gray-600 hover:text-indigo-600 transition-colors" data-tab="text-tab">Text</button>
            </div>
            
            <!-- Items Grid -->
            <div id="items-tab" class="tab-content flex-grow overflow-y-auto">
                <div class="grid grid-cols-2 gap-3">
                    <?php foreach ($sampleProducts as $product): ?>
                    <div class="draggable-item cursor-move bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                        <img src="<?php echo !empty($product['imageUrls']) ? $product['imageUrls'][0] : 'https://via.placeholder.com/150'; ?>" 
                             alt="<?php echo isset($product['name']) ? htmlspecialchars($product['name']) : 'Product'; ?>" 
                             class="w-full h-24 object-contain p-2">
                        <div class="p-2 bg-gray-50">
                            <div class="text-xs font-medium text-gray-900 truncate">
                                <?php echo isset($product['name']) ? htmlspecialchars($product['name']) : (isset($product['product']['name']) ? htmlspecialchars($product['product']['name']) : 'Product Name'); ?>
                            </div>
                            <?php if (isset($product['priceDetails']['lowest'])): ?>
                            <div class="text-xs text-gray-600">$<?php echo number_format($product['priceDetails']['lowest'], 2); ?></div>
                            <?php elseif (isset($product['product']['priceDetails']['lowest'])): ?>
                            <div class="text-xs text-gray-600">$<?php echo number_format($product['product']['priceDetails']['lowest'], 2); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Colors Tab Content -->
            <div id="colors-tab" class="tab-content flex-grow overflow-y-auto hidden">
                <div class="grid grid-cols-4 gap-3">
                    <!-- Pre-defined color swatches -->
                    <div class="color-swatch bg-red-500 h-16 rounded-lg cursor-move" data-color="#ef4444"></div>
                    <div class="color-swatch bg-orange-500 h-16 rounded-lg cursor-move" data-color="#f97316"></div>
                    <div class="color-swatch bg-yellow-500 h-16 rounded-lg cursor-move" data-color="#eab308"></div>
                    <div class="color-swatch bg-green-500 h-16 rounded-lg cursor-move" data-color="#22c55e"></div>
                    <div class="color-swatch bg-teal-500 h-16 rounded-lg cursor-move" data-color="#14b8a6"></div>
                    <div class="color-swatch bg-blue-500 h-16 rounded-lg cursor-move" data-color="#3b82f6"></div>
                    <div class="color-swatch bg-indigo-500 h-16 rounded-lg cursor-move" data-color="#6366f1"></div>
                    <div class="color-swatch bg-purple-500 h-16 rounded-lg cursor-move" data-color="#a855f7"></div>
                    <div class="color-swatch bg-pink-500 h-16 rounded-lg cursor-move" data-color="#ec4899"></div>
                    <div class="color-swatch bg-gray-500 h-16 rounded-lg cursor-move" data-color="#6b7280"></div>
                    <div class="color-swatch bg-gray-800 h-16 rounded-lg cursor-move" data-color="#1f2937"></div>
                    <div class="color-swatch bg-white h-16 rounded-lg border border-gray-200 cursor-move" data-color="#ffffff"></div>
                    
                    <div class="col-span-4 mt-4">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Custom color:</label>
                        <div class="flex items-center space-x-2">
                            <input type="color" id="custom-color-picker" class="w-8 h-8 rounded-md border-0">
                            <button id="add-custom-color" class="px-3 py-1 text-xs bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors">
                                Add Color
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Text Tab Content -->
            <div id="text-tab" class="tab-content flex-grow overflow-y-auto hidden">
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Add text:</label>
                        <textarea id="text-content" class="w-full px-3 py-2 bg-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none" rows="3" placeholder="Enter your text here"></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Text style:</label>
                        <div class="flex space-x-2">
                            <select id="text-font" class="text-xs bg-gray-100 rounded-md px-2 py-1 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="sans">Sans Serif</option>
                                <option value="serif">Serif</option>
                                <option value="mono">Monospace</option>
                            </select>
                            <select id="text-size" class="text-xs bg-gray-100 rounded-md px-2 py-1 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="sm">Small</option>
                                <option value="md" selected>Medium</option>
                                <option value="lg">Large</option>
                                <option value="xl">Extra Large</option>
                            </select>
                            <input type="color" id="text-color" value="#000000" class="w-6 h-6 rounded-md border-0">
                        </div>
                    </div>
                    
                    <button id="add-text" class="w-full py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        Add Text to Board
                    </button>
                    
                    <div class="mt-4">
                        <div class="text-xs font-medium text-gray-500">Drag text templates:</div>
                        <div class="grid grid-cols-1 gap-2 mt-2">
                            <div class="text-template cursor-move bg-white rounded-lg border border-gray-200 p-3 text-center">
                                <span class="text-xl font-bold">Title Text</span>
                            </div>
                            <div class="text-template cursor-move bg-white rounded-lg border border-gray-200 p-3">
                                <span class="text-sm">Description or notes about your design concept and ideas.</span>
                            </div>
                            <div class="text-template cursor-move bg-white rounded-lg border border-gray-200 p-3 text-center">
                                <span class="font-medium">Room Name</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Moodboard Canvas -->
        <div class="lg:w-3/4 bg-white rounded-2xl shadow-lg p-4 h-[700px] relative overflow-hidden">
            <!-- Board Title -->
            <div class="absolute top-4 left-4 z-10">
                <input type="text" id="board-title" value="My Dream Living Room" class="text-xl font-bold text-gray-900 bg-white/80 backdrop-blur-sm px-3 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            
            <!-- Board Background -->
            <div id="board-background" class="absolute inset-0 bg-gray-100 z-0"></div>
            
            <!-- Moodboard Items Will Be Added Here -->
            <div id="moodboard-canvas" class="absolute inset-0 z-10">
                <!-- Sample Positioned Items -->
                <div class="moodboard-item absolute top-20 left-20 w-48 h-48 shadow-lg cursor-move" style="z-index: 1;">
                    <img src="https://assets.wfcdn.com/im/06900104/resize-h800-w800%5Ecompr-r85/2246/224606489/Modway+Celebrate+Channel+Tufted+Performance+Velvet+Ottoman%2C+Mint+Velvet.jpg" alt="Mint Ottoman" class="w-full h-full object-contain">
                    <div class="absolute top-0 right-0 flex space-x-1 p-1">
                        <button class="w-6 h-6 bg-white rounded-full shadow-md flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors item-info">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 21h7a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v11m0 5l4.879-4.879m0 0a3 3 0 104.243-4.242 3 3 0 00-4.243 4.242z" />
                            </svg>
                        </button>
                        <button class="delete-item w-6 h-6 bg-white rounded-full shadow-md flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="moodboard-item absolute top-80 left-80 w-48 h-48 shadow-lg cursor-move" style="z-index: 2;">
                    <img src="https://i5.walmartimages.com/seo/Meridian-Furniture-Claude-34-5-H-Velvet-Adjustable-Bar-Stool-in-Black_f60a3f35-2715-42b6-ab7f-991c2846fbb9.155f230bcfff1f80a5b7188d7a4cf969.jpeg" alt="Bar Stool" class="w-full h-full object-contain">
                    <div class="absolute top-0 right-0 flex space-x-1 p-1">
                        <button class="w-6 h-6 bg-white rounded-full shadow-md flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors item-info">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 21h7a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v11m0 5l4.879-4.879m0 0a3 3 0 104.243-4.242 3 3 0 00-4.243 4.242z" />
                            </svg>
                        </button>
                        <button class="delete-item w-6 h-6 bg-white rounded-full shadow-md flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="color-item moodboard-item absolute top-40 left-200 w-48 h-48 cursor-move" style="z-index: 3;">
                    <div class="w-full h-full rounded-xl bg-[#98D8C8]"></div>
                    <div class="absolute top-0 right-0 flex space-x-1 p-1">
                        <button class="delete-item w-6 h-6 bg-white rounded-full shadow-md flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Toolbar -->
            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-white/90 backdrop-blur-sm rounded-xl shadow-lg px-4 py-2 flex items-center space-x-3 z-20">
                <button id="zoom-in" class="w-8 h-8 flex items-center justify-center text-gray-700 hover:text-indigo-600 transition-colors" title="Zoom In">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                    </svg>
                </button>
                <button id="zoom-out" class="w-8 h-8 flex items-center justify-center text-gray-700 hover:text-indigo-600 transition-colors" title="Zoom Out">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7" />
                    </svg>
                </button>
                <div class="h-6 border-l border-gray-300 mx-1"></div>
                <button id="change-background" class="w-8 h-8 flex items-center justify-center text-gray-700 hover:text-indigo-600 transition-colors" title="Change Background">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </button>
                <button id="add-text-btn" class="w-8 h-8 flex items-center justify-center text-gray-700 hover:text-indigo-600 transition-colors" title="Add Text">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                    </svg>
                </button>
                <button id="add-color-btn" class="w-8 h-8 flex items-center justify-center text-gray-700 hover:text-indigo-600 transition-colors" title="Add Color Swatch">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                    </svg>
                </button>
                <div class="h-6 border-l border-gray-300 mx-1"></div>
                <button id="clear-board" class="w-8 h-8 flex items-center justify-center text-gray-700 hover:text-indigo-600 transition-colors" title="Clear Board">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
                <button id="export-board" class="w-8 h-8 flex items-center justify-center text-gray-700 hover:text-indigo-600 transition-colors" title="Export">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Background Color Modal -->
<div id="bg-color-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Change Background</h3>
            <button class="close-modal text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Background Color</label>
                <div class="grid grid-cols-5 gap-2">
                    <button class="bg-white h-10 rounded-md border border-gray-200 hover:border-indigo-500 bg-option" data-color="#ffffff"></button>
                    <button class="bg-gray-100 h-10 rounded-md hover:border hover:border-indigo-500 bg-option" data-color="#f3f4f6"></button>
                    <button class="bg-gray-200 h-10 rounded-md hover:border hover:border-indigo-500 bg-option" data-color="#e5e7eb"></button>
                    <button class="bg-indigo-50 h-10 rounded-md hover:border hover:border-indigo-500 bg-option" data-color="#eef2ff"></button>
                    <button class="bg-blue-50 h-10 rounded-md hover:border hover:border-indigo-500 bg-option" data-color="#eff6ff"></button>
                    <button class="bg-green-50 h-10 rounded-md hover:border hover:border-indigo-500 bg-option" data-color="#ecfdf5"></button>
                    <button class="bg-pink-50 h-10 rounded-md hover:border hover:border-indigo-500 bg-option" data-color="#fdf2f8"></button>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Custom Color</label>
                <div class="flex space-x-2">
                    <input type="color" id="bg-color-picker" class="h-10 w-10 rounded">
                    <button id="apply-custom-bg" class="flex-1 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        Apply Custom Color
                    </button>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Upload Image</label>
                <div class="flex flex-col space-y-2">
                    <input type="file" id="bg-image-upload" accept="image/*" class="hidden">
                    <button id="select-bg-image" class="py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm text-gray-700">
                        Select Image
                    </button>
                    <div id="bg-image-preview" class="hidden mt-2">
                        <img id="preview-bg-image" src="#" alt="Background Preview" class="max-h-32 max-w-full mx-auto">
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-2 mt-6">
                <button class="close-modal px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors">
                    Cancel
                </button>
                <button id="apply-bg-changes" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    Apply
                </button>
            </div>
        </div>
    </div>
</div>

<!-- My Moodboards Section -->
<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">My Moodboards</h2>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Sample Moodboard -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                <div class="h-48 bg-gray-200 relative">
                    <div class="absolute inset-0 p-3">
                        <div class="grid grid-cols-3 gap-2 h-full">
                            <div class="col-span-2 row-span-2">
                                <img src="https://assets.wfcdn.com/im/06900104/resize-h800-w800%5Ecompr-r85/2246/224606489/Modway+Celebrate+Channel+Tufted+Performance+Velvet+Ottoman%2C+Mint+Velvet.jpg" alt="Moodboard item" class="w-full h-full object-cover rounded-lg">
                            </div>
                            <div>
                                <div class="w-full h-full rounded-lg bg-[#98D8C8]"></div>
                            </div>
                            <div>
                                <img src="https://i5.walmartimages.com/seo/Meridian-Furniture-Claude-34-5-H-Velvet-Adjustable-Bar-Stool-in-Black_f60a3f35-2715-42b6-ab7f-991c2846fbb9.155f230bcfff1f80a5b7188d7a4cf969.jpeg" alt="Moodboard item" class="w-full h-full object-cover rounded-lg">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="font-medium text-gray-900">Modern Living Room</h3>
                    <p class="text-sm text-gray-600 mt-1">Last edited 3 days ago</p>
                    <div class="flex justify-between items-center mt-3">
                        <div class="text-xs text-gray-500">5 items</div>
                        <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Edit</button>
                    </div>
                </div>
            </div>
            
            <!-- Sample Moodboard -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                <div class="h-48 bg-gray-200 relative">
                    <div class="absolute inset-0 p-3">
                        <div class="grid grid-cols-3 gap-2 h-full">
                            <div>
                                <img src="https://m.media-amazon.com/images/I/71H6jYGaZ3L._AC_SL1500_.jpg" alt="Moodboard item" class="w-full h-full object-cover rounded-lg">
                            </div>
                            <div class="col-span-2">
                                <div class="w-full h-full rounded-lg bg-gray-800"></div>
                            </div>
                            <div class="col-span-3">
                                <div class="w-full h-full rounded-lg flex items-center justify-center bg-gray-100 text-gray-500 text-sm font-medium">
                                    Minimalist Dining Room
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="font-medium text-gray-900">Minimalist Dining Room</h3>
                    <p class="text-sm text-gray-600 mt-1">Last edited 1 week ago</p>
                    <div class="flex justify-between items-center mt-3">
                        <div class="text-xs text-gray-500">3 items</div>
                        <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Edit</button>
                    </div>
                </div>
            </div>
            
            <!-- Sample Moodboard -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow cursor-pointer" id="create-new-moodboard">
                <div class="h-48 bg-gray-100 flex items-center justify-center">
                    <div class="text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        <div class="text-gray-500 font-medium">Create New Moodboard</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Moodboard Tips -->
<section class="py-12">
    <div class="container mx-auto px-4">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Moodboard Design Tips</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Start with a Color Scheme</h3>
                <p class="text-gray-600">
                    Choose 3-5 colors that complement each other. Include a primary color, secondary color, and neutral tones for balance.
                </p>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Mix Textures and Materials</h3>
                <p class="text-gray-600">
                    Combine different textures like wood, metal, fabric, and glass to create visual interest and depth in your design.
                </p>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Visualize Scale and Proportion</h3>
                <p class="text-gray-600">
                    Pay attention to the size of furniture relative to your space. Include a mix of large and small items for balance.
                </p>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?>

<!-- JavaScript code moved outside of PHP block -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Variables for scaling
    let currentScale = 1;
    const canvas = document.getElementById('moodboard-canvas');
    const scaleIncrement = 0.1;
    const maxScale = 2;
    const minScale = 0.5;
    
    // Tab switching
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            
            // Remove active state from all tabs
            tabButtons.forEach(btn => {
                btn.classList.remove('text-indigo-600', 'border-b-2', 'border-indigo-600');
                btn.classList.add('text-gray-600');
            });
            
            // Hide all tab contents
            tabContents.forEach(content => {
                content.classList.add('hidden');
            });
            
            // Activate current tab and content
            this.classList.add('text-indigo-600', 'border-b-2', 'border-indigo-600');
            this.classList.remove('text-gray-600');
            document.getElementById(tabId).classList.remove('hidden');
        });
    });
    
    // Initialize draggable elements from items tab
    interact('.draggable-item')
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
                end: function (event) {
                    // Clone the dragged item and add it to the moodboard
                    const canvas = document.getElementById('moodboard-canvas');
                    const clone = event.target.cloneNode(true);
                    
                    // Position the clone at the drop position
                    const rect = canvas.getBoundingClientRect();
                    const x = event.clientX - rect.left - (clone.offsetWidth / 2);
                    const y = event.clientY - rect.top - (clone.offsetHeight / 2);
                    
                    clone.classList.remove('draggable-item');
                    clone.classList.add('moodboard-item');
                    clone.style.position = 'absolute';
                    clone.style.left = `${Math.max(0, x)}px`;
                    clone.style.top = `${Math.max(0, y)}px`;
                    clone.style.zIndex = 10;
                    clone.style.width = '120px';
                    clone.style.height = 'auto';
                    clone.style.transform = 'none';
                    
                    // Add control buttons for the item
                    const controls = document.createElement('div');
                    controls.className = 'absolute top-0 right-0 flex space-x-1 p-1';
                    controls.innerHTML = `
                        <button class="item-info w-6 h-6 bg-white rounded-full shadow-md flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 21h7a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v11m0 5l4.879-4.879m0 0a3 3 0 104.243-4.242 3 3 0 00-4.243 4.242z" />
                            </svg>
                        </button>
                        <button class="delete-item w-6 h-6 bg-white rounded-full shadow-md flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    `;
                    
                    clone.appendChild(controls);
                    canvas.appendChild(clone);
                    
                    // Make the new item draggable
                    makeMoodboardItemDraggable(clone);
                    
                    // Add event listener for the delete button
                    const deleteBtn = clone.querySelector('.delete-item');
                    if (deleteBtn) {
                        deleteBtn.addEventListener('click', function() {
                            clone.remove();
                        });
                    }
                    
                    // Add event listener for the info button
                    const infoBtn = clone.querySelector('.item-info');
                    if (infoBtn) {
                        infoBtn.addEventListener('click', function() {
                            alert('Item Info: This would show product details in a real app.');
                        });
                    }
                }
            }
        });
    
    // Initialize draggable color swatches
    interact('.color-swatch')
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
                end: function (event) {
                    const canvas = document.getElementById('moodboard-canvas');
                    const color = event.target.getAttribute('data-color');
                    
                    // Create a new color element
                    const colorItem = document.createElement('div');
                    colorItem.className = 'color-item moodboard-item';
                    
                    // Position the color item at the drop position
                    const rect = canvas.getBoundingClientRect();
                    const x = event.clientX - rect.left - 60; // 60 = half of element width
                    const y = event.clientY - rect.top - 60;  // 60 = half of element height
                    
                    colorItem.style.position = 'absolute';
                    colorItem.style.left = `${Math.max(0, x)}px`;
                    colorItem.style.top = `${Math.max(0, y)}px`;
                    colorItem.style.width = '120px';
                    colorItem.style.height = '120px';
                    colorItem.style.zIndex = 10;
                    
                    // Add the color swatch
                    const swatch = document.createElement('div');
                    swatch.className = 'w-full h-full rounded-xl';
                    swatch.style.backgroundColor = color;
                    colorItem.appendChild(swatch);
                    
                    // Add delete button
                    const controls = document.createElement('div');
                    controls.className = 'absolute top-0 right-0 flex space-x-1 p-1';
                    controls.innerHTML = `
                        <button class="delete-item w-6 h-6 bg-white rounded-full shadow-md flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    `;
                    
                    colorItem.appendChild(controls);
                    canvas.appendChild(colorItem);
                    
                    // Make the new color draggable and resizable
                    makeMoodboardItemDraggable(colorItem);
                    
                    // Add event listener for the delete button
                    const deleteBtn = colorItem.querySelector('.delete-item');
                    if (deleteBtn) {
                        deleteBtn.addEventListener('click', function() {
                            colorItem.remove();
                        });
                    }
                }
            }
        });
    
    // Initialize draggable text templates
    interact('.text-template')
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
                end: function (event) {
                    const canvas = document.getElementById('moodboard-canvas');
                    const textContent = event.target.querySelector('span').innerHTML;
                    const textClass = event.target.querySelector('span').className;
                    
                    // Create a new text element
                    const textItem = document.createElement('div');
                    textItem.className = 'text-item moodboard-item';
                    
                    // Position the text item at the drop position
                    const rect = canvas.getBoundingClientRect();
                    const x = event.clientX - rect.left - 75; // adjust as needed
                    const y = event.clientY - rect.top - 20;  // adjust as needed
                    
                    textItem.style.position = 'absolute';
                    textItem.style.left = `${Math.max(0, x)}px`;
                    textItem.style.top = `${Math.max(0, y)}px`;
                    textItem.style.minWidth = '150px';
                    textItem.style.padding = '8px';
                    textItem.style.backgroundColor = 'rgba(255, 255, 255, 0.8)';
                    textItem.style.borderRadius = '6px';
                    textItem.style.zIndex = 10;
                    
                    // Add the text content
                    const textElement = document.createElement('div');
                    textElement.className = textClass;
                    textElement.contentEditable = true;
                    textElement.innerHTML = textContent;
                    textItem.appendChild(textElement);
                    
                    // Add controls
                    const controls = document.createElement('div');
                    controls.className = 'absolute -top-3 -right-3 flex space-x-1 p-1';
                    controls.innerHTML = `
                        <button class="delete-item w-6 h-6 bg-white rounded-full shadow-md flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    `;
                    
                    textItem.appendChild(controls);
                    canvas.appendChild(textItem);
                    
                    // Make the new text draggable
                    makeMoodboardItemDraggable(textItem);
                    
                    // Add event listener for the delete button
                    const deleteBtn = textItem.querySelector('.delete-item');
                    if (deleteBtn) {
                        deleteBtn.addEventListener('click', function() {
                            textItem.remove();
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
    
    function makeMoodboardItemDraggable(element) {
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
                    move: function (event) {
                        const target = event.target;
                        let x = parseFloat(target.getAttribute('data-x')) || 0;
                        let y = parseFloat(target.getAttribute('data-y')) || 0;

                        // Update the element's dimensions
                        target.style.width = `${event.rect.width}px`;
                        target.style.height = `${event.rect.height}px`;

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
    
    // Make existing moodboard items draggable
    document.querySelectorAll('.moodboard-item').forEach(item => {
        makeMoodboardItemDraggable(item);
    });
    
    // Make existing delete buttons functional
    document.querySelectorAll('.delete-item').forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.moodboard-item').remove();
        });
    });
    
    // Create New Board Button
    document.getElementById('create-new-board').addEventListener('click', function() {
        clearBoard();
        
        // Reset the board title
        document.getElementById('board-title').value = 'New Moodboard';
    });
    
    // Create New Moodboard card click
    document.getElementById('create-new-moodboard').addEventListener('click', function() {
        clearBoard();
        document.getElementById('board-title').value = 'New Moodboard';
    });
    
    // Clear board function
    function clearBoard() {
        // Clear the board
        const canvas = document.getElementById('moodboard-canvas');
        while (canvas.firstChild) {
            canvas.removeChild(canvas.firstChild);
        }
    }
    
    // Clear board button
    document.getElementById('clear-board').addEventListener('click', clearBoard);
    
    // Save Board Button
    document.getElementById('save-board').addEventListener('click', function() {
        // In a real application, this would save the moodboard to a database
        const title = document.getElementById('board-title').value || 'Untitled Moodboard';
        alert(`Moodboard "${title}" saved successfully!`);
    });
    
    // Zoom functionality
    document.getElementById('zoom-in').addEventListener('click', function() {
        if (currentScale < maxScale) {
            currentScale += scaleIncrement;
            updateScale();
        }
    });
    
    document.getElementById('zoom-out').addEventListener('click', function() {
        if (currentScale > minScale) {
            currentScale -= scaleIncrement;
            updateScale();
        }
    });
    
    function updateScale() {
        canvas.style.transform = `scale(${currentScale})`;
        canvas.style.transformOrigin = 'center center';
    }
    
    // Add custom text button
    document.getElementById('add-text').addEventListener('click', function() {
        const textContent = document.getElementById('text-content').value;
        const textFont = document.getElementById('text-font').value;
        const textSize = document.getElementById('text-size').value;
        const textColor = document.getElementById('text-color').value;
        
        if (textContent.trim() === '') {
            alert('Please enter some text first.');
            return;
        }
        
        // Create text element
        const textItem = document.createElement('div');
        textItem.className = 'text-item moodboard-item';
        textItem.style.position = 'absolute';
        textItem.style.left = '50%';
        textItem.style.top = '50%';
        textItem.style.transform = 'translate(-50%, -50%)';
        textItem.style.minWidth = '150px';
        textItem.style.padding = '8px';
        textItem.style.backgroundColor = 'rgba(255, 255, 255, 0.8)';
        textItem.style.borderRadius = '6px';
        textItem.style.zIndex = 15;
        
        // Set font family
        let fontFamily = 'sans-serif';
        if (textFont === 'serif') fontFamily = 'serif';
        if (textFont === 'mono') fontFamily = 'monospace';
        
        // Set font size
        let fontSize = '16px';
        if (textSize === 'sm') fontSize = '14px';
        if (textSize === 'lg') fontSize = '20px';
        if (textSize === 'xl') fontSize = '24px';
        
        // Add text content
        const textElement = document.createElement('div');
        textElement.contentEditable = true;
        textElement.style.fontFamily = fontFamily;
        textElement.style.fontSize = fontSize;
        textElement.style.color = textColor;
        textElement.innerHTML = textContent;
        textItem.appendChild(textElement);
        
        // Add controls
        const controls = document.createElement('div');
        controls.className = 'absolute -top-3 -right-3 flex space-x-1';
        controls.innerHTML = `
            <button class="delete-item w-6 h-6 bg-white rounded-full shadow-md flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        `;
        
           textItem.appendChild(controls);
        document.getElementById('moodboard-canvas').appendChild(textItem);
        
        // Make the new text draggable
        makeMoodboardItemDraggable(textItem);
        
        // Add event listener for the delete button
        const deleteBtn = textItem.querySelector('.delete-item');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', function() {
                textItem.remove();
            });
        }
        
        // Clear the text input
        document.getElementById('text-content').value = '';
    });
    
    // Add custom color button
    document.getElementById('add-custom-color').addEventListener('click', function() {
        const color = document.getElementById('custom-color-picker').value;
        
        // Create color item
        const colorItem = document.createElement('div');
        colorItem.className = 'color-item moodboard-item';
        colorItem.style.position = 'absolute';
        colorItem.style.left = '50%';
        colorItem.style.top = '50%';
        colorItem.style.transform = 'translate(-50%, -50%)';
        colorItem.style.width = '120px';
        colorItem.style.height = '120px';
        colorItem.style.zIndex = 10;
        
        // Add the color swatch
        const swatch = document.createElement('div');
        swatch.className = 'w-full h-full rounded-xl';
        swatch.style.backgroundColor = color;
        colorItem.appendChild(swatch);
        
        // Add delete button
        const controls = document.createElement('div');
        controls.className = 'absolute top-0 right-0 flex space-x-1 p-1';
        controls.innerHTML = `
            <button class="delete-item w-6 h-6 bg-white rounded-full shadow-md flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        `;
        
        colorItem.appendChild(controls);
        document.getElementById('moodboard-canvas').appendChild(colorItem);
        
        // Make the new color draggable and resizable
        makeMoodboardItemDraggable(colorItem);
        
        // Add event listener for the delete button
        const deleteBtn = colorItem.querySelector('.delete-item');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', function() {
                colorItem.remove();
            });
        }
    });
    
    // Background color modal
    const bgColorModal = document.getElementById('bg-color-modal');
    const closeBtns = document.querySelectorAll('.close-modal');
    const boardBackground = document.getElementById('board-background');
    
    // Show background modal
    document.getElementById('change-background').addEventListener('click', function() {
        bgColorModal.classList.remove('hidden');
    });
    
    // Close background modal
    closeBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            bgColorModal.classList.add('hidden');
        });
    });
    
    // Apply background color options
    document.querySelectorAll('.bg-option').forEach(option => {
        option.addEventListener('click', function() {
            const color = this.getAttribute('data-color');
            
            // Remove any background image
            boardBackground.style.backgroundImage = 'none';
            // Set background color
            boardBackground.style.backgroundColor = color;
            
            // Highlight selected option
            document.querySelectorAll('.bg-option').forEach(opt => {
                opt.classList.remove('border-indigo-500', 'border-2');
            });
            this.classList.add('border-indigo-500', 'border-2');
        });
    });
    
    // Custom background color
    document.getElementById('apply-custom-bg').addEventListener('click', function() {
        const color = document.getElementById('bg-color-picker').value;
        boardBackground.style.backgroundImage = 'none';
        boardBackground.style.backgroundColor = color;
    });
    
    // Background image upload
    document.getElementById('select-bg-image').addEventListener('click', function() {
        document.getElementById('bg-image-upload').click();
    });
    
    document.getElementById('bg-image-upload').addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview-bg-image').src = e.target.result;
                document.getElementById('bg-image-preview').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Apply background changes
    document.getElementById('apply-bg-changes').addEventListener('click', function() {
        const previewImg = document.getElementById('preview-bg-image').src;
        
        if (previewImg && previewImg !== '#') {
            boardBackground.style.backgroundImage = `url(${previewImg})`;
            boardBackground.style.backgroundSize = 'cover';
            boardBackground.style.backgroundPosition = 'center';
            boardBackground.style.backgroundColor = 'transparent';
        }
        
        bgColorModal.classList.add('hidden');
    });
    
    // Add text button from toolbar
    document.getElementById('add-text-btn').addEventListener('click', function() {
        // Switch to text tab
        document.querySelector('[data-tab="text-tab"]').click();
    });
    
    // Add color button from toolbar
    document.getElementById('add-color-btn').addEventListener('click', function() {
        // Switch to colors tab
        document.querySelector('[data-tab="colors-tab"]').click();
    });
    
    // Export board
    document.getElementById('export-board').addEventListener('click', function() {
        // In a real app, this would use a library like html2canvas to generate an image
        alert('This would generate a shareable image of your moodboard in a real application.');
    });
    
    // Search functionality
    document.getElementById('moodboard-search').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const items = document.querySelectorAll('#items-tab .draggable-item');
        
        items.forEach(item => {
            const itemName = item.querySelector('.text-xs').textContent.toLowerCase();
            if (itemName.includes(searchTerm) || searchTerm === '') {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
    
    // Initialize: Make existing items draggable and functional
    document.addEventListener('load', function() {
        document.querySelectorAll('.moodboard-item').forEach(item => {
            makeMoodboardItemDraggable(item);
        });
        
        document.querySelectorAll('.delete-item').forEach(button => {
            button.addEventListener('click', function() {
                this.closest('.moodboard-item').remove();
            });
        });
    });
});
</script>

        
