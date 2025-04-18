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

$sql = "SELECT * FROM weddings";
if ($status_filter !== 'all') {
  $sql .= " WHERE status='$status_filter'";
}

$weddings = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Wedding Requests - Grace Community Church</title>
  
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
          <a href="admin_weddings.php" class="text-primary-600 border-b-2 border-primary-600 px-3 py-2 text-sm font-medium">
            <span class="flex items-center">
              <i data-lucide="heart" class="h-4 w-4 mr-1"></i>
              Weddings
            </span>
          </a>
          <a href="admin_events.php" class="text-secondary-600 hover:text-primary-600 px-3 py-2 text-sm font-medium">
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
        <a href="admin_weddings.php" class="bg-primary-50 text-primary-600 block px-3 py-2 rounded-md text-base font-medium">
          <span class="flex items-center">
            <i data-lucide="heart" class="h-5 w-5 mr-2"></i>
            Weddings
          </span>
        </a>
        <a href="admin_events.php" class="text-secondary-600 hover:bg-gray-100 block px-3 py-2 rounded-md text-base font-medium">
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
        <h1 class="text-2xl font-heading font-bold text-secondary-900">Wedding Requests</h1>
        <p class="mt-2 text-sm text-secondary-600">
          Manage and respond to wedding ceremony requests from church members.
        </p>
      </div>
      
      <!-- Filter tabs -->
      <div class="px-4 sm:px-0">
        <div class="border-b border-gray-200">
          <nav class="flex -mb-px space-x-4 overflow-x-auto" aria-label="Tabs">
            <a href="?status=all" class="<?= $status_filter === 'all' ? 'border-primary-500 text-primary-600' : 'border-transparent text-secondary-500 hover:text-secondary-700 hover:border-secondary-300' ?> whitespace-nowrap py-3 px-4 border-b-2 font-medium text-sm flex items-center">
              <i data-lucide="layers" class="h-4 w-4 mr-2"></i>
              All Requests
            </a>
            <a href="?status=pending" class="<?= $status_filter === 'pending' ? 'border-primary-500 text-primary-600' : 'border-transparent text-secondary-500 hover:text-secondary-700 hover:border-secondary-300' ?> whitespace-nowrap py-3 px-4 border-b-2 font-medium text-sm flex items-center">
              <i data-lucide="clock" class="h-4 w-4 mr-2"></i>
              Pending
              <?php 
              $pending_count = $conn->query("SELECT COUNT(*) as count FROM weddings WHERE status='pending'")->fetch_assoc()['count'];
              if ($pending_count > 0): 
              ?>
              <span class="ml-2 bg-primary-100 text-primary-600 py-0.5 px-2 rounded-full text-xs"><?= $pending_count ?></span>
              <?php endif; ?>
            </a>
            <a href="?status=approved" class="<?= $status_filter === 'approved' ? 'border-primary-500 text-primary-600' : 'border-transparent text-secondary-500 hover:text-secondary-700 hover:border-secondary-300' ?> whitespace-nowrap py-3 px-4 border-b-2 font-medium text-sm flex items-center">
              <i data-lucide="check-circle" class="h-4 w-4 mr-2"></i>
              Approved
            </a>
            <a href="?status=rejected" class="<?= $status_filter === 'rejected' ? 'border-primary-500 text-primary-600' : 'border-transparent text-secondary-500 hover:text-secondary-700 hover:border-secondary-300' ?> whitespace-nowrap py-3 px-4 border-b-2 font-medium text-sm flex items-center">
              <i data-lucide="x-circle" class="h-4 w-4 mr-2"></i>
              Rejected
            </a>
          </nav>
        </div>
      </div>
      
      <!-- Wedding requests table -->
      <div class="mt-6 px-4 sm:px-0">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
          <?php if ($weddings->num_rows > 0): ?>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Couple</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Contact Information</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Wedding Date</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Notes</th>
                  <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-secondary-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <?php while($row = $weddings->fetch_assoc()): ?>
                <tr class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10 bg-primary-100 rounded-full flex items-center justify-center text-primary-600">
                        <i data-lucide="heart" class="h-5 w-5"></i>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-secondary-900"><?= htmlspecialchars($row['bride_name']) ?></div>
                        <div class="text-sm text-secondary-500"><?= htmlspecialchars($row['groom_name']) ?></div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-secondary-900 flex items-center">
                      <i data-lucide="phone" class="h-4 w-4 mr-1 text-secondary-400"></i>
                      <?= htmlspecialchars($row['bride_phone']) ?>
                    </div>
                    <div class="text-sm text-secondary-900 flex items-center">
                      <i data-lucide="phone" class="h-4 w-4 mr-1 text-secondary-400"></i>
                      <?= htmlspecialchars($row['groom_phone']) ?>
                    </div>
                    <div class="text-sm text-secondary-900 flex items-center">
                      <i data-lucide="mail" class="h-4 w-4 mr-1 text-secondary-400"></i>
                      <?= htmlspecialchars($row['email']) ?>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-secondary-900 flex items-center">
                      <i data-lucide="calendar" class="h-4 w-4 mr-1 text-secondary-400"></i>
                      <?= date('F j, Y', strtotime($row['wedding_date'])) ?>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-secondary-500 max-w-xs truncate">
                      <?= htmlspecialchars($row['notes']) ?>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <?php if($row['status'] === 'pending'): ?>
                    <div class="flex justify-end space-x-2">
                      <a href="approve.php?type=wedding&id=<?= $row['id'] ?>&action=approve&status=<?= $status_filter ?>" 
                         class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <i data-lucide="check" class="h-3.5 w-3.5 mr-1"></i>
                        Approve
                      </a>
                      <a href="approve.php?type=wedding&id=<?= $row['id'] ?>&action=reject&status=<?= $status_filter ?>" 
                         class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <i data-lucide="x" class="h-3.5 w-3.5 mr-1"></i>
                        Reject
                      </a>
                    </div>
                    <?php else: ?>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                      <?php if($row['status'] === 'approved'): ?>
                        bg-green-100 text-green-800
                      <?php else: ?>
                        bg-red-100 text-red-800
                      <?php endif; ?>
                    ">
                      <?php if($row['status'] === 'approved'): ?>
                        <i data-lucide="check-circle" class="h-3.5 w-3.5 mr-1"></i>
                      <?php else: ?>
                        <i data-lucide="x-circle" class="h-3.5 w-3.5 mr-1"></i>
                      <?php endif; ?>
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
          <div class="py-12 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary-100 text-primary-600 mb-4">
              <i data-lucide="calendar-x" class="h-8 w-8"></i>
            </div>
            <h3 class="mt-2 text-sm font-medium text-secondary-900">No wedding requests</h3>
            <p class="mt-1 text-sm text-secondary-500">
              There are no <?= $status_filter !== 'all' ? $status_filter : '' ?> wedding requests at this time.
            </p>
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
