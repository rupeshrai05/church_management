<?php
session_start();
include 'config.php';

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

$status_filter = $_GET['status'] ?? 'pending';
$valid_statuses = ['all', 'pending', 'approved', 'rejected'];
$status_filter = in_array($status_filter, $valid_statuses) ? $status_filter : 'pending';

$sql = "SELECT * FROM appointments";
if ($status_filter !== 'all') {
    $sql .= " WHERE status='$status_filter'";
}

$appointments = $conn->query($sql);

// Get counts for dashboard
$pending_appts = $conn->query("SELECT COUNT(*) as count FROM appointments WHERE status='pending'")->fetch_assoc()['count'];
$pending_weddings = $conn->query("SELECT COUNT(*) as count FROM weddings WHERE status='pending'")->fetch_assoc()['count'];
$total_members = $conn->query("SELECT COUNT(*) as count FROM users WHERE is_admin=0")->fetch_assoc()['count'];
$total_events = $conn->query("SELECT COUNT(*) as count FROM events")->fetch_assoc()['count'];
$total_donations = $conn->query("SELECT SUM(amount) as total FROM donations")->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Grace Community Church</title>
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
                            A
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-white">Admin</p>
                            <p class="text-xs font-medium text-primary-200">Administrator</p>
                        </div>
                    </div>
                    <nav class="flex-1 space-y-1">
                        <a href="admin_dashboard.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-md bg-secondary-700 text-white group">
                            <i data-lucide="layout-dashboard" class="mr-3 h-5 w-5 text-primary-400"></i>
                            Dashboard
                        </a>
                        <a href="admin_weddings.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-md text-secondary-300 hover:bg-secondary-700 hover:text-white group">
                            <i data-lucide="heart" class="mr-3 h-5 w-5 text-secondary-400 group-hover:text-primary-400"></i>
                            Weddings
                        </a>
                        <a href="admin_events.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-md text-secondary-300 hover:bg-secondary-700 hover:text-white group">
                            <i data-lucide="calendar-days" class="mr-3 h-5 w-5 text-secondary-400 group-hover:text-primary-400"></i>
                            Events
                        </a>
                        <a href="admin_members.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-md text-secondary-300 hover:bg-secondary-700 hover:text-white group">
                            <i data-lucide="users" class="mr-3 h-5 w-5 text-secondary-400 group-hover:text-primary-400"></i>
                            Members
                        </a>
                        <a href="admin_donations.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-md text-secondary-300 hover:bg-secondary-700 hover:text-white group">
                            <i data-lucide="banknote" class="mr-3 h-5 w-5 text-secondary-400 group-hover:text-primary-400"></i>
                            Donations
                        </a>
                        <a href="admin_users.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-md text-secondary-300 hover:bg-secondary-700 hover:text-white group">
                            <i data-lucide="user-cog" class="mr-3 h-5 w-5 text-secondary-400 group-hover:text-primary-400"></i>
                            User Management
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
                        <h1 class="ml-2 md:ml-0 text-xl font-semibold text-secondary-900">Admin Dashboard</h1>
                    </div>
                    <div class="flex items-center">
                        <a href="index.html" class="text-secondary-500 hover:text-secondary-700 mr-4">
                            <i data-lucide="home" class="h-5 w-5"></i>
                        </a>
                        <div class="relative">
                            <button type="button" class="flex items-center max-w-xs text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500" id="user-menu-button">
                                <span class="sr-only">Open user menu</span>
                                <div class="h-8 w-8 rounded-full bg-primary-600 flex items-center justify-center text-white">
                                    A
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
                            <a href="admin_dashboard.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-md bg-secondary-700 text-white group">
                                <i data-lucide="layout-dashboard" class="mr-3 h-5 w-5 text-primary-400"></i>
                                Dashboard
                            </a>
                            <a href="admin_weddings.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-md text-secondary-300 hover:bg-secondary-700 hover:text-white group">
                                <i data-lucide="heart" class="mr-3 h-5 w-5 text-secondary-400 group-hover:text-primary-400"></i>
                                Weddings
                            </a>
                            <a href="admin_events.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-md text-secondary-300 hover:bg-secondary-700 hover:text-white group">
                                <i data-lucide="calendar-days" class="mr-3 h-5 w-5 text-secondary-400 group-hover:text-primary-400"></i>
                                Events
                            </a>
                            <a href="admin_members.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-md text-secondary-300 hover:bg-secondary-700 hover:text-white group">
                                <i data-lucide="users" class="mr-3 h-5 w-5 text-secondary-400 group-hover:text-primary-400"></i>
                                Members
                            </a>
                            <a href="admin_donations.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-md text-secondary-300 hover:bg-secondary-700 hover:text-white group">
                                <i data-lucide="banknote" class="mr-3 h-5 w-5 text-secondary-400 group-hover:text-primary-400"></i>
                                Donations
                            </a>
                            <a href="admin_users.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-md text-secondary-300 hover:bg-secondary-700 hover:text-white group">
                                <i data-lucide="user-cog" class="mr-3 h-5 w-5 text-secondary-400 group-hover:text-primary-400"></i>
                                User Management
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
                <!-- Dashboard Stats -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Pending Appointments -->
                    <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-primary-500">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-primary-100 rounded-md p-3">
                                <i data-lucide="calendar" class="h-6 w-6 text-primary-600"></i>
                            </div>
                            <div class="ml-5">
                                <p class="text-sm font-medium text-secondary-500">Pending Appointments</p>
                                <h3 class="text-xl font-semibold text-secondary-900"><?= $pending_appts ?></h3>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="admin_dashboard.php?status=pending" class="text-sm font-medium text-primary-600 hover:text-primary-700">
                                View pending appointments <i data-lucide="arrow-right" class="inline-block h-4 w-4 ml-1"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Pending Weddings -->
                    <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-accent-500">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-accent-100 rounded-md p-3">
                                <i data-lucide="heart" class="h-6 w-6 text-accent-600"></i>
                            </div>
                            <div class="ml-5">
                                <p class="text-sm font-medium text-secondary-500">Pending Weddings</p>
                                <h3 class="text-xl font-semibold text-secondary-900"><?= $pending_weddings ?></h3>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="admin_weddings.php?status=pending" class="text-sm font-medium text-accent-600 hover:text-accent-700">
                                View pending weddings <i data-lucide="arrow-right" class="inline-block h-4 w-4 ml-1"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Total Members -->
                    <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                                <i data-lucide="users" class="h-6 w-6 text-green-600"></i>
                            </div>
                            <div class="ml-5">
                                <p class="text-sm font-medium text-secondary-500">Total Members</p>
                                <h3 class="text-xl font-semibold text-secondary-900"><?= $total_members ?></h3>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="admin_members.php" class="text-sm font-medium text-green-600 hover:text-green-700">
                                View all members <i data-lucide="arrow-right" class="inline-block h-4 w-4 ml-1"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Total Donations -->
                    <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-purple-500">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-purple-100 rounded-md p-3">
                                <i data-lucide="banknote" class="h-6 w-6 text-purple-600"></i>
                            </div>
                            <div class="ml-5">
                                <p class="text-sm font-medium text-secondary-500">Total Donations</p>
                                <h3 class="text-xl font-semibold text-secondary-900">₹<?= number_format($total_donations, 2) ?></h3>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="admin_donations.php" class="text-sm font-medium text-purple-600 hover:text-purple-700">
                                View all donations <i data-lucide="arrow-right" class="inline-block h-4 w-4 ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Appointment Filters -->
                <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                    <div class="flex flex-wrap gap-4">
                        <a href="?status=all" class="inline-flex items-center px-4 py-2 <?= $status_filter === 'all' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-secondary-700' ?> rounded-md hover:bg-primary-700 hover:text-white transition-colors">
                            <i data-lucide="list" class="mr-2 h-4 w-4"></i>
                            All Appointments
                        </a>
                        <a href="?status=pending" class="inline-flex items-center px-4 py-2 <?= $status_filter === 'pending' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-secondary-700' ?> rounded-md hover:bg-primary-700 hover:text-white transition-colors">
                            <i data-lucide="clock" class="mr-2 h-4 w-4"></i>
                            Pending
                        </a>
                        <a href="?status=approved" class="inline-flex items-center px-4 py-2 <?= $status_filter === 'approved' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-secondary-700' ?> rounded-md hover:bg-primary-700 hover:text-white transition-colors">
                            <i data-lucide="check-circle" class="mr-2 h-4 w-4"></i>
                            Approved
                        </a>
                        <a href="?status=rejected" class="inline-flex items-center px-4 py-2 <?= $status_filter === 'rejected' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-secondary-700' ?> rounded-md hover:bg-primary-700 hover:text-white transition-colors">
                            <i data-lucide="x-circle" class="mr-2 h-4 w-4"></i>
                            Rejected
                        </a>
                    </div>
                </div>

                <!-- Appointments Table -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-xl font-bold text-secondary-900 font-heading">Appointments (<?= ucfirst($status_filter) ?>)</h2>
                    </div>
                    <div class="p-6">
                        <?php if ($appointments->num_rows > 0): ?>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Name</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Contact</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Date/Time</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Description</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php while($row = $appointments->fetch_assoc()): ?>
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-secondary-900"><?= htmlspecialchars($row['full_name']) ?></div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-secondary-900"><?= htmlspecialchars($row['email']) ?></div>
                                                    <div class="text-sm text-secondary-500"><?= htmlspecialchars($row['phone']) ?></div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-secondary-900"><?= date('F j, Y', strtotime($row['appt_date'])) ?></div>
                                                    <div class="text-sm text-secondary-500"><?= date('g:i A', strtotime($row['appt_time'])) ?></div>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <div class="text-sm text-secondary-900"><?= htmlspecialchars($row['description']) ?></div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <?php if($row['status'] === 'pending'): ?>
                                                        <a href="approve.php?type=appointment&id=<?= $row['id'] ?>&action=approve&status=<?= $status_filter ?>" 
                                                           class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 mr-2">
                                                            <i data-lucide="check" class="h-3 w-3 mr-1"></i> Approve
                                                        </a>
                                                        <a href="approve.php?type=appointment&id=<?= $row['id'] ?>&action=reject&status=<?= $status_filter ?>" 
                                                           class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                            <i data-lucide="x" class="h-3 w-3 mr-1"></i> Reject
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                            <?= $row['status'] === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                                            <?= ucfirst($row['status']) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-8">
                                <i data-lucide="calendar-x" class="h-12 w-12 mx-auto text-secondary-300 mb-4"></i>
                                <p class="text-secondary-500">No <?= $status_filter !== 'all' ? $status_filter : '' ?> appointments found.</p>
                            </div>
                        <?php endif; ?>
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
