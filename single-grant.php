<?php
/**
 * Grant Single Page - Minna Bank Style Design
 * 助成金詳細ページ - みんなの銀行スタイルデザイン
 * SEO最適化 + ACF完全連携 + レスポンシブ対応
 * 
 * @package Grant_Insight_Perfect
 * @version 12.0.0-minna-bank-style
 * @author Grant Insight Team
 */

// セキュリティとパフォーマンス最適化
if (!defined('ABSPATH')) {
    exit;
}

// 早期リダイレクト（SEO対策）
if (!have_posts()) {
    wp_redirect(home_url('/404'), 302);
    exit;
}

get_header();

// 投稿データ取得
the_post();
$post_id = get_the_ID();

// SEO用メタデータ生成
$seo_title = get_the_title();
$seo_description = '';
$seo_keywords = [];
$canonical_url = get_permalink($post_id);

// AI要約をSEO descriptionに使用
if (function_exists('get_field')) {
    $ai_summary = get_field('ai_summary', $post_id);
    if ($ai_summary) {
        $seo_description = wp_trim_words(strip_tags($ai_summary), 25, '...');
    }
}

// フォールバック：本文からSEO descriptionを生成
if (empty($seo_description)) {
    $content = get_the_content();
    if ($content) {
        $seo_description = wp_trim_words(strip_tags($content), 25, '...');
    }
}

// タクソノミーからキーワード生成
$categories = wp_get_post_terms($post_id, 'grant_category', ['fields' => 'names']);
$prefectures = wp_get_post_terms($post_id, 'grant_prefecture', ['fields' => 'names']);
$tags = wp_get_post_tags($post_id, ['fields' => 'names']);

if (!is_wp_error($categories)) $seo_keywords = array_merge($seo_keywords, $categories);
if (!is_wp_error($prefectures)) $seo_keywords = array_merge($seo_keywords, $prefectures);
if (!is_wp_error($tags)) $seo_keywords = array_merge($seo_keywords, $tags);
$seo_keywords = array_unique($seo_keywords);

// ACFフィールド完全対応データ取得（フォールバック付き）
$grant_data = array(
    // 基本情報
    'organization' => function_exists('get_field') ? get_field('organization', $post_id) : get_post_meta($post_id, 'organization', true),
    'organization_type' => function_exists('get_field') ? get_field('organization_type', $post_id) : get_post_meta($post_id, 'organization_type', true),
    
    // 金額情報
    'max_amount' => function_exists('get_field') ? get_field('max_amount', $post_id) : get_post_meta($post_id, 'max_amount', true),
    'max_amount_numeric' => function_exists('get_field') ? intval(get_field('max_amount_numeric', $post_id)) : intval(get_post_meta($post_id, 'max_amount_numeric', true)),
    'min_amount' => function_exists('get_field') ? intval(get_field('min_amount', $post_id)) : intval(get_post_meta($post_id, 'min_amount', true)),
    'amount_note' => function_exists('get_field') ? get_field('amount_note', $post_id) : get_post_meta($post_id, 'amount_note', true),
    'subsidy_rate' => function_exists('get_field') ? get_field('subsidy_rate', $post_id) : get_post_meta($post_id, 'subsidy_rate', true),
    
    // 期間・締切情報
    'deadline' => function_exists('get_field') ? get_field('deadline', $post_id) : get_post_meta($post_id, 'deadline', true),
    'deadline_date' => function_exists('get_field') ? get_field('deadline_date', $post_id) : get_post_meta($post_id, 'deadline_date', true),
    'deadline_note' => function_exists('get_field') ? get_field('deadline_note', $post_id) : get_post_meta($post_id, 'deadline_note', true),
    'application_period' => function_exists('get_field') ? get_field('application_period', $post_id) : get_post_meta($post_id, 'application_period', true),
    
    // 申請情報
    'grant_target' => function_exists('get_field') ? get_field('grant_target', $post_id) : get_post_meta($post_id, 'grant_target', true),
    'application_method' => function_exists('get_field') ? get_field('application_method', $post_id) : get_post_meta($post_id, 'application_method', true),
    'contact_info' => function_exists('get_field') ? get_field('contact_info', $post_id) : get_post_meta($post_id, 'contact_info', true),
    'official_url' => function_exists('get_field') ? get_field('official_url', $post_id) : get_post_meta($post_id, 'official_url', true),
    
    // 地域・ステータス情報
    'regional_limitation' => function_exists('get_field') ? get_field('regional_limitation', $post_id) : get_post_meta($post_id, 'regional_limitation', true),
    'application_status' => function_exists('get_field') ? get_field('application_status', $post_id) : get_post_meta($post_id, 'application_status', true),
    
    // 新規拡張フィールド（31列対応）
    'external_link' => function_exists('get_field') ? get_field('external_link', $post_id) : get_post_meta($post_id, 'external_link', true),
    'region_notes' => function_exists('get_field') ? get_field('region_notes', $post_id) : get_post_meta($post_id, 'region_notes', true),
    'required_documents' => function_exists('get_field') ? get_field('required_documents', $post_id) : get_post_meta($post_id, 'required_documents', true),
    'adoption_rate' => function_exists('get_field') ? floatval(get_field('adoption_rate', $post_id)) : floatval(get_post_meta($post_id, 'adoption_rate', true)),
    'grant_difficulty' => function_exists('get_field') ? get_field('grant_difficulty', $post_id) : get_post_meta($post_id, 'grant_difficulty', true),
    'target_expenses' => function_exists('get_field') ? get_field('target_expenses', $post_id) : get_post_meta($post_id, 'target_expenses', true),
    
    // 管理・統計情報
    'is_featured' => function_exists('get_field') ? get_field('is_featured', $post_id) : get_post_meta($post_id, 'is_featured', true),
    'views_count' => function_exists('get_field') ? intval(get_field('views_count', $post_id)) : intval(get_post_meta($post_id, 'views_count', true)),
    'last_updated' => function_exists('get_field') ? get_field('last_updated', $post_id) : get_post_meta($post_id, 'last_updated', true),
    
    // AI関連
    'ai_summary' => function_exists('get_field') ? get_field('ai_summary', $post_id) : get_post_meta($post_id, 'ai_summary', true),
);

// デフォルト値設定
$grant_data = array_merge(array(
    'organization' => '',
    'organization_type' => 'national',
    'max_amount' => '',
    'max_amount_numeric' => 0,
    'min_amount' => 0,
    'amount_note' => '',
    'subsidy_rate' => '',
    'deadline' => '',
    'deadline_date' => '',
    'deadline_note' => '',
    'application_period' => '',
    'grant_target' => '',
    'application_method' => 'online',
    'contact_info' => '',
    'official_url' => '',
    'regional_limitation' => 'nationwide',
    'application_status' => 'open',
    'external_link' => '',
    'region_notes' => '',
    'required_documents' => '',
    'adoption_rate' => 0,
    'grant_difficulty' => 'normal',
    'target_expenses' => '',
    'is_featured' => false,
    'views_count' => 0,
    'last_updated' => '',
    'ai_summary' => ''
), $grant_data);

// タクソノミーデータ取得（エラーハンドリング付き）
$taxonomies = array(
    'categories' => wp_get_post_terms($post_id, 'grant_category'),
    'prefectures' => wp_get_post_terms($post_id, 'grant_prefecture'), 
    'municipalities' => wp_get_post_terms($post_id, 'grant_municipality'),
    'tags' => wp_get_post_tags($post_id),
);

// エラーチェックとデフォルト値設定
foreach ($taxonomies as $key => $terms) {
    if (is_wp_error($terms) || empty($terms)) {
        $taxonomies[$key] = array();
    }
}

$main_category = !empty($taxonomies['categories']) ? $taxonomies['categories'][0] : null;
$main_prefecture = !empty($taxonomies['prefectures']) ? $taxonomies['prefectures'][0] : null;

// 金額フォーマット処理（改善版）
$formatted_amount = '';
$max_amount_yen = intval($grant_data['max_amount_numeric']);

if ($max_amount_yen > 0) {
    if ($max_amount_yen >= 100000000) {
        $formatted_amount = number_format($max_amount_yen / 100000000, 1) . '億円';
    } elseif ($max_amount_yen >= 10000) {
        $formatted_amount = number_format($max_amount_yen / 10000) . '万円';
    } else {
        $formatted_amount = number_format($max_amount_yen) . '円';
    }
} elseif (!empty($grant_data['max_amount'])) {
    $formatted_amount = $grant_data['max_amount'];
} else {
    $formatted_amount = '金額未設定';
}

// 組織タイプラベルマッピング
$org_type_labels = array(
    'national' => '国（省庁）',
    'prefecture' => '都道府県',
    'city' => '市区町村', 
    'public_org' => '公的機関',
    'private_org' => '民間団体',
    'foundation' => '財団法人',
    'jgrants' => 'Jグランツ',
    'other' => 'その他'
);

// 申請方法ラベルマッピング
$method_labels = array(
    'online' => 'オンライン申請',
    'mail' => '郵送申請',
    'visit' => '窓口申請',
    'mixed' => 'オンライン・郵送併用'
);

// 地域制限ラベルマッピング
$region_labels = array(
    'nationwide' => '全国対象',
    'prefecture_only' => '都道府県内限定',
    'municipality_only' => '市町村限定',
    'region_group' => '地域グループ限定',
    'specific_area' => '特定地域限定'
);

// 締切日計算（改善版・エラーハンドリング付き）
$deadline_info = '';
$deadline_class = '';
$days_remaining = 0;

if (!empty($grant_data['deadline_date'])) {
    $deadline_timestamp = strtotime($grant_data['deadline_date']);
    if ($deadline_timestamp && $deadline_timestamp > 0) {
        $deadline_info = date('Y年n月j日', $deadline_timestamp);
        $current_time = function_exists('current_time') ? current_time('timestamp') : time();
        $days_remaining = ceil(($deadline_timestamp - $current_time) / 86400); // 86400 = 60*60*24
        
        if ($days_remaining <= 0) {
            $deadline_class = 'closed';
            $deadline_info .= ' (募集終了)';
        } elseif ($days_remaining <= 7) {
            $deadline_class = 'urgent';
            $deadline_info .= ' (あと' . $days_remaining . '日)';
        } elseif ($days_remaining <= 30) {
            $deadline_class = 'warning';
            $deadline_info .= ' (あと' . $days_remaining . '日)';
        }
    }
} elseif (!empty($grant_data['deadline'])) {
    $deadline_info = $grant_data['deadline'];
} else {
    $deadline_info = '締切日未設定';
}

// 申請難易度設定（エラーハンドリング付き）
$difficulty_configs = array(
    'easy' => array('label' => '簡単', 'dots' => 1),
    'normal' => array('label' => '普通', 'dots' => 2),
    'hard' => array('label' => '難しい', 'dots' => 3),
    'very_hard' => array('label' => '非常に困難', 'dots' => 4),
    'expert' => array('label' => '専門的', 'dots' => 4)
);

$difficulty = !empty($grant_data['grant_difficulty']) ? $grant_data['grant_difficulty'] : 'normal';
$difficulty_data = isset($difficulty_configs[$difficulty]) ? $difficulty_configs[$difficulty] : $difficulty_configs['normal'];

// ステータスマッピング（エラーハンドリング付き）
$status_configs = array(
    'open' => array('label' => '募集中', 'class' => 'open'),
    'upcoming' => array('label' => '募集予定', 'class' => 'upcoming'),
    'closed' => array('label' => '募集終了', 'class' => 'closed'),
    'suspended' => array('label' => '一時停止', 'class' => 'suspended')
);

$application_status = !empty($grant_data['application_status']) ? $grant_data['application_status'] : 'open';
$status_data = isset($status_configs[$application_status]) ? $status_configs[$application_status] : $status_configs['open'];

// 閲覧数更新（エラーハンドリング付き）
$current_views = intval($grant_data['views_count']);
$new_views = $current_views + 1;
if (function_exists('update_post_meta')) {
    update_post_meta($post_id, 'views_count', $new_views);
    $grant_data['views_count'] = $new_views;
}
?>

<!-- SEO Meta Tags -->
<meta name="description" content="<?php echo esc_attr($seo_description); ?>">
<meta name="keywords" content="<?php echo esc_attr(implode(', ', $seo_keywords)); ?>">
<link rel="canonical" href="<?php echo esc_url($canonical_url); ?>">

<!-- Open Graph Tags -->
<meta property="og:title" content="<?php echo esc_attr($seo_title); ?>">
<meta property="og:description" content="<?php echo esc_attr($seo_description); ?>">
<meta property="og:url" content="<?php echo esc_url($canonical_url); ?>">
<meta property="og:type" content="article">
<meta property="og:site_name" content="<?php echo esc_attr(get_bloginfo('name')); ?>">

<!-- Twitter Cards -->
<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="<?php echo esc_attr($seo_title); ?>">
<meta name="twitter:description" content="<?php echo esc_attr($seo_description); ?>">

<!-- JSON-LD Structured Data -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "GovernmentService",
  "name": "<?php echo esc_js($seo_title); ?>",
  "description": "<?php echo esc_js($seo_description); ?>",
  "url": "<?php echo esc_js($canonical_url); ?>",
  "provider": {
    "@type": "Organization",
    "name": "<?php echo esc_js($grant_data['organization']); ?>"
  },
  "audience": "<?php echo esc_js(wp_strip_all_tags($grant_data['grant_target'])); ?>",
  "availableChannel": {
    "@type": "ServiceChannel",
    "serviceUrl": "<?php echo esc_js($grant_data['official_url']); ?>"
  }
}
</script>

<style>
/* ===============================================
   MINNA BANK STYLE - GRANT SINGLE PAGE
   =============================================== */

:root {
    /* Minna Bank Inspired Color System */
    --color-bg: #ffffff;
    --color-surface: #F7F8FA;
    --color-muted: #F1F3F5;
    --color-gray-200: #E6E9EE;
    --color-gray-300: #CBD2DB;
    --color-gray-400: #9AA6B2;
    --color-gray-500: #6B7A86;
    --color-gray-700: #2F3B45;
    --color-gray-900: #0B1722;
    --color-border: rgba(11,23,34,0.08);
    --accent: #5B6CFF;
    --accent-danger: #FF5B5B;
    --accent-warning: #FFB800;
    --accent-success: #00C896;
    --accent-info: #00A8FF;
    
    /* Typography Scale */
    --type-xxs: 0.75rem;     /* 12px */
    --type-xs: 0.8125rem;    /* 13px */
    --type-sm: 0.875rem;     /* 14px */
    --type-base: 1rem;       /* 16px */
    --type-lg: 1.125rem;     /* 18px */
    --type-xl: 1.25rem;      /* 20px */
    --type-2xl: 1.5rem;      /* 24px */
    --type-3xl: 2rem;        /* 32px */
    
    /* Spacing Scale (8px base) */
    --space-xxs: 4px;
    --space-xs: 8px;
    --space-sm: 12px;
    --space-md: 16px;
    --space-lg: 24px;
    --space-xl: 32px;
    --space-2xl: 48px;
    
    /* Shadows */
    --shadow-sm: 0 1px 2px rgba(11,23,34,0.06);
    --shadow-md: 0 6px 18px rgba(11,23,34,0.08);
    --shadow-lg: 0 10px 25px rgba(11,23,34,0.12);
    
    /* Border Radius */
    --radius-sm: 8px;
    --radius-md: 12px;
    --radius-lg: 16px;
    
    /* Transitions */
    --transition: 0.2s ease-out;
}

/* Base Styles - Minna Bank Typography */
* {
    box-sizing: border-box;
}

html, body {
    font-family: "Noto Sans JP", "Hiragino Kaku Gothic ProN", "Yu Gothic", "Meiryo", sans-serif;
    background: var(--color-bg);
    color: var(--color-gray-900);
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    line-height: 1.6;
}

/* Main Container - Mobile First */
.grant-minna {
    width: 100%;
    padding-inline: var(--space-md);
    margin-inline: auto;
    max-width: 1200px;
    background: var(--color-bg);
    color: var(--color-gray-900);
    font-size: var(--type-sm);
}

@media (min-width: 600px) {
    .grant-minna {
        padding-inline: var(--space-lg);
        font-size: var(--type-base);
    }
}

@media (min-width: 960px) {
    .grant-minna {
        padding-inline: 40px;
    }
}

/* 新規フィールド専用スタイル */
.field-enhanced {
    background: linear-gradient(135deg, var(--mono-off-white) 0%, var(--mono-white) 100%);
    border-left: 4px solid var(--accent-info);
    padding: var(--space-5);
    border-radius: var(--radius-base);
    margin: var(--space-4) 0;
}

.difficulty-enhanced {
    display: flex;
    align-items: center;
    gap: var(--space-3);
    font-weight: 600;
}

@media (min-width: 768px) {
    .grant-stylish {
        padding: var(--space-16) var(--space-8);
    }
}

/* Hero Section - Clean Minna Bank Style */
.grant-hero {
    background: var(--color-surface);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-sm);
    padding: var(--space-xl);
    margin-bottom: var(--space-2xl);
    text-align: center;
    position: relative;
}

@media (min-width: 600px) {
    .grant-hero {
        padding: var(--space-2xl);
    }
}

@media (min-width: 960px) {
    .grant-hero {
        padding: 48px;
    }
}

/* Status Badge - Minna Bank Style */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: var(--space-xs);
    padding: 6px var(--space-sm);
    border-radius: var(--radius-sm);
    font-size: var(--type-xs);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: var(--space-md);
    transition: var(--transition);
}

.status-badge.open {
    background: var(--accent-success);
    color: white;
}

.status-badge.warning {
    background: var(--accent-warning);
    color: white;
}

.status-badge.urgent {
    background: var(--accent-danger);
    color: white;
}

.status-badge.closed {
    background: var(--color-gray-500);
    color: white;
}

/* Typography - Minna Bank Style */
.grant-title {
    font-size: var(--type-2xl);
    font-weight: 700;
    line-height: 1.3;
    color: var(--color-gray-900);
    margin: 0 0 var(--space-md);
    letter-spacing: -0.01em;
}

@media (min-width: 600px) {
    .grant-title {
        font-size: var(--type-3xl);
        line-height: 1.2;
    }
}

.grant-subtitle {
    font-size: var(--type-base);
    color: var(--color-gray-700);
    margin-bottom: var(--space-xl);
    font-weight: 400;
    line-height: 1.6;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

@media (min-width: 600px) {
    .grant-subtitle {
        font-size: var(--type-lg);
    }
}

/* Key Information Grid - Minna Bank Style */
.key-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: var(--space-md);
    margin-bottom: var(--space-2xl);
}

@media (min-width: 600px) {
    .key-info-grid {
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: var(--space-lg);
    }
}

/* Info Cards - Clean Minna Bank Style */
.info-card {
    background: var(--color-bg);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-sm);
    padding: var(--space-md);
    text-align: center;
    transition: var(--transition);
    position: relative;
}

.info-card:hover {
    border-color: var(--color-gray-300);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

@media (min-width: 600px) {
    .info-card {
        padding: var(--space-lg);
    }
}

/* Icon Style - Minimalist */
.info-icon {
    width: 40px;
    height: 40px;
    background: var(--color-muted);
    color: var(--color-gray-700);
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto var(--space-sm);
    font-size: var(--type-lg);
    transition: var(--transition);
}

.info-card:hover .info-icon {
    background: var(--accent);
    color: white;
    transform: scale(1.05);
}

@media (min-width: 600px) {
    .info-icon {
        width: 48px;
        height: 48px;
        margin-bottom: var(--space-md);
    }
}

.info-label {
    font-size: var(--type-xs);
    color: var(--color-gray-500);
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.05em;
    margin-bottom: var(--space-xs);
}

.info-value {
    font-size: var(--type-xl);
    font-weight: 700;
    color: var(--color-gray-900);
    line-height: 1.2;
}

@media (min-width: 600px) {
    .info-value {
        font-size: var(--type-2xl);
    }
}

.info-value.highlight {
    color: var(--accent);
}

/* Content Layout - Responsive Grid */
.content-layout {
    display: grid;
    grid-template-columns: 1fr;
    gap: var(--space-xl);
    align-items: start;
}

@media (min-width: 960px) {
    .content-layout {
        grid-template-columns: 2fr 1fr;
        gap: var(--space-2xl);
    }
}

/* Content Sections - Minna Bank Style */
.content-main {
    display: flex;
    flex-direction: column;
    gap: var(--space-xl);
}

.content-section {
    background: var(--color-surface);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-md);
    padding: var(--space-lg);
    box-shadow: var(--shadow-sm);
    border-left: 4px solid var(--accent);
    transition: var(--transition);
}

.content-section:hover {
    box-shadow: var(--shadow-md);
    border-left-color: var(--color-gray-900);
}

@media (min-width: 600px) {
    .content-section {
        padding: var(--space-xl);
    }
}

.section-header {
    display: flex;
    align-items: center;
    gap: var(--space-sm);
    margin-bottom: var(--space-lg);
    padding-bottom: var(--space-md);
    border-bottom: 1px solid var(--color-border);
}

.section-icon {
    width: 24px;
    height: 24px;
    color: var(--color-gray-700);
    font-size: var(--type-lg);
}

@media (min-width: 600px) {
    .section-icon {
        width: 28px;
        height: 28px;
    }
}

.section-title {
    font-size: var(--type-lg);
    font-weight: 700;
    color: var(--color-gray-900);
    margin: 0;
}

@media (min-width: 600px) {
    .section-title {
        font-size: var(--type-xl);
    }
}

.section-content {
    color: var(--color-gray-700);
    line-height: 1.7;
}

.section-content p {
    margin-bottom: var(--space-md);
}

.section-content ul,
.section-content ol {
    margin: var(--space-md) 0;
    padding-left: var(--space-lg);
}

.section-content li {
    margin-bottom: var(--space-xs);
}

/* Information Table - Clean Minna Bank Style */
.info-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    background: var(--color-bg);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-md);
    overflow: hidden;
}

.info-table th,
.info-table td {
    padding: var(--space-sm) var(--space-md);
    text-align: left;
    border-bottom: 1px solid var(--color-border);
}

@media (min-width: 600px) {
    .info-table th,
    .info-table td {
        padding: var(--space-md) var(--space-lg);
    }
}

.info-table th {
    background: var(--color-muted);
    font-weight: 600;
    color: var(--color-gray-700);
    font-size: var(--type-sm);
    width: 35%;
}

.info-table td {
    font-weight: 500;
    color: var(--color-gray-900);
    font-size: var(--type-sm);
}

@media (min-width: 600px) {
    .info-table td {
        font-size: var(--type-base);
    }
}

.info-table tr:hover {
    background: var(--color-surface);
}

.info-table tr:last-child th,
.info-table tr:last-child td {
    border-bottom: none;
}

/* Sidebar - Minna Bank Style */
.sidebar {
    display: flex;
    flex-direction: column;
    gap: var(--space-lg);
}

@media (min-width: 960px) {
    .sidebar {
        position: sticky;
        top: var(--space-xl);
    }
}

.sidebar-card {
    background: var(--color-surface);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-md);
    padding: var(--space-lg);
    box-shadow: var(--shadow-sm);
}

.sidebar-title {
    font-size: var(--type-base);
    font-weight: 700;
    color: var(--color-gray-900);
    margin-bottom: var(--space-md);
    display: flex;
    align-items: center;
    gap: var(--space-xs);
}

@media (min-width: 600px) {
    .sidebar-title {
        font-size: var(--type-lg);
    }
}

/* Action Buttons - Clean Minna Bank Style */
.action-buttons {
    display: flex;
    flex-direction: column;
    gap: var(--space-sm);
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: var(--space-xs);
    padding: 10px var(--space-md);
    border-radius: var(--radius-sm);
    text-decoration: none;
    font-weight: 600;
    font-size: var(--type-sm);
    transition: var(--transition);
    border: none;
    cursor: pointer;
    min-height: 44px; /* Touch friendly */
}

.btn-primary {
    background: var(--accent);
    color: white;
    box-shadow: none;
}

.btn-primary:hover {
    background: var(--color-gray-900);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

.btn-secondary {
    background: transparent;
    color: var(--color-gray-700);
    border: 1px solid var(--color-gray-300);
}

.btn-secondary:hover {
    border-color: var(--color-gray-900);
    background: var(--color-surface);
    color: var(--color-gray-900);
}

/* Statistics Grid - Clean & Minimal */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--space-sm);
}

@media (min-width: 600px) {
    .stats-grid {
        gap: var(--space-md);
    }
}

.stat-item {
    text-align: center;
    padding: var(--space-md);
    background: var(--color-bg);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-sm);
    transition: var(--transition);
}

.stat-item:hover {
    border-color: var(--color-gray-300);
    box-shadow: var(--shadow-sm);
}

.stat-number {
    font-size: var(--type-xl);
    font-weight: 700;
    color: var(--color-gray-900);
    display: block;
    line-height: 1.1;
}

@media (min-width: 600px) {
    .stat-number {
        font-size: var(--type-2xl);
    }
}

.stat-label {
    font-size: var(--type-xxs);
    color: var(--color-gray-500);
    margin-top: var(--space-xxs);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    font-weight: 600;
}

@media (min-width: 600px) {
    .stat-label {
        font-size: var(--type-xs);
    }
}

/* Difficulty Indicator - Simple & Clean */
.difficulty-indicator {
    display: flex;
    align-items: center;
    gap: var(--space-sm);
}

.difficulty-dots {
    display: flex;
    gap: var(--space-xxs);
}

.difficulty-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--color-gray-300);
    transition: var(--transition);
}

.difficulty-dot.filled {
    background: var(--accent);
}

/* Tags - Minna Bank Style */
.tags-section {
    margin-top: var(--space-md);
}

.tags-list {
    display: flex;
    flex-wrap: wrap;
    gap: var(--space-xs);
}

.tag {
    display: inline-flex;
    align-items: center;
    gap: var(--space-xxs);
    padding: var(--space-xxs) var(--space-sm);
    background: var(--color-muted);
    color: var(--color-gray-700);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-sm);
    font-size: var(--type-xxs);
    text-decoration: none;
    transition: var(--transition);
    font-weight: 500;
}

@media (min-width: 600px) {
    .tag {
        font-size: var(--type-xs);
        padding: var(--space-xs) var(--space-sm);
    }
}

.tag:hover {
    background: var(--accent);
    color: white;
    border-color: var(--accent);
}

/* Progress Bar - Clean Style */
.progress-bar {
    width: 100%;
    height: 6px;
    background: var(--color-gray-200);
    border-radius: var(--radius-sm);
    overflow: hidden;
    margin-top: var(--space-xs);
}

.progress-fill {
    height: 100%;
    background: var(--accent);
    border-radius: var(--radius-sm);
    transition: width 0.8s ease-out;
    position: relative;
}

/* Responsive Design - Mobile First */
@media (max-width: 599px) {
    .key-info-grid {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .info-table th,
    .info-table td {
        padding: var(--space-xs) var(--space-sm);
        font-size: var(--type-xs);
    }
}

/* Print Styles */
@media print {
    .grant-minna {
        background: white;
        color: black;
        box-shadow: none;
    }
    
    .sidebar {
        display: none;
    }
    
    .content-layout {
        grid-template-columns: 1fr;
    }
    
    .btn {
        display: none;
    }
    
    .grant-hero {
        box-shadow: none;
        border: 1px solid #ccc;
    }
}

/* Accessibility - High Contrast */
@media (prefers-contrast: high) {
    .info-card,
    .content-section,
    .sidebar-card {
        border-width: 2px;
    }
    
    .btn {
        border-width: 2px;
    }
}

/* Accessibility - Reduced Motion */
@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

/* Focus States for Accessibility */
a:focus, button:focus, .btn:focus {
    outline: 3px solid var(--accent);
    outline-offset: 2px;
}

/* White & Black Icons Only - No Emojis */
.icon-yen {
    width: 20px;
    height: 20px;
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="%23374151"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 002-2v-3a2 2 0 012-2h8a2 2 0 012 2v3a2 2 0 002 2m-6 0v1a2 2 0 11-4 0v-1m4 0H9m5 0v1a2 2 0 01-2 2H9.5m.5-3H15M9 8h6" /></svg>');
    background-repeat: no-repeat;
    background-position: center;
    background-size: contain;
}
.icon-calendar {
    width: 20px;
    height: 20px;
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="%23374151"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>');
    background-repeat: no-repeat;
    background-position: center;
    background-size: contain;
}
.icon-chart {
    width: 20px;
    height: 20px;
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="%23374151"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>');
    background-repeat: no-repeat;
    background-position: center;
    background-size: contain;
}
.icon-building {
    width: 20px;
    height: 20px;
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="%23374151"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>');
    background-repeat: no-repeat;
    background-position: center;
    background-size: contain;
}
.icon-document {
    width: 20px;
    height: 20px;
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="%23374151"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>');
    background-repeat: no-repeat;
    background-position: center;
    background-size: contain;
}
.icon-target {
    width: 20px;
    height: 20px;
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="%23374151"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>');
    background-repeat: no-repeat;
    background-position: center;
    background-size: contain;
}
.icon-location {
    width: 20px;
    height: 20px;
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="%23374151"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /></svg>');
    background-repeat: no-repeat;
    background-position: center;
    background-size: contain;
}
.icon-phone {
    width: 20px;
    height: 20px;
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="%23374151"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>');
    background-repeat: no-repeat;
    background-position: center;
    background-size: contain;
}
.icon-clock {
    width: 20px;
    height: 20px;
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="%23374151"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>');
    background-repeat: no-repeat;
    background-position: center;
    background-size: contain;
}
.icon-money {
    width: 20px;
    height: 20px;
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="%23374151"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" /></svg>');
    background-repeat: no-repeat;
    background-position: center;
    background-size: contain;
}
.icon-map {
    width: 20px;
    height: 20px;
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="%23374151"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" /></svg>');
    background-repeat: no-repeat;
    background-position: center;
    background-size: contain;
}
.icon-link {
    width: 20px;
    height: 20px;
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="%23374151"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>');
    background-repeat: no-repeat;
    background-position: center;
    background-size: contain;
}
.icon-globe {
    width: 20px;
    height: 20px;
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="%23374151"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" /></svg>');
    background-repeat: no-repeat;
    background-position: center;
    background-size: contain;
}
.icon-heart {
    width: 20px;
    height: 20px;
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="%23374151"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>');
    background-repeat: no-repeat;
    background-position: center;
    background-size: contain;
}
.icon-share {
    width: 20px;
    height: 20px;
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="%23374151"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" /></svg>');
    background-repeat: no-repeat;
    background-position: center;
    background-size: contain;
}
.icon-print {
    width: 20px;
    height: 20px;
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="%23374151"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>');
    background-repeat: no-repeat;
    background-position: center;
    background-size: contain;
}
.icon-tag {
    width: 20px;
    height: 20px;
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="%23374151"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>');
    background-repeat: no-repeat;
    background-position: center;
    background-size: contain;
}
.icon-home {
    width: 20px;
    height: 20px;
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="%23374151"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>');
    background-repeat: no-repeat;
    background-position: center;
    background-size: contain;
}

/* Common icon styles */
[class*="icon-"] {
    display: inline-block;
    background-repeat: no-repeat;
    background-position: center;
    background-size: contain;
}

/* Icon hover states */
.info-card:hover [class*="icon-"]:not(.icon-yen) {
    filter: invert(1);
}

.btn:hover [class*="icon-"]:not(.icon-yen) {
    filter: invert(1);
}
</style>

<main class="grant-minna">
    <!-- Hero Section -->
    <header class="grant-hero">
        <?php if ($grant_data['is_featured']): ?>
        <div class="status-badge" style="background: var(--accent-warning); margin-bottom: var(--space-sm);">
            注目の助成金
        </div>
        <?php endif; ?>
        
        <div class="status-badge <?php echo $status_data['class']; ?> <?php echo $deadline_class; ?>">
            <?php echo $status_data['label']; ?>
            <?php if ($days_remaining > 0 && $days_remaining <= 30): ?>
                · <?php echo $days_remaining; ?>日
            <?php endif; ?>
        </div>
        
        <h1 class="grant-title"><?php the_title(); ?></h1>
        
        <?php if ($grant_data['ai_summary']): ?>
        <p class="grant-subtitle"><?php echo esc_html(wp_trim_words($grant_data['ai_summary'], 30, '...')); ?></p>
        <?php endif; ?>
        
        <!-- Key Information Grid -->
        <div class="key-info-grid">
            <?php if ($formatted_amount): ?>
            <div class="info-card">
                <div class="info-icon icon-yen"></div>
                <div class="info-label">最大助成額</div>
                <div class="info-value highlight"><?php echo esc_html($formatted_amount); ?></div>
            </div>
            <?php endif; ?>
            
            <?php if ($deadline_info): ?>
            <div class="info-card">
                <div class="info-icon icon-calendar"></div>
                <div class="info-label">申請締切</div>
                <div class="info-value <?php echo $deadline_class === 'urgent' ? 'urgent' : ''; ?>">
                    <?php echo esc_html($deadline_info); ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($grant_data['adoption_rate'] > 0): ?>
            <div class="info-card">
                <div class="info-icon icon-chart"></div>
                <div class="info-label">採択率</div>
                <div class="info-value"><?php echo number_format($grant_data['adoption_rate'], 1); ?>%</div>
            </div>
            <?php endif; ?>
            
            <?php if ($grant_data['organization']): ?>
            <div class="info-card">
                <div class="info-icon icon-building"></div>
                <div class="info-label">実施機関</div>
                <div class="info-value" style="font-size: var(--type-base);"><?php echo esc_html($grant_data['organization']); ?></div>
            </div>
            <?php endif; ?>
        </div>
    </header>
    
    <!-- Main Content Layout -->
    <div class="content-layout">
        <!-- Main Content -->
        <div class="content-main">
            <?php if ($grant_data['ai_summary']): ?>
            <!-- AI Summary Section -->
            <section class="content-section" style="border-left-color: var(--accent-info);">
                <header class="section-header">
                    <div class="section-icon">AI</div>
                    <h2 class="section-title">AI要約</h2>
                </header>
                <div class="section-content">
                    <div style="background: var(--color-surface); padding: var(--space-lg); border-radius: var(--radius-md); border-left: 4px solid var(--accent-info);">
                        <p style="font-size: var(--type-base); line-height: 1.7; margin: 0; color: var(--color-gray-700);">
                            <?php echo esc_html($grant_data['ai_summary']); ?>
                        </p>
                    </div>
                </div>
            </section>
            <?php endif; ?>
            
            <!-- Main Content Section -->
            <section class="content-section">
                <header class="section-header">
                    <div class="section-icon icon-document"></div>
                    <h2 class="section-title">詳細情報</h2>
                </header>
                <div class="section-content">
                    <?php the_content(); ?>
                </div>
            </section>
            
            <!-- Detailed Information Table -->
            <section class="content-section">
                <header class="section-header">
                    <div class="section-icon icon-document"></div>
                    <h2 class="section-title">助成金詳細情報</h2>
                </header>
                <div class="section-content">
                    <table class="info-table">
                        <?php if ($grant_data['organization']): ?>
                        <tr>
                            <th>実施機関</th>
                            <td>
                                <?php echo esc_html($grant_data['organization']); ?>
                                <?php if ($grant_data['organization_type']): ?>
                                    <br><small style="color: var(--mono-mid-gray);"><?php echo $org_type_labels[$grant_data['organization_type']] ?? $grant_data['organization_type']; ?></small>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                        
                        <?php if ($formatted_amount): ?>
                        <tr>
                            <th>最大助成額</th>
                            <td><strong style="font-size: var(--type-lg); color: var(--accent);"><?php echo esc_html($formatted_amount); ?></strong></td>
                        </tr>
                        <?php endif; ?>
                        
                        <?php if ($grant_data['min_amount'] > 0): ?>
                        <tr>
                            <th>最小助成額</th>
                            <td><?php echo number_format($grant_data['min_amount']); ?>円</td>
                        </tr>
                        <?php endif; ?>
                        
                        <?php if ($grant_data['subsidy_rate']): ?>
                        <tr>
                            <th>補助率</th>
                            <td><strong><?php echo esc_html($grant_data['subsidy_rate']); ?></strong></td>
                        </tr>
                        <?php endif; ?>
                        
                        <?php if ($grant_data['amount_note']): ?>
                        <tr>
                            <th>金額に関する備考</th>
                            <td><?php echo esc_html($grant_data['amount_note']); ?></td>
                        </tr>
                        <?php endif; ?>
                        
                        <?php if ($deadline_info): ?>
                        <tr>
                            <th>申請締切</th>
                            <td><strong style="<?php echo $deadline_class === 'urgent' ? 'color: var(--accent-danger);' : 'color: var(--color-gray-900);'; ?>"><?php echo esc_html($deadline_info); ?></strong></td>
                        </tr>
                        <?php endif; ?>
                        
                        <?php if ($grant_data['application_period']): ?>
                        <tr>
                            <th>申請期間</th>
                            <td><?php echo esc_html($grant_data['application_period']); ?></td>
                        </tr>
                        <?php endif; ?>
                        
                        <?php if ($grant_data['deadline_note']): ?>
                        <tr>
                            <th>締切に関する備考</th>
                            <td><?php echo esc_html($grant_data['deadline_note']); ?></td>
                        </tr>
                        <?php endif; ?>
                        
                        <?php if ($grant_data['application_method']): ?>
                        <tr>
                            <th>申請方法</th>
                            <td><?php echo $method_labels[$grant_data['application_method']] ?? esc_html($grant_data['application_method']); ?></td>
                        </tr>
                        <?php endif; ?>
                        
                        <?php if ($grant_data['regional_limitation']): ?>
                        <tr>
                            <th>地域制限</th>
                            <td><?php echo esc_html($grant_data['regional_limitation']); ?></td>
                        </tr>
                        <?php endif; ?>
                        
                        <?php if ($grant_data['adoption_rate'] > 0): ?>
                        <tr>
                            <th>採択率</th>
                            <td>
                                <strong style="font-size: var(--type-lg); color: var(--accent);"><?php echo number_format($grant_data['adoption_rate'], 1); ?>%</strong>
                                <div class="progress-bar" style="margin-top: var(--space-xs);">
                                    <div class="progress-fill" style="width: <?php echo min($grant_data['adoption_rate'], 100); ?>%"></div>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                        
                        <tr>
                            <th>申請難易度</th>
                            <td>
                                <div class="difficulty-indicator">
                                    <strong><?php echo $difficulty_data['label']; ?></strong>
                                    <div class="difficulty-dots">
                                        <?php for ($i = 1; $i <= 4; $i++): ?>
                                            <div class="difficulty-dot <?php echo $i <= $difficulty_data['dots'] ? 'filled' : ''; ?>"></div>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        
                        <tr>
                            <th>ステータス</th>
                            <td>
                                <span class="status-badge <?php echo $status_data['class']; ?>" style="display: inline-block;">
                                    <?php echo $status_data['label']; ?>
                                </span>
                            </td>
                        </tr>
                        
                        <?php if ($grant_data['last_updated']): ?>
                        <tr>
                            <th>最終更新日</th>
                            <td><?php echo esc_html($grant_data['last_updated']); ?></td>
                        </tr>
                        <?php endif; ?>
                        
                        <tr>
                            <th>閲覧数</th>
                            <td><?php echo number_format($grant_data['views_count']); ?> 回</td>
                        </tr>
                    </table>
                </div>
            </section>
            
            <?php if ($grant_data['grant_target']): ?>
            <!-- Target Details -->
            <section class="content-section">
                <header class="section-header">
                    <div class="section-icon icon-target"></div>
                    <h2 class="section-title">対象者・対象事業</h2>
                </header>
                <div class="section-content">
                    <?php echo wp_kses_post($grant_data['grant_target']); ?>
                </div>
            </section>
            <?php endif; ?>
            
            <?php if ($grant_data['target_expenses']): ?>
            <!-- Target Expenses (31列対応 - AC列) -->
            <section class="content-section">
                <header class="section-header">
                    <div class="section-icon icon-money"></div>
                    <h2 class="section-title">対象経費</h2>
                </header>
                <div class="section-content">
                    <?php echo wp_kses_post($grant_data['target_expenses']); ?>
                </div>
            </section>
            <?php endif; ?>
            
            <?php if ($grant_data['required_documents']): ?>
            <!-- Required Documents (31列対応 - Z列) -->
            <section class="content-section">
                <header class="section-header">
                    <div class="section-icon icon-document"></div>
                    <h2 class="section-title">必要書類</h2>
                </header>
                <div class="section-content">
                    <div style="background: var(--color-surface); padding: var(--space-lg); border-radius: var(--radius-md); border-left: 4px solid var(--accent-info);">
                        <?php echo wp_kses_post($grant_data['required_documents']); ?>
                    </div>
                </div>
            </section>
            <?php endif; ?>
            
            <?php if ($grant_data['region_notes']): ?>
            <!-- Region Notes (31列対応 - Y列) -->
            <section class="content-section">
                <header class="section-header">
                    <div class="section-icon icon-location"></div>
                    <h2 class="section-title">地域に関する備考</h2>
                </header>
                <div class="section-content">
                    <div style="background: var(--color-surface); padding: var(--space-lg); border-radius: var(--radius-md); border-left: 4px solid var(--accent-warning);">
                        <?php echo wp_kses_post($grant_data['region_notes']); ?>
                    </div>
                </div>
            </section>
            <?php endif; ?>
            
            <?php if ($grant_data['amount_note']): ?>
            <!-- Amount Notes -->
            <section class="content-section">
                <header class="section-header">
                    <div class="section-icon icon-money"></div>
                    <h2 class="section-title">金額に関する備考</h2>
                </header>
                <div class="section-content">
                    <div style="background: var(--color-surface); padding: var(--space-lg); border-radius: var(--radius-md); border-left: 4px solid var(--accent-success);">
                        <?php echo wp_kses_post($grant_data['amount_note']); ?>
                    </div>
                </div>
            </section>
            <?php endif; ?>
            
            <?php if ($grant_data['deadline_note']): ?>
            <!-- Deadline Notes -->
            <section class="content-section">
                <header class="section-header">
                    <div class="section-icon icon-clock"></div>
                    <h2 class="section-title">締切に関する備考</h2>
                </header>
                <div class="section-content">
                    <div style="background: var(--color-surface); padding: var(--space-lg); border-radius: var(--radius-md); border-left: 4px solid var(--accent-danger);">
                        <?php echo wp_kses_post($grant_data['deadline_note']); ?>
                    </div>
                </div>
            </section>
            <?php endif; ?>
            
            <?php if ($grant_data['application_period']): ?>
            <!-- Application Period -->
            <section class="content-section">
                <header class="section-header">
                    <div class="section-icon icon-calendar"></div>
                    <h2 class="section-title">申請期間</h2>
                </header>
                <div class="section-content">
                    <div style="background: var(--color-surface); padding: var(--space-lg); border-radius: var(--radius-md); border-left: 4px solid var(--accent-info);">
                        <p style="font-size: var(--type-base); font-weight: 600; margin: 0; color: var(--color-gray-900);">
                            <?php echo esc_html($grant_data['application_period']); ?>
                        </p>
                    </div>
                </div>
            </section>
            <?php endif; ?>
            
            <?php if ($grant_data['regional_limitation']): ?>
            <!-- Regional Limitation -->
            <section class="content-section">
                <header class="section-header">
                    <div class="section-icon icon-map"></div>
                    <h2 class="section-title">地域制限</h2>
                </header>
                <div class="section-content">
                    <div style="background: var(--color-surface); padding: var(--space-lg); border-radius: var(--radius-md); border-left: 4px solid var(--accent-warning);">
                        <?php echo wp_kses_post($grant_data['regional_limitation']); ?>
                    </div>
                </div>
            </section>
            <?php endif; ?>
            
            <?php if ($grant_data['contact_info']): ?>
            <!-- Contact Information -->
            <section class="content-section">
                <header class="section-header">
                    <div class="section-icon icon-phone"></div>
                    <h2 class="section-title">お問い合わせ先</h2>
                </header>
                <div class="section-content">
                    <div style="background: var(--color-surface); padding: var(--space-lg); border-radius: var(--radius-md); border-left: 4px solid var(--color-gray-900);">
                        <?php echo nl2br(esc_html($grant_data['contact_info'])); ?>
                    </div>
                </div>
            </section>
            <?php endif; ?>
        </div>
        
        <!-- Sidebar -->
        <aside class="sidebar">
            <!-- Action Buttons -->
            <div class="sidebar-card">
                <h3 class="sidebar-title">
                    <span class="icon-link"></span> アクション
                </h3>
                <div class="action-buttons">
                    <?php if ($grant_data['official_url']): ?>
                    <a href="<?php echo esc_url($grant_data['official_url']); ?>" class="btn btn-primary" target="_blank" rel="noopener">
                        <span class="icon-link"></span> 公式サイトで申請
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($grant_data['external_link']): ?>
                    <a href="<?php echo esc_url($grant_data['external_link']); ?>" class="btn btn-secondary" target="_blank" rel="noopener">
                        <span class="icon-globe"></span> 参考リンク
                    </a>
                    <?php endif; ?>
                    
                    <button class="btn btn-secondary" onclick="toggleFavorite(<?php echo $post_id; ?>)">
                        <span class="icon-heart"></span> お気に入りに追加
                    </button>
                    
                    <button class="btn btn-secondary" onclick="shareGrant()">
                        <span class="icon-share"></span> この助成金をシェア
                    </button>
                    
                    <button class="btn btn-secondary" onclick="window.print()">
                        <span class="icon-print"></span> 印刷用ページ
                    </button>
                </div>
            </div>
            
            <!-- Statistics -->
            <div class="sidebar-card">
                <h3 class="sidebar-title">
                    <span class="icon-chart"></span> 統計情報
                </h3>
                <div class="stats-grid">
                    <?php if ($grant_data['adoption_rate'] > 0): ?>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo number_format($grant_data['adoption_rate'], 1); ?>%</span>
                        <span class="stat-label">採択率</span>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo min($grant_data['adoption_rate'], 100); ?>%"></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="stat-item">
                        <span class="stat-number"><?php echo number_format($grant_data['views_count']); ?></span>
                        <span class="stat-label">閲覧数</span>
                    </div>
                    
                    <?php if ($days_remaining > 0): ?>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $days_remaining; ?></span>
                        <span class="stat-label">残り日数</span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $difficulty_data['dots']; ?>/4</span>
                        <span class="stat-label">申請難易度</span>
                    </div>
                    
                    <?php if ($grant_data['max_amount_numeric'] > 0): ?>
                    <div class="stat-item">
                        <span class="stat-number" style="font-size: var(--type-base);">¥<?php echo number_format($grant_data['max_amount_numeric']); ?></span>
                        <span class="stat-label">最大助成額</span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($grant_data['min_amount'] > 0): ?>
                    <div class="stat-item">
                        <span class="stat-number" style="font-size: var(--type-base);">¥<?php echo number_format($grant_data['min_amount']); ?></span>
                        <span class="stat-label">最小助成額</span>
                    </div>
                    <?php endif; ?>
                </div>
                
                <?php if ($grant_data['last_updated']): ?>
                <div style="margin-top: var(--space-md); padding-top: var(--space-md); border-top: 1px solid var(--color-border); text-align: center;">
                    <small style="color: var(--color-gray-500); font-size: var(--type-xs);">
                        最終更新: <?php echo esc_html($grant_data['last_updated']); ?>
                    </small>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Tags and Taxonomies -->
            <?php if ($taxonomies['categories'] || $taxonomies['prefectures'] || $taxonomies['tags']): ?>
            <div class="sidebar-card">
                <h3 class="sidebar-title">
                    <span class="icon-tag"></span> 関連分類
                </h3>
                
                <?php if ($taxonomies['categories'] && !is_wp_error($taxonomies['categories'])): ?>
                <div class="tags-section">
                    <h4 style="margin-bottom: var(--space-sm); color: var(--color-gray-500); font-size: var(--type-xs); font-weight: 600;">カテゴリー</h4>
                    <div class="tags-list">
                        <?php foreach ($taxonomies['categories'] as $category): ?>
                        <a href="<?php echo get_term_link($category); ?>" class="tag">
                            <span class="icon-tag"></span> <?php echo esc_html($category->name); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if ($taxonomies['prefectures'] && !is_wp_error($taxonomies['prefectures'])): ?>
                <div class="tags-section">
                    <h4 style="margin: var(--space-md) 0 var(--space-sm) 0; color: var(--color-gray-500); font-size: var(--type-xs); font-weight: 600;">対象地域</h4>
                    <div class="tags-list">
                        <?php foreach ($taxonomies['prefectures'] as $prefecture): ?>
                        <a href="<?php echo get_term_link($prefecture); ?>" class="tag">
                            <span class="icon-location"></span> <?php echo esc_html($prefecture->name); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if ($taxonomies['municipalities'] && !is_wp_error($taxonomies['municipalities'])): ?>
                <div class="tags-section">
                    <h4 style="margin: var(--space-md) 0 var(--space-sm) 0; color: var(--color-gray-500); font-size: var(--type-xs); font-weight: 600;">市町村</h4>
                    <div class="tags-list">
                        <?php foreach ($taxonomies['municipalities'] as $municipality): ?>
                        <a href="<?php echo get_term_link($municipality); ?>" class="tag">
                            <span class="icon-home"></span> <?php echo esc_html($municipality->name); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if ($taxonomies['tags'] && !is_wp_error($taxonomies['tags'])): ?>
                <div class="tags-section">
                    <h4 style="margin: var(--space-md) 0 var(--space-sm) 0; color: var(--color-gray-500); font-size: var(--type-xs); font-weight: 600;">タグ</h4>
                    <div class="tags-list">
                        <?php foreach ($taxonomies['tags'] as $tag): ?>
                        <a href="<?php echo get_term_link($tag); ?>" class="tag">
                            # <?php echo esc_html($tag->name); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </aside>
    </div>
    
    <?php
    // ============================================
    // 提案3: AI類似助成金レコメンド
    // ============================================
    if (function_exists('gi_get_similar_grants')) {
        $similar_grants = gi_get_similar_grants($post_id, 4);
        
        if (!empty($similar_grants)) :
    ?>
    <!-- Similar Grants Recommendation Section -->
    <section class="similar-grants-section" style="margin-top: var(--space-2xl); padding: var(--space-2xl); border: 2px solid var(--color-border); border-radius: var(--radius-lg); background: linear-gradient(135deg, var(--color-bg) 0%, var(--color-muted) 100%); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);">
        <header style="text-align: center; margin-bottom: var(--space-2xl);">
            <div style="display: inline-flex; align-items: center; gap: var(--space-sm); background: var(--color-gray-900); color: white; padding: var(--space-sm) var(--space-lg); border-radius: var(--radius-md); margin-bottom: var(--space-md);">
                <span style="font-weight: 700;">AI</span>
                <span style="font-weight: 700; letter-spacing: 0.05em;">RECOMMENDATION</span>
            </div>
            <h2 style="font-size: var(--type-2xl); font-weight: 700; color: var(--color-gray-900); margin: 0 0 var(--space-sm); letter-spacing: -0.01em;">
                類似する助成金
            </h2>
            <p style="color: var(--color-gray-500); font-size: var(--type-base); max-width: 600px; margin: 0 auto;">
                AIがあなたに最適な類似助成金を分析・推薦しています
            </p>
        </header>
        
        <div class="similar-grants-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: var(--space-xl); padding: var(--space-md); background: var(--color-muted); border-radius: var(--radius-lg); border: 1px solid var(--color-border);">
            <?php foreach ($similar_grants as $similar_post) : 
                $similar_id = $similar_post->ID;
                $similar_amount = get_field('max_amount', $similar_id);
                $similar_deadline = get_field('deadline_date', $similar_id);
                $similar_categories = wp_get_post_terms($similar_id, 'grant_category', ['fields' => 'names']);
                $similar_prefecture = wp_get_post_terms($similar_id, 'grant_prefecture', ['fields' => 'names', 'number' => 1]);
                
                // Calculate match score
                $match_score = 0;
                if (function_exists('gi_calculate_match_score')) {
                    $match_score = gi_calculate_match_score($similar_id);
                }
                
                // Format deadline
                $deadline_text = '';
                $deadline_class_similar = '';
                if ($similar_deadline) {
                    $deadline_ts = strtotime($similar_deadline);
                    $days_left = ceil(($deadline_ts - current_time('timestamp')) / 86400);
                    if ($days_left > 0) {
                        $deadline_text = '残り' . $days_left . '日';
                        if ($days_left <= 7) $deadline_class_similar = 'urgent';
                        elseif ($days_left <= 30) $deadline_class_similar = 'warning';
                    }
                }
            ?>
            <article class="similar-grant-card" style="background: var(--color-bg); border: 2px solid var(--color-border); border-radius: var(--radius-md); padding: var(--space-lg); transition: var(--transition); position: relative; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); cursor: pointer;" onmouseover="this.style.borderColor='var(--color-gray-900)'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 20px rgba(0, 0, 0, 0.15)';" onmouseout="this.style.borderColor='var(--color-border)'; this.style.transform='none'; this.style.boxShadow='0 2px 8px rgba(0, 0, 0, 0.1)';">
                <!-- AI Match Score Badge -->
                <?php if ($match_score >= 70) : ?>
                <div style="position: absolute; top: var(--space-md); right: var(--space-md); background: var(--color-gray-900); color: white; padding: var(--space-xs) var(--space-sm); border-radius: var(--radius-sm); font-size: var(--type-xs); font-weight: 700; z-index: 10;">
                    <span>AI <?php echo $match_score; ?>%</span>
                </div>
                <?php endif; ?>
                
                <!-- Category Badge -->
                <?php if (!empty($similar_categories)) : ?>
                <div style="margin-bottom: var(--space-md);">
                    <span style="display: inline-block; background: var(--color-muted); color: var(--color-gray-700); padding: var(--space-xs) var(--space-sm); border-radius: var(--radius-sm); font-size: var(--type-xs); font-weight: 600; border: 1px solid var(--color-border);">
                        <?php echo esc_html($similar_categories[0]); ?>
                    </span>
                </div>
                <?php endif; ?>
                
                <!-- Title -->
                <h3 style="font-size: var(--type-lg); font-weight: 700; color: var(--color-gray-900); margin: 0 0 var(--space-md); line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                    <?php echo esc_html($similar_post->post_title); ?>
                </h3>
                
                <!-- Details Grid -->
                <div style="display: grid; gap: var(--space-sm); margin-bottom: var(--space-lg);">
                    <?php if ($similar_amount) : ?>
                    <div style="display: flex; align-items: center; gap: var(--space-xs); font-size: var(--type-sm); color: var(--color-gray-700);">
                        <div class="icon-money" style="width: 20px; height: 20px; flex-shrink: 0;"></div>
                        <span style="font-weight: 600;"><?php echo esc_html($similar_amount); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($deadline_text) : ?>
                    <div style="display: flex; align-items: center; gap: var(--space-xs); font-size: var(--type-sm); color: <?php echo $deadline_class_similar === 'urgent' ? 'var(--accent-danger)' : 'var(--color-gray-700)'; ?>;">
                        <div class="icon-clock" style="width: 20px; height: 20px; flex-shrink: 0;"></div>
                        <span style="font-weight: 600;"><?php echo esc_html($deadline_text); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($similar_prefecture)) : ?>
                    <div style="display: flex; align-items: center; gap: var(--space-xs); font-size: var(--type-sm); color: var(--color-gray-700);">
                        <div class="icon-location" style="width: 20px; height: 20px; flex-shrink: 0;"></div>
                        <span><?php echo esc_html($similar_prefecture[0]); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- View Button -->
                <a href="<?php echo get_permalink($similar_id); ?>" class="btn btn-secondary" style="width: 100%; text-decoration: none; margin: 0; justify-content: space-between;">
                    <span>詳細を見る</span>
                    <span style="font-size: 0.75rem;">→</span>
                </a>
                
                <!-- Hover Effect -->
                <style>
.similar-grant-card:hover {
    border-color: var(--color-gray-900);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}
                </style>
            </article>
            <?php endforeach; ?>
        </div>
        
        <!-- View More Button -->
        <div style="text-align: center; margin-top: var(--space-2xl);">
            <a href="<?php echo get_post_type_archive_link('grant'); ?>" class="btn btn-primary" style="display: inline-flex; text-decoration: none; gap: var(--space-xs);">
                <div class="icon-document" style="width: 16px; height: 16px;"></div>
                <span>他の助成金を探す</span>
            </a>
        </div>
    </section>
    <?php 
        endif;
    }
    ?>
</main>

<script>
// Enhanced functionality for stylish design
function toggleFavorite(postId) {
    const button = event.target.closest('.btn');
    
    // Visual feedback
    button.style.transform = 'scale(0.95)';
    setTimeout(() => {
        button.style.transform = '';
        button.innerHTML = '<span class="icon-heart"></span> お気に入り登録済み';
        button.style.background = 'var(--accent-danger)';
        button.style.color = 'white';
    }, 100);
    
    console.log('Toggle favorite for post:', postId);
}

function shareGrant() {
    const title = document.title;
    const url = window.location.href;
    const text = '<?php echo esc_js(wp_trim_words($grant_data["ai_summary"] ?: get_the_excerpt(), 20, "")); ?>';
    
    if (navigator.share) {
        navigator.share({
            title: title,
            text: text,
            url: url
        }).catch(err => console.log('Error sharing:', err));
    } else {
        navigator.clipboard.writeText(url).then(() => {
            // Visual feedback
            const button = event.target.closest('.btn');
            const originalText = button.innerHTML;
            button.innerHTML = '<span class="icon-share"></span> URLをコピーしました！';
            button.style.background = 'var(--accent-success)';
            button.style.color = 'white';
            
            setTimeout(() => {
                button.innerHTML = originalText;
                button.style.background = '';
                button.style.color = '';
            }, 2000);
        }).catch(err => {
            alert('URLのコピーに失敗しました');
        });
    }
}

// Initialize page functionality - Minna Bank Style
document.addEventListener('DOMContentLoaded', function() {
    // Animate progress bars with smooth timing
    setTimeout(() => {
        document.querySelectorAll('.progress-fill').forEach((bar, index) => {
            const width = bar.style.width;
            bar.style.width = '0';
            setTimeout(() => {
                bar.style.width = width;
            }, 200 + (index * 80));
        });
    }, 300);
    
    // Smooth scroll animation for sections
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animation = 'fadeInUp 0.4s ease-out forwards';
                }
            });
        }, {
            threshold: 0.05,
            rootMargin: '50px'
        });
        
        document.querySelectorAll('.content-section, .sidebar-card, .info-card').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(15px)';
            observer.observe(el);
        });
    }
    
    // Add CSS animation keyframes with Minna Bank style
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Icon styling - SVG based, no emojis */
        [class*="icon-"]:not(.icon-yen) {
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
        }
    `;
    document.head.appendChild(style);
    
    // Enhanced touch support for mobile
    document.querySelectorAll('.btn').forEach(btn => {
        btn.addEventListener('touchstart', function() {
            this.style.transform = 'scale(0.98)';
        });
        btn.addEventListener('touchend', function() {
            this.style.transform = '';
        });
    });
});

// Enhanced print functionality - Minna Bank Style
window.addEventListener('beforeprint', function() {
    document.body.style.background = 'white';
    document.body.style.color = 'black';
});

window.addEventListener('afterprint', function() {
    document.body.style.background = '';
    document.body.style.color = '';
});

// Performance optimization - Lazy loading for images
if ('loading' in HTMLImageElement.prototype) {
    document.querySelectorAll('img').forEach(img => {
        img.loading = 'lazy';
    });
}
</script>

<?php get_footer(); ?>