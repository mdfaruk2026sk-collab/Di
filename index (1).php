<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/bootstrap.php';
$lang = ($_GET['lang'] ?? 'bn') === 'en' ? 'en' : 'bn';
$announcement = setting('announcement', '');
$maintenance = setting('maintenance_mode', '0') === '1';
if ($maintenance && !is_admin()) {
    http_response_code(503);
}
?><!doctype html>
<html lang="<?= e($lang) ?>">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="theme-color" content="#090817">
<title>DIAMONDX BD — Free Fire Player Checker</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body data-lang="<?= e($lang) ?>">
<header class="nav">
 <a class="brand" href="index.php"><span class="brand-mark">◆</span><span>DIAMONDX <b>BD</b></span></a>
 <nav id="navLinks"><a href="#home" data-bn="হোম" data-en="Home">হোম</a><a href="#checker" data-bn="প্লেয়ার চেকার" data-en="Player Checker">প্লেয়ার চেকার</a><a href="#about" data-bn="আমাদের সম্পর্কে" data-en="About">আমাদের সম্পর্কে</a><a href="#contact" data-bn="যোগাযোগ" data-en="Contact">যোগাযোগ</a></nav>
 <div class="nav-actions"><button class="lang-btn" id="langToggle">বাংলা / EN</button><button class="menu-btn" id="menuBtn" aria-label="Menu">☰</button></div>
</header>
<?php if ($announcement !== ''): ?><div class="announcement"><?= e($announcement) ?></div><?php endif; ?>
<?php if ($maintenance && !is_admin()): ?>
<main class="maintenance"><div class="orb">✦</div><h1>সাইটটি সাময়িক রক্ষণাবেক্ষণে</h1><p>অনুগ্রহ করে কিছুক্ষণ পর আবার চেষ্টা করুন।</p></main>
<?php else: ?>
<main>
<section class="hero" id="home">
 <div class="eyebrow"><i></i><span data-bn="ফ্রি ফায়ার প্লেয়ার ইনফরমেশন" data-en="FREE FIRE PLAYER INFORMATION">ফ্রি ফায়ার প্লেয়ার ইনফরমেশন</span></div>
 <h1><span data-bn="আপনার প্লেয়ারকে" data-en="DISCOVER YOUR">আপনার প্লেয়ারকে</span><br><em data-bn="নতুনভাবে জানুন" data-en="PLAYER PROFILE">নতুনভাবে জানুন</em></h1>
 <p data-bn="UID লিখে প্লেয়ারের API-তে থাকা তথ্য সুন্দরভাবে দেখুন — কোনো টেকনিক্যাল কোড বা JSON নয়।" data-en="Enter a UID to explore available player information in a clean visual layout — no raw code or JSON.">UID লিখে প্লেয়ারের API-তে থাকা তথ্য সুন্দরভাবে দেখুন — কোনো টেকনিক্যাল কোড বা JSON নয়।</p>
 <div class="search-panel" id="checker">
  <label for="uid" data-bn="ফ্রি ফায়ার UID" data-en="FREE FIRE UID">ফ্রি ফায়ার UID</label>
  <div class="search-row"><input id="uid" inputmode="numeric" maxlength="20" placeholder="যেমন: 11621455669" autocomplete="off"><button id="searchBtn"><span>⌕</span> <span data-bn="প্লেয়ার খুঁজুন" data-en="Find Player">প্লেয়ার খুঁজুন</span></button></div>
  <div class="search-meta"><span><i class="status-dot" id="apiDot"></i><span id="apiStatus" data-bn="API স্ট্যাটাস যাচাই হয়নি" data-en="API status not checked">API স্ট্যাটাস যাচাই হয়নি</span></span><span data-bn="সার্চ ফলাফল API ডেটার ওপর নির্ভরশীল" data-en="Results depend on API data">সার্চ ফলাফল API ডেটার ওপর নির্ভরশীল</span></div>
 </div>
 <div class="progress-box" id="progressBox" hidden><div class="progress-label"><span data-bn="প্লেয়ার তথ্য লোড হচ্ছে" data-en="Loading player information">প্লেয়ার তথ্য লোড হচ্ছে</span><b id="progressValue">1%</b></div><div class="progress-track"><div id="progressBar"></div></div><small data-bn="এটি শুধু ভিজ্যুয়াল অ্যানিমেশন; API-এর প্রকৃত অগ্রগতি নয়।" data-en="Visual animation only; not actual API progress.">এটি শুধু ভিজ্যুয়াল অ্যানিমেশন; API-এর প্রকৃত অগ্রগতি নয়।</small></div>
 <div class="notice" id="notice" role="status" aria-live="polite"></div>
</section>
<section class="result-section" id="result" hidden>
 <div class="result-top"><div><div class="eyebrow">PLAYER PROFILE</div><h2 data-bn="প্লেয়ার প্রোফাইল" data-en="Player Profile">প্লেয়ার প্রোফাইল</h2></div><div class="profile-actions"><button class="soft-btn" id="copyUid">UID কপি</button><button class="soft-btn" id="copyName">নাম কপি</button><button class="soft-btn" id="shareBtn">শেয়ার</button><button class="soft-btn" id="downloadCard">PNG ডাউনলোড</button></div></div>
 <div id="profileCard" class="profile-card">
  <div class="profile-banner"><div class="avatar">♢</div><div class="player-identity"><span class="mini-label" data-bn="প্লেয়ার পরিচিতি" data-en="PLAYER IDENTITY">প্লেয়ার পরিচিতি</span><h3 id="playerName">N/A</h3><p>UID: <strong id="playerUid">N/A</strong></p><span class="region-pill" id="playerRegion">🌐 N/A</span></div><div class="profile-glow"></div></div>
  <div class="quick-stats" id="quickStats"></div>
  <div id="sections"></div>
 </div>
 <div class="compare-panel"><div><b data-bn="দুইজন প্লেয়ার তুলনা" data-en="Compare two players">দুইজন প্লেয়ার তুলনা</b><small data-bn="দ্বিতীয় UID দিলে পাশাপাশি তুলনা করতে পারবেন" data-en="Enter another UID to compare side by side">দ্বিতীয় UID দিলে পাশাপাশি তুলনা করতে পারবেন</small></div><div class="compare-row"><input id="compareUid" inputmode="numeric" placeholder="দ্বিতীয় Player UID"><button id="compareBtn">তুলনা</button></div><div id="compareResult"></div></div>
</section>
<section class="features" id="about"><div class="section-heading"><div class="eyebrow">WHY DIAMONDX BD</div><h2 data-bn="সহজ, দ্রুত ও ভিজ্যুয়াল" data-en="Simple, fast & visual">সহজ, দ্রুত ও ভিজ্যুয়াল</h2></div><div class="feature-grid">
 <article><span>◈</span><h3 data-bn="সুন্দর প্রোফাইল" data-en="Visual Profile">সুন্দর প্রোফাইল</h3><p data-bn="API-তে থাকা তথ্য আলাদা কার্ডে সাজানো হয়।" data-en="Available API fields are arranged into clear cards.">API-তে থাকা তথ্য আলাদা কার্ডে সাজানো হয়।</p></article>
 <article><span>⌁</span><h3 data-bn="দুই ভাষা" data-en="Two Languages">দুই ভাষা</h3><p data-bn="বাংলা ও ইংরেজিতে তথ্য দেখা যায়।" data-en="Switch between Bengali and English.">বাংলা ও ইংরেজিতে তথ্য দেখা যায়।</p></article>
 <article><span>⛨</span><h3 data-bn="নিরাপদ API প্রক্সি" data-en="Protected API Proxy">নিরাপদ API প্রক্সি</h3><p data-bn="API কল সার্ভার থেকে হয়; ব্রাউজারে ব্যাকএন্ড গোপন থাকে।" data-en="API requests run server-side; backend details stay private.">API কল সার্ভার থেকে হয়; ব্রাউজারে ব্যাকএন্ড গোপন থাকে।</p></article>
</div></section>
<section class="recent" id="recentSection"><div class="section-heading"><div class="eyebrow">YOUR ACTIVITY</div><h2 data-bn="সাম্প্রতিক সার্চ" data-en="Recent Searches">সাম্প্রতিক সার্চ</h2></div><div id="recentList" class="recent-list"><p class="muted" data-bn="এখনো কোনো সার্চ নেই।" data-en="No searches yet.">এখনো কোনো সার্চ নেই।</p></div></section>
<section class="contact" id="contact"><div><div class="eyebrow">CONTACT</div><h2 data-bn="DIAMONDX BD" data-en="DIAMONDX BD">DIAMONDX BD</h2><p data-bn="এই ওয়েবসাইটটি তৃতীয় পক্ষের তথ্য প্রদর্শক; Garena-এর অফিসিয়াল সাইট নয়।" data-en="This is a third-party information viewer and is not affiliated with Garena.">এই ওয়েবসাইটটি তৃতীয় পক্ষের তথ্য প্রদর্শক; Garena-এর অফিসিয়াল সাইট নয়।</p></div><a class="soft-btn" href="mailto:admin@example.com">ইমেইল</a></section>
</main>
<?php endif; ?>
<footer><a class="brand" href="#"><span class="brand-mark">◆</span><span>DIAMONDX <b>BD</b></span></a><p>© <?= date('Y') ?> DIAMONDX BD. All rights reserved.</p><a href="../admin/index.php">Admin</a></footer>
<script src="assets/js/app.js" defer></script>
</body></html>
