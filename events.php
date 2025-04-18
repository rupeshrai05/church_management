<?php
include 'config.php';

$today = date('Y-m-d');
$events = $conn->query("SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date, event_time");

// Add debug output here ▼
echo "<!-- Debug: Current date $today -->";
while($row = $events->fetch_assoc()) {
    echo "<!-- Event date: {$row['event_date']} -->";
}
// Reset pointer to beginning for actual display
$events->data_seek(0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Church Events - Grace Community Church</title>
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
<body class="bg-gray-50 min-h-screen flex flex-col">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <div class="flex items-center">
                    <a href="index.html" class="flex-shrink-0 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-primary-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                        </svg>
                        <span class="ml-3 text-xl font-heading font-semibold text-secondary-800">Grace Community</span>
                    </a>
                </div>
                
                <!-- Desktop Navigation -->
                <div class="hidden md:ml-6 md:flex md:items-center md:space-x-8">
                    <a href="index.html" class="text-secondary-500 hover:text-primary-600 hover:border-b-2 hover:border-primary-600 px-1 pt-1 font-medium text-sm transition-colors">Home</a>
                    <a href="about.html" class="text-secondary-500 hover:text-primary-600 hover:border-b-2 hover:border-primary-600 px-1 pt-1 font-medium text-sm transition-colors">About</a>
                    <a href="events.php" class="text-primary-600 border-b-2 border-primary-600 px-1 pt-1 font-medium text-sm">Events</a>
                    <a href="donation.php" class="text-secondary-500 hover:text-primary-600 hover:border-b-2 hover:border-primary-600 px-1 pt-1 font-medium text-sm transition-colors">Donate</a>
                    <a href="contact.html" class="text-secondary-500 hover:text-primary-600 hover:border-b-2 hover:border-primary-600 px-1 pt-1 font-medium text-sm transition-colors">Contact</a>
                </div>

                <!-- Auth Links -->
                <div class="flex items-center">
                    <a href="login.php" class="hidden md:inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-primary-700 bg-primary-50 hover:bg-primary-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 mr-3 transition-colors">
                        Sign in
                    </a>
                    <a href="signup.php" class="flex-1 px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 text-center">
                            Join Us
                        </a>
                    
                    <!-- Mobile menu button -->
                    <button type="button" class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-secondary-400 hover:text-secondary-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500" aria-expanded="false" id="mobile-menu-button">
                        <span class="sr-only">Open main menu</span>
                        <i data-lucide="menu" class="h-6 w-6"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu, show/hide based on menu state -->
        <div class="md:hidden hidden" id="mobile-menu">
            <div class="pt-2 pb-3 space-y-1">
                <a href="index.html" class="border-transparent text-secondary-500 hover:bg-gray-50 hover:border-l-4 hover:border-primary-300 hover:text-secondary-700 block pl-3 pr-4 py-2 text-base font-medium">Home</a>
                <a href="about.html" class="border-transparent text-secondary-500 hover:bg-gray-50 hover:border-l-4 hover:border-primary-300 hover:text-secondary-700 block pl-3 pr-4 py-2 text-base font-medium">About</a>
                <a href="events.php" class="bg-primary-50 border-l-4 border-primary-500 text-primary-700 block pl-3 pr-4 py-2 text-base font-medium">Events</a>
                <a href="donation.php" class="border-transparent text-secondary-500 hover:bg-gray-50 hover:border-l-4 hover:border-primary-300 hover:text-secondary-700 block pl-3 pr-4 py-2 text-base font-medium">Donate</a>
                <a href="contact.html" class="border-transparent text-secondary-500 hover:bg-gray-50 hover:border-l-4 hover:border-primary-300 hover:text-secondary-700 block pl-3 pr-4 py-2 text-base font-medium">Contact</a>
            </div>
            <div class="pt-4 pb-3 border-t border-gray-200">
                <div class="flex items-center px-4">
                    <a href="login.php" class="flex-1 px-4 py-2 border border-transparent text-sm font-medium rounded-md text-primary-700 bg-primary-50 hover:bg-primary-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 text-center">
                        Sign in
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative bg-primary-700">
        <div class="absolute inset-0">
            <img class="w-full h-full object-cover" src="https://images.unsplash.com/photo-1511795409834-ef04bbd61622?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1169&q=80" alt="Church event">
            <div class="absolute inset-0 bg-primary-700 mix-blend-multiply" aria-hidden="true"></div>
        </div>
        <div class="relative max-w-7xl mx-auto py-24 px-4 sm:py-32 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl font-heading">Upcoming Events</h1>
            <p class="mt-6 max-w-3xl text-xl text-primary-100">Join us for worship, fellowship, and community service. There's always something happening at Grace Community Church.</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <!-- Event Calendar Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div>
                <h2 class="text-3xl font-bold text-secondary-900 font-heading">Church Events Calendar</h2>
                <p class="mt-2 text-lg text-secondary-600">Find out what's happening in our community</p>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="#" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    <i data-lucide="calendar" class="mr-2 h-5 w-5"></i>
                    Subscribe to Calendar
                </a>
            </div>
        </div>

        <!-- Event Filters -->
        <div class="bg-white rounded-lg shadow-md p-4 mb-8">
            <div class="flex flex-wrap gap-4">
                <a href="#today" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 transition-colors">
                    <i data-lucide="calendar-check" class="mr-2 h-4 w-4"></i>
                    Today's Events
                </a>
                <a href="#upcoming" class="inline-flex items-center px-4 py-2 bg-secondary-100 text-secondary-800 rounded-md hover:bg-secondary-200 transition-colors">
                    <i data-lucide="calendar-days" class="mr-2 h-4 w-4"></i>
                    Upcoming Events
                </a>
                <a href="#" class="inline-flex items-center px-4 py-2 bg-secondary-100 text-secondary-800 rounded-md hover:bg-secondary-200 transition-colors">
                    <i data-lucide="users" class="mr-2 h-4 w-4"></i>
                    Family Events
                </a>
                <a href="#" class="inline-flex items-center px-4 py-2 bg-secondary-100 text-secondary-800 rounded-md hover:bg-secondary-200 transition-colors">
                    <i data-lucide="heart" class="mr-2 h-4 w-4"></i>
                    Outreach
                </a>
                <a href="#" class="inline-flex items-center px-4 py-2 bg-secondary-100 text-secondary-800 rounded-md hover:bg-secondary-200 transition-colors">
                    <i data-lucide="music" class="mr-2 h-4 w-4"></i>
                    Worship
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content - Events -->
            <div class="lg:col-span-2">
                <!-- Today's Events -->
                <section id="today" class="mb-12">
                    <div class="flex items-center mb-6">
                        <div class="flex-shrink-0 bg-primary-100 p-2 rounded-full">
                            <i data-lucide="calendar-check" class="h-6 w-6 text-primary-600"></i>
                        </div>
                        <h2 class="ml-3 text-2xl font-bold text-secondary-900 font-heading">Today's Events</h2>
                    </div>
                    
                    <div class="space-y-6">
                        <?php 
                        $has_today_events = false;
                        while($row = $events->fetch_assoc()):
                            if ($row['event_date'] == $today):
                                $has_today_events = true;
                        ?>
                        <div class="bg-white rounded-lg shadow-md overflow-hidden transition-transform hover:shadow-lg hover:-translate-y-1">
                            <div class="md:flex">
                                <div class="md:flex-shrink-0 bg-primary-600 text-white md:w-32 flex flex-col items-center justify-center p-4">
                                    <span class="text-2xl font-bold"><?= date('d', strtotime($row['event_date'])) ?></span>
                                    <span class="text-sm uppercase"><?= date('M', strtotime($row['event_date'])) ?></span>
                                    <span class="mt-2 text-primary-100"><?= date('g:i A', strtotime($row['event_time'])) ?></span>
                                </div>
                                <div class="p-6">
                                    <div class="flex items-center justify-between mb-2">
                                        <h3 class="text-xl font-bold text-secondary-900"><?= htmlspecialchars($row['event_name']) ?></h3>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Today
                                        </span>
                                    </div>
                                    <p class="text-secondary-600 mb-4"><?= htmlspecialchars($row['description']) ?></p>
                                    <div class="flex flex-wrap gap-4 text-sm text-secondary-500">
                                        <div class="flex items-center">
                                            <i data-lucide="map-pin" class="h-4 w-4 mr-1 text-primary-500"></i>
                                            <?= htmlspecialchars($row['address']) ?>
                                        </div>
                                        <div class="flex items-center">
                                            <i data-lucide="tag" class="h-4 w-4 mr-1 text-primary-500"></i>
                                            <?= $row['event_type'] === 'paid' ? 'Paid Event' : 'Free Entry' ?>
                                        </div>
                                        <?php if(!empty($row['contact_details'])): ?>
                                        <div class="flex items-center">
                                            <i data-lucide="phone" class="h-4 w-4 mr-1 text-primary-500"></i>
                                            <?= htmlspecialchars($row['contact_details']) ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php 
                            endif;
                        endwhile; 
                        if (!$has_today_events): ?>
                        <div class="bg-white p-6 rounded-lg shadow-md text-center">
                            <i data-lucide="calendar-x" class="h-12 w-12 mx-auto text-secondary-300 mb-4"></i>
                            <p class="text-secondary-500">No events scheduled for today</p>
                            <p class="text-secondary-400 text-sm mt-2">Check out our upcoming events below</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </section>

                <!-- Upcoming Events -->
                <section id="upcoming">
                    <div class="flex items-center mb-6">
                        <div class="flex-shrink-0 bg-primary-100 p-2 rounded-full">
                            <i data-lucide="calendar-days" class="h-6 w-6 text-primary-600"></i>
                        </div>
                        <h2 class="ml-3 text-2xl font-bold text-secondary-900 font-heading">Upcoming Events</h2>
                    </div>
                    
                    <div class="space-y-6">
                        <?php 
                        $events->data_seek(0); // Reset pointer
                        $has_upcoming = false;
                        while($row = $events->fetch_assoc()):
                            if ($row['event_date'] > $today):
                                $has_upcoming = true;
                        ?>
                        <div class="bg-white rounded-lg shadow-md overflow-hidden transition-transform hover:shadow-lg hover:-translate-y-1">
                            <div class="md:flex">
                                <div class="md:flex-shrink-0 bg-accent-500 text-white md:w-32 flex flex-col items-center justify-center p-4">
                                    <span class="text-2xl font-bold"><?= date('d', strtotime($row['event_date'])) ?></span>
                                    <span class="text-sm uppercase"><?= date('M', strtotime($row['event_date'])) ?></span>
                                    <span class="mt-2 text-accent-100"><?= date('g:i A', strtotime($row['event_time'])) ?></span>
                                </div>
                                <div class="p-6">
                                    <div class="flex items-center justify-between mb-2">
                                        <h3 class="text-xl font-bold text-secondary-900"><?= htmlspecialchars($row['event_name']) ?></h3>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <?= date('D, M j', strtotime($row['event_date'])) ?>
                                        </span>
                                    </div>
                                    <p class="text-secondary-600 mb-4"><?= htmlspecialchars($row['description']) ?></p>
                                    <div class="flex flex-wrap gap-4 text-sm text-secondary-500">
                                        <div class="flex items-center">
                                            <i data-lucide="map-pin" class="h-4 w-4 mr-1 text-accent-500"></i>
                                            <?= htmlspecialchars($row['address']) ?>
                                        </div>
                                        <div class="flex items-center">
                                            <i data-lucide="tag" class="h-4 w-4 mr-1 text-accent-500"></i>
                                            <?= $row['event_type'] === 'paid' ? 'Paid Event' : 'Free Entry' ?>
                                        </div>
                                        <?php if(!empty($row['contact_details'])): ?>
                                        <div class="flex items-center">
                                            <i data-lucide="phone" class="h-4 w-4 mr-1 text-accent-500"></i>
                                            <?= htmlspecialchars($row['contact_details']) ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="mt-4">
                                        <button class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded text-accent-700 bg-accent-100 hover:bg-accent-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-500 transition-colors">
                                            <i data-lucide="calendar-plus" class="mr-1.5 h-4 w-4"></i>
                                            Add to Calendar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php 
                            endif;
                        endwhile; 
                        if (!$has_upcoming): ?>
                        <div class="bg-white p-6 rounded-lg shadow-md text-center">
                            <i data-lucide="calendar-x" class="h-12 w-12 mx-auto text-secondary-300 mb-4"></i>
                            <p class="text-secondary-500">No upcoming events scheduled at this time</p>
                            <p class="text-secondary-400 text-sm mt-2">Please check back soon for new events</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </section>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Weekly Schedule -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                    <div class="bg-primary-600 px-4 py-3">
                        <h3 class="text-lg font-medium text-white font-heading">Weekly Schedule</h3>
                    </div>
                    <div class="p-4">
                        <ul class="space-y-4">
                            <li class="flex items-start">
                                <div class="flex-shrink-0 bg-primary-100 rounded-md p-1.5">
                                    <i data-lucide="sun" class="h-5 w-5 text-primary-600"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-secondary-900">Sunday</p>
                                    <p class="text-sm text-secondary-500">9:00 AM - Sunday School</p>
                                    <p class="text-sm text-secondary-500">10:30 AM - Worship Service</p>
                                    <p class="text-sm text-secondary-500">6:00 PM - Evening Service</p>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0 bg-primary-100 rounded-md p-1.5">
                                    <i data-lucide="book-open" class="h-5 w-5 text-primary-600"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-secondary-900">Wednesday</p>
                                    <p class="text-sm text-secondary-500">6:30 PM - Bible Study</p>
                                    <p class="text-sm text-secondary-500">7:00 PM - Prayer Meeting</p>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <div class="flex-shrink-0 bg-primary-100 rounded-md p-1.5">
                                    <i data-lucide="users" class="h-5 w-5 text-primary-600"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-secondary-900">Friday</p>
                                    <p class="text-sm text-secondary-500">6:30 PM - Youth Group</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Event Categories -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                    <div class="bg-primary-600 px-4 py-3">
                        <h3 class="text-lg font-medium text-white font-heading">Event Categories</h3>
                    </div>
                    <div class="p-4">
                        <ul class="space-y-2">
                            <li>
                                <a href="#" class="flex items-center p-2 rounded-md hover:bg-primary-50 transition-colors">
                                    <span class="w-3 h-3 bg-primary-500 rounded-full mr-2"></span>
                                    <span class="text-secondary-700">Worship Services</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center p-2 rounded-md hover:bg-primary-50 transition-colors">
                                    <span class="w-3 h-3 bg-accent-500 rounded-full mr-2"></span>
                                    <span class="text-secondary-700">Bible Studies</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center p-2 rounded-md hover:bg-primary-50 transition-colors">
                                    <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                                    <span class="text-secondary-700">Youth Activities</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center p-2 rounded-md hover:bg-primary-50 transition-colors">
                                    <span class="w-3 h-3 bg-purple-500 rounded-full mr-2"></span>
                                    <span class="text-secondary-700">Community Outreach</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center p-2 rounded-md hover:bg-primary-50 transition-colors">
                                    <span class="w-3 h-3 bg-red-500 rounded-full mr-2"></span>
                                    <span class="text-secondary-700">Special Events</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Newsletter Signup -->
                <div class="bg-secondary-800 rounded-lg shadow-md overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-white font-heading mb-3">Stay Updated</h3>
                        <p class="text-secondary-300 mb-4">Subscribe to our newsletter to receive event updates and announcements.</p>
                        <form class="space-y-4">
                            <div>
                                <label for="email" class="sr-only">Email address</label>
                                <input id="email" name="email" type="email" required class="appearance-none block w-full px-3 py-2 border border-secondary-600 rounded-md shadow-sm placeholder-secondary-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 bg-secondary-700 text-white sm:text-sm" placeholder="Your email address">
                            </div>
                            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                                Subscribe
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-secondary-800 text-white mt-12">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Logo and About -->
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                        </svg>
                        <span class="ml-3 text-xl font-heading font-semibold text-white">Grace Community</span>
                    </div>
                    <p class="mt-4 text-base text-secondary-300">
                        A welcoming community of faith, hope, and love. Join us as we worship, learn, and serve together.
                    </p>
                    <div class="mt-6 flex space-x-6">
                        <a href="#" class="text-secondary-400 hover:text-primary-400">
                            <span class="sr-only">Facebook</span>
                            <i data-lucide="facebook" class="h-6 w-6"></i>
                        </a>
                        <a href="#" class="text-secondary-400 hover:text-primary-400">
                            <span class="sr-only">Instagram</span>
                            <i data-lucide="instagram" class="h-6 w-6"></i>
                        </a>
                        <a href="#" class="text-secondary-400 hover:text-primary-400">
                            <span class="sr-only">Twitter</span>
                            <i data-lucide="twitter" class="h-6 w-6"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-sm font-semibold text-secondary-300 tracking-wider uppercase">Quick Links</h3>
                    <ul class="mt-4 space-y-4">
                        <li>
                            <a href="about.html" class="text-base text-secondary-300 hover:text-white">About Us</a>
                        </li>
                        <li>
                            <a href="events.php" class="text-base text-secondary-300 hover:text-white">Events</a>
                        </li>
                        <li>
                            <a href="donation.php" class="text-base text-secondary-300 hover:text-white">Donate</a>
                        </li>
                        <li>
                            <a href="contact.html" class="text-base text-secondary-300 hover:text-white">Contact</a>
                        </li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h3 class="text-sm font-semibold text-secondary-300 tracking-wider uppercase">Contact Us</h3>
                    <ul class="mt-4 space-y-4">
                        <li class="flex">
                            <i data-lucide="map-pin" class="h-6 w-6 text-secondary-400 mr-2"></i>
                            <span class="text-secondary-300">123 Faith Street, Anytown, USA</span>
                        </li>
                        <li class="flex">
                            <i data-lucide="mail" class="h-6 w-6 text-secondary-400 mr-2"></i>
                            <span class="text-secondary-300">info@gracecommunity.org</span>
                        </li>
                        <li class="flex">
                            <i data-lucide="phone" class="h-6 w-6 text-secondary-400 mr-2"></i>
                            <span class="text-secondary-300">(555) 123-4567</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="mt-12 border-t border-secondary-700 pt-8">
                <p class="text-base text-secondary-400 text-center">&copy; 2023 Grace Community Church. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        
        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
        
        // Initialize Lucide icons
        lucide.createIcons();
    </script>
</body>
</html>
