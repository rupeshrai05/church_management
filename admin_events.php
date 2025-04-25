<?php
session_start();
include 'config.php';

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
  header("Location: login.php");
  exit();
}

// Handle event creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_event'])) {
  $event_name = $_POST['event_name'];
  $event_date = $_POST['event_date'];
  $event_time = $_POST['event_time'];
  $description = $_POST['description'];
  $event_type = $_POST['event_type'];
  $address = $_POST['address'];
  $contact_details = $_POST['contact_details'];

  $stmt = $conn->prepare("INSERT INTO events (event_name, event_date, event_time, description, event_type, address, contact_details) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("sssssss", $event_name, $event_date, $event_time, $description, $event_type, $address, $contact_details);
  $stmt->execute();
}       

// Handle filtering
$filter = $_GET['filter'] ?? 'today';
$current_date = date('Y-m-d');

switch($filter) {
  case 'all':
      $sql = "SELECT * FROM events";
      break;
  case 'previous':
      $sql = "SELECT * FROM events WHERE event_date < '$current_date'";
      break;
  case 'upcoming':
      $sql = "SELECT * FROM events WHERE event_date > '$current_date'";
      break;
  default: // today
      $sql = "SELECT * FROM events WHERE event_date = '$current_date'";
      break;
}

$events = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Events - Grace Community Church</title>
  
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
                        <a href="admin_events.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-md bg-secondary-700 text-white group">
                            <i data-lucide="calendar-days" class="mr-3 h-5 w-5 text-primary-400"></i>
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
                        <h1 class="ml-2 md:ml-0 text-xl font-semibold text-secondary-900">Manage Events</h1>
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
                            <a href="admin_events.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-md bg-secondary-700 text-white group">
                                <i data-lucide="calendar-days" class="mr-3 h-5 w-5 text-primary-400"></i>
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
                <!-- Page header -->
                <div class="px-4 sm:px-0 mb-8">
                    <h1 class="text-2xl font-heading font-bold text-secondary-900">Manage Events</h1>
                    <p class="mt-2 text-sm text-secondary-600">
                      Create and manage church events, services, and gatherings.
                    </p>
                </div>
                
                <!-- Create Event Form -->
                <div class="px-4 sm:px-0 mb-8">
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg divide-y divide-gray-200">
                        <div class="px-6 py-5 bg-white">
                            <div class="flex items-center">
                                <i data-lucide="calendar-plus" class="h-5 w-5 text-primary-600 mr-2"></i>
                                <h3 class="text-lg font-medium leading-6 text-secondary-900">Create New Event</h3>
                            </div>
                            <p class="mt-1 text-sm text-secondary-500">
                              Fill out the form below to create a new church event.
                            </p>
                        </div>
                        
                        <div class="px-6 py-5 bg-white">
                            <form method="post" class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="event_name" class="block text-sm font-medium text-secondary-700">Event Name</label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i data-lucide="bookmark" class="h-5 w-5 text-secondary-400"></i>
                                            </div>
                                            <input type="text" name="event_name" id="event_name" required
                                              class="focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md"
                                              placeholder="Sunday Service">
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label for="event_type" class="block text-sm font-medium text-secondary-700">Event Type</label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i data-lucide="tag" class="h-5 w-5 text-secondary-400"></i>
                                            </div>
                                            <select name="event_type" id="event_type" 
                                              class="focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md">
                                              <option value="free">Free Event</option>
                                              <option value="paid">Paid Event</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label for="event_date" class="block text-sm font-medium text-secondary-700">Event Date</label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i data-lucide="calendar" class="h-5 w-5 text-secondary-400"></i>
                                            </div>
                                            <input type="date" name="event_date" id="event_date" required
                                              class="focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md"
                                              value="<?= date('Y-m-d') ?>" 
                                              min="<?= date('Y-m-d') ?>">
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label for="event_time" class="block text-sm font-medium text-secondary-700">Event Time</label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i data-lucide="clock" class="h-5 w-5 text-secondary-400"></i>
                                            </div>
                                            <input type="time" name="event_time" id="event_time" required
                                              class="focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md">
                                        </div>
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="description" class="block text-sm font-medium text-secondary-700">Description</label>
                                    <div class="mt-1">
                                        <textarea name="description" id="description" rows="3"
                                          class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                          placeholder="Describe the event details, purpose, and what attendees can expect."></textarea>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="address" class="block text-sm font-medium text-secondary-700">Address</label>
                                        <div class="mt-1">
                                            <textarea name="address" id="address" rows="2" required
                                              class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                              placeholder="123 Church Street, City, State, ZIP"></textarea>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label for="contact_details" class="block text-sm font-medium text-secondary-700">Contact Details</label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i data-lucide="phone" class="h-5 w-5 text-secondary-400"></i>
                                            </div>
                                            <input type="text" name="contact_details" id="contact_details"
                                              class="focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md"
                                              placeholder="(123) 456-7890 or events@church.org">
                                        </div>
                                    </div>
                                </div>
                                
                                <div>
                                    <button type="submit" name="create_event"
                                      class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                      <i data-lucide="plus-circle" class="h-5 w-5 mr-2"></i>
                                      Create Event
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Filter tabs -->
                <div class="px-4 sm:px-0 mb-6">
                    <div class="flex flex-wrap gap-2">
                        <a href="?filter=today" 
                           class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium <?= $filter === 'today' ? 'bg-primary-600 text-white' : 'bg-white text-secondary-700 hover:bg-gray-50 border border-gray-300' ?>">
                            <i data-lucide="calendar-check" class="h-4 w-4 mr-2"></i>
                            Today's Events
                        </a>
                        <a href="?filter=all" 
                           class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium <?= $filter === 'all' ? 'bg-primary-600 text-white' : 'bg-white text-secondary-700 hover:bg-gray-50 border border-gray-300' ?>">
                            <i data-lucide="layers" class="h-4 w-4 mr-2"></i>
                            All Events
                        </a>
                        <a href="?filter=previous" 
                           class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium <?= $filter === 'previous' ? 'bg-primary-600 text-white' : 'bg-white text-secondary-700 hover:bg-gray-50 border border-gray-300' ?>">
                            <i data-lucide="calendar-x" class="h-4 w-4 mr-2"></i>
                            Previous Events
                        </a>
                        <a href="?filter=upcoming" 
                           class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium <?= $filter === 'upcoming' ? 'bg-primary-600 text-white' : 'bg-white text-secondary-700 hover:bg-gray-50 border border-gray-300' ?>">
                            <i data-lucide="calendar-clock" class="h-4 w-4 mr-2"></i>
                            Upcoming Events
                        </a>
                    </div>
                </div>
                
                <!-- Events List -->
                <div class="px-4 sm:px-0">
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <?php if ($events->num_rows > 0): ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Event Details</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Date & Time</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Location</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Contact</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-secondary-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php while($row = $events->fetch_assoc()): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-<?= $row['event_type'] === 'paid' ? 'accent' : 'primary' ?>-100 flex items-center justify-center text-<?= $row['event_type'] === 'paid' ? 'accent' : 'primary' ?>-600">
                                                    <i data-lucide="<?= $row['event_type'] === 'paid' ? 'ticket' : 'calendar-days' ?>" class="h-5 w-5"></i>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-secondary-900"><?= htmlspecialchars($row['event_name']) ?></div>
                                                    <div class="text-sm text-secondary-500"><?= htmlspecialchars($row['description']) ?></div>
                                                    <div class="mt-1">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $row['event_type'] === 'paid' ? 'bg-accent-100 text-accent-800' : 'bg-green-100 text-green-800' ?>">
                                                            <?= ucfirst($row['event_type']) ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-secondary-900 flex items-center">
                                                <i data-lucide="calendar" class="h-4 w-4 mr-1 text-secondary-400"></i>
                                                <?= date('F j, Y', strtotime($row['event_date'])) ?>
                                            </div>
                                            <div class="text-sm text-secondary-900 flex items-center mt-1">
                                                <i data-lucide="clock" class="h-4 w-4 mr-1 text-secondary-400"></i>
                                                <?= date('h:i A', strtotime($row['event_time'])) ?>
                                            </div>
                                            <?php 
                                            $event_date = new DateTime($row['event_date'] . ' ' . $row['event_time']);
                                            $now = new DateTime();
                                            $interval = $now->diff($event_date);
                                            $is_past = $event_date < $now;
                                            
                                            if ($is_past) {
                                              echo '<div class="mt-1 text-xs text-red-600 flex items-center">';
                                              echo '<i data-lucide="alert-circle" class="h-3 w-3 mr-1"></i>';
                                              echo 'Event has passed';
                                              echo '</div>';
                                            } else if ($interval->days == 0) {
                                              echo '<div class="mt-1 text-xs text-accent-600 flex items-center">';
                                              echo '<i data-lucide="alert-circle" class="h-3 w-3 mr-1"></i>';
                                              echo 'Today';
                                              echo '</div>';
                                            } else if ($interval->days < 7) {
                                              echo '<div class="mt-1 text-xs text-primary-600 flex items-center">';
                                              echo '<i data-lucide="alert-circle" class="h-3 w-3 mr-1"></i>';
                                              echo 'Coming soon (' . $interval->days . ' days)';
                                              echo '</div>';
                                            }
                                            ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-secondary-900 flex items-start">
                                                <i data-lucide="map-pin" class="h-4 w-4 mr-1 text-secondary-400 mt-0.5"></i>
                                                <span><?= nl2br(htmlspecialchars($row['address'])) ?></span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-secondary-900 flex items-center">
                                                <i data-lucide="phone" class="h-4 w-4 mr-1 text-secondary-400"></i>
                                                <?= htmlspecialchars($row['contact_details']) ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="delete_event.php?id=<?= $row['id'] ?>" 
                                               onclick="return confirm('Are you sure you want to delete this event?')"
                                               class="inline-flex items-center text-red-600 hover:text-red-900">
                                              <i data-lucide="trash-2" class="h-4 w-4 mr-1"></i>
                                              Delete
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="py-12 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary-100 text-primary-600 mb-4">
                                <i data-lucide="calendar-x" class="h-8 w-8"></i>
                            </div>
                            <h3 class="mt-2 text-sm font-medium text-secondary-900">No events found</h3>
                            <p class="mt-1 text-sm text-secondary-500">
                              There are no <?= $filter !== 'all' ? $filter : '' ?> events at this time.
                            </p>
                            <div class="mt-6">
                                <button type="button" onclick="document.getElementById('event_name').focus()"
                                  class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                  <i data-lucide="plus-circle" class="h-5 w-5 mr-2"></i>
                                  Create an event
                                </button>
                            </div>
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