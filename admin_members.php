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
          <a href="admin_members.php" class="text-primary-600 border-b-2 border-primary-600 px-3 py-2 text-sm font-medium">
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
        <a href="admin_events.php" class="text-secondary-600 hover:bg-gray-100 block px-3 py-2 rounded-md text-base font-medium">
          <span class="flex items-center">
            <i data-lucide="calendar-days" class="h-5 w-5 mr-2"></i>
            Events
          </span>
        </a>
        <a href="admin_members.php" class="bg-primary-50 text-primary-600 block px-3 py-2 rounded-md text-base font-medium">
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
