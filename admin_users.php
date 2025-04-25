<?php
session_start();
include 'config.php';

// Check if user is admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

// Handle user deletion
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $user_id = $_GET['id'];
    
    // Prevent admin from deleting themselves
    if ($user_id == $_SESSION['user_id']) {
        $delete_error = "You cannot delete your own account.";
    } else {
        $delete_stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $delete_stmt->bind_param("i", $user_id);
        
        if ($delete_stmt->execute()) {
            $success_message = "User deleted successfully.";
        } else {
            $delete_error = "Error deleting user.";
        }
        $delete_stmt->close();
    }
}

// Handle form submission for adding/editing users
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if we're editing or adding a user
    $edit_mode = isset($_POST['user_id']) && !empty($_POST['user_id']);
    
    $username = $_POST['username'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'] ?? '';
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;
    
    // For edit mode, only update password if provided
    if ($edit_mode) {
        $user_id = $_POST['user_id'];
        
        // Check if password is being updated
        if (!empty($_POST['password']) && !empty($_POST['confirm_password'])) {
            if ($_POST['password'] !== $_POST['confirm_password']) {
                $error = "Passwords do not match.";
            } else {
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET username=?, full_name=?, email=?, phone=?, password=?, is_admin=? WHERE id=?");
                $stmt->bind_param("sssssii", $username, $full_name, $email, $phone, $password, $is_admin, $user_id);
            }
        } else {
            // Update without changing password
            $stmt = $conn->prepare("UPDATE users SET username=?, full_name=?, email=?, phone=?, is_admin=? WHERE id=?");
            $stmt->bind_param("ssssii", $username, $full_name, $email, $phone, $is_admin, $user_id);
        }
        
        if (empty($error)) {
            if ($stmt->execute()) {
                $success = "User updated successfully.";
            } else {
                $error = "Error updating user. Username or email may already exist.";
            }
            $stmt->close();
        }
    } else {
        // Adding a new user
        if (empty($_POST['password']) || empty($_POST['confirm_password'])) {
            $error = "Password is required.";
        } else if ($_POST['password'] !== $_POST['confirm_password']) {
            $error = "Passwords do not match.";
        } else {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            
            $stmt = $conn->prepare("INSERT INTO users (username, full_name, email, phone, password, is_admin) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssi", $username, $full_name, $email, $phone, $password, $is_admin);
            
            if ($stmt->execute()) {
                $success = "User added successfully.";
            } else {
                $error = "Error adding user. Username or email may already exist.";
            }
            $stmt->close();
        }
    }
}

// Get user to edit if in edit mode
$edit_user = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $edit_stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $edit_stmt->bind_param("i", $user_id);
    $edit_stmt->execute();
    $edit_result = $edit_stmt->get_result();
    
    if ($edit_result->num_rows > 0) {
        $edit_user = $edit_result->fetch_assoc();
    }
    $edit_stmt->close();
}

// Get all users
$users_query = "SELECT * FROM users ORDER BY is_admin DESC, full_name ASC";
$users = $conn->query($users_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management - Grace Community Church</title>
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
                        <a href="admin_dashboard.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-md text-secondary-300 hover:bg-secondary-700 hover:text-white group">
                            <i data-lucide="layout-dashboard" class="mr-3 h-5 w-5 text-secondary-400 group-hover:text-primary-400"></i>
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
                        <a href="admin_users.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-md bg-secondary-700 text-white group">
                            <i data-lucide="user-cog" class="mr-3 h-5 w-5 text-primary-400"></i>
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
                        <h1 class="ml-2 md:ml-0 text-xl font-semibold text-secondary-900">User Management</h1>
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
                            <a href="admin_dashboard.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-md text-secondary-300 hover:bg-secondary-700 hover:text-white group">
                                <i data-lucide="layout-dashboard" class="mr-3 h-5 w-5 text-secondary-400 group-hover:text-primary-400"></i>
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
                            <a href="admin_users.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-md bg-secondary-700 text-white group">
                                <i data-lucide="user-cog" class="mr-3 h-5 w-5 text-primary-400"></i>
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
                <!-- Page Header -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-secondary-900 font-heading">User Management</h2>
                        <p class="mt-1 text-sm text-secondary-500">Manage user accounts and admin access</p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <a href="?action=add" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <i data-lucide="user-plus" class="h-4 w-4 mr-2"></i>
                            Add New User
                        </a>
                    </div>
                </div>

                <!-- Alerts -->
                <?php if (isset($success) && !empty($success)): ?>
                    <div class="rounded-md bg-green-50 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i data-lucide="check-circle" class="h-5 w-5 text-green-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800"><?= $success ?></p>
                            </div>
                            <div class="ml-auto pl-3">
                                <div class="-mx-1.5 -my-1.5">
                                    <button type="button" class="inline-flex bg-green-50 rounded-md p-1.5 text-green-500 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-600" onclick="this.parentElement.parentElement.parentElement.style.display='none'">
                                        <span class="sr-only">Dismiss</span>
                                        <i data-lucide="x" class="h-5 w-5"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($error) && !empty($error)): ?>
                    <div class="rounded-md bg-red-50 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i data-lucide="alert-circle" class="h-5 w-5 text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-800"><?= $error ?></p>
                            </div>
                            <div class="ml-auto pl-3">
                                <div class="-mx-1.5 -my-1.5">
                                    <button type="button" class="inline-flex bg-red-50 rounded-md p-1.5 text-red-500 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-600" onclick="this.parentElement.parentElement.parentElement.style.display='none'">
                                        <span class="sr-only">Dismiss</span>
                                        <i data-lucide="x" class="h-5 w-5"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($delete_error)): ?>
                    <div class="rounded-md bg-red-50 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i data-lucide="alert-circle" class="h-5 w-5 text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-800"><?= $delete_error ?></p>
                            </div>
                            <div class="ml-auto pl-3">
                                <div class="-mx-1.5 -my-1.5">
                                    <button type="button" class="inline-flex bg-red-50 rounded-md p-1.5 text-red-500 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-600" onclick="this.parentElement.parentElement.parentElement.style.display='none'">
                                        <span class="sr-only">Dismiss</span>
                                        <i data-lucide="x" class="h-5 w-5"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($success_message)): ?>
                    <div class="rounded-md bg-green-50 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i data-lucide="check-circle" class="h-5 w-5 text-green-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800"><?= $success_message ?></p>
                            </div>
                            <div class="ml-auto pl-3">
                                <div class="-mx-1.5 -my-1.5">
                                    <button type="button" class="inline-flex bg-green-50 rounded-md p-1.5 text-green-500 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-600" onclick="this.parentElement.parentElement.parentElement.style.display='none'">
                                        <span class="sr-only">Dismiss</span>
                                        <i data-lucide="x" class="h-5 w-5"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Add/Edit User Form -->
                <?php if ((isset($_GET['action']) && $_GET['action'] == 'add') || $edit_user): ?>
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-lg font-medium text-secondary-900 font-heading">
                                <?= $edit_user ? 'Edit User' : 'Add New User' ?>
                            </h3>
                        </div>
                        <div class="p-6">
                            <form method="post" action="admin_users.php">
                                <?php if ($edit_user): ?>
                                    <input type="hidden" name="user_id" value="<?= $edit_user['id'] ?>">
                                <?php endif; ?>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="username" class="block text-sm font-medium text-secondary-700">Username</label>
                                        <input type="text" name="username" id="username" required 
                                               class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-secondary-300 rounded-md"
                                               value="<?= $edit_user ? htmlspecialchars($edit_user['username']) : '' ?>">
                                    </div>
                                    
                                    <div>
                                        <label for="full_name" class="block text-sm font-medium text-secondary-700">Full Name</label>
                                        <input type="text" name="full_name" id="full_name" required 
                                               class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-secondary-300 rounded-md"
                                               value="<?= $edit_user ? htmlspecialchars($edit_user['full_name']) : '' ?>">
                                    </div>
                                    
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-secondary-700">Email</label>
                                        <input type="email" name="email" id="email" required 
                                               class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-secondary-300 rounded-md"
                                               value="<?= $edit_user ? htmlspecialchars($edit_user['email']) : '' ?>">
                                    </div>
                                    
                                    <div>
                                        <label for="phone" class="block text-sm font-medium text-secondary-700">Phone</label>
                                        <input type="text" name="phone" id="phone" 
                                               class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-secondary-300 rounded-md"
                                               value="<?= $edit_user ? htmlspecialchars($edit_user['phone']) : '' ?>">
                                    </div>
                                    
                                    <div>
                                        <label for="password" class="block text-sm font-medium text-secondary-700">
                                            Password <?= $edit_user ? '(Leave blank to keep current)' : '' ?>
                                        </label>
                                        <input type="password" name="password" id="password" <?= $edit_user ? '' : 'required' ?> 
                                               class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-secondary-300 rounded-md">
                                    </div>
                                    
                                    <div>
                                        <label for="confirm_password" class="block text-sm font-medium text-secondary-700">
                                            Confirm Password <?= $edit_user ? '(Leave blank to keep current)' : '' ?>
                                        </label>
                                        <input type="password" name="confirm_password" id="confirm_password" <?= $edit_user ? '' : 'required' ?> 
                                               class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-secondary-300 rounded-md">
                                    </div>
                                </div>
                                
                                <div class="mt-6">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="is_admin" name="is_admin" type="checkbox" 
                                                   class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-secondary-300 rounded"
                                                   <?= ($edit_user && $edit_user['is_admin'] == 1) ? 'checked' : '' ?>>
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="is_admin" class="font-medium text-secondary-700">Administrator Access</label>
                                            <p class="text-secondary-500">Grant this user administrative privileges</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-6 flex justify-end space-x-3">
                                    <a href="admin_users.php" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-secondary-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                        Cancel
                                    </a>
                                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                        <?= $edit_user ? 'Update User' : 'Add User' ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Users Table -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-medium text-secondary-900 font-heading">All Users</h3>
                    </div>
                    <div class="p-6">
                        <?php if ($users->num_rows > 0): ?>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Name</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Username</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Contact</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Role</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Created</th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-secondary-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php while($row = $users->fetch_assoc()): ?>
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-secondary-900"><?= htmlspecialchars($row['full_name']) ?></div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-secondary-900"><?= htmlspecialchars($row['username']) ?></div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-secondary-900"><?= htmlspecialchars($row['email']) ?></div>
                                                    <?php if (!empty($row['phone'])): ?>
                                                        <div class="text-sm text-secondary-500"><?= htmlspecialchars($row['phone']) ?></div>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <?php if ($row['is_admin'] == 1): ?>
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-primary-100 text-primary-800">
                                                            Administrator
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                            Member
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary-500">
                                                    <?= date('M j, Y', strtotime($row['created_at'])) ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <a href="?action=edit&id=<?= $row['id'] ?>" class="text-primary-600 hover:text-primary-900 mr-3">Edit</a>
                                                    <?php if ($row['id'] != $_SESSION['user_id']): ?>
                                                        <a href="?action=delete&id=<?= $row['id'] ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-8">
                                <i data-lucide="users-x" class="h-12 w-12 mx-auto text-secondary-300 mb-4"></i>
                                <p class="text-secondary-500">No users found.</p>
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
