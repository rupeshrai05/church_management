<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $bride_name = $_POST['bride_name'];
    $groom_name = $_POST['groom_name'];
    $bride_phone = $_POST['bride_phone'];
    $groom_phone = $_POST['groom_phone'];
    $email = $_POST['email'];
    $wedding_date = $_POST['wedding_date'];
    $notes = $_POST['notes'];

    try {
        $stmt = $conn->prepare("INSERT INTO weddings (user_id, bride_name, groom_name, bride_phone, groom_phone, email, wedding_date, notes) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssss", $user_id, $bride_name, $groom_name, $bride_phone, $groom_phone, $email, $wedding_date, $notes);
        
        if ($stmt->execute()) {
            $success = "Wedding booking submitted successfully!";
        } else {
            $error = "Error submitting wedding booking. Please try again.";
        }
    } catch (Exception $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

// Get wedding history
$user_id = $_SESSION['user_id'];
$weddings = $conn->query("SELECT * FROM weddings WHERE user_id = $user_id ORDER BY wedding_date DESC");

// Get user information
$user_query = $conn->query("SELECT * FROM users WHERE id = $user_id");
$user = $user_query->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Wedding Booking - Grace Community Church</title>
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
<body class="bg-gray-50 min-h-screen">
    <div class="flex h-screen bg-gray-50">
        <!-- Sidebar -->
        <div class="hidden md:flex md:flex-shrink-0">
            <div class="flex flex-col w-64 bg-secondary-800 border-r border-secondary-700">
                <div class="flex items-center h-16 px-4 bg-secondary-900 border-b border-secondary-700">
                    <a href="index.html" class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                        </svg>
                        <span class="ml-2 text-xl font-heading font-semibold text-white">Grace Community</span>
                    </a>
                </div>
                <div class="flex flex-col flex-grow px-4 py-6 overflow-y-auto">
                    <div class="flex flex-shrink-0 items-center px-4 mb-5">
                        <div class="h-10 w-10 rounded-full bg-primary-600 flex items-center justify-center text-white font-semibold text-lg">
                            <?= strtoupper(substr($user['full_name'] ?? 'U', 0, 1)) ?>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-white"><?= htmlspecialchars($user['full_name'] ?? 'User') ?></p>
                            <p class="text-xs font-medium text-primary-200">Member</p>
                        </div>
                    </div>
                    <nav class="flex-1 space-y-1">
                        <a href="user_dashboard.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-md text-secondary-300 hover:bg-secondary-700 hover:text-white group">
                            <i data-lucide="layout-dashboard" class="mr-3 h-5 w-5 text-secondary-400 group-hover:text-primary-400"></i>
                            Dashboard
                        </a>
                        <a href="user_appointments.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-md text-secondary-300 hover:bg-secondary-700 hover:text-white group">
                            <i data-lucide="calendar" class="mr-3 h-5 w-5 text-secondary-400 group-hover:text-primary-400"></i>
                            Appointments
                        </a>
                        <a href="user_weddings.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-md bg-secondary-700 text-white group">
                            <i data-lucide="heart" class="mr-3 h-5 w-5 text-primary-400"></i>
                            Wedding Bookings
                        </a>
                        <a href="events.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-md text-secondary-300 hover:bg-secondary-700 hover:text-white group">
                            <i data-lucide="calendar-days" class="mr-3 h-5 w-5 text-secondary-400 group-hover:text-primary-400"></i>
                            Events
                        </a>
                        <a href="donation.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-md text-secondary-300 hover:bg-secondary-700 hover:text-white group">
                            <i data-lucide="hand-heart" class="mr-3 h-5 w-5 text-secondary-400 group-hover:text-primary-400"></i>
                            Donate
                        </a>
                    </nav>
                </div>
                <div class="flex-shrink-0 flex border-t border-secondary-700 p-4">
                    <a href="logout.php" class="flex-shrink-0 w-full group block">
                        <div class="flex items-center">
                            <div>
                                <i data-lucide="log-out" class="h-5 w-5 text-secondary-400 group-hover:text-primary-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-secondary-300 group-hover:text-white">Logout</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex flex-col flex-1 overflow-hidden">
            <!-- Top Navigation -->
            <div class="bg-white shadow-sm z-10">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center">
                        <button type="button" class="md:hidden text-secondary-500 hover:text-secondary-600 focus:outline-none" id="sidebar-toggle">
                            <i data-lucide="menu" class="h-6 w-6"></i>
                        </button>
                        <h1 class="ml-2 md:ml-0 text-xl font-semibold text-secondary-900">Wedding Booking</h1>
                    </div>
                    <div class="flex items-center">
                        <a href="index.html" class="text-secondary-500 hover:text-secondary-700 mr-4">
                            <i data-lucide="home" class="h-5 w-5"></i>
                        </a>
                        <div class="relative">
                            <button type="button" class="flex items-center max-w-xs text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500" id="user-menu-button">
                                <span class="sr-only">Open user menu</span>
                                <div class="h-8 w-8 rounded-full bg-primary-600 flex items-center justify-center text-white">
                                    <?= strtoupper(substr($user['full_name'] ?? 'U', 0, 1)) ?>
                                </div>
                            </button>
                            <!-- User dropdown menu -->
                            <div class="hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" id="user-menu">
                                <a href="#" class="block px-4 py-2 text-sm text-secondary-700 hover:bg-gray-100">Your Profile</a>
                                <a href="#" class="block px-4 py-2 text-sm text-secondary-700 hover:bg-gray-100">Settings</a>
                                <a href="logout.php" class="block px-4 py-2 text-sm text-secondary-700 hover:bg-gray-100">Sign out</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Sidebar -->
            <div class="md:hidden fixed inset-0 z-40 hidden" id="mobile-sidebar">
                <div class="fixed inset-0 bg-secondary-600 bg-opacity-75"></div>
                <div class="relative flex-1 flex flex-col max-w-xs w-full bg-secondary-800">
                    <div class="absolute top-0 right-0 -mr-12 pt-2">
                        <button type="button" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white" id="close-sidebar">
                            <span class="sr-only">Close sidebar</span>
                            <i data-lucide="x" class="h-6 w-6 text-white"></i>
                        </button>
                    </div>
                    <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                        <div class="flex-shrink-0 flex items-center px-4">
                            <span class="text-xl font-heading font-semibold text-white">Grace Community</span>
                        </div>
                        <nav class="mt-5 px-2 space-y-1">
                            <a href="user_dashboard.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-md text-secondary-300 hover:bg-secondary-700 hover:text-white group">
                                <i data-lucide="layout-dashboard" class="mr-3 h-5 w-5 text-secondary-400 group-hover:text-primary-400"></i>
                                Dashboard
                            </a>
                            <a href="user_appointments.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-md text-secondary-300 hover:bg-secondary-700 hover:text-white group">
                                <i data-lucide="calendar" class="mr-3 h-5 w-5 text-secondary-400 group-hover:text-primary-400"></i>
                                Appointments
                            </a>
                            <a href="user_weddings.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-md bg-secondary-700 text-white group">
                                <i data-lucide="heart" class="mr-3 h-5 w-5 text-primary-400"></i>
                                Wedding Bookings
                            </a>
                            <a href="events.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-md text-secondary-300 hover:bg-secondary-700 hover:text-white group">
                                <i data-lucide="calendar-days" class="mr-3 h-5 w-5 text-secondary-400 group-hover:text-primary-400"></i>
                                Events
                            </a>
                            <a href="donation.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-md text-secondary-300 hover:bg-secondary-700 hover:text-white group">
                                <i data-lucide="hand-heart" class="mr-3 h-5 w-5 text-secondary-400 group-hover:text-primary-400"></i>
                                Donate
                            </a>
                        </nav>
                    </div>
                    <div class="flex-shrink-0 flex border-t border-secondary-700 p-4">
                        <a href="logout.php" class="flex-shrink-0 group block">
                            <div class="flex items-center">
                                <div>
                                    <i data-lucide="log-out" class="h-5 w-5 text-secondary-400 group-hover:text-primary-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-secondary-300 group-hover:text-white">Logout</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="flex-shrink-0 w-14"></div>
            </div>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-4 sm:p-6 lg:p-8">
                <div class="max-w-4xl mx-auto">
                    <!-- Wedding Booking Form -->
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-8">
                        <div class="bg-gradient-to-r from-accent-600 to-accent-700 px-6 py-4">
                            <h2 class="text-xl font-bold text-white font-heading">Wedding Service Request</h2>
                            <p class="text-accent-100 text-sm">Request our church for your special day</p>
                        </div>
                        <div class="p-6">
                            <?php if ($success): ?>
                                <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <i data-lucide="check-circle" class="h-5 w-5 text-green-500"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-green-700"><?= $success ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($error): ?>
                                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <i data-lucide="alert-circle" class="h-5 w-5 text-red-500"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-red-700"><?= $error ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <form method="post" class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="bride_name" class="block text-sm font-medium text-secondary-700">Bride's Full Name</label>
                                        <input type="text" name="bride_name" id="bride_name" required
                                            class="mt-1 block w-full border-secondary-300 rounded-md shadow-sm focus:ring-accent-500 focus:border-accent-500 sm:text-sm"
                                            value="<?= isset($_POST['bride_name']) ? htmlspecialchars($_POST['bride_name']) : '' ?>">
                                    </div>
                                    
                                    <div>
                                        <label for="groom_name" class="block text-sm font-medium text-secondary-700">Groom's Full Name</label>
                                        <input type="text" name="groom_name" id="groom_name" required
                                            class="mt-1 block w-full border-secondary-300 rounded-md shadow-sm focus:ring-accent-500 focus:border-accent-500 sm:text-sm"
                                            value="<?= isset($_POST['groom_name']) ? htmlspecialchars($_POST['groom_name']) : '' ?>">
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="bride_phone" class="block text-sm font-medium text-secondary-700">Bride's Phone Number</label>
                                        <input type="tel" name="bride_phone" id="bride_phone" required
                                            class="mt-1 block w-full border-secondary-300 rounded-md shadow-sm focus:ring-accent-500 focus:border-accent-500 sm:text-sm"
                                            value="<?= isset($_POST['bride_phone']) ? htmlspecialchars($_POST['bride_phone']) : '' ?>">
                                    </div>
                                    
                                    <div>
                                        <label for="groom_phone" class="block text-sm font-medium text-secondary-700">Groom's Phone Number</label>
                                        <input type="tel" name="groom_phone" id="groom_phone" required
                                            class="mt-1 block w-full border-secondary-300 rounded-md shadow-sm focus:ring-accent-500 focus:border-accent-500 sm:text-sm"
                                            value="<?= isset($_POST['groom_phone']) ? htmlspecialchars($_POST['groom_phone']) : '' ?>">
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="email" class="block text-sm font-medium text-secondary-700">Contact Email</label>
                                    <input type="email" name="email" id="email" required
                                        class="mt-1 block w-full border-secondary-300 rounded-md shadow-sm focus:ring-accent-500 focus:border-accent-500 sm:text-sm"
                                        value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : htmlspecialchars($user['email'] ?? '') ?>">
                                </div>
                                
                                <div>
                                    <label for="wedding_date" class="block text-sm font-medium text-secondary-700">Proposed Wedding Date</label>
                                    <input type="date" name="wedding_date" id="wedding_date" required
                                        class="mt-1 block w-full border-secondary-300 rounded-md shadow-sm focus:ring-accent-500 focus:border-accent-500 sm:text-sm"
                                        min="<?= date('Y-m-d', strtotime('+30 days')) ?>"
                                        value="<?= isset($_POST['wedding_date']) ? htmlspecialchars($_POST['wedding_date']) : '' ?>">
                                    <p class="mt-1 text-sm text-secondary-500">Please select a date at least 30 days in the future.</p>
                                </div>
                                
                                <div>
                                    <label for="notes" class="block text-sm font-medium text-secondary-700">Additional Notes</label>
                                    <textarea name="notes" id="notes" rows="4"
                                        class="mt-1 block w-full border-secondary-300 rounded-md shadow-sm focus:ring-accent-500 focus:border-accent-500 sm:text-sm"><?= isset($_POST['notes']) ? htmlspecialchars($_POST['notes']) : '' ?></textarea>
                                    <p class="mt-1 text-sm text-secondary-500">Please include any special requests or additional information.</p>
                                </div>
                                
                                <div>
                                    <button type="submit"
                                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-accent-600 hover:bg-accent-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-500 transition-colors">
                                        <i data-lucide="heart" class="mr-2 h-5 w-5"></i>
                                        Submit Wedding Request
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Wedding Booking History -->
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                            <h2 class="text-xl font-bold text-secondary-900 font-heading">Your Wedding Booking Requests</h2>
                        </div>
                        <div class="p-6">
                            <?php if ($weddings->num_rows > 0): ?>
                                <div class="space-y-6">
                                    <?php while($row = $weddings->fetch_assoc()): ?>
                                        <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                                            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                                                <div>
                                                    <h3 class="text-lg font-medium text-secondary-900">
                                                        <?= htmlspecialchars($row['bride_name']) ?> & <?= htmlspecialchars($row['groom_name']) ?>
                                                    </h3>
                                                    <p class="text-sm text-secondary-500">
                                                        <i data-lucide="calendar" class="inline-block h-4 w-4 mr-1 text-secondary-400"></i>
                                                        <?= date('F j, Y', strtotime($row['wedding_date'])) ?>
                                                    </p>
                                                </div>
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                                    <?= $row['status'] === 'approved' ? 'bg-green-100 text-green-800' : 
                                                       ($row['status'] === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') ?>">
                                                    <?= ucfirst($row['status']) ?>
                                                </span>
                                            </div>
                                            <div class="px-6 py-4">
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                                    <div>
                                                        <h4 class="text-sm font-medium text-secondary-500">Contact Information</h4>
                                                        <p class="text-sm text-secondary-900">
                                                            <span class="block">Bride: <?= htmlspecialchars($row['bride_phone']) ?></span>
                                                            <span class="block">Groom: <?= htmlspecialchars($row['groom_phone']) ?></span>
                                                            <span class="block">Email: <?= htmlspecialchars($row['email']) ?></span>
                                                        </p>
                                                    </div>
                                                    <?php if (!empty($row['notes'])): ?>
                                                    <div>
                                                        <h4 class="text-sm font-medium text-secondary-500">Additional Notes</h4>
                                                        <p class="text-sm text-secondary-900"><?= htmlspecialchars($row['notes']) ?></p>
                                                    </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="mt-4 text-sm">
                                                    <p class="text-secondary-500">
                                                        <i data-lucide="info" class="inline-block h-4 w-4 mr-1 text-secondary-400"></i>
                                                        Request submitted on <?= date('F j, Y', strtotime($row['created_at'])) ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-8">
                                    <i data-lucide="heart-off" class="h-12 w-12 mx-auto text-secondary-300 mb-4"></i>
                                    <p class="text-secondary-500">No wedding bookings found. Submit your first request above.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Mobile menu toggle
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            document.getElementById('mobile-sidebar').classList.toggle('hidden');
        });

        document.getElementById('close-sidebar').addEventListener('click', function() {
            document.getElementById('mobile-sidebar').classList.add('hidden');
        });

        // User dropdown toggle
        document.getElementById('user-menu-button').addEventListener('click', function() {
            document.getElementById('user-menu').classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        window.addEventListener('click', function(e) {
            if (!document.getElementById('user-menu-button').contains(e.target)) {
                document.getElementById('user-menu').classList.add('hidden');
            }
        });
    </script>
</body>
</html>

