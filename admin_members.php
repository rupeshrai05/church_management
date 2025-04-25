<?php
session_start();
include 'config.php';

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
  header("Location: login.php");
  exit();
}

$success = '';
$error = '';

// Handle member addition
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $name = $_POST['name'];
  $position = $_POST['position'];
  $phone = $_POST['phone'];
  $email = $_POST['email'];
  $member_id = $_POST['member_id'];

  try {
      $stmt = $conn->prepare("INSERT INTO members (name, position, phone, email, member_id) 
                            VALUES (?, ?, ?, ?, ?)");
      $stmt->bind_param("sssss", $name, $position, $phone, $email, $member_id);
      
      if ($stmt->execute()) {
          $success = "Member added successfully!";
      } else {
          $error = "Error adding member. Please try again.";
      }
  } catch (Exception $e) {
      $error = "Database error: " . $e->getMessage();
  }
}

// Get all members
$members = $conn->query("SELECT * FROM members ORDER BY name");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Members Directory - Grace Community Church</title>
  
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
              50: '#fff7ed',
              100: '#ffedd5',
              200: '#fed7aa',
              300: '#fdba74',
              400: '#fb923c',
              500: '#f97316',
              600: '#ea580c',
              700: '#c2410c',
              800: '#9a3412',
              900: '#7c2d12',
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
                        <a href="admin_members.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-md bg-secondary-700 text-white group">
                            <i data-lucide="users" class="mr-3 h-5 w-5 text-primary-400"></i>
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
                        <h1 class="ml-2 md:ml-0 text-xl font-semibold text-secondary-900">Members Directory</h1>
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
                            <a href="admin_members.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-md bg-secondary-700 text-white group">
                                <i data-lucide="users" class="mr-3 h-5 w-5 text-primary-400"></i>
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
                <!-- Page header -->
                <div class="px-4 sm:px-0 mb-8">
                    <h1 class="text-2xl font-heading font-bold text-secondary-900">Members Directory</h1>
                    <p class="mt-2 text-sm text-secondary-600">
                      Manage church members, add new members, and view member information.
                    </p>
                </div>
                
                <!-- Add Member Form -->
                <div class="px-4 sm:px-0 mb-8">
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg divide-y divide-gray-200">
                        <div class="px-6 py-5 bg-white">
                            <div class="flex items-center">
                                <i data-lucide="user-plus" class="h-5 w-5 text-primary-600 mr-2"></i>
                                <h3 class="text-lg font-medium leading-6 text-secondary-900">Add New Member</h3>
                            </div>
                            <p class="mt-1 text-sm text-secondary-500">
                              Fill out the form below to add a new member to the church directory.
                            </p>
                        </div>
                        
                        <div class="px-6 py-5 bg-white">
                            <?php if ($success): ?>
                              <div class="rounded-md bg-green-50 p-4 mb-6">
                                <div class="flex">
                                  <div class="flex-shrink-0">
                                    <i data-lucide="check-circle" class="h-5 w-5 text-green-400"></i>
                                  </div>
                                  <div class="ml-3">
                                    <p class="text-sm font-medium text-green-800"><?= $success ?></p>
                                  </div>
                                </div>
                              </div>
                            <?php endif; ?>
                            
                            <?php if ($error): ?>
                              <div class="rounded-md bg-red-50 p-4 mb-6">
                                <div class="flex">
                                  <div class="flex-shrink-0">
                                    <i data-lucide="alert-circle" class="h-5 w-5 text-red-400"></i>
                                  </div>
                                  <div class="ml-3">
                                    <p class="text-sm font-medium text-red-800"><?= $error ?></p>
                                  </div>
                                </div>
                              </div>
                            <?php endif; ?>
                            
                            <form method="post" class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-secondary-700">Full Name</label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i data-lucide="user" class="h-5 w-5 text-secondary-400"></i>
                                            </div>
                                            <input type="text" name="name" id="name" required
                                              class="focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md"
                                              placeholder="John Doe">
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label for="position" class="block text-sm font-medium text-secondary-700">Position/Role</label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i data-lucide="briefcase" class="h-5 w-5 text-secondary-400"></i>
                                            </div>
                                            <input type="text" name="position" id="position"
                                              class="focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md"
                                              placeholder="Elder, Deacon, Volunteer, etc.">
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label for="phone" class="block text-sm font-medium text-secondary-700">Phone Number</label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i data-lucide="phone" class="h-5 w-5 text-secondary-400"></i>
                                            </div>
                                            <input type="tel" name="phone" id="phone"
                                              class="focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md"
                                              placeholder="(123) 456-7890">
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-secondary-700">Email Address</label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i data-lucide="mail" class="h-5 w-5 text-secondary-400"></i>
                                            </div>
                                            <input type="email" name="email" id="email"
                                              class="focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md"
                                              placeholder="johndoe@example.com">
                                        </div>
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="member_id" class="block text-sm font-medium text-secondary-700">Member ID</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i data-lucide="id-card" class="h-5 w-5 text-secondary-400"></i>
                                        </div>
                                        <input type="text" name="member_id" id="member_id" required
                                          class="focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md"
                                          placeholder="MEM12345">
                                    </div>
                                    <p class="mt-2 text-sm text-secondary-500">
                                      A unique identifier for this member. This will be used for tracking attendance and participation.
                                    </p>
                                </div>
                                
                                <div>
                                    <button type="submit"
                                      class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                      <i data-lucide="user-plus" class="h-5 w-5 mr-2"></i>
                                      Add Member
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Members Directory -->
                <div class="px-4 sm:px-0">
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg divide-y divide-gray-200">
                        <div class="px-6 py-5 bg-white">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i data-lucide="users" class="h-5 w-5 text-primary-600 mr-2"></i>
                                    <h3 class="text-lg font-medium leading-6 text-secondary-900">Members Directory</h3>
                                </div>
                                <div class="relative">
                                    <input type="text" id="search-members" placeholder="Search members..." 
                                      class="focus:ring-primary-500 focus:border-primary-500 block w-full pr-10 sm:text-sm border-gray-300 rounded-md">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <i data-lucide="search" class="h-5 w-5 text-secondary-400"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Member</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Position</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Contact Information</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Member ID</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php if ($members->num_rows > 0): ?>
                                      <?php while($row = $members->fetch_assoc()): ?>
                                      <tr class="hover:bg-gray-50 member-row">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 bg-primary-100 rounded-full flex items-center justify-center text-primary-600">
                                                    <span class="font-medium text-lg"><?= strtoupper(substr($row['name'], 0, 1)) ?></span>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-secondary-900 member-name"><?= htmlspecialchars($row['name']) ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php if (!empty($row['position'])): ?>
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                <?= htmlspecialchars($row['position']) ?>
                                            </span>
                                            <?php else: ?>
                                            <span class="text-secondary-500 text-sm">Not specified</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <?php if (!empty($row['phone'])): ?>
                                            <div class="text-sm text-secondary-900 flex items-center">
                                                <i data-lucide="phone" class="h-4 w-4 mr-1 text-secondary-400"></i>
                                                <?= htmlspecialchars($row['phone']) ?>
                                            </div>
                                            <?php endif; ?>
                                            <?php if (!empty($row['email'])): ?>
                                            <div class="text-sm text-secondary-900 flex items-center">
                                                <i data-lucide="mail" class="h-4 w-4 mr-1 text-secondary-400"></i>
                                                <?= htmlspecialchars($row['email']) ?>
                                            </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary-900">
                                            <div class="flex items-center">
                                                <i data-lucide="id-card" class="h-4 w-4 mr-1 text-secondary-400"></i>
                                                <?= htmlspecialchars($row['member_id']) ?>
                                            </div>
                                        </td>
                                      </tr>
                                      <?php endwhile; ?>
                                    <?php else: ?>
                                      <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-sm text-secondary-500">
                                          No members found. Add your first member using the form above.
                                        </td>
                                      </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
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
        
        // Simple search functionality
        document.getElementById('search-members').addEventListener('keyup', function() {
          const searchValue = this.value.toLowerCase();
          const memberRows = document.querySelectorAll('.member-row');
          
          memberRows.forEach(row => {
            const memberName = row.querySelector('.member-name').textContent.toLowerCase();
            if (memberName.includes(searchValue)) {
              row.style.display = '';
            } else {
              row.style.display = 'none';
            }
          });
        });
    </script>
</body>
</html>