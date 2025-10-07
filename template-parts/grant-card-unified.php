<?php
/**
 * Grant Card Unified - Original Structure with Minna Bank Professional Design
 * template-parts/grant-card-unified.php
 * 
 * 🏦 みんなの銀行スタイル - 元の仕組みを維持してデザインのみ変更
 * Original card structure with banking-grade professional styling
 * 
 * @package Grant_Insight_Minna_Bank
 * @version 13.0.0-original-structure-minna-design
 */

// セキュリティチェック
if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

// グローバル変数から必要データを取得
global $post;

$post_id = get_the_ID();
if (!$post_id) return;

// 基本データ取得（元の仕組み通り）
$title = get_the_title($post_id);
$permalink = get_permalink($post_id);

// 元のACFフィールド取得方法を維持
$organization = get_field('organization') ?: '実施機関未指定';
$max_amount = get_field('max_amount');
$deadline = get_field('deadline_date');
$success_rate = get_field('adoption_rate');
$categories = get_the_terms(get_the_ID(), 'grant_category');

// 期限計算（元の仕組み通り）
$deadline_display = '随時募集';
if ($deadline) {
    $deadline_ts = strtotime($deadline);
    $days_left = ceil(($deadline_ts - current_time('timestamp')) / 86400);
    if ($days_left > 0) {
        $deadline_display = "残り{$days_left}日";
    } else {
        $deadline_display = '募集終了';
    }
}

// カテゴリ表示（元の仕組み通り）
$category_display = !empty($categories) ? esc_html($categories[0]->name) : 'その他';
?>

<style>
/* ===== MINNA BANK PROFESSIONAL CARD DESIGN ===== */
:root {
    /* Minna Bank Brand Colors - Professional Monochrome Palette */
    --mb-white: #ffffff;
    --mb-gray-50: #fafafa;
    --mb-gray-100: #f5f5f5;
    --mb-gray-200: #e5e5e5;
    --mb-gray-300: #d4d4d4;
    --mb-gray-400: #a3a3a3;
    --mb-gray-500: #737373;
    --mb-gray-600: #525252;
    --mb-gray-700: #404040;
    --mb-gray-800: #262626;
    --mb-gray-900: #171717;
    
    /* Semantic Colors */
    --mb-success: #22c55e;
    --mb-warning: #f59e0b;
    --mb-danger: #ef4444;
    --mb-info: #3b82f6;
    
    /* Typography */
    --mb-font-primary: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
    --mb-font-japanese: 'Noto Sans JP', 'Hiragino Kaku Gothic ProN', 'Hiragino Sans', 'Yu Gothic Medium', sans-serif;
    
    /* Spacing */
    --mb-space-xs: 0.25rem;
    --mb-space-sm: 0.5rem;
    --mb-space-md: 0.75rem;
    --mb-space-lg: 1rem;
    --mb-space-xl: 1.25rem;
    --mb-space-2xl: 1.5rem;
    --mb-space-3xl: 2rem;
    
    /* Border Radius */
    --mb-radius-sm: 0.25rem;
    --mb-radius-md: 0.375rem;
    --mb-radius-lg: 0.5rem;
    --mb-radius-xl: 0.75rem;
    --mb-radius-2xl: 1rem;
    
    /* Shadows */
    --mb-shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --mb-shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --mb-shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    
    /* Transitions */
    --mb-transition-fast: all 0.15s ease;
    --mb-transition-medium: all 0.3s ease;
}

/* ===== MINNA GRANT CARD - PROFESSIONAL BANKING STYLE ===== */
.minna-grant-card {
    /* Container Styling - Banking Grade */
    background: linear-gradient(135deg, var(--mb-white) 0%, var(--mb-gray-50) 100%);
    border: 2px solid var(--mb-gray-200);
    border-radius: var(--mb-radius-xl);
    padding: var(--mb-space-2xl);
    margin-bottom: var(--mb-space-xl);
    
    /* Professional Shadow System */
    box-shadow: 
        var(--mb-shadow-sm),
        0 0 0 1px rgba(0, 0, 0, 0.02);
    
    /* Interactive States */
    cursor: pointer;
    transition: var(--mb-transition-medium);
    position: relative;
    overflow: hidden;
}

.minna-grant-card::before {
    /* Subtle Gradient Overlay */
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, 
        var(--mb-gray-300) 0%, 
        var(--mb-gray-400) 50%, 
        var(--mb-gray-300) 100%);
    opacity: 0;
    transition: var(--mb-transition-fast);
}

.minna-grant-card:hover {
    /* Hover Enhancement - Professional Banking Style */
    transform: translateY(-2px);
    border-color: var(--mb-gray-300);
    box-shadow: 
        var(--mb-shadow-lg),
        0 0 0 1px rgba(0, 0, 0, 0.05),
        0 0 20px rgba(0, 0, 0, 0.04);
}

.minna-grant-card:hover::before {
    opacity: 1;
}

.minna-grant-card:active {
    /* Click Feedback */
    transform: translateY(0);
    transition: transform 0.1s ease;
}

/* ===== CARD HEADER - PROFESSIONAL LAYOUT ===== */
.minna-grant-card-header {
    margin-bottom: var(--mb-space-xl);
    padding-bottom: var(--mb-space-lg);
    border-bottom: 1px solid var(--mb-gray-200);
}

.minna-grant-title {
    /* Professional Typography */
    font-family: var(--mb-font-japanese);
    font-size: 1.125rem;
    font-weight: 700;
    line-height: 1.4;
    color: var(--mb-gray-900);
    margin: 0 0 var(--mb-space-md) 0;
    
    /* Text Enhancement */
    letter-spacing: -0.01em;
    text-rendering: optimizeLegibility;
    
    /* Responsive Typography */
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
    overflow: hidden;
}

.minna-grant-organization {
    /* Organization Display */
    display: flex;
    align-items: center;
    gap: var(--mb-space-sm);
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--mb-gray-600);
    font-family: var(--mb-font-japanese);
}

/* ===== CARD BODY - INFO GRID SYSTEM ===== */
.minna-grant-card-body {
    /* Body Container */
}

.minna-grant-info-grid {
    /* Professional Grid Layout */
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--mb-space-lg);
    
    /* Responsive Breakpoints */
    @media (max-width: 768px) {
        grid-template-columns: 1fr;
        gap: var(--mb-space-md);
    }
}

.minna-info-item {
    /* Individual Info Items */
    display: flex;
    align-items: center;
    gap: var(--mb-space-sm);
    padding: var(--mb-space-md);
    background: var(--mb-gray-50);
    border: 1px solid var(--mb-gray-200);
    border-radius: var(--mb-radius-lg);
    transition: var(--mb-transition-fast);
}

.minna-info-item:hover {
    /* Subtle Hover Enhancement */
    background: var(--mb-white);
    border-color: var(--mb-gray-300);
    transform: translateY(-1px);
    box-shadow: var(--mb-shadow-sm);
}

.minna-info-value {
    /* Value Typography */
    font-family: var(--mb-font-japanese);
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--mb-gray-800);
    line-height: 1.3;
}

/* ===== PROFESSIONAL ICONS SYSTEM ===== */
.minna-info-icon {
    /* Icon Base Styling */
    width: 16px;
    height: 16px;
    flex-shrink: 0;
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
    opacity: 0.7;
    transition: var(--mb-transition-fast);
}

.minna-info-item:hover .minna-info-icon {
    opacity: 1;
}

/* Icon Definitions - Professional Banking Icons */
.icon-building {
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="%23525252"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>');
}

.icon-money {
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="%23525252"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>');
}

.icon-calendar {
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="%23525252"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>');
}

.icon-chart {
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="%23525252"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>');
}

.icon-location {
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="%23525252"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>');
}

/* ===== RESPONSIVE DESIGN ===== */
@media (max-width: 768px) {
    .minna-grant-card {
        padding: var(--mb-space-xl);
        margin-bottom: var(--mb-space-lg);
    }
    
    .minna-grant-title {
        font-size: 1rem;
    }
    
    .minna-grant-organization {
        font-size: 0.8125rem;
    }
    
    .minna-info-value {
        font-size: 0.8125rem;
    }
}

@media (max-width: 480px) {
    .minna-grant-card {
        padding: var(--mb-space-lg);
    }
    
    .minna-grant-info-grid {
        gap: var(--mb-space-sm);
    }
    
    .minna-info-item {
        padding: var(--mb-space-sm);
    }
}

/* ===== PROFESSIONAL ENHANCEMENTS ===== */
.minna-grant-card {
    /* Additional Professional Touches */
    backdrop-filter: blur(1px);
    -webkit-backdrop-filter: blur(1px);
}

/* Print Styles */
@media print {
    .minna-grant-card {
        border: 1px solid var(--mb-gray-400);
        box-shadow: none;
        background: var(--mb-white);
        margin-bottom: var(--mb-space-lg);
        page-break-inside: avoid;
    }
    
    .minna-grant-card:hover {
        transform: none;
    }
}
</style>

<article class="minna-grant-card" onclick="window.location.href='<?php echo esc_url($permalink); ?>'">
    <div class="minna-grant-card-header">
        <h3 class="minna-grant-title"><?php echo esc_html($title); ?></h3>
        <div class="minna-grant-organization">
            <div class="icon-building minna-info-icon"></div>
            <?php echo esc_html($organization); ?>
        </div>
    </div>
    
    <div class="minna-grant-card-body">
        <div class="minna-grant-info-grid">
            <div class="minna-info-item">
                <div class="icon-money minna-info-icon"></div>
                <span class="minna-info-value">
                    <?php echo $max_amount ? esc_html($max_amount) : '金額応相談'; ?>
                </span>
            </div>
            
            <div class="minna-info-item">
                <div class="icon-calendar minna-info-icon"></div>
                <span class="minna-info-value">
                    <?php echo esc_html($deadline_display); ?>
                </span>
            </div>
            
            <div class="minna-info-item">
                <div class="icon-chart minna-info-icon"></div>
                <span class="minna-info-value">
                    <?php echo $success_rate ? number_format($success_rate, 1) . '%' : '採択率未公開'; ?>
                </span>
            </div>
            
            <div class="minna-info-item">
                <div class="icon-location minna-info-icon"></div>
                <span class="minna-info-value">
                    <?php echo esc_html($category_display); ?>
                </span>
            </div>
        </div>
    </div>
</article>