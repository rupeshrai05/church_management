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
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
  
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
<body class="bg-gray-50 min-h-screen flex flex-col">
  <!-- Header -->
  <header class="bg-white shadow-sm border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <div class="flex items-center">
          <a href="admin_dashboard.php" class="flex-shrink-0 flex items-center">
            <span class="text-2xl text-primary-600 mr-2">
              <i data-lucide="church"></i>
            </span>
            <span class="font-heading font-bold text-xl text-secondary-800">Grace Admin</span>
          </a>
        </div>
        <nav class="hidden md:flex space-x-6">
          <a href="admin_dashboard.php" class="text-secondary-600 hover:text-primary-600 px-3 py-2 text-sm font-medium">
            <span class="flex items-center">
              <i data-lucide="calendar" class="h-4 w-4 mr-1"></i>
              Appointments
            </span>
          </a>
          <a href="admin_weddings.php" class="text-secondary-600 hover:text-primary-600 px-3 py-2 text-sm font-medium">
            <span class="flex items-center">
              <i data-lucide="heart" class="h-4 w-4 mr-1"></i>
              Weddings
            </span>
          </a>
          <a href="admin_events.php" class="text-primary-600 border-b-2 border-primary-600 px-3 py-2 text-sm font-medium">
            <span class="flex items-center">
              <i data-lucide="calendar-days" class="h-4 w-4 mr-1"></i>
              Events
            </span>
          </a>
          <a href="admin_members.php" class="text-secondary-600 hover:text-primary-600 px-3 py-2 text-sm font-medium">
            <span class="flex items-center">
              <i data-lucide="users" class="h-4 w-4 mr-1"></i>
              Members
            </span>
          </a>
          <a href="admin_donations.php" class="text-secondary-600 hover:text-primary-600 px-3 py-2 text-sm font-medium">
            <span class="flex items-center">
              <i data-lucide="hand-heart" class="h-4 w-4 mr-1"></i>
              Donations
            </span>
          </a>
        </nav>
        <div class="flex items-center">
          <button id="mobile-menu-button" class="md:hidden rounded-md p-2 text-gray-400 hover:bg-gray-100">
            <i data-lucide="menu" class="h-6 w-6"></i>
          </button>
          <a href="logout.php" class="ml-4 flex items-center text-sm font-medium text-secondary-600 hover:text-primary-600">
            <i data-lucide="log-out" class="h-4 w-4 mr-1"></i>
            Logout
          </a>
        </div>
      </div>
    </div>
    
    <!-- Mobile menu, show/hide based on menu state -->
    <div id="mobile-menu" class="md:hidden hidden bg-white border-b border-gray-200">
      <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
        <a href="admin_dashboard.php" class="text-secondary-600 hover:bg-gray-100 block px-3 py-2 rounded-md text-base font-medium">
          <span class="flex items-center">
            <i data-lucide="calendar" class="h-5 w-5 mr-2"></i>
            Appointments
          </span>
        </a>
        <a href="admin_weddings.php" class="text-secondary-600 hover:bg-gray-100 block px-3 py-2 rounded-md text-base font-medium">
          <span class="flex items-center">
            <i data-lucide="heart" class="h-5 w-5 mr-2"></i>
            Weddings
          </span>
        </a>
        <a href="admin_events.php" class="bg-primary-50 text-primary-600 block px-3 py-2 rounded-md text-base font-medium">
          <span class="flex items-center">
            <i data-lucide="calendar-days" class="h-5 w-5 mr-2"></i>
            Events
          </span>
        </a>
        <a href="admin_members.php" class="text-secondary-600 hover:bg-gray-100 block px-3 py-2 rounded-md text-base font-medium">
          <span class="flex items-center">
            <i data-lucide="users" class="h-5 w-5 mr-2"></i>
            Members
          </span>
        </a>
        <a href="admin_donations.php" class="text-secondary-600 hover:bg-gray-100 block px-3 py-2 rounded-md text-base font-medium">
          <span class="flex items-center">
            <i data-lucide="hand-heart" class="h-5 w-5 mr-2"></i>
            Donations
          </span>
        </a>
        <a href="logout.php" class="text-secondary-600 hover:bg-gray-100 block px-3 py-2 rounded-md text-base font-medium">
          <span class="flex items-center">
            <i data-lucide="log-out" class="h-5 w-5 mr-2"></i>
            Logout
          </span>
        </a>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="flex-grow">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
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
    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-white border-t border-gray-200">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <p class="text-center text-sm text-secondary-500">&copy; <?= date('Y') ?> Grace Community Church. All rights reserved.</p>
    </div>
  </footer>

  <script>
    // Initialize Lucide icons
    lucide.createIcons();
    
    // Mobile menu toggle
    document.getElementById('mobile-menu-button').addEventListener('click', function() {
      const menu = document.getElementById('mobile-menu');
      menu.classList.toggle('hidden');
    });
  </script>
</body>
</html>
