<?php
/**
 * Grant Archive Template - Minna Bank Style Edition v1.0
 * File: archive-grant.php
 * 
 * みんなの銀行スタイルデザイン - プロフェッショナル & スタイリッシュ
 * 上部詳細フィルター・横バー要素・白黒アイコン完全対応
 * 
 * @package Grant_Insight_Minna_Bank
 * @version 1.0.0
 */

// セキュリティチェック
if (!defined('ABSPATH')) {
    exit;
}

get_header();

// URLパラメータから検索条件を取得
$search_params = [
    'search' => sanitize_text_field($_GET['s'] ?? ''),
    'category' => sanitize_text_field($_GET['category'] ?? $_GET['grant_category'] ?? ''),
    'prefecture' => sanitize_text_field($_GET['prefecture'] ?? ''),
    'municipality' => sanitize_text_field($_GET['municipality'] ?? ''),
    'amount' => sanitize_text_field($_GET['amount'] ?? ''),
    'status' => sanitize_text_field($_GET['status'] ?? ''),
    'difficulty' => sanitize_text_field($_GET['difficulty'] ?? ''),
    'success_rate' => sanitize_text_field($_GET['success_rate'] ?? ''),
    'application_method' => sanitize_text_field($_GET['method'] ?? ''),
    'is_featured' => sanitize_text_field($_GET['featured'] ?? ''),
    'sort' => sanitize_text_field($_GET['sort'] ?? 'date_desc'),
    'view' => sanitize_text_field($_GET['view'] ?? 'grid'),
    'page' => max(1, intval($_GET['paged'] ?? 1))
];

// 統計データ取得
$stats = function_exists('gi_get_cached_stats') ? gi_get_cached_stats() : [
    'total_grants' => wp_count_posts('grant')->publish ?? 0,
    'active_grants' => 0,
    'prefecture_count' => count(get_terms(['taxonomy' => 'grant_prefecture', 'hide_empty' => true])),
    'avg_success_rate' => 65
];

// タクソノミー取得
$all_categories = get_terms([
    'taxonomy' => 'grant_category',
    'hide_empty' => true,
    'orderby' => 'count',
    'order' => 'DESC'
]);

$all_prefectures = get_terms([
    'taxonomy' => 'grant_prefecture',
    'hide_empty' => true,
    'orderby' => 'name',
    'order' => 'ASC'
]);
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>助成金・補助金検索 | <?php bloginfo('name'); ?></title>
    
    <!-- Preload Critical Resources -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" as="style">
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;600;700;800;900&display=swap" as="style">
    
    <style>
    /* ===== MINNA BANK DESIGN SYSTEM - GRANT ARCHIVE TEMPLATE ===== */
    :root {
        /* Minna Bank Color System - Professional Monochrome */
        --minna-primary: #000000;
        --minna-secondary: #1a1a1a;
        --minna-accent: #262626;
        
        /* Grayscale Palette */
        --minna-white: #ffffff;
        --minna-gray-50: #fafafa;
        --minna-gray-100: #f5f5f5;
        --minna-gray-200: #e5e5e5;
        --minna-gray-300: #d4d4d4;
        --minna-gray-400: #a3a3a3;
        --minna-gray-500: #737373;
        --minna-gray-600: #525252;
        --minna-gray-700: #404040;
        --minna-gray-800: #262626;
        --minna-gray-900: #171717;
        
        /* Semantic Colors */
        --minna-success: #22c55e;
        --minna-warning: #f59e0b;
        --minna-danger: #ef4444;
        --minna-info: #3b82f6;
        
        /* Typography System */
        --minna-font-primary: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
        --minna-font-japanese: 'Noto Sans JP', 'Hiragino Kaku Gothic ProN', 'Hiragino Sans', 'Yu Gothic Medium', sans-serif;
        
        /* Spacing Scale - Minna Bank Style */
        --space-xs: 0.25rem;
        --space-sm: 0.5rem;
        --space-md: 0.75rem;
        --space-lg: 1rem;
        --space-xl: 1.25rem;
        --space-2xl: 1.5rem;
        --space-3xl: 2rem;
        --space-4xl: 2.5rem;
        --space-5xl: 3rem;
        --space-6xl: 4rem;
        
        /* Border Radius */
        --radius-sm: 0.25rem;
        --radius-md: 0.375rem;
        --radius-lg: 0.5rem;
        --radius-xl: 0.75rem;
        --radius-2xl: 1rem;
        
        /* Shadows */
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        
        /* Transitions */
        --transition-fast: all 0.15s ease;
        --transition-medium: all 0.3s ease;
        --transition-slow: all 0.5s ease;
        
        /* Layout */
        --container-max: 1400px;
        --header-height: 80px;
        --filter-height: 120px;
    }

    /* ===== RESET & BASE STYLES ===== */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: var(--minna-font-japanese);
        background: var(--minna-gray-50);
        color: var(--minna-gray-900);
        line-height: 1.6;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    .minna-container {
        max-width: var(--container-max);
        margin: 0 auto;
        padding: 0 var(--space-lg);
    }

    @media (max-width: 768px) {
        .minna-container {
            padding: 0 var(--space-md);
        }
    }

    /* ===== MINNA BANK HEADER SECTION ===== */
    .minna-hero-section {
        background: linear-gradient(135deg, var(--minna-primary) 0%, var(--minna-gray-800) 100%);
        color: var(--minna-white);
        padding: var(--space-5xl) 0 var(--space-4xl);
        position: relative;
        overflow: hidden;
    }

    .minna-hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.03)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
        opacity: 0.4;
    }

    .minna-hero-content {
        position: relative;
        z-index: 2;
        text-align: center;
    }

    .minna-hero-title {
        font-size: 3rem;
        font-weight: 900;
        margin-bottom: var(--space-lg);
        letter-spacing: -0.02em;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .minna-hero-subtitle {
        font-size: 1.125rem;
        font-weight: 400;
        opacity: 0.9;
        max-width: 600px;
        margin: 0 auto var(--space-3xl);
        line-height: 1.7;
    }

    .minna-stats-bar {
        display: flex;
        justify-content: center;
        gap: var(--space-4xl);
        margin-top: var(--space-3xl);
    }

    .minna-stat-item {
        text-align: center;
        opacity: 0.95;
    }

    .minna-stat-number {
        font-size: 2.5rem;
        font-weight: 800;
        display: block;
        color: var(--minna-white);
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .minna-stat-label {
        font-size: 0.875rem;
        font-weight: 500;
        opacity: 0.8;
        margin-top: var(--space-xs);
    }

    @media (max-width: 768px) {
        .minna-hero-title {
            font-size: 2rem;
        }
        
        .minna-hero-subtitle {
            font-size: 1rem;
            margin-bottom: var(--space-2xl);
        }
        
        .minna-stats-bar {
            gap: var(--space-2xl);
            flex-wrap: wrap;
        }
        
        .minna-stat-number {
            font-size: 1.75rem;
        }
    }

    /* ===== ADVANCED TOP FILTER SYSTEM ===== */
    .minna-advanced-filters {
        background: var(--minna-white);
        border-bottom: 1px solid var(--minna-gray-200);
        padding: var(--space-2xl) 0;
        position: sticky;
        top: 0;
        z-index: 100;
        box-shadow: var(--shadow-sm);
    }

    .minna-filter-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: var(--space-xl);
    }

    .minna-filter-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--minna-gray-900);
        display: flex;
        align-items: center;
        gap: var(--space-sm);
    }

    .minna-filter-icon {
        width: 24px;
        height: 24px;
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 2v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"/></svg>');
        background-repeat: no-repeat;
        background-position: center;
        background-size: contain;
    }

    .minna-filter-actions {
        display: flex;
        gap: var(--space-md);
        align-items: center;
    }

    .minna-filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: var(--space-xl);
        margin-bottom: var(--space-xl);
    }

    .minna-filter-group {
        background: var(--minna-gray-50);
        border: 1px solid var(--minna-gray-200);
        border-radius: var(--radius-lg);
        padding: var(--space-lg);
    }

    .minna-filter-group-title {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--minna-gray-700);
        margin-bottom: var(--space-md);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: flex;
        align-items: center;
        gap: var(--space-sm);
    }

    .minna-search-input {
        width: 100%;
        padding: var(--space-md) var(--space-lg);
        border: 2px solid var(--minna-gray-200);
        border-radius: var(--radius-lg);
        font-size: 1rem;
        font-weight: 400;
        background: var(--minna-white);
        transition: var(--transition-fast);
        box-shadow: var(--shadow-sm);
    }

    .minna-search-input:focus {
        outline: none;
        border-color: var(--minna-primary);
        box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.1);
        transform: translateY(-1px);
    }

    .minna-select {
        width: 100%;
        padding: var(--space-md) var(--space-lg);
        border: 2px solid var(--minna-gray-200);
        border-radius: var(--radius-lg);
        background: var(--minna-white);
        font-size: 0.875rem;
        color: var(--minna-gray-700);
        cursor: pointer;
        transition: var(--transition-fast);
        box-shadow: var(--shadow-sm);
        appearance: none;
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="black"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>');
        background-repeat: no-repeat;
        background-position: right var(--space-md) center;
        background-size: 16px;
        padding-right: var(--space-4xl);
    }

    .minna-select:focus {
        outline: none;
        border-color: var(--minna-primary);
        box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.1);
    }

    /* ===== HORIZONTAL STATUS BARS ===== */
    .minna-status-bars {
        background: var(--minna-white);
        padding: var(--space-lg) 0;
        border-bottom: 1px solid var(--minna-gray-200);
    }

    .minna-status-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: var(--space-lg);
    }

    .minna-status-bar {
        background: linear-gradient(135deg, var(--minna-gray-50) 0%, var(--minna-white) 100%);
        border: 1px solid var(--minna-gray-200);
        border-radius: var(--radius-xl);
        padding: var(--space-lg);
        display: flex;
        align-items: center;
        gap: var(--space-lg);
        transition: var(--transition-medium);
        cursor: pointer;
    }

    .minna-status-bar:hover {
        border-color: var(--minna-gray-300);
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    .minna-status-icon {
        width: 48px;
        height: 48px;
        background: var(--minna-gray-900);
        border-radius: var(--radius-lg);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .minna-status-icon svg {
        width: 24px;
        height: 24px;
        color: var(--minna-white);
    }

    .minna-status-content {
        flex: 1;
    }

    .minna-status-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--minna-gray-900);
        margin-bottom: var(--space-xs);
    }

    .minna-status-description {
        font-size: 0.875rem;
        color: var(--minna-gray-600);
        line-height: 1.5;
    }

    .minna-status-count {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--minna-primary);
        text-align: center;
        min-width: 60px;
    }

    /* ===== QUICK FILTER PILLS ===== */
    .minna-quick-filters {
        background: var(--minna-white);
        padding: var(--space-xl) 0;
        border-bottom: 1px solid var(--minna-gray-200);
    }

    .minna-filter-pills {
        display: flex;
        gap: var(--space-md);
        align-items: center;
        flex-wrap: wrap;
        margin-bottom: var(--space-lg);
    }

    .minna-filter-pill {
        padding: var(--space-md) var(--space-xl);
        background: var(--minna-gray-100);
        border: 2px solid var(--minna-gray-200);
        border-radius: 9999px;
        color: var(--minna-gray-700);
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: var(--transition-fast);
        display: flex;
        align-items: center;
        gap: var(--space-sm);
        white-space: nowrap;
        user-select: none;
    }

    .minna-filter-pill:hover {
        background: var(--minna-gray-200);
        border-color: var(--minna-gray-300);
        transform: translateY(-1px);
    }

    .minna-filter-pill.active {
        background: var(--minna-primary);
        color: var(--minna-white);
        border-color: var(--minna-primary);
    }

    .minna-filter-pill .count {
        background: rgba(255, 255, 255, 0.3);
        padding: var(--space-xs) var(--space-sm);
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        min-width: 20px;
        text-align: center;
    }

    .minna-filter-pill.active .count {
        background: rgba(255, 255, 255, 0.2);
    }

    /* ===== MAIN CONTENT AREA ===== */
    .minna-main-content {
        padding: var(--space-3xl) 0;
    }

    .minna-content-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: var(--space-2xl);
    }

    .minna-results-info {
        color: var(--minna-gray-600);
        font-size: 0.875rem;
    }

    .minna-view-controls {
        display: flex;
        gap: var(--space-sm);
        align-items: center;
    }

    .minna-view-btn {
        padding: var(--space-sm) var(--space-md);
        background: var(--minna-gray-100);
        border: 1px solid var(--minna-gray-200);
        border-radius: var(--radius-md);
        color: var(--minna-gray-700);
        cursor: pointer;
        transition: var(--transition-fast);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .minna-view-btn:hover {
        background: var(--minna-gray-200);
    }

    .minna-view-btn.active {
        background: var(--minna-primary);
        color: var(--minna-white);
        border-color: var(--minna-primary);
    }

    .minna-view-icon {
        width: 18px;
        height: 18px;
        background-repeat: no-repeat;
        background-position: center;
        background-size: contain;
    }

    .grid-icon {
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>');
    }

    .list-icon {
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>');
    }

    /* ===== GRANTS GRID LAYOUT ===== */
    .minna-grants-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
        gap: var(--space-2xl);
        margin-bottom: var(--space-3xl);
    }

    .minna-grants-list {
        display: flex;
        flex-direction: column;
        gap: var(--space-lg);
        margin-bottom: var(--space-3xl);
    }

    /* ===== GRANT CARD STYLING ===== */
    .minna-grant-card {
        background: var(--minna-white);
        border: 2px solid var(--minna-gray-200);
        border-radius: var(--radius-xl);
        overflow: hidden;
        transition: var(--transition-medium);
        cursor: pointer;
        position: relative;
    }

    .minna-grant-card:hover {
        border-color: var(--minna-gray-400);
        box-shadow: var(--shadow-xl);
        transform: translateY(-4px);
    }

    .minna-grant-card-header {
        padding: var(--space-xl);
        background: linear-gradient(135deg, var(--minna-gray-50) 0%, var(--minna-white) 100%);
        border-bottom: 1px solid var(--minna-gray-200);
        position: relative;
    }

    .minna-grant-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--minna-gray-900);
        line-height: 1.4;
        margin-bottom: var(--space-md);
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .minna-grant-organization {
        font-size: 0.875rem;
        color: var(--minna-gray-600);
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: var(--space-sm);
    }

    .minna-grant-card-body {
        padding: var(--space-xl);
    }

    .minna-grant-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: var(--space-lg);
        margin-bottom: var(--space-lg);
    }

    .minna-info-item {
        display: flex;
        align-items: center;
        gap: var(--space-sm);
        font-size: 0.875rem;
    }

    .minna-info-icon {
        width: 20px;
        height: 20px;
        background-repeat: no-repeat;
        background-position: center;
        background-size: contain;
        flex-shrink: 0;
    }

    .minna-info-value {
        font-weight: 600;
        color: var(--minna-gray-900);
    }

    /* ===== BLACK & WHITE ICONS ONLY ===== */
    .icon-money {
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="black"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>');
    }

    .icon-calendar {
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="black"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>');
    }

    .icon-building {
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="black"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>');
    }

    .icon-chart {
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="black"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>');
    }

    .icon-location {
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="black"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>');
    }

    .icon-clock {
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="black"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>');
    }

    .icon-target {
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="black"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>');
    }

    /* ===== RESPONSIVE DESIGN ===== */
    @media (max-width: 768px) {
        .minna-filter-grid {
            grid-template-columns: 1fr;
            gap: var(--space-lg);
        }
        
        .minna-status-grid {
            grid-template-columns: 1fr;
        }
        
        .minna-grants-grid {
            grid-template-columns: 1fr;
            gap: var(--space-lg);
        }
        
        .minna-content-header {
            flex-direction: column;
            gap: var(--space-lg);
            align-items: stretch;
        }
        
        .minna-filter-pills {
            justify-content: center;
        }
    }

    /* ===== LOADING & NO RESULTS STATES ===== */
    .minna-loading {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: var(--space-6xl);
        color: var(--minna-gray-600);
    }

    .minna-spinner {
        width: 40px;
        height: 40px;
        border: 4px solid var(--minna-gray-200);
        border-top: 4px solid var(--minna-primary);
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-right: var(--space-lg);
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .minna-no-results {
        text-align: center;
        padding: var(--space-6xl);
        background: var(--minna-white);
        border: 2px solid var(--minna-gray-200);
        border-radius: var(--radius-xl);
        margin: var(--space-3xl) 0;
    }

    .minna-no-results-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto var(--space-xl);
        background: var(--minna-gray-200);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="gray"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>');
        background-repeat: no-repeat;
        background-position: center;
        background-size: 40px;
    }

    .minna-no-results-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--minna-gray-900);
        margin-bottom: var(--space-md);
    }

    .minna-no-results-text {
        color: var(--minna-gray-600);
        margin-bottom: var(--space-xl);
        max-width: 500px;
        margin-left: auto;
        margin-right: auto;
    }

    /* ===== BUTTON STYLES ===== */
    .minna-btn {
        padding: var(--space-md) var(--space-xl);
        border-radius: var(--radius-lg);
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition-fast);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: var(--space-sm);
        text-decoration: none;
        border: 2px solid transparent;
        font-size: 0.875rem;
    }

    .minna-btn-primary {
        background: var(--minna-primary);
        color: var(--minna-white);
        border-color: var(--minna-primary);
    }

    .minna-btn-primary:hover {
        background: var(--minna-gray-800);
        border-color: var(--minna-gray-800);
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }

    .minna-btn-secondary {
        background: var(--minna-gray-100);
        color: var(--minna-gray-700);
        border-color: var(--minna-gray-200);
    }

    .minna-btn-secondary:hover {
        background: var(--minna-gray-200);
        border-color: var(--minna-gray-300);
        transform: translateY(-1px);
    }

    /* ===== ACCESSIBILITY ===== */
    @media (prefers-reduced-motion: reduce) {
        * {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
    }

    /* Focus visible for keyboard navigation */
    .minna-btn:focus-visible,
    .minna-filter-pill:focus-visible,
    .minna-select:focus-visible,
    .minna-search-input:focus-visible {
        outline: 3px solid var(--minna-info);
        outline-offset: 2px;
    }
    </style>
    
    <!-- Load fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>

<body class="minna-archive-page">

    <!-- Minna Bank Style Hero Section -->
    <section class="minna-hero-section">
        <div class="minna-container">
            <div class="minna-hero-content">
                <h1 class="minna-hero-title">助成金・補助金検索</h1>
                <p class="minna-hero-subtitle">
                    全国の助成金・補助金を簡単検索。プロフェッショナルなツールで、あなたにピッタリの制度を見つけてビジネスの成長を支援します。
                </p>
                
                <!-- Statistics Bar -->
                <div class="minna-stats-bar">
                    <div class="minna-stat-item">
                        <span class="minna-stat-number"><?php echo number_format($stats['total_grants']); ?></span>
                        <span class="minna-stat-label">総助成金数</span>
                    </div>
                    <div class="minna-stat-item">
                        <span class="minna-stat-number"><?php echo number_format($stats['active_grants']); ?></span>
                        <span class="minna-stat-label">募集中</span>
                    </div>
                    <div class="minna-stat-item">
                        <span class="minna-stat-number"><?php echo $stats['prefecture_count']; ?></span>
                        <span class="minna-stat-label">対応地域</span>
                    </div>
                    <div class="minna-stat-item">
                        <span class="minna-stat-number"><?php echo $stats['avg_success_rate']; ?>%</span>
                        <span class="minna-stat-label">平均採択率</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Advanced Top Filter System -->
    <section class="minna-advanced-filters">
        <div class="minna-container">
            <div class="minna-filter-header">
                <h2 class="minna-filter-title">
                    <div class="minna-filter-icon"></div>
                    詳細検索フィルター
                </h2>
                <div class="minna-filter-actions">
                    <button class="minna-btn minna-btn-secondary" onclick="clearAllFilters()">
                        <div class="icon-target" style="width: 16px; height: 16px;"></div>
                        フィルターをクリア
                    </button>
                    <button class="minna-btn minna-btn-primary" onclick="applyFilters()">
                        <div class="icon-search" style="width: 16px; height: 16px; background-image: url('data:image/svg+xml;utf8,<svg xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 24 24\" stroke=\"white\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z\"/></svg>');"></div>
                        検索実行
                    </button>
                </div>
            </div>
            
            <div class="minna-filter-grid">
                <!-- Search Input -->
                <div class="minna-filter-group">
                    <div class="minna-filter-group-title">
                        <div class="icon-search" style="width: 14px; height: 14px; background-image: url('data:image/svg+xml;utf8,<svg xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 24 24\" stroke=\"black\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z\"/></svg>');"></div>
                        キーワード検索
                    </div>
                    <input type="text" 
                           class="minna-search-input" 
                           placeholder="AI検索：「DXを推進したい」「環境に優しい事業」など自然な言葉で検索..."
                           value="<?php echo esc_attr($search_params['search']); ?>"
                           id="minna-search-input">
                </div>

                <!-- Category Filter -->
                <div class="minna-filter-group">
                    <div class="minna-filter-group-title">
                        <div class="icon-target" style="width: 14px; height: 14px;"></div>
                        カテゴリー
                    </div>
                    <select class="minna-select" id="category-filter">
                        <option value="">すべてのカテゴリー</option>
                        <?php foreach ($all_categories as $category): ?>
                            <option value="<?php echo esc_attr($category->slug); ?>" 
                                    <?php selected($search_params['category'], $category->slug); ?>>
                                <?php echo esc_html($category->name); ?> (<?php echo $category->count; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Prefecture Filter -->
                <div class="minna-filter-group">
                    <div class="minna-filter-group-title">
                        <div class="icon-location" style="width: 14px; height: 14px;"></div>
                        都道府県
                    </div>
                    <select class="minna-select" id="prefecture-filter">
                        <option value="">全国対応</option>
                        <?php foreach ($all_prefectures as $prefecture): ?>
                            <option value="<?php echo esc_attr($prefecture->slug); ?>" 
                                    <?php selected($search_params['prefecture'], $prefecture->slug); ?>>
                                <?php echo esc_html($prefecture->name); ?> (<?php echo $prefecture->count; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Amount Range Filter -->
                <div class="minna-filter-group">
                    <div class="minna-filter-group-title">
                        <div class="icon-money" style="width: 14px; height: 14px;"></div>
                        助成金額
                    </div>
                    <select class="minna-select" id="amount-filter">
                        <option value="">すべての金額</option>
                        <option value="0-100" <?php selected($search_params['amount'], '0-100'); ?>>〜100万円</option>
                        <option value="100-500" <?php selected($search_params['amount'], '100-500'); ?>>100万円〜500万円</option>
                        <option value="500-1000" <?php selected($search_params['amount'], '500-1000'); ?>>500万円〜1000万円</option>
                        <option value="1000-3000" <?php selected($search_params['amount'], '1000-3000'); ?>>1000万円〜3000万円</option>
                        <option value="3000+" <?php selected($search_params['amount'], '3000+'); ?>>3000万円以上</option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="minna-filter-group">
                    <div class="minna-filter-group-title">
                        <div class="icon-clock" style="width: 14px; height: 14px;"></div>
                        募集状況
                    </div>
                    <select class="minna-select" id="status-filter">
                        <option value="">すべてのステータス</option>
                        <option value="active" <?php selected($search_params['status'], 'active'); ?>>募集中</option>
                        <option value="upcoming" <?php selected($search_params['status'], 'upcoming'); ?>>募集予定</option>
                        <option value="closed" <?php selected($search_params['status'], 'closed'); ?>>募集終了</option>
                    </select>
                </div>

                <!-- Sort Filter -->
                <div class="minna-filter-group">
                    <div class="minna-filter-group-title">
                        <div class="icon-chart" style="width: 14px; height: 14px;"></div>
                        並び順
                    </div>
                    <select class="minna-select" id="sort-filter">
                        <option value="date_desc" <?php selected($search_params['sort'], 'date_desc'); ?>>新着順</option>
                        <option value="featured_first" <?php selected($search_params['sort'], 'featured_first'); ?>>おすすめ順</option>
                        <option value="amount_desc" <?php selected($search_params['sort'], 'amount_desc'); ?>>金額が高い順</option>
                        <option value="deadline_asc" <?php selected($search_params['sort'], 'deadline_asc'); ?>>締切が近い順</option>
                        <option value="success_rate_desc" <?php selected($search_params['sort'], 'success_rate_desc'); ?>>採択率順</option>
                    </select>
                </div>
            </div>
        </div>
    </section>

    <!-- Horizontal Status Bars -->
    <section class="minna-status-bars">
        <div class="minna-container">
            <div class="minna-status-grid">
                <div class="minna-status-bar" onclick="filterByStatus('active')">
                    <div class="minna-status-icon">
                        <div class="icon-clock" style="width: 24px; height: 24px; background-image: url('data:image/svg+xml;utf8,<svg xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 24 24\" stroke=\"white\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z\"/></svg>');"></div>
                    </div>
                    <div class="minna-status-content">
                        <div class="minna-status-title">募集中の助成金</div>
                        <div class="minna-status-description">現在申請可能な助成金制度</div>
                    </div>
                    <div class="minna-status-count"><?php echo number_format($stats['active_grants']); ?></div>
                </div>

                <div class="minna-status-bar" onclick="filterByAmount('1000+')">
                    <div class="minna-status-icon">
                        <div class="icon-money" style="width: 24px; height: 24px; background-image: url('data:image/svg+xml;utf8,<svg xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 24 24\" stroke=\"white\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1\"/></svg>');"></div>
                    </div>
                    <div class="minna-status-content">
                        <div class="minna-status-title">高額助成金</div>
                        <div class="minna-status-description">1000万円以上の大型支援制度</div>
                    </div>
                    <div class="minna-status-count">120+</div>
                </div>

                <div class="minna-status-bar" onclick="filterByFeatured()">
                    <div class="minna-status-icon">
                        <div class="icon-target" style="width: 24px; height: 24px; background-image: url('data:image/svg+xml;utf8,<svg xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 24 24\" stroke=\"white\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z\"/></svg>');"></div>
                    </div>
                    <div class="minna-status-content">
                        <div class="minna-status-title">おすすめ助成金</div>
                        <div class="minna-status-description">採択率が高く申請しやすい制度</div>
                    </div>
                    <div class="minna-status-count">85+</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Filter Pills -->
    <section class="minna-quick-filters">
        <div class="minna-container">
            <div class="minna-filter-pills">
                <button class="minna-filter-pill <?php echo empty($search_params['status']) && empty($search_params['is_featured']) && empty($search_params['amount']) ? 'active' : ''; ?>" 
                        onclick="clearAllFilters()">
                    すべて
                </button>
                <button class="minna-filter-pill <?php echo $search_params['is_featured'] === '1' ? 'active' : ''; ?>" 
                        onclick="filterByFeatured()">
                    おすすめ
                    <span class="count">85</span>
                </button>
                <button class="minna-filter-pill <?php echo $search_params['status'] === 'active' ? 'active' : ''; ?>" 
                        onclick="filterByStatus('active')">
                    募集中
                    <span class="count"><?php echo $stats['active_grants']; ?></span>
                </button>
                <button class="minna-filter-pill <?php echo $search_params['amount'] === '1000+' ? 'active' : ''; ?>" 
                        onclick="filterByAmount('1000+')">
                    高額助成金
                    <span class="count">120+</span>
                </button>
                <button class="minna-filter-pill" onclick="filterByDeadline()">
                    締切間近
                    <span class="count">25</span>
                </button>
                <button class="minna-filter-pill" onclick="filterByDifficulty('easy')">
                    申請しやすい
                    <span class="count">65</span>
                </button>
                <button class="minna-filter-pill" onclick="openAIOptimization()" 
                        style="background: linear-gradient(135deg, var(--minna-primary) 0%, var(--minna-gray-800) 100%); color: var(--minna-white); border-color: var(--minna-primary);">
                    <div class="icon-target" style="width: 16px; height: 16px; background-image: url('data:image/svg+xml;utf8,<svg xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 24 24\" stroke=\"white\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z\"/></svg>');"></div>
                    AI最適化
                </button>
            </div>
        </div>
    </section>

    <!-- Main Content Area -->
    <section class="minna-main-content">
        <div class="minna-container">
            <div class="minna-content-header">
                <div class="minna-results-info">
                    <strong><?php echo number_format($stats['total_grants']); ?>件</strong>の助成金が見つかりました
                </div>
                <div class="minna-view-controls">
                    <button class="minna-view-btn <?php echo $search_params['view'] === 'grid' ? 'active' : ''; ?>" onclick="switchView('grid')">
                        <div class="minna-view-icon grid-icon"></div>
                    </button>
                    <button class="minna-view-btn <?php echo $search_params['view'] === 'list' ? 'active' : ''; ?>" onclick="switchView('list')">
                        <div class="minna-view-icon list-icon"></div>
                    </button>
                </div>
            </div>

            <!-- Grants Content Container -->
            <div id="minna-grants-container">
                <?php if (have_posts()): ?>
                    <div class="minna-grants-<?php echo $search_params['view']; ?>" id="grants-<?php echo $search_params['view']; ?>">
                        <?php while (have_posts()): the_post(); ?>
                            <article class="minna-grant-card" onclick="window.location.href='<?php the_permalink(); ?>'">
                                <div class="minna-grant-card-header">
                                    <h3 class="minna-grant-title"><?php the_title(); ?></h3>
                                    <div class="minna-grant-organization">
                                        <div class="icon-building minna-info-icon"></div>
                                        <?php echo esc_html(get_field('organization') ?: '実施機関未指定'); ?>
                                    </div>
                                </div>
                                
                                <div class="minna-grant-card-body">
                                    <div class="minna-grant-info-grid">
                                        <div class="minna-info-item">
                                            <div class="icon-money minna-info-icon"></div>
                                            <span class="minna-info-value">
                                                <?php 
                                                $max_amount = get_field('max_amount');
                                                echo $max_amount ? esc_html($max_amount) : '金額応相談';
                                                ?>
                                            </span>
                                        </div>
                                        
                                        <div class="minna-info-item">
                                            <div class="icon-calendar minna-info-icon"></div>
                                            <span class="minna-info-value">
                                                <?php 
                                                $deadline = get_field('deadline_date');
                                                if ($deadline) {
                                                    $deadline_ts = strtotime($deadline);
                                                    $days_left = ceil(($deadline_ts - current_time('timestamp')) / 86400);
                                                    if ($days_left > 0) {
                                                        echo "残り{$days_left}日";
                                                    } else {
                                                        echo '募集終了';
                                                    }
                                                } else {
                                                    echo '随時募集';
                                                }
                                                ?>
                                            </span>
                                        </div>
                                        
                                        <div class="minna-info-item">
                                            <div class="icon-chart minna-info-icon"></div>
                                            <span class="minna-info-value">
                                                <?php 
                                                $success_rate = get_field('adoption_rate');
                                                echo $success_rate ? number_format($success_rate, 1) . '%' : '採択率未公開';
                                                ?>
                                            </span>
                                        </div>
                                        
                                        <div class="minna-info-item">
                                            <div class="icon-location minna-info-icon"></div>
                                            <span class="minna-info-value">
                                                <?php 
                                                $categories = get_the_terms(get_the_ID(), 'grant_category');
                                                echo !empty($categories) ? esc_html($categories[0]->name) : 'その他';
                                                ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="minna-no-results">
                        <div class="minna-no-results-icon"></div>
                        <h3 class="minna-no-results-title">該当する助成金が見つかりませんでした</h3>
                        <p class="minna-no-results-text">
                            検索条件を変更して再度お試しください。または、AI最適化機能をご利用いただくと、より適切な助成金をご提案できます。
                        </p>
                        <button class="minna-btn minna-btn-primary" onclick="clearAllFilters()">
                            フィルターをクリアして再検索
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- JavaScript for interactions -->
    <script>
    // Filter functions
    function clearAllFilters() {
        document.getElementById('minna-search-input').value = '';
        document.getElementById('category-filter').value = '';
        document.getElementById('prefecture-filter').value = '';
        document.getElementById('amount-filter').value = '';
        document.getElementById('status-filter').value = '';
        document.getElementById('sort-filter').value = 'date_desc';
        
        // Remove active class from all pills
        document.querySelectorAll('.minna-filter-pill').forEach(pill => {
            pill.classList.remove('active');
        });
        
        // Add active to "すべて" pill
        document.querySelector('.minna-filter-pill[onclick="clearAllFilters()"]').classList.add('active');
        
        applyFilters();
    }

    function filterByStatus(status) {
        document.getElementById('status-filter').value = status;
        updateActivePill(`filterByStatus('${status}')`);
        applyFilters();
    }

    function filterByAmount(amount) {
        document.getElementById('amount-filter').value = amount;
        updateActivePill(`filterByAmount('${amount}')`);
        applyFilters();
    }

    function filterByFeatured() {
        // Toggle featured filter logic here
        updateActivePill('filterByFeatured()');
        applyFilters();
    }

    function filterByDeadline() {
        // Custom deadline filter logic
        updateActivePill('filterByDeadline()');
        applyFilters();
    }

    function filterByDifficulty(level) {
        // Difficulty filter logic
        updateActivePill(`filterByDifficulty('${level}')`);
        applyFilters();
    }

    function updateActivePill(onclickValue) {
        document.querySelectorAll('.minna-filter-pill').forEach(pill => {
            pill.classList.remove('active');
        });
        
        document.querySelectorAll('.minna-filter-pill').forEach(pill => {
            if (pill.getAttribute('onclick') === onclickValue) {
                pill.classList.add('active');
            }
        });
    }

    function switchView(view) {
        document.querySelectorAll('.minna-view-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        
        event.target.closest('.minna-view-btn').classList.add('active');
        
        // Update view parameter and reload
        const url = new URL(window.location);
        url.searchParams.set('view', view);
        window.location.href = url.toString();
    }

    function applyFilters() {
        const url = new URL(window.location);
        
        // Get all filter values
        const search = document.getElementById('minna-search-input').value;
        const category = document.getElementById('category-filter').value;
        const prefecture = document.getElementById('prefecture-filter').value;
        const amount = document.getElementById('amount-filter').value;
        const status = document.getElementById('status-filter').value;
        const sort = document.getElementById('sort-filter').value;
        
        // Update URL parameters
        if (search) url.searchParams.set('s', search);
        else url.searchParams.delete('s');
        
        if (category) url.searchParams.set('category', category);
        else url.searchParams.delete('category');
        
        if (prefecture) url.searchParams.set('prefecture', prefecture);
        else url.searchParams.delete('prefecture');
        
        if (amount) url.searchParams.set('amount', amount);
        else url.searchParams.delete('amount');
        
        if (status) url.searchParams.set('status', status);
        else url.searchParams.delete('status');
        
        if (sort) url.searchParams.set('sort', sort);
        else url.searchParams.delete('sort');
        
        // Reset to page 1
        url.searchParams.delete('paged');
        
        // Apply filters with smooth loading
        window.location.href = url.toString();
    }

    function openAIOptimization() {
        // AI Optimization modal or functionality
        alert('AI最適化機能は近日公開予定です。現在の検索条件を基に最適な助成金をご提案します。');
    }

    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
        // Add smooth scroll behavior
        document.documentElement.style.scrollBehavior = 'smooth';
        
        // Add loading states to buttons
        document.querySelectorAll('.minna-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (this.onclick && this.onclick.toString().includes('applyFilters')) {
                    this.innerHTML = '<div class="minna-spinner" style="width: 16px; height: 16px; margin-right: 8px;"></div>検索中...';
                }
            });
        });
        
        // Add keyboard navigation support
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.target.matches('.minna-search-input')) {
                applyFilters();
            }
        });
    });
    </script>

</body>
</html>