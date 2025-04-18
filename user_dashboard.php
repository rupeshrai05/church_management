<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user information
$user_id = $_SESSION['user_id'];
$user_query = $conn->query("SELECT * FROM users WHERE id = $user_id");
$user = $user_query->fetch_assoc();

// Get appointment count
$appt_query = $conn->query("SELECT COUNT(*) as count FROM appointments WHERE user_id = $user_id");
$appt_count = $appt_query->fetch_assoc()['count'];

// Get wedding request count
$wedding_query = $conn->query("SELECT COUNT(*) as count FROM weddings WHERE user_id = $user_id");
$wedding_count = $wedding_query->fetch_assoc()['count'];

// Get upcoming appointments
$upcoming_appts = $conn->query("SELECT * FROM appointments WHERE user_id = $user_id AND appt_date >= CURDATE() ORDER BY appt_date ASC LIMIT 3");

// Get upcoming events
$events = $conn->query("SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT 3");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Grace Community Church</title>
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
                        <a href="user_dashboard.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-md bg-secondary-700 text-white group">
                            <i data-lucide="layout-dashboard" class="mr-3 h-5 w-5 text-primary-400"></i>
                            Dashboard
                        </a>
                        <a href="user_appointments.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-md text-secondary-300 hover:bg-secondary-700 hover:text-white group">
                            <i data-lucide="calendar" class="mr-3 h-5 w-5 text-secondary-400 group-hover:text-primary-400"></i>
                            Appointments
                        </a>
                        <a href="user_weddings.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-md text-secondary-300 hover:bg-secondary-700 hover:text-white group">
                            <i data-lucide="heart" class="mr-3 h-5 w-5 text-secondary-400 group-hover:text-primary-400"></i>
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
                        <h1 class="ml-2 md:ml-0 text-xl font-semibold text-secondary-900">Member Dashboard</h1>
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
                                <a href="#" id="profile" class="block px-4 py-2 text-sm text-secondary-700 hover:bg-gray-100">Your Profile</a>
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
                            <a href="user_dashboard.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-md bg-secondary-700 text-white group">
                                <i data-lucide="layout-dashboard" class="mr-3 h-5 w-5 text-primary-400"></i>
                                Dashboard
                            </a>
                            <a href="user_appointments.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-md text-secondary-300 hover:bg-secondary-700 hover:text-white group">
                                <i data-lucide="calendar" class="mr-3 h-5 w-5 text-secondary-400 group-hover:text-primary-400"></i>
                                Appointments
                            </a>
                            <a href="user_weddings.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-md text-secondary-300 hover:bg-secondary-700 hover:text-white group">
                                <i data-lucide="heart" class="mr-3 h-5 w-5 text-secondary-400 group-hover:text-primary-400"></i>
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
                <!-- Welcome Banner -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-8">
                    <div class="bg-gradient-to-r from-primary-600 to-primary-800 px-6 py-8 md:py-10 md:px-8">
                        <div class="flex flex-col md:flex-row items-start md:items-center justify-between">
                            <div>
                                <h2 class="text-2xl font-bold text-white font-heading">Welcome, <?= htmlspecialchars($user['full_name'] ?? 'Friend') ?>!</h2>
                                <p class="mt-1 text-primary-100">Welcome to your personal dashboard at Grace Community Church.</p>
                            </div>
                            <div class="mt-4 md:mt-0">
                                <a href="events.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-primary-700 bg-white hover:bg-primary-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-colors">
                                    <i data-lucide="calendar-days" class="mr-2 h-5 w-5 text-primary-500"></i>
                                    View Upcoming Events
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <!-- Appointments Card -->
                    <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-primary-500">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-primary-100 rounded-md p-3">
                                <i data-lucide="calendar" class="h-6 w-6 text-primary-600"></i>
                            </div>
                            <div class="ml-5">
                                <p class="text-sm font-medium text-secondary-500">Your Appointments</p>
                                <h3 class="text-xl font-semibold text-secondary-900"><?= $appt_count ?></h3>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="user_appointments.php" class="text-sm font-medium text-primary-600 hover:text-primary-700">
                                View all appointments <i data-lucide="arrow-right" class="inline-block h-4 w-4 ml-1"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Wedding Requests Card -->
                    <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-accent-500">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-accent-100 rounded-md p-3">
                                <i data-lucide="heart" class="h-6 w-6 text-accent-600"></i>
                            </div>
                            <div class="ml-5">
                                <p class="text-sm font-medium text-secondary-500">Wedding Requests</p>
                                <h3 class="text-xl font-semibold text-secondary-900"><?= $wedding_count ?></h3>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="user_weddings.php" class="text-sm font-medium text-accent-600 hover:text-accent-700">
                                View all wedding requests <i data-lucide="arrow-right" class="inline-block h-4 w-4 ml-1"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Quick Actions Card -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-medium text-secondary-900 mb-4">Quick Actions</h3>
                        <div class="space-y-3">
                            <a href="user_appointments.php" class="flex items-center p-2 rounded-md hover:bg-gray-50 transition-colors">
                                <i data-lucide="plus-circle" class="h-5 w-5 text-primary-500 mr-3"></i>
                                <span class="text-secondary-700">Book an Appointment</span>
                            </a>
                            <a href="user_weddings.php" class="flex items-center p-2 rounded-md hover:bg-gray-50 transition-colors">
                                <i data-lucide="heart" class="h-5 w-5 text-accent-500 mr-3"></i>
                                <span class="text-secondary-700">Request Wedding Service</span>
                            </a>
                            <a href="donation.php" class="flex items-center p-2 rounded-md hover:bg-gray-50 transition-colors">
                                <i data-lucide="hand-heart" class="h-5 w-5 text-green-500 mr-3"></i>
                                <span class="text-secondary-700">Make a Donation</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Upcoming Appointments -->
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-lg font-medium text-secondary-900">Your Upcoming Appointments</h3>
                        </div>
                        <div class="p-6">
                            <?php if ($upcoming_appts->num_rows > 0): ?>
                                <ul class="divide-y divide-gray-200">
                                    <?php while($appt = $upcoming_appts->fetch_assoc()): ?>
                                        <li class="py-4">
                                            <div class="flex items-start">
                                                <div class="flex-shrink-0 bg-primary-100 rounded-md p-2">
                                                    <i data-lucide="calendar" class="h-5 w-5 text-primary-600"></i>
                                                </div>
                                                <div class="ml-4 flex-1">
                                                    <div class="flex items-center justify-between">
                                                        <h4 class="text-sm font-medium text-secondary-900"><?= htmlspecialchars($appt['description']) ?></h4>
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $appt['status'] === 'approved' ? 'bg-green-100 text-green-800' : ($appt['status'] === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') ?>">
                                                            <?= ucfirst($appt['status']) ?>
                                                        </span>
                                                    </div>
                                                    <p class="mt-1 text-sm text-secondary-500">
                                                        <i data-lucide="clock" class="inline-block h-4 w-4 mr-1 text-secondary-400"></i>
                                                        <?= date('F j, Y', strtotime($appt['appt_date'])) ?> at <?= date('g:i A', strtotime($appt['appt_time'])) ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endwhile; ?>
                                </ul>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i data-lucide="calendar-x" class="h-12 w-12 mx-auto text-secondary-300 mb-4"></i>
                                    <p class="text-secondary-500">No upcoming appointments</p>
                                    <a href="user_appointments.php" class="mt-2 inline-flex items-center text-sm text-primary-600 hover:text-primary-700">
                                        Book an appointment <i data-lucide="arrow-right" class="ml-1 h-4 w-4"></i>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Upcoming Events -->
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-lg font-medium text-secondary-900">Upcoming Church Events</h3>
                        </div>
                        <div class="p-6">
                            <?php if ($events->num_rows > 0): ?>
                                <ul class="divide-y divide-gray-200">
                                    <?php while($event = $events->fetch_assoc()): ?>
                                        <li class="py-4">
                                            <div class="flex items-start">
                                                <div class="flex-shrink-0 bg-accent-100 rounded-md p-2">
                                                    <i data-lucide="calendar-days" class="h-5 w-5 text-accent-600"></i>
                                                </div>
                                                <div class="ml-4 flex-1">
                                                    <div class="flex items-center justify-between">
                                                        <h4 class="text-sm font-medium text-secondary-900"><?= htmlspecialchars($event['event_name']) ?></h4>
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $event['event_type'] === 'paid' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' ?>">
                                                            <?= ucfirst($event['event_type']) ?>
                                                        </span>
                                                    </div>
                                                    <p class="mt-1 text-sm text-secondary-500">
                                                        <i data-lucide="clock" class="inline-block h-4 w-4 mr-1 text-secondary-400"></i>
                                                        <?= date('F j, Y', strtotime($event['event_date'])) ?> at <?= date('g:i A', strtotime($event['event_time'])) ?>
                                                    </p>
                                                    <p class="mt-1 text-sm text-secondary-500">
                                                        <i data-lucide="map-pin" class="inline-block h-4 w-4 mr-1 text-secondary-400"></i>
                                                        <?= htmlspecialchars($event['address']) ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endwhile; ?>
                                </ul>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i data-lucide="calendar-x" class="h-12 w-12 mx-auto text-secondary-300 mb-4"></i>
                                    <p class="text-secondary-500">No upcoming events</p>
                                    <a href="events.php" class="mt-2 inline-flex items-center text-sm text-primary-600 hover:text-primary-700">
                                        View all events <i data-lucide="arrow-right" class="ml-1 h-4 w-4"></i>
                                    </a>
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
        document.getElementById('profile').addEventListener('click', function() {
           alert("Profile feature will available soon.");
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
