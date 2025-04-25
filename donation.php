<?php
session_start();
include 'config.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone'];
    $amount = $_POST['amount'];
    $transaction_id = $_POST['transaction_id'];
    $note = $_POST['note'];

    try {
        $stmt = $conn->prepare("INSERT INTO donations (full_name, phone, amount, transaction_id, note) 
                              VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdss", $full_name, $phone, $amount, $transaction_id, $note);
        
        if ($stmt->execute()) {
            $success = "Thank you for your donation!";
        } else {
            $error = "Error processing donation. Please try again.";
        }
    } catch (Exception $e) {
        $error = "Database error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Make a Donation - Grace Community Church</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        secondary: {
                            50: '#f8fafc',
                            100: '#f1f5f9',
                            200: '#e2e8f0',
                            300: '#cbd5e1',
                            400: '#94a3b8',
                            500: '#64748b',
                            600: '#475569',
                            700: '#334155',
                            800: '#1e293b',
                            900: '#0f172a',
                        },
                        accent: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            200: '#fde68a',
                            300: '#fcd34d',
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                            800: '#92400e',
                            900: '#78350f',
                        }
                    },
                    fontFamily: {
                        'heading': ['Playfair Display', 'serif'],
                        'sans': ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .font-heading {
            font-family: 'Playfair Display', serif;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <div class="flex items-center">
                    <a href="index.html" class="flex-shrink-0 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-primary-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                        </svg>
                        <span class="ml-3 text-xl font-heading font-semibold text-secondary-800">Grace Community</span>
                    </a>
                </div>
                
                <!-- Desktop Navigation -->
                <div class="hidden md:ml-6 md:flex md:items-center md:space-x-8">
                    <a href="index.html" class="text-secondary-500 hover:text-primary-600 hover:border-b-2 hover:border-primary-600 px-1 pt-1 font-medium text-sm transition-colors">Home</a>
                    <a href="about.html" class="text-secondary-500 hover:text-primary-600 hover:border-b-2 hover:border-primary-600 px-1 pt-1 font-medium text-sm transition-colors">About</a>
                    <a href="events.php" class="text-secondary-500 hover:text-primary-600 hover:border-b-2 hover:border-primary-600 px-1 pt-1 font-medium text-sm transition-colors">Events</a>
                    <a href="donation.php" class="text-primary-600 border-b-2 border-primary-600 px-1 pt-1 font-medium text-sm">Donate</a>
                    <a href="contact.html" class="text-secondary-500 hover:text-primary-600 hover:border-b-2 hover:border-primary-600 px-1 pt-1 font-medium text-sm transition-colors">Contact</a>
                </div>

                <!-- Auth Links -->
                <div class="flex items-center">
                    <a href="login.php" class="hidden md:inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-primary-700 bg-primary-50 hover:bg-primary-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 mr-3 transition-colors">
                        Sign in
                    </a>
                    <a href="signup.php" class="flex-1 px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 text-center">
                            Join Us
                        </a>
                    
                    <!-- Mobile menu button -->
                    <button type="button" class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-secondary-400 hover:text-secondary-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500" aria-expanded="false" id="mobile-menu-button">
                        <span class="sr-only">Open main menu</span>
                        <i data-lucide="menu" class="h-6 w-6"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu, show/hide based on menu state -->
        <div class="md:hidden hidden" id="mobile-menu">
            <div class="pt-2 pb-3 space-y-1">
                <a href="index.html" class="border-transparent text-secondary-500 hover:bg-gray-50 hover:border-l-4 hover:border-primary-300 hover:text-secondary-700 block pl-3 pr-4 py-2 text-base font-medium">Home</a>
                <a href="about.html" class="border-transparent text-secondary-500 hover:bg-gray-50 hover:border-l-4 hover:border-primary-300 hover:text-secondary-700 block pl-3 pr-4 py-2 text-base font-medium">About</a>
                <a href="events.php" class="border-transparent text-secondary-500 hover:bg-gray-50 hover:border-l-4 hover:border-primary-300 hover:text-secondary-700 block pl-3 pr-4 py-2 text-base font-medium">Events</a>
                <a href="donation.php" class="bg-primary-50 border-l-4 border-primary-500 text-primary-700 block pl-3 pr-4 py-2 text-base font-medium">Donate</a>
                <a href="contact.html" class="border-transparent text-secondary-500 hover:bg-gray-50 hover:border-l-4 hover:border-primary-300 hover:text-secondary-700 block pl-3 pr-4 py-2 text-base font-medium">Contact</a>
            </div>
            <div class="pt-4 pb-3 border-t border-gray-200">
                <div class="flex items-center px-4">
                    <a href="login.php" class="flex-1 px-4 py-2 border border-transparent text-sm font-medium rounded-md text-primary-700 bg-primary-50 hover:bg-primary-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 text-center">
                        Sign in
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative bg-primary-700">
        <div class="absolute inset-0">
            <img class="w-full h-full object-cover" src="https://images.unsplash.com/photo-1601142634808-38923eb7c560?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1170&q=80" alt="Giving hands">
            <div class="absolute inset-0 bg-primary-700 mix-blend-multiply" aria-hidden="true"></div>
        </div>
        <div class="relative max-w-7xl mx-auto py-24 px-4 sm:py-32 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl font-heading">Support Our Ministry</h1>
            <p class="mt-6 max-w-3xl text-xl text-primary-100">Your generous donations help us continue our mission of spreading faith, hope, and love throughout our community.</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Donation Impact -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="bg-primary-600 px-6 py-4">
                        <h2 class="text-xl font-bold text-white font-heading">Your Donation Makes a Difference</h2>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-primary-100 text-primary-600">
                                    <i data-lucide="heart" class="h-6 w-6"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-secondary-900">Community Outreach</h3>
                                <p class="mt-2 text-secondary-500">Support our food pantry, clothing drives, and assistance programs for those in need.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-primary-100 text-primary-600">
                                    <i data-lucide="book-open" class="h-6 w-6"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-secondary-900">Youth Programs</h3>
                                <p class="mt-2 text-secondary-500">Help fund our youth ministry, educational programs, and summer camps.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-primary-100 text-primary-600">
                                    <i data-lucide="music" class="h-6 w-6"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-secondary-900">Worship & Arts</h3>
                                <p class="mt-2 text-secondary-500">Support our music ministry, choir, and creative arts programs.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-primary-100 text-primary-600">
                                    <i data-lucide="home" class="h-6 w-6"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-secondary-900">Building & Maintenance</h3>
                                <p class="mt-2 text-secondary-500">Help us maintain and improve our facilities to better serve our community.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                
            </div>
            
            <!-- Right Column - Donation Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="bg-primary-600 px-6 py-4">
                        <h2 class="text-xl font-bold text-white font-heading">Make a Donation</h2>
                    </div>
                    <div class="p-6">
                        <?php if ($success): ?>
                            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i data-lucide="check-circle" class="h-5 w-5 text-green-500"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm"><?= $success ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($error): ?>
                            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i data-lucide="alert-circle" class="h-5 w-5 text-red-500"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm"><?= $error ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <form method="post" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="full_name" class="block text-sm font-medium text-secondary-700">Full Name</label>
                                    <input type="text" name="full_name" id="full_name" required
                                        class="mt-1 block w-full border-secondary-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                                </div>
                                
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-secondary-700">Phone Number</label>
                                    <input type="tel" name="phone" id="phone" required
                                        class="mt-1 block w-full border-secondary-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                                </div>
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-medium text-secondary-700">Email (Optional)</label>
                                <input type="email" name="email" id="email"
                                    class="mt-1 block w-full border-secondary-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                            </div>
                            
                            <div>
                                <label for="amount" class="block text-sm font-medium text-secondary-700">Donation Amount</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-secondary-500 sm:text-sm">₹</span>
                                    </div>
                                    <input type="number" name="amount" id="amount" required min="1" step="0.01"
                                        class="pl-7 block w-full border-secondary-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                                </div>
                            </div>
                            
                            <div>
                                <label for="transaction_id" class="block text-sm font-medium text-secondary-700">Transaction ID</label>
                                <input type="text" name="transaction_id" id="transaction_id" required
                                    class="mt-1 block w-full border-secondary-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                                <p class="mt-1 text-sm text-secondary-500">Enter the transaction ID from your payment method.</p>
                            </div>
                            
                            <div>
                                <label for="note" class="block text-sm font-medium text-secondary-700">Note (Optional)</label>
                                <textarea name="note" id="note" rows="3"
                                    class="mt-1 block w-full border-secondary-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"></textarea>
                            </div>
                            
                            <div class="bg-primary-50 p-4 rounded-lg">
                                <h3 class="font-medium text-secondary-900 mb-2 flex items-center">
                                    <i data-lucide="credit-card" class="h-5 w-5 mr-2 text-primary-600"></i>
                                    Payment Details
                                </h3>
                                <p class="mb-2 text-secondary-600">UPI ID: churchdonations@examplebank</p>
                                <button type="button" onclick="showQR()" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-primary-700 bg-primary-100 hover:bg-primary-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                                    <i data-lucide="qr-code" class="mr-2 h-4 w-4"></i>
                                    Show QR Code
                                </button>
                            </div>
                            
                            <button type="submit"
                                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                                <i data-lucide="heart" class="mr-2 h-5 w-5"></i>
                                Submit Donation
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="mt-8 bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-medium text-secondary-900 mb-4 font-heading">Other Ways to Give</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-10 w-10 rounded-md bg-primary-100 text-primary-600">
                                    <i data-lucide="building" class="h-5 w-5"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-base font-medium text-secondary-900">In Person</h4>
                                <p class="mt-1 text-secondary-500">Drop your donation in the offering box during any of our services.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-10 w-10 rounded-md bg-primary-100 text-primary-600">
                                    <i data-lucide="mail" class="h-5 w-5"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-base font-medium text-secondary-900">By Mail</h4>
                                <p class="mt-1 text-secondary-500">Send a check to: Grace Community Church, 123 Faith Street, Anytown, USA</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-10 w-10 rounded-md bg-primary-100 text-primary-600">
                                    <i data-lucide="gift" class="h-5 w-5"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-base font-medium text-secondary-900">Legacy Giving</h4>
                                <p class="mt-1 text-secondary-500">Consider including our church in your will or estate planning.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- QR Modal -->
    <div id="qrModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg max-w-sm w-full">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-secondary-900">Scan to Donate</h3>
                <button onclick="hideQR()" class="text-secondary-400 hover:text-secondary-500">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
            <div class="bg-primary-50 p-4 rounded-lg flex justify-center">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=upi://pay?pa=churchdonations@examplebank" alt="Donation QR Code" class="w-48 h-48">
            </div>
            <p class="mt-4 text-sm text-secondary-500 text-center">Scan this QR code with your UPI app to make a donation</p>
            <button onclick="hideQR()" 
                class="mt-4 w-full bg-primary-600 text-white py-2 rounded-lg hover:bg-primary-700 transition-colors flex items-center justify-center">
                <i data-lucide="x-circle" class="mr-2 h-4 w-4"></i>
                Close
            </button>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-secondary-800 text-white mt-12">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Logo and About -->
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                        </svg>
                        <span class="ml-3 text-xl font-heading font-semibold text-white">Grace Community</span>
                    </div>
                    <p class="mt-4 text-base text-secondary-300">
                        A welcoming community of faith, hope, and love. Join us as we worship, learn, and serve together.
                    </p>
                    <div class="mt-6 flex space-x-6">
                        <a href="#" class="text-secondary-400 hover:text-primary-400">
                            <span class="sr-only">Facebook</span>
                            <i data-lucide="facebook" class="h-6 w-6"></i>
                        </a>
                        <a href="#" class="text-secondary-400 hover:text-primary-400">
                            <span class="sr-only">Instagram</span>
                            <i data-lucide="instagram" class="h-6 w-6"></i>
                        </a>
                        <a href="#" class="text-secondary-400 hover:text-primary-400">
                            <span class="sr-only">Twitter</span>
                            <i data-lucide="twitter" class="h-6 w-6"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-sm font-semibold text-secondary-300 tracking-wider uppercase">Quick Links</h3>
                    <ul class="mt-4 space-y-4">
                        <li>
                            <a href="about.html" class="text-base text-secondary-300 hover:text-white">About Us</a>
                        </li>
                        <li>
                            <a href="events.php" class="text-base text-secondary-300 hover:text-white">Events</a>
                        </li>
                        <li>
                            <a href="donation.php" class="text-base text-secondary-300 hover:text-white">Donate</a>
                        </li>
                        <li>
                            <a href="contact.html" class="text-base text-secondary-300 hover:text-white">Contact</a>
                        </li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h3 class="text-sm font-semibold text-secondary-300 tracking-wider uppercase">Contact Us</h3>
                    <ul class="mt-4 space-y-4">
                        <li class="flex">
                            <i data-lucide="map-pin" class="h-6 w-6 text-secondary-400 mr-2"></i>
                            <span class="text-secondary-300">G.T ROAD , PHAGWARA,LOVELY PROFESSIONAL UNIVERSITY</span>
                        </li>
                        <li class="flex">
                            <i data-lucide="mail" class="h-6 w-6 text-secondary-400 mr-2"></i>
                            <span class="text-secondary-300">info@gracecommunity.org</span>
                        </li>
                        <li class="flex">
                            <i data-lucide="phone" class="h-6 w-6 text-secondary-400 mr-2"></i>
                            <span class="text-secondary-300">+ 011-441-444 </span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="mt-12 border-t border-secondary-700 pt-8">
                <p class="text-base text-secondary-400 text-center">&copy; 2023 Grace Community Church. All rights reserved.</p>
            </div>
        </div>
    </footer>  2023 Grace Community Church. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        
        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
        
        // QR code modal functions
        function showQR() {
            document.getElementById('qrModal').classList.remove('hidden');
        }
        
        function hideQR() {
            document.getElementById('qrModal').classList.add('hidden');
        }
        
        // Initialize Lucide icons
        lucide.createIcons();
    </script>
</body>
</html>
