<?php
session_start();
include 'config.php';

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
  header("Location: login.php");
  exit();
}

// Get all donations
$donations = $conn->query("SELECT * FROM donations ORDER BY created_at DESC");

// Calculate total donations
$total_result = $conn->query("SELECT SUM(amount) as total FROM donations");
$total_donations = $total_result->fetch_assoc()['total'] ?? 0;

// Get recent donations (last 30 days)
$thirty_days_ago = date('Y-m-d', strtotime('-30 days'));
$recent_result = $conn->query("SELECT SUM(amount) as recent_total FROM donations WHERE created_at >= '$thirty_days_ago'");
$recent_donations = $recent_result->fetch_assoc()['recent_total'] ?? 0;

// Count donors
$donors_result = $conn->query("SELECT COUNT(DISTINCT full_name) as donor_count FROM donations");
$donor_count = $donors_result->fetch_assoc()['donor_count'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Donations Management - Grace Community Church</title>
  
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
          <a href="admin_donations.php" class="text-primary-600 border-b-2 border-primary-600 px-3 py-2 text-sm font-medium">
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
        <a href="admin_donations.php" class="bg-primary-50 text-primary-600 block px-3 py-2 rounded-md text-base font-medium">
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
        <h1 class="text-2xl font-heading font-bold text-secondary-900">Donations Management</h1>
        <p class="mt-2 text-sm text-secondary-600">
          Track and manage all donations made to the church.
        </p>
      </div>
      
      <!-- Stats cards -->
      <div class="px-4 sm:px-0 mb-8">
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
          <!-- Total Donations Card -->
          <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="px-4 py-5 sm:p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0 bg-primary-100 rounded-md p-3">
                  <i data-lucide="landmark" class="h-6 w-6 text-primary-600"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-secondary-500 truncate">Total Donations</dt>
                    <dd>
                      <div class="text-lg font-semibold text-secondary-900">₹<?= number_format($total_donations, 2) ?></div>
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Recent Donations Card -->
          <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="px-4 py-5 sm:p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0 bg-accent-100 rounded-md p-3">
                  <i data-lucide="trending-up" class="h-6 w-6 text-accent-600"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-secondary-500 truncate">Last 30 Days</dt>
                    <dd>
                      <div class="text-lg font-semibold text-secondary-900">₹<?= number_format($recent_donations, 2) ?></div>
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Donors Card -->
          <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="px-4 py-5 sm:p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                  <i data-lucide="users" class="h-6 w-6 text-green-600"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-secondary-500 truncate">Total Donors</dt>
                    <dd>
                      <div class="text-lg font-semibold text-secondary-900"><?= $donor_count ?></div>
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Donations Table -->
      <div class="px-4 sm:px-0">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg divide-y divide-gray-200">
          <div class="px-6 py-5 bg-white">
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <i data-lucide="hand-heart" class="h-5 w-5 text-primary-600 mr-2"></i>
                <h3 class="text-lg font-medium leading-6 text-secondary-900">All Donations</h3>
              </div>
              <a href="export_donations.php" 
                 class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <i data-lucide="download" class="h-4 w-4 mr-2"></i>
                Export to Excel
              </a>
            </div>
          </div>
          
          <div class="overflow-x-auto">
            <?php if ($donations->num_rows > 0): ?>
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Date & Time</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Donor</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Amount</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Transaction ID</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Contact</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Note</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <?php while($row = $donations->fetch_assoc()): ?>
                <tr class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary-900">
                    <div class="flex items-center">
                      <i data-lucide="calendar" class="h-4 w-4 mr-1 text-secondary-400"></i>
                      <?= date('d M Y', strtotime($row['created_at'])) ?>
                    </div>
                    <div class="text-xs text-secondary-500 mt-1">
                      <i data-lucide="clock" class="h-3 w-3 inline mr-1"></i>
                      <?= date('h:i A', strtotime($row['created_at'])) ?>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10 bg-primary-100 rounded-full flex items-center justify-center text-primary-600">
                        <span class="font-medium text-lg"><?= strtoupper(substr($row['full_name'], 0, 1)) ?></span>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-secondary-900"><?= htmlspecialchars($row['full_name']) ?></div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-green-600">₹<?= number_format($row['amount'], 2) ?></div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary-900">
                    <div class="flex items-center">
                      <i data-lucide="credit-card" class="h-4 w-4 mr-1 text-secondary-400"></i>
                      <?= htmlspecialchars($row['transaction_id']) ?>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-secondary-900">
                      <?php if (!empty($row['phone'])): ?>
                      <div class="flex items-center">
                        <i data-lucide="phone" class="h-4 w-4 mr-1 text-secondary-400"></i>
                        <?= htmlspecialchars($row['phone']) ?>
                      </div>
                      <?php endif; ?>
                      
                      <?php if (!empty($row['email'])): ?>
                      <div class="flex items-center mt-1">
                        <i data-lucide="mail" class="h-4 w-4 mr-1 text-secondary-400"></i>
                        <?= htmlspecialchars($row['email']) ?>
                      </div>
                      <?php endif; ?>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-secondary-500 max-w-xs truncate">
                      <?= htmlspecialchars($row['note']) ?>
                    </div>
                  </td>
                </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
            <?php else: ?>
            <div class="py-12 text-center">
              <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary-100 text-primary-600 mb-4">
                <i data-lucide="hand-heart" class="h-8 w-8"></i>
              </div>
              <h3 class="mt-2 text-sm font-medium text-secondary-900">No donations yet</h3>
              <p class="mt-1 text-sm text-secondary-500">
                There are no donations recorded in the system.
              </p>
            </div>
            <?php endif; ?>
          </div>
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
