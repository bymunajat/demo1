<?php
session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: login.php");
    exit;
}

// DEMO: session storage untuk Ads dan Ads Code
if (!isset($_SESSION['ads'])) $_SESSION['ads'] = [];
if (!isset($_SESSION['ads_code'])) $_SESSION['ads_code'] = '';

// Handle tambah iklan (demo)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_ad'])) {
        $ad_name = htmlspecialchars($_POST['ad_name']);
        $ad_image = htmlspecialchars($_POST['ad_image']);
        $ad_link = htmlspecialchars($_POST['ad_link']);
        $ad_status = isset($_POST['ad_status']) ? 'active' : 'inactive';
        $_SESSION['ads'][] = ['name'=>$ad_name,'image'=>$ad_image,'link'=>$ad_link,'status'=>$ad_status];
    }
    if (isset($_POST['save_ads_code'])) {
        $_SESSION['ads_code'] = $_POST['ads_code'];
    }
}
?>
<!DOCTYPE html>
<html lang="en" x-data="{ tab: 'dashboard' }">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard — Instagram Downloader</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="//unpkg.com/alpinejs" defer></script>
</head>
<body class="bg-gray-900 text-gray-200 min-h-screen flex">

<!-- SIDEBAR -->
<aside class="w-64 bg-gray-800 border-r border-gray-700 hidden md:block">
<div class="p-6 text-2xl font-bold text-green-500">Admin Panel</div>
<nav class="px-4 space-y-2 text-sm">
    <button @click="tab='dashboard'" :class="tab==='dashboard'?'bg-gray-700':'hover:bg-gray-700'" class="w-full text-left px-4 py-2 rounded">Dashboard</button>
    <button @click="tab='downloads'" :class="tab==='downloads'?'bg-gray-700':'hover:bg-gray-700'" class="w-full text-left px-4 py-2 rounded">Downloads</button>
    <button @click="tab='api'" :class="tab==='api'?'bg-gray-700':'hover:bg-gray-700'" class="w-full text-left px-4 py-2 rounded">API Status</button>
    <button @click="tab='logs'" :class="tab==='logs'?'bg-gray-700':'hover:bg-gray-700'" class="w-full text-left px-4 py-2 rounded">Logs</button>
    <button @click="tab='users'" :class="tab==='users'?'bg-gray-700':'hover:bg-gray-700'" class="w-full text-left px-4 py-2 rounded">Users</button>
    <button @click="tab='settings'" :class="tab==='settings'?'bg-gray-700':'hover:bg-gray-700'" class="w-full text-left px-4 py-2 rounded">Settings</button>
    <button @click="tab='ads_code'" :class="tab==='ads_code'?'bg-gray-700':'hover:bg-gray-700'" class="w-full text-left px-4 py-2 rounded">Ads Code</button>
    <a href="logout.php" class="block px-4 py-2 rounded text-red-500 hover:text-red-400 mt-4">Logout</a>
</nav>
</aside>

<!-- MAIN CONTENT -->
<main class="flex-1 p-6">

<!-- HEADER -->
<div class="flex justify-between items-center mb-8">
    <h1 class="text-3xl font-bold" x-text="tab.charAt(0).toUpperCase() + tab.slice(1)"></h1>
</div>

<!-- DASHBOARD TAB -->
<div x-show="tab==='dashboard'" class="space-y-10">

<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
    <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow">
        <p class="text-sm text-gray-400">Total Downloads</p>
        <p class="text-3xl font-bold mt-2">1,284</p>
    </div>
    <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow">
        <p class="text-sm text-gray-400">Requests Today</p>
        <p class="text-3xl font-bold mt-2">76</p>
    </div>
    <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow">
        <p class="text-sm text-gray-400">Failed Downloads</p>
        <p class="text-3xl font-bold mt-2 text-red-400">5</p>
    </div>
    <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow">
        <p class="text-sm text-gray-400">Cobalt API</p>
        <p class="mt-2 text-green-400 font-medium">Connected</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow">
        <h2 class="text-lg font-semibold mb-4">Downloads Last 7 Days</h2>
        <canvas id="lineChart" class="w-full h-60"></canvas>
    </div>
    <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow">
        <h2 class="text-lg font-semibold mb-4">Download Type Distribution</h2>
        <canvas id="pieChart" class="w-full h-60"></canvas>
    </div>
</div>

<!-- Active Ads Preview -->
<div class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow mt-6">
    <h2 class="text-lg font-semibold mb-4">Active Ads Preview</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <?php foreach($_SESSION['ads'] as $ad): ?>
            <?php if($ad['status']==='active'): ?>
            <a href="<?php echo $ad['link']; ?>" target="_blank" class="block bg-gray-700 rounded overflow-hidden shadow hover:shadow-lg">
                <img src="<?php echo $ad['image']; ?>" alt="<?php echo $ad['name']; ?>" class="w-full h-32 object-cover">
                <div class="p-2 text-sm font-medium"><?php echo $ad['name']; ?></div>
            </a>
            <?php endif; ?>
        <?php endforeach; ?>
        <?php if(!array_filter($_SESSION['ads'], fn($a)=>$a['status']==='active')): ?>
            <p class="text-gray-400 col-span-full">No active ads yet.</p>
        <?php endif; ?>
    </div>
</div>

</div>

<!-- DOWNLOADS TAB -->
<div x-show="tab==='downloads'" class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow mb-10">
<h2 class="text-lg font-semibold mb-4">Downloads</h2>
<table class="w-full text-sm">
<thead class="text-gray-400 border-b border-gray-700">
<tr>
<th class="py-2 text-left">Time</th>
<th class="py-2 text-left">Type</th>
<th class="py-2 text-left">File Name</th>
<th class="py-2 text-left">Status</th>
</tr>
</thead>
<tbody class="text-gray-300">
<tr class="border-b border-gray-700"><td>10:21</td><td>Video</td><td>vid123.mp4</td><td class="text-green-400">Success</td></tr>
<tr class="border-b border-gray-700"><td>10:15</td><td>Image</td><td>img456.jpg</td><td class="text-green-400">Success</td></tr>
<tr><td>10:01</td><td>Carousel</td><td>car789.zip</td><td class="text-yellow-400">Partial</td></tr>
</tbody>
</table>
</div>

<!-- API TAB -->
<div x-show="tab==='api'" class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow mb-10">
<h2 class="text-lg font-semibold mb-4">API Status</h2>
<p class="text-sm text-gray-400 mb-2">API URL: <span class="text-gray-200">http://localhost:9000</span></p>
<p class="text-sm text-gray-400 mb-2">Last Request: <span class="text-gray-200">2 minutes ago</span></p>
<p class="text-sm text-gray-400 mb-2">Status: <span class="text-green-400">Online</span></p>
<button class="mt-4 bg-green-600 hover:bg-green-500 text-white px-4 py-2 rounded">Test API</button>
</div>

<!-- LOGS TAB -->
<div x-show="tab==='logs'" class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow mb-10">
<h2 class="text-lg font-semibold mb-4">Recent Logs</h2>
<p class="text-gray-400">No logs yet (demo version).</p>
</div>

<!-- USERS TAB -->
<div x-show="tab==='users'" class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow mb-10">
<h2 class="text-lg font-semibold mb-4">Users</h2>
<table class="w-full text-sm">
<thead class="text-gray-400 border-b border-gray-700">
<tr>
<th class="py-2 text-left">ID</th>
<th class="py-2 text-left">Username</th>
<th class="py-2 text-left">Role</th>
<th class="py-2 text-left">Status</th>
</tr>
</thead>
<tbody class="text-gray-300">
<tr class="border-b border-gray-700"><td>1</td><td>admin</td><td>Admin</td><td class="text-green-400">Active</td></tr>
<tr class="border-b border-gray-700"><td>2</td><td>moderator</td><td>Moderator</td><td class="text-green-400">Active</td></tr>
</tbody>
</table>
</div>

<!-- SETTINGS TAB -->
<div x-show="tab==='settings'" class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow mb-10">
<h2 class="text-lg font-semibold mb-4">Settings</h2>
<div class="space-y-4 text-sm">
<div class="flex justify-between items-center">
<span>Maintenance Mode</span>
<span class="text-gray-400">OFF</span>
</div>
<div class="flex justify-between items-center">
<span>Default Language</span>
<span class="text-gray-400">English</span>
</div>
<div class="flex justify-between items-center">
<span>Download Limit</span>
<span class="text-gray-400">Unlimited</span>
</div>
</div>
</div>

<!-- ADS CODE TAB -->
<div x-show="tab==='ads_code'" class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow mb-10">
<h2 class="text-lg font-semibold mb-4">Ads Code</h2>
<p class="text-gray-400 mb-4">Paste your HTML / JS ad code here. It will render on dashboard/main page.</p>
<form method="POST" class="space-y-4">
<textarea name="ads_code" rows="10" class="w-full p-3 rounded bg-gray-700 border border-gray-600 text-gray-200"><?php echo htmlspecialchars($_SESSION['ads_code']); ?></textarea>
<button type="submit" name="save_ads_code" class="bg-green-600 hover:bg-green-500 text-white px-4 py-2 rounded">Save Ads Code</button>
</form>

<!-- Preview -->
<div class="mt-6">
<h3 class="text-md font-semibold mb-2">Preview:</h3>
<div class="bg-gray-900 p-4 rounded border border-gray-700">
<?php echo $_SESSION['ads_code']; ?>
</div>
</div>
</div>

</main>

<!-- CHARTS.JS -->
<script>
const lineCtx = document.getElementById('lineChart').getContext('2d');
const lineChart = new Chart(lineCtx, {
    type: 'line',
    data: {
        labels: ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
        datasets: [{ label:'Downloads', data:[120,190,140,230,180,200,250], borderColor:'rgba(34,197,94,1)', backgroundColor:'rgba(34,197,94,0.2)', tension:0.3, fill:true }]
    },
    options: {
        responsive:true,
        plugins:{ legend:{ labels:{ color:'#D1D5DB' } } },
        scales: { x:{ ticks:{ color:'#9CA3AF' }, grid:{ color:'#374151' } }, y:{ ticks:{ color:'#9CA3AF' }, grid:{ color:'#374151' } } }
    }
});

const pieCtx = document.getElementById('pieChart').getContext('2d');
const pieChart = new Chart(pieCtx, {
    type:'pie',
    data:{ labels:['Image','Video','Carousel'], datasets:[{ data:[500,600,184], backgroundColor:['#22c55e','#3b82f6','#facc15'], borderColor:'#1f2937', borderWidth:2 }] },
    options:{ responsive:true, plugins:{ legend:{ labels:{ color:'#D1D5DB' } } } }
});
</script>

</body>
</html>
