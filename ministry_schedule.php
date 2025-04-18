<?php
include 'config.php';
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle form submission for adding new ministry schedule
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_schedule'])) {
    $ministry_name = $_POST['ministry_name'];
    $schedule_date = $_POST['schedule_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $description = $_POST['description'];
    $volunteers_needed = $_POST['volunteers_needed'];

    $sql = "INSERT INTO ministry_schedules (ministry_name, schedule_date, start_time, end_time, description, volunteers_needed) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $ministry_name, $schedule_date, $start_time, $end_time, $description, $volunteers_needed);
    
    if ($stmt->execute()) {
        $success_message = "Ministry schedule added successfully!";
    } else {
        $error_message = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Handle form submission for deleting a ministry schedule
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_schedule'])) {
    $schedule_id = $_POST['schedule_id'];
    
    $sql = "DELETE FROM ministry_schedules WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $schedule_id);
    
    if ($stmt->execute()) {
        $success_message = "Ministry schedule deleted successfully!";
    } else {
        $error_message = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch all ministry schedules
$sql = "SELECT * FROM ministry_schedules ORDER BY schedule_date DESC";
$result = $conn->query($sql);
$schedules = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $schedules[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ministry Schedules - Church Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>
<body class="bg-gradient-to-br from-purple-50 to-indigo-50 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg border-b border-purple-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <span class="text-2xl font-bold text-purple-800">Grace Church</span>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="admin_dashboard.php" class="border-transparent text-gray-600 hover:text-purple-700 hover:border-purple-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Dashboard
                        </a>
                        <a href="admin_members.php" class="border-transparent text-gray-600 hover:text-purple-700 hover:border-purple-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Members
                        </a>
                        <a href="admin_events.php" class="border-transparent text-gray-600 hover:text-purple-700 hover:border-purple-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Events
                        </a>
                        <a href="admin_weddings.php" class="border-transparent text-gray-600 hover:text-purple-700 hover:border-purple-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Weddings
                        </a>
                        <a href="admin_donations.php" class="border-transparent text-gray-600 hover:text-purple-700 hover:border-purple-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Donations
                        </a>
                        <a href="ministry_schedule.php" class="border-purple-700 text-purple-700 hover:text-purple-700 hover:border-purple-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Ministry Schedules
                        </a>
                    </div>
                </div>
                <div class="hidden sm:ml-6 sm:flex sm:items-center">
                    <div class="ml-3 relative">
                        <div class="flex items-center space-x-4">
                            <a href="logout.php" class="text-gray-600 hover:text-purple-700 px-3 py-2 rounded-md text-sm font-medium flex items-center">
                                <i data-lucide="log-out" class="h-4 w-4 mr-1"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
                <div class="-mr-2 flex items-center sm:hidden">
                    <button type="button" class="mobile-menu-button inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-purple-700 hover:bg-purple-50 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-purple-500" aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <i data-lucide="menu" class="h-6 w-6"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu, show/hide based on menu state. -->
        <div class="sm:hidden hidden" id="mobile-menu">
            <div class="pt-2 pb-3 space-y-1">
                <a href="admin_dashboard.php" class="text-gray-600 hover:bg-purple-50 hover:text-purple-700 block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium">Dashboard</a>
                <a href="admin_members.php" class="text-gray-600 hover:bg-purple-50 hover:text-purple-700 block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium">Members</a>
                <a href="admin_events.php" class="text-gray-600 hover:bg-purple-50 hover:text-purple-700 block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium">Events</a>
                <a href="admin_weddings.php" class="text-gray-600 hover:bg-purple-50 hover:text-purple-700 block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium">Weddings</a>
                <a href="admin_donations.php" class="text-gray-600 hover:bg-purple-50 hover:text-purple-700 block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium">Donations</a>
                <a href="ministry_schedule.php" class="bg-purple-50 text-purple-700 block pl-3 pr-4 py-2 border-l-4 border-purple-700 text-base font-medium">Ministry Schedules</a>
                <a href="logout.php" class="text-gray-600 hover:bg-purple-50 hover:text-purple-700 block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium flex items-center">
                    <i data-lucide="log-out" class="h-4 w-4 mr-1"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="px-4 py-6 sm:px-0">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-extrabold text-purple-900">Ministry Schedules</h1>
                <button id="addScheduleBtn" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-700 hover:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    <i data-lucide="plus" class="h-4 w-4 mr-2"></i>
                    Add New Schedule
                </button>
            </div>

            <!-- Success/Error Messages -->
            <?php if (isset($success_message)): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p><?php echo $success_message; ?></p>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p><?php echo $error_message; ?></p>
                </div>
            <?php endif; ?>

            <!-- Add Schedule Form (Hidden by default) -->
            <div id="scheduleForm" class="hidden bg-white shadow-md rounded-lg p-6 mb-6 border border-purple-200">
                <h2 class="text-xl font-semibold text-purple-800 mb-4">Add New Ministry Schedule</h2>
                <form method="POST" action="">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="ministry_name" class="block text-sm font-medium text-gray-700">Ministry Name</label>
                            <input type="text" name="ministry_name" id="ministry_name" required class="mt-1 focus:ring-purple-500 focus:border-purple-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label for="schedule_date" class="block text-sm font-medium text-gray-700">Date</label>
                            <input type="date" name="schedule_date" id="schedule_date" required class="mt-1 focus:ring-purple-500 focus:border-purple-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label for="start_time" class="block text-sm font-medium text-gray-700">Start Time</label>
                            <input type="time" name="start_time" id="start_time" required class="mt-1 focus:ring-purple-500 focus:border-purple-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label for="end_time" class="block text-sm font-medium text-gray-700">End Time</label>
                            <input type="time" name="end_time" id="end_time" required class="mt-1 focus:ring-purple-500 focus:border-purple-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label for="volunteers_needed" class="block text-sm font-medium text-gray-700">Volunteers Needed</label>
                            <input type="number" name="volunteers_needed" id="volunteers_needed" min="1" required class="mt-1 focus:ring-purple-500 focus:border-purple-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" id="description" rows="3" class="mt-1 focus:ring-purple-500 focus:border-purple-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" id="cancelBtn" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                            Cancel
                        </button>
                        <button type="submit" name="add_schedule" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-700 hover:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                            <i data-lucide="save" class="h-4 w-4 mr-2"></i>
                            Save Schedule
                        </button>
                    </div>
                </form>
            </div>

            <!-- Schedules Table -->
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <?php if (empty($schedules)): ?>
                        <div class="p-8 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-purple-100 text-purple-600 mb-4">
                                <i data-lucide="calendar" class="h-8 w-8"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Ministry Schedules</h3>
                            <p class="text-gray-500 mb-4">There are no ministry schedules created yet.</p>
                            <button id="emptyAddBtn" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-700 hover:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                <i data-lucide="plus" class="h-4 w-4 mr-2"></i>
                                Add Your First Schedule
                            </button>
                        </div>
                    <?php else: ?>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ministry</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Volunteers</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($schedules as $schedule): ?>
                                    <tr class="hover:bg-purple-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($schedule['ministry_name']); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                <?php 
                                                    $date = new DateTime($schedule['schedule_date']);
                                                    echo $date->format('M d, Y'); 
                                                ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <?php echo htmlspecialchars($schedule['start_time']); ?> - <?php echo htmlspecialchars($schedule['end_time']); ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 max-w-xs truncate"><?php echo htmlspecialchars($schedule['description']); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                <?php echo htmlspecialchars($schedule['volunteers_needed']); ?> needed
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <button class="view-details text-indigo-600 hover:text-indigo-900" data-id="<?php echo $schedule['id']; ?>">
                                                    <i data-lucide="eye" class="h-5 w-5"></i>
                                                </button>
                                                <form method="POST" action="" class="inline" onsubmit="return confirm('Are you sure you want to delete this schedule?');">
                                                    <input type="hidden" name="schedule_id" value="<?php echo $schedule['id']; ?>">
                                                    <button type="submit" name="delete_schedule" class="text-red-600 hover:text-red-900">
                                                        <i data-lucide="trash-2" class="h-5 w-5"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule Details Modal -->
    <div id="scheduleModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Schedule Details</h3>
                    <button id="closeModal" class="text-gray-400 hover:text-gray-500">
                        <i data-lucide="x" class="h-5 w-5"></i>
                    </button>
                </div>
            </div>
            <div class="px-6 py-4" id="modalContent">
                <!-- Content will be dynamically populated -->
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                <button id="closeModalBtn" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Mobile menu toggle
        document.querySelector('.mobile-menu-button').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });

        // Add Schedule Form Toggle
        document.getElementById('addScheduleBtn').addEventListener('click', function() {
            document.getElementById('scheduleForm').classList.remove('hidden');
        });

        // Empty state add button
        const emptyAddBtn = document.getElementById('emptyAddBtn');
        if (emptyAddBtn) {
            emptyAddBtn.addEventListener('click', function() {
                document.getElementById('scheduleForm').classList.remove('hidden');
            });
        }

        // Cancel button
        document.getElementById('cancelBtn').addEventListener('click', function() {
            document.getElementById('scheduleForm').classList.add('hidden');
        });

        // View Schedule Details
        const viewButtons = document.querySelectorAll('.view-details');
        const modal = document.getElementById('scheduleModal');
        const closeModal = document.getElementById('closeModal');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const modalContent = document.getElementById('modalContent');

        viewButtons.forEach(button => {
            button.addEventListener('click', function() {
                const scheduleId = this.getAttribute('data-id');
                // Find the schedule in the PHP array
                <?php echo "const schedules = " . json_encode($schedules) . ";"; ?>
                
                const schedule = schedules.find(s => s.id == scheduleId);
                
                if (schedule) {
                    // Format the date
                    const dateObj = new Date(schedule.schedule_date);
                    const formattedDate = dateObj.toLocaleDateString('en-US', { 
                        weekday: 'long', 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric' 
                    });
                    
                    // Populate modal content
                    modalContent.innerHTML = `
                        <div class="space-y-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Ministry</h4>
                                <p class="text-base font-medium text-gray-900">${schedule.ministry_name}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Date</h4>
                                <p class="text-base font-medium text-gray-900">${formattedDate}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Time</h4>
                                <p class="text-base font-medium text-gray-900">${schedule.start_time} - ${schedule.end_time}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Volunteers Needed</h4>
                                <p class="text-base font-medium text-gray-900">${schedule.volunteers_needed}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Description</h4>
                                <p class="text-base text-gray-900">${schedule.description || 'No description provided.'}</p>
                            </div>
                        </div>
                    `;
                    
                    // Show modal
                    modal.classList.remove('hidden');
                }
            });
        });

        // Close modal
        closeModal.addEventListener('click', function() {
            modal.classList.add('hidden');
        });

        closeModalBtn.addEventListener('click', function() {
            modal.classList.add('hidden');
        });

        // Close modal when clicking outside
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
