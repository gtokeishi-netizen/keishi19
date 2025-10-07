<?php
/**
 * Grant Card Unified - Minna Bank Professional Edition
 * template-parts/grant-card-unified.php
 * 
 * 🏦 みんなの銀行スタイル - プロフェッショナルカードシステム
 * バンキンググレードUI・モノクロデザイン・高級感のある仕上がり
 * 
 * @package Grant_Insight_Minna_Bank
 * @version 12.0.0-minna-bank-professional
 */

// セキュリティチェック
if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

// グローバル変数から必要データを取得
global $post, $current_view, $display_mode;

$post_id = get_the_ID();
if (!$post_id) return;

// 表示モードの判定
$display_mode = $display_mode ?? (isset($_GET['view']) ? sanitize_text_field($_GET['view']) : 'card');
$view_class = 'grant-view-' . $display_mode;

// 基本データ取得
$title = get_the_title($post_id);
$permalink = get_permalink($post_id);
$excerpt = get_the_excerpt($post_id);

// 📋 完全31列対応 ACFフィールド取得（single-grant.phpと統一）
$grant_data = array(
    // 基本情報 (A-G列)
    'organization' => get_field('organization', $post_id) ?: '',
    'organization_type' => get_field('organization_type', $post_id) ?: '',
    
    // 金額情報 (H-I列)
    'max_amount' => get_field('max_amount', $post_id) ?: '',
    'max_amount_numeric' => intval(get_field('max_amount_numeric', $post_id)),
    'min_amount' => intval(get_field('min_amount', $post_id)),
    'amount_note' => get_field('amount_note', $post_id) ?: '',
    
    // 期間・締切情報 (J-K列)
    'deadline' => get_field('deadline', $post_id) ?: '',
    'deadline_date' => get_field('deadline_date', $post_id) ?: '',
    'application_period' => get_field('application_period', $post_id) ?: '',
    'deadline_note' => get_field('deadline_note', $post_id) ?: '',
    
    // 申請・組織情報 (L-Q列)
    'grant_target' => get_field('grant_target', $post_id) ?: '',
    'application_method' => get_field('application_method', $post_id) ?: '',
    'contact_info' => get_field('contact_info', $post_id) ?: '',
    'official_url' => get_field('official_url', $post_id) ?: '',
    
    // 地域・ステータス情報 (R-S列)
    'regional_limitation' => get_field('regional_limitation', $post_id) ?: '',
    'application_status' => get_field('application_status', $post_id) ?: 'open',
    
    // ★ 新規拡張フィールド (X-AD列) - 31列対応
    'external_link' => get_field('external_link', $post_id) ?: '',
    'region_notes' => get_field('region_notes', $post_id) ?: '',
    'required_documents' => get_field('required_documents', $post_id) ?: '',
    'adoption_rate' => floatval(get_field('adoption_rate', $post_id)),
    'grant_difficulty' => get_field('grant_difficulty', $post_id) ?: 'normal',
    'target_expenses' => get_field('target_expenses', $post_id) ?: '',
    'subsidy_rate' => get_field('subsidy_rate', $post_id) ?: '',
    
    // 管理・統計情報
    'is_featured' => get_field('is_featured', $post_id) ?: false,
    'views_count' => intval(get_field('views_count', $post_id)),
    'priority_order' => intval(get_field('priority_order', $post_id)) ?: 100,
    
    // AI関連
    'ai_summary' => get_field('ai_summary', $post_id) ?: get_post_meta($post_id, 'ai_summary', true),
);

// 個別変数に展開（後方互換性のため）
$ai_summary = $grant_data['ai_summary'];
$max_amount = $grant_data['max_amount'];
$max_amount_numeric = $grant_data['max_amount_numeric'];
$application_status = $grant_data['application_status'];
$organization = $grant_data['organization'];
$grant_target = $grant_data['grant_target'];
$subsidy_rate = $grant_data['subsidy_rate'];
$grant_difficulty = $grant_data['grant_difficulty'];
$grant_success_rate = $grant_data['adoption_rate'];
$official_url = $grant_data['official_url'];
$eligible_expenses = $grant_data['target_expenses'];
$application_method = $grant_data['application_method'];
$required_documents = $grant_data['required_documents'];
$contact_info = $grant_data['contact_info'];
$is_featured = $grant_data['is_featured'];
$priority_order = $grant_data['priority_order'];
$application_period = $grant_data['application_period'];

// 締切日の計算（single-grant.phpと完全統一）
$deadline_info_text = '';
$deadline_class = '';
$days_remaining = 0;
$deadline_timestamp = 0;
$deadline_formatted = '';

if ($grant_data['deadline_date']) {
    $deadline_timestamp = strtotime($grant_data['deadline_date']);
    if ($deadline_timestamp && $deadline_timestamp > 0) {
        $deadline_formatted = date('Y年n月j日', $deadline_timestamp);
        $current_time = current_time('timestamp');
        $days_remaining = ceil(($deadline_timestamp - $current_time) / (60 * 60 * 24));
    }
} elseif ($grant_data['deadline']) {
    $deadline_formatted = $grant_data['deadline'];
    $deadline_timestamp = strtotime($grant_data['deadline']);
    if ($deadline_timestamp && $deadline_timestamp > 0) {
        $current_time = current_time('timestamp');
        $days_remaining = ceil(($deadline_timestamp - $current_time) / (60 * 60 * 24));
    }
}

// タクソノミーデータ（single-grant.phpと統一）
$taxonomies = array(
    'categories' => get_the_terms($post_id, 'grant_category'),
    'prefectures' => get_the_terms($post_id, 'grant_prefecture'),
    'municipalities' => get_the_terms($post_id, 'grant_municipality'),
    'tags' => get_the_tags($post_id),
);

$main_category = ($taxonomies['categories'] && !is_wp_error($taxonomies['categories'])) ? $taxonomies['categories'][0]->name : '';
$main_prefecture = ($taxonomies['prefectures'] && !is_wp_error($taxonomies['prefectures'])) ? $taxonomies['prefectures'][0] : null;
$prefecture = $main_prefecture ? $main_prefecture->name : '全国';
$main_industry = '';

// 金額フォーマット（single-grant.phpと完全同一）
$formatted_amount = '';
$max_amount_yen = $grant_data['max_amount_numeric'];
if ($max_amount_yen > 0) {
    if ($max_amount_yen >= 100000000) {
        $formatted_amount = number_format($max_amount_yen / 100000000, 1) . '億円';
    } elseif ($max_amount_yen >= 10000) {
        $formatted_amount = number_format($max_amount_yen / 10000) . '万円';
    } else {
        $formatted_amount = number_format($max_amount_yen) . '円';
    }
} elseif ($grant_data['max_amount']) {
    $formatted_amount = $grant_data['max_amount'];
}
$amount_display = $formatted_amount;

// ステータス表示
$status_labels = array(
    'open' => '募集中',
    'closed' => '募集終了',
    'planned' => '募集予定',
    'suspended' => '一時停止'
);
$status_display = $status_labels[$application_status] ?? '募集中';

// 締切日情報の処理（single-grant.phpと完全統一）
$deadline_info = array();
if ($deadline_timestamp > 0 && $days_remaining > 0) {
    if ($days_remaining <= 0) {
        $deadline_class = 'expired';
        $deadline_info_text = '募集終了';
        $deadline_info = array('class' => 'expired', 'text' => '募集終了');
    } elseif ($days_remaining <= 7) {
        $deadline_class = 'urgent';
        $deadline_info_text = 'あと' . $days_remaining . '日';
        $deadline_info = array('class' => 'urgent', 'text' => '残り'.$days_remaining.'日');
    } elseif ($days_remaining <= 30) {
        $deadline_class = 'warning';
        $deadline_info_text = 'あと' . $days_remaining . '日';
        $deadline_info = array('class' => 'warning', 'text' => '残り'.$days_remaining.'日');
    } else {
        $deadline_info = array('class' => 'normal', 'text' => $deadline_formatted);
    }
} elseif ($deadline_formatted) {
    $deadline_info = array('class' => 'normal', 'text' => $deadline_formatted);
}

// 難易度設定（single-grant.phpと完全統一、アイコン削除）
$difficulty_configs = array(
    'easy' => array('label' => '簡単', 'dots' => 1, 'color' => '#16a34a'),
    'normal' => array('label' => '普通', 'dots' => 2, 'color' => '#525252'),
    'hard' => array('label' => '難しい', 'dots' => 3, 'color' => '#d97706'),
    'very_hard' => array('label' => '非常に困難', 'dots' => 4, 'color' => '#dc2626')
);
$difficulty = $grant_data['grant_difficulty'];
$difficulty_data = $difficulty_configs[$difficulty] ?? $difficulty_configs['normal'];

// CSS・JSの重複防止
static $assets_loaded = false;
?>

<?php if (!$assets_loaded): $assets_loaded = true; ?>

<style>
/* ===============================================
   🏦 MINNA BANK PROFESSIONAL CARD SYSTEM
   みんなの銀行グレード - プロフェッショナルデザイン
   =============================================== */

:root {
    /* みんなの銀行 コアカラーシステム */
    --mb-black: #000000;
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
    
    /* セマンティックカラー - 最小限使用 */
    --mb-success: #16a34a;
    --mb-warning: #f59e0b;
    --mb-danger: #dc2626;
    
    /* プロフェッショナル・グラデーション */
    --mb-gradient-primary: linear-gradient(135deg, var(--mb-black) 0%, var(--mb-gray-800) 100%);
    --mb-gradient-subtle: linear-gradient(135deg, var(--mb-white) 0%, var(--mb-gray-50) 100%);
    --mb-gradient-card: linear-gradient(145deg, var(--mb-white) 0%, var(--mb-gray-50) 100%);
    
    /* プロフェッショナル・シャドウ */
    --mb-shadow-card: 0 2px 12px rgba(0, 0, 0, 0.08);
    --mb-shadow-hover: 0 8px 32px rgba(0, 0, 0, 0.15);
    --mb-shadow-premium: 0 16px 48px rgba(0, 0, 0, 0.2);
    --mb-shadow-sm: 0 1px 4px rgba(0, 0, 0, 0.06);
    --mb-shadow-md: 0 4px 16px rgba(0, 0, 0, 0.1);
    --mb-shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.12);
    --mb-shadow-overlay: 0 20px 60px rgba(0, 0, 0, 0.25);
    
    /* バンキング・タイポグラフィ */
    --mb-font-primary: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
    --mb-font-mono: 'SF Mono', Monaco, 'Roboto Mono', Consolas, monospace;
    
    /* プロフェッショナル・スペーシング */
    --mb-space-xs: 0.25rem;
    --mb-space-sm: 0.5rem;
    --mb-space-md: 1rem;
    --mb-space-lg: 1.5rem;
    --mb-space-xl: 2rem;
    
    /* バンキング・トランジション */
    --mb-transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    --mb-transition-slow: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    --mb-transition-smooth: all 0.25s cubic-bezier(0.25, 0.8, 0.25, 1);
    --mb-ease-smooth: cubic-bezier(0.25, 0.8, 0.25, 1);
    
    /* バンキング・ボーダーラジウス */
    --mb-radius-xs: 2px;
    --mb-radius-sm: 4px;
    --mb-radius-md: 8px;
    --mb-radius-lg: 12px;
    --mb-radius-xl: 16px;
    --mb-radius-2xl: 24px;
    --mb-radius-full: 9999px;
}

/* ===============================================
   グリッドシステム - プロフェッショナル
   =============================================== */
.grants-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
    gap: var(--mb-space-lg);
    padding: var(--mb-space-xl);
    max-width: 1400px;
    margin: 0 auto;
}

@media (max-width: 1200px) {
    .grants-grid {
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: var(--mb-space-md);
    }
}

@media (max-width: 768px) {
    .grants-grid {
        grid-template-columns: 1fr;
        padding: var(--mb-space-md);
        gap: var(--mb-space-md);
    }
}

/* ===============================================
   🏦 MINNA BANK PROFESSIONAL CARD DESIGN
   =============================================== */
.grant-card-unified {
    position: relative;
    background: var(--mb-gradient-card);
    border: 2px solid var(--mb-gray-300);
    border-radius: 16px;
    overflow: hidden;
    transition: var(--mb-transition-slow);
    display: flex;
    flex-direction: column;
    min-height: 420px;
    box-shadow: var(--mb-shadow-card);
    cursor: pointer;
    
    /* 改善されたプロフェッショナル・ボーダー */
    box-shadow: 
        var(--mb-shadow-card),
        inset 0 1px 0 rgba(255, 255, 255, 0.7),
        inset 0 0 0 1px rgba(0, 0, 0, 0.05);
    
    /* より明確な境界線 */
    outline: 1px solid var(--mb-gray-200);
    outline-offset: -1px;
}

.grant-card-unified:hover {
    transform: translateY(-4px) scale(1.01);
    box-shadow: var(--mb-shadow-hover);
    border-color: var(--mb-black);
    
    /* より鮮明なホバーエフェクト */
    background: var(--mb-white);
    outline-color: var(--mb-gray-400);
    
    /* 強化されたボーダー効果 */
    border-width: 3px;
}

.grant-card-unified::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--mb-gradient-primary);
    z-index: 1;
}

/* ===============================================
   🏦 MINNA BANK STATUS HEADER - プロフェッショナル
   =============================================== */
.grant-status-header {
    position: relative;
    height: 56px;
    background: var(--mb-gradient-primary);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 var(--mb-space-lg);
    margin: -1px -1px 0 -1px;
    z-index: 2;
    
    /* バンキンググレード・テクスチャ */
    background-size: 200% 200%;
    animation: subtle-gradient 8s ease-in-out infinite;
}

@keyframes subtle-gradient {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

.grant-status-header.status--active {
    background: linear-gradient(135deg, var(--mb-success) 0%, #059669 100%);
}

.grant-status-header.status--closed {
    background: linear-gradient(135deg, var(--mb-gray-600) 0%, var(--mb-gray-700) 100%);
}

.grant-status-header.status--urgent {
    background: linear-gradient(135deg, var(--mb-danger) 0%, #b91c1c 100%);
    animation: urgent-pulse 2s ease-in-out infinite;
}

@keyframes urgent-pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.9; }
}

.grant-status-badge {
    display: flex;
    align-items: center;
    gap: var(--mb-space-sm);
    color: var(--mb-white);
    font-family: var(--mb-font-primary);
    font-size: 0.875rem;
    font-weight: 600;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

.grant-deadline-indicator {
    display: flex;
    align-items: center;
    gap: var(--mb-space-xs);
    padding: var(--mb-space-xs) var(--mb-space-sm);
    background: rgba(255, 255, 255, 0.15);
    border-radius: 20px;
    color: var(--mb-white);
    font-size: 0.75rem;
    font-weight: 700;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    font-family: var(--mb-font-mono);
}

/* ===============================================
   🏦 CARD CONTENT - プロフェッショナル・レイアウト
   =============================================== */
.grant-card-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    padding: var(--mb-space-lg);
    position: relative;
    z-index: 2;
}

.grant-title-section {
    margin-bottom: var(--mb-space-lg);
    position: relative;
}

.grant-category-tag {
    display: inline-flex;
    align-items: center;
    gap: var(--mb-space-xs);
    padding: var(--mb-space-sm) var(--mb-space-md);
    background: var(--mb-black);
    color: var(--mb-white);
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: var(--mb-space-md);
    font-family: var(--mb-font-mono);
    
    /* プレミアム・エッジ */
    box-shadow: 
        0 2px 8px rgba(0, 0, 0, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.grant-title {
    font-size: 1.25rem;
    font-weight: 700;
    line-height: 1.3;
    color: var(--mb-gray-900);
    margin: 0;
    font-family: var(--mb-font-primary);
    
    /* テキスト・エリプシス */
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    min-height: 2.6rem;
    
    /* プレミアム・テキスト・シャドウ */
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
}

.grant-title a {
    color: inherit;
    text-decoration: none;
    transition: var(--mb-transition);
}

.grant-title a:hover {
    color: var(--mb-black);
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

/* ===============================================
   🏦 AI SUMMARY - バンキング・インテリジェンス
   =============================================== */
.grant-ai-summary {
    position: relative;
    background: var(--mb-gradient-subtle);
    border: 1px solid var(--mb-gray-200);
    border-radius: 12px;
    padding: var(--mb-space-lg);
    margin-bottom: var(--mb-space-lg);
    min-height: 140px;
    overflow: hidden;
    transition: var(--mb-transition-slow);
    
    /* プロフェッショナル・ボーダー */
    box-shadow: 
        var(--mb-shadow-card),
        inset 0 1px 0 rgba(255, 255, 255, 0.8);
}

.grant-ai-summary:hover {
    background: var(--mb-white);
    border-color: var(--mb-gray-400);
    box-shadow: var(--mb-shadow-hover);
    transform: translateY(-2px);
}

.grant-ai-summary-label {
    display: flex;
    align-items: center;
    gap: var(--mb-space-sm);
    color: var(--mb-gray-700);
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    margin-bottom: var(--mb-space-md);
    font-family: var(--mb-font-mono);
    
    /* アイコン：プロフェッショナル・インジケーター */
    &::before {
        content: '◆';
        color: var(--mb-black);
        font-size: 0.75rem;
        font-weight: 900;
    }
}

.grant-ai-summary-text {
    color: var(--mb-gray-800);
    font-size: 0.9rem;
    line-height: 1.6;
    font-weight: 400;
    margin: 0;
    font-family: var(--mb-font-primary);
    
    /* テキスト制限 */
    display: -webkit-box;
    -webkit-line-clamp: 4;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* ===============================================
   🏦 INFO GRID - プロフェッショナル・データ表示
   =============================================== */
.grant-info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--mb-space-md);
    margin-bottom: var(--mb-space-lg);
}

.grant-info-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: var(--mb-space-md);
    background: var(--mb-white);
    border: 1px solid var(--mb-gray-200);
    border-radius: 8px;
    text-align: center;
    min-height: 80px;
    transition: var(--mb-transition);
    
    /* プロフェッショナル・シャドウ */
    box-shadow: 
        var(--mb-shadow-card),
        inset 0 1px 0 rgba(255, 255, 255, 0.5);
}

.grant-info-item:hover {
    background: var(--mb-gray-50);
    border-color: var(--mb-gray-400);
    transform: translateY(-1px);
    box-shadow: var(--mb-shadow-hover);
}

.grant-info-label {
    font-size: 0.65rem;
    font-weight: 700;
    color: var(--mb-gray-600);
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: var(--mb-space-xs);
    font-family: var(--mb-font-mono);
}

.grant-info-label[data-type="amount"]::before {
    content: '¥ ';
    color: var(--mb-black);
    font-weight: 900;
}

.grant-info-label[data-type="region"]::before {
    content: '📍 ';
    filter: grayscale(1);
}

.grant-info-value {
    font-size: 1rem;
    font-weight: 700;
    color: var(--mb-gray-900);
    line-height: 1.2;
    font-family: var(--mb-font-primary);
    word-break: break-word;
}

/* ===============================================
   🏦 CARD FOOTER - プロフェッショナル・アクション
   =============================================== */
.grant-card-footer {
    padding: var(--mb-space-lg);
    background: var(--mb-gray-50);
    border-top: 1px solid var(--mb-gray-200);
    margin-top: auto;
    z-index: 3;
    position: relative;
}

.grant-actions {
    display: flex;
    gap: var(--mb-space-sm);
    width: 100%;
}

.grant-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: var(--mb-space-xs);
    padding: var(--mb-space-sm) var(--mb-space-md);
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 600;
    font-family: var(--mb-font-primary);
    cursor: pointer;
    transition: var(--mb-transition);
    text-decoration: none;
    white-space: nowrap;
    min-height: 44px;
    flex: 1;
    
    /* バンキング・グレード・ボーダー */
    border: 1px solid transparent;
    box-shadow: var(--mb-shadow-card);
}

/* プライマリボタン - みんなの銀行スタイル */
.grant-btn--primary {
    background: var(--mb-gradient-primary);
    color: var(--mb-white);
    border-color: var(--mb-black);
}

.grant-btn--primary:hover {
    background: var(--mb-black);
    transform: translateY(-1px);
    box-shadow: var(--mb-shadow-hover);
}

/* セカンダリボタン */
.grant-btn--secondary {
    background: var(--mb-white);
    color: var(--mb-gray-900);
    border-color: var(--mb-gray-300);
}

.grant-btn--secondary:hover {
    background: var(--mb-gray-100);
    border-color: var(--mb-gray-500);
    transform: translateY(-1px);
}

/* AIボタン - インテリジェント・スタイル */
.grant-btn--ai {
    background: var(--mb-gray-900);
    color: var(--mb-white);
    border-color: var(--mb-gray-700);
    position: relative;
}

.grant-btn--ai::before {
    content: '◆';
    font-size: 0.75rem;
    margin-right: var(--mb-space-xs);
}

.grant-btn--ai:hover {
    background: var(--mb-black);
    transform: translateY(-1px);
    box-shadow: var(--mb-shadow-hover);
}

/* AI Checklist Button styles removed per user request */
}

/* Compare button styles removed per user request */

/* ============================================
   AI機能バッジ群（モノクローム）
============================================ */

/* AI適合度スコア - 配置調整 */
.grant-match-score {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: #000;
    color: #fff;
    padding: 0.6rem 0.9rem;
    border-radius: 2rem;
    font-size: 0.8rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    display: flex;
    align-items: center;
    gap: 0.4rem;
    z-index: 15;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
    border: 2px solid rgba(255, 255, 255, 0.3);
    min-width: 80px;
    justify-content: center;
}

/* AIバッジモバイルコンテナ */
.grant-ai-badges-mobile {
    display: none;
}

/* アイコンのモノクローム化 - 全体適用 */
.grant-detail-label span,
.grant-stat-label,
.grant-hover-details .grant-detail-label span,
.grant-quick-stats .grant-stat-label {
    filter: grayscale(1) brightness(0) !important;
    opacity: 0.8;
}

/* 特定のアイコンモノクローム化 */
emoji,
[role="img"],
.emoji {
    filter: grayscale(1) brightness(0) !important;
    opacity: 0.9;
}

/* バッジの重複防止 - デスクトップ用改良 */
.grant-badge-container {
    position: absolute;
    top: 1rem;
    right: 1rem;
    left: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    z-index: 20;
    pointer-events: none;
}

.grant-badge-left {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    align-items: flex-start;
}

.grant-badge-right {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    align-items: flex-end;
}

/* スマホ対応: PC版を非表示、モバイル版を表示 */
@media (max-width: 640px) {
    .grant-match-score,
    .grant-ai-difficulty,
    .grant-urgency-alert,
    .grant-badge-container {
        display: none !important;
    }
    
    .grant-ai-badges-mobile {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1rem;
        padding: 0 0.5rem;
    }
    
    .grant-match-score-mobile,
    .grant-ai-difficulty-mobile,
    .grant-urgency-alert-mobile {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.5rem 0.75rem;
        border-radius: 1.5rem;
        font-size: 0.75rem;
        font-weight: 700;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .grant-match-score-mobile {
        background: #000;
        color: #fff;
        border: 2px solid rgba(255, 255, 255, 0.3);
    }
    
    .grant-ai-difficulty-mobile {
        background: #fff;
        color: #000;
        border: 2px solid #000;
    }
    
    .grant-urgency-alert-mobile {
        color: #fff;
        border: 2px solid rgba(255, 255, 255, 0.3);
    }
    
    .grant-title-section {
        padding-right: 0;
    }
}

.grant-match-score:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
}

.grant-match-score i {
    font-size: 0.875rem;
    animation: pulse-brain 2s ease-in-out infinite;
    filter: grayscale(1) brightness(0) invert(1);
}

@keyframes pulse-brain {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.6; }
}

/* みんなの銀行 AI申請難易度インジケーター */
.grant-ai-difficulty {
    position: absolute;
    bottom: 5.5rem;
    left: 1rem;
    background: var(--mb-gradient-subtle);
    border: 1px solid var(--mb-gray-300);
    padding: 0.75rem 1rem;
    border-radius: var(--mb-radius-lg);
    font-size: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    z-index: 10;
    transition: var(--mb-transition-smooth);
    box-shadow: var(--mb-shadow-sm);
    min-width: 100px;
    font-family: var(--mb-font-primary);
    backdrop-filter: blur(8px);
}

.grant-ai-difficulty:hover {
    background: var(--mb-gradient-primary);
    color: var(--mb-white);
    transform: translateY(-1px);
    box-shadow: var(--mb-shadow-md);
}

.difficulty-stars {
    font-size: 0.875rem;
    letter-spacing: 0.1em;
    font-weight: 900;
    font-family: var(--mb-font-mono);
}

.difficulty-label {
    font-weight: 600;
    font-family: var(--mb-font-primary);
}

/* みんなの銀行 難易度レベル - モノクロ階層 */
.grant-ai-difficulty[data-level="very-easy"] {
    border-color: var(--mb-gray-400);
    background: linear-gradient(135deg, var(--mb-white), var(--mb-gray-50));
}

.grant-ai-difficulty[data-level="easy"] {
    border-color: var(--mb-gray-500);
    background: linear-gradient(135deg, var(--mb-gray-50), var(--mb-gray-100));
}

.grant-ai-difficulty[data-level="normal"] {
    border-color: var(--mb-gray-700);
    background: linear-gradient(135deg, var(--mb-gray-100), var(--mb-gray-200));
}

.grant-ai-difficulty[data-level="hard"] {
    border-color: var(--mb-gray-800);
    background: linear-gradient(135deg, var(--mb-gray-200), var(--mb-gray-300));
}

.grant-ai-difficulty[data-level="very-hard"] {
    border-color: var(--mb-black);
    background: linear-gradient(135deg, var(--mb-gray-300), var(--mb-gray-400));
}

/* みんなの銀行 AI期限アラートシステム */
.grant-urgency-alert {
    position: absolute;
    top: 4.5rem;
    left: 1rem;
    color: var(--mb-white);
    padding: 0.75rem 1.25rem;
    border-radius: var(--mb-radius-full);
    font-size: 0.8rem;
    font-weight: 700;
    font-family: var(--mb-font-primary);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    z-index: 12;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: var(--mb-shadow-lg);
    min-width: 110px;
    justify-content: center;
    backdrop-filter: blur(10px);
    background: var(--mb-gradient-primary);
}

/* みんなの銀行 レスポンシブアラート */
@media (max-width: 640px) {
    .grant-urgency-alert {
        position: static;
        display: inline-flex;
        margin-bottom: 0.5rem;
        font-size: 0.7rem;
        padding: 0.5rem 0.875rem;
        box-shadow: var(--mb-shadow-md);
        animation: mb-urgency-pulse 2s ease-in-out infinite;
    }
}

/* みんなの銀行 アニメーション - バンキング品質 */
@keyframes mb-urgency-pulse {
    0%, 100% { 
        transform: scale(1);
        box-shadow: var(--mb-shadow-md);
    }
    50% { 
        transform: scale(1.02);
        box-shadow: var(--mb-shadow-lg);
    }
}

.grant-urgency-alert[data-level="critical"] {
    animation: mb-urgency-critical 1s ease-in-out infinite;
    background: linear-gradient(135deg, var(--mb-gray-800), var(--mb-black));
}

@keyframes mb-urgency-critical {
    0%, 100% { 
        transform: translateX(0) scale(1);
        filter: brightness(1);
    }
    25% { 
        transform: translateX(-1px) scale(1.01);
        filter: brightness(1.1);
    }
    75% { 
        transform: translateX(1px) scale(1.01);
        filter: brightness(1.1);
    }
}

/* みんなの銀行 ホバー詳細システム - バンキンググレード */
.grant-hover-details {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.97);
    backdrop-filter: blur(20px) saturate(150%);
    padding: 0;
    opacity: 0;
    visibility: hidden;
    transition: all 0.35s var(--mb-ease-smooth);
    overflow: hidden;
    z-index: 5;
    border-radius: var(--mb-radius-xl);
    display: flex;
    flex-direction: column;
    pointer-events: none;
    box-shadow: var(--mb-shadow-overlay);
    border: 1px solid var(--mb-gray-300);
}

/* みんなの銀行 ホバーインタラクション */
.grant-card-unified:hover .grant-hover-details {
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
    transform: scale(1.005) translateY(-2px);
}

/* みんなの銀行 ボタンアクセシビリティ */
.grant-card-unified:hover .grant-card-footer {
    position: relative;
    z-index: 20;
    pointer-events: auto;
}

.grant-card-footer .grant-btn {
    position: relative;
    z-index: 25;
    pointer-events: auto;
}

/* インタラクティブ要素のアクセシビリティ保証 */
.grant-hover-details.show-details ~ .grant-card-footer,
.grant-card-unified:hover .grant-card-footer {
    z-index: 30;
}

.grant-hover-details.show-details ~ .grant-card-footer .grant-btn,
.grant-card-unified:hover .grant-card-footer .grant-btn {
    z-index: 35;
    pointer-events: auto;
}

/* みんなの銀行 スクロールアリア */
.grant-hover-scrollable {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
    padding: 1.5rem;
    height: 100%;
    scroll-behavior: smooth;
    position: relative;
    scrollbar-width: thin;
    scrollbar-color: var(--mb-gray-400) var(--mb-gray-100);
}

/* みんなの銀行 カスタムスクロールバー */
.grant-hover-scrollable::-webkit-scrollbar {
    width: 4px;
}

.grant-hover-scrollable::-webkit-scrollbar-track {
    background: var(--mb-gray-100);
    border-radius: var(--mb-radius-sm);
}

.grant-hover-scrollable::-webkit-scrollbar-thumb {
    background: var(--mb-gray-400);
    border-radius: var(--mb-radius-sm);
    transition: var(--mb-transition);
}

.grant-hover-scrollable::-webkit-scrollbar-thumb:hover {
    background: var(--mb-gray-600);
}

.grant-hover-details::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: var(--mb-gradient-primary);
    z-index: 10;
    border-radius: var(--mb-radius-xl) var(--mb-radius-xl) 0 0;
}

/* みんなの銀行 ホバーヘッダー */
.grant-hover-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1.5rem;
    padding-top: 0.75rem;
    position: sticky;
    top: 0;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(8px);
    z-index: 10;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--mb-gray-200);
}

.grant-hover-title {
    font-size: 1.25rem;
    font-weight: 700;
    font-family: var(--mb-font-primary);
    color: var(--mb-black);
    line-height: 1.3;
    flex: 1;
    padding-right: 1rem;
    letter-spacing: -0.02em;
}

.grant-hover-close {
    width: 2rem;
    height: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--mb-gray-100);
    border-radius: 50%;
    color: var(--mb-gray-600);
    cursor: pointer;
    flex-shrink: 0;
    transition: var(--mb-transition-smooth);
    border: 1px solid var(--mb-gray-200);
    pointer-events: auto;
    font-size: 0.875rem;
}

.grant-hover-close:hover {
    background: var(--mb-black);
    color: var(--mb-white);
    transform: rotate(90deg) scale(1.05);
    border-color: var(--mb-black);
}

/* みんなの銀行 クイックスタッツバー */
.grant-quick-stats {
    display: flex;
    gap: 0;
    padding: 0;
    background: var(--mb-gradient-subtle);
    border: 1px solid var(--mb-gray-200);
    border-radius: var(--mb-radius-lg);
    margin-bottom: 1.25rem;
    overflow: hidden;
}

.grant-stat-item {
    flex: 1;
    text-align: center;
    padding: 1rem 0.75rem;
    border-right: 1px solid var(--mb-gray-200);
    position: relative;
    transition: var(--mb-transition);
}

.grant-stat-item:last-child {
    border-right: none;
}

.grant-stat-item:hover {
    background: var(--mb-white);
    transform: translateY(-1px);
}

.grant-stat-value {
    display: block;
    font-size: 1.125rem;
    font-weight: 700;
    font-family: var(--mb-font-mono);
    color: var(--mb-black);
    margin-bottom: 0.25rem;
    letter-spacing: -0.02em;
}

.grant-stat-label {
    display: block;
    font-size: 0.65rem;
    color: var(--mb-gray-600);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    font-weight: 600;
    font-family: var(--mb-font-primary);
}

/* みんなの銀行 詳細セクション */
.grant-detail-sections {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    flex: 1;
}

.grant-detail-section {
    padding: 1.25rem;
    background: var(--mb-white);
    border: 1px solid var(--mb-gray-200);
    border-radius: var(--mb-radius-md);
    transition: var(--mb-transition-smooth);
    position: relative;
}

.grant-detail-section:hover {
    box-shadow: var(--mb-shadow-md);
    transform: translateY(-1px);
    border-color: var(--mb-gray-400);
    background: var(--mb-gradient-subtle);
}

.grant-detail-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.7rem;
    font-weight: 700;
    font-family: var(--mb-font-primary);
    color: var(--mb-gray-800);
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-bottom: 0.75rem;
}

.grant-detail-value {
    font-size: 0.875rem;
    font-family: var(--mb-font-primary);
    color: var(--mb-gray-700);
    line-height: 1.6;
    letter-spacing: -0.01em;
}

/* みんなの銀行 ステータスインジケーター */
.grant-status-indicator {
    position: absolute;
    top: 1rem;
    right: 1rem;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--mb-gray-700);
    box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.1);
    z-index: 10;
    transition: var(--mb-transition);
}

.grant-status-indicator.open {
    background: var(--mb-black);
    box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.2);
    animation: mb-status-pulse 2s ease-in-out infinite;
}

.grant-status-indicator.closed {
    background: var(--mb-gray-400);
    box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.05);
}

@keyframes mb-status-pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.6; }
}

/* 注目バッジ */
.grant-featured-badge {
    position: absolute;
    top: 4rem;
    right: -2.25rem;
    background: var(--clean-gradient-primary);
    color: var(--clean-white);
    padding: 0.375rem 2.75rem;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    transform: rotate(45deg);
    box-shadow: var(--clean-shadow-md);
    z-index: 10;
}

/* 難易度インジケーター */
.grant-difficulty-badge {
    position: absolute;
    top: 1rem;
    left: 1rem;
    padding: 0.375rem 0.75rem;
    background: var(--clean-white);
    border: 1px solid var(--clean-gray-200);
    border-radius: var(--clean-radius-sm);
    display: flex;
    align-items: center;
    gap: 0.375rem;
    font-size: 0.6875rem;
    font-weight: 600;
    box-shadow: var(--clean-shadow-sm);
    z-index: 10;
}

/* プログレスバー（採択率） */
.grant-success-rate {
    margin-top: auto;
    padding-top: 1rem;
}

.grant-success-label {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.75rem;
    color: var(--clean-gray-500);
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.grant-success-bar {
    height: 0.375rem;
    background: var(--clean-gray-200);
    border-radius: 0.1875rem;
    overflow: hidden;
    position: relative;
}

.grant-success-fill {
    height: 100%;
    background: var(--clean-gradient-primary);
    border-radius: 0.1875rem;
    transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
}

/* タグシステム */
.grant-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 1rem;
}

.grant-tag {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.25rem 0.75rem;
    background: var(--clean-gray-100);
    color: var(--clean-gray-800);
    border: 1px solid var(--clean-gray-200);
    border-radius: var(--clean-radius-2xl);
    font-size: 0.6875rem;
    font-weight: 600;
    transition: var(--clean-transition);
}

.grant-tag:hover {
    background: var(--clean-gray-900);
    color: var(--clean-white);
    transform: scale(1.02);
}

/* アニメーション */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.grant-card-unified {
    animation: slideIn 0.5s ease-out;
    animation-fill-mode: both;
}

.grant-card-unified:nth-child(1) { animation-delay: 0.05s; }
.grant-card-unified:nth-child(2) { animation-delay: 0.1s; }
.grant-card-unified:nth-child(3) { animation-delay: 0.15s; }
.grant-card-unified:nth-child(4) { animation-delay: 0.2s; }
.grant-card-unified:nth-child(5) { animation-delay: 0.25s; }
.grant-card-unified:nth-child(6) { animation-delay: 0.3s; }

/* トースト通知 */
.grant-toast {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    padding: 1.25rem 1.5rem;
    background: var(--clean-gray-900);
    color: var(--clean-white);
    border-radius: var(--clean-radius-lg);
    box-shadow: var(--clean-shadow-xl);
    display: flex;
    align-items: center;
    gap: 1rem;
    font-size: 0.9375rem;
    font-weight: 600;
    z-index: 9999;
    opacity: 0;
    transform: translateY(100%);
    transition: var(--clean-transition-slow);
}

.grant-toast.show {
    opacity: 1;
    transform: translateY(0);
}

.grant-toast-icon {
    width: 1.75rem;
    height: 1.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--clean-white);
    border-radius: 50%;
    color: var(--clean-gray-900);
}

/* レスポンシブ対応 - 改良版 */
@media (max-width: 768px) {
    .grants-grid {
        grid-template-columns: 1fr;
        padding: 1rem;
        gap: 1.25rem;
    }
    
    .grant-view-card .grant-card-unified {
        height: auto;
        min-height: auto;
        max-width: 100%;
        border-width: 2px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .grant-info-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
        padding: 0;
    }
    
    .grant-info-item {
        min-height: 80px;
        padding: 1rem;
        border-width: 3px;
        border-radius: var(--clean-radius-xl);
        margin-bottom: 0.5rem;
    }
    
    /* モバイルでの情報グリッドのギャップ調整 */
    .grant-info-grid {
        gap: 1rem;
        margin-bottom: 1rem;
    }
    
    .grant-info-label {
        font-size: 0.75rem;
        font-weight: 800;
    }
    
    .grant-info-value {
        font-size: 0.9rem;
        font-weight: 700;
    }
    
    .grant-hover-details {
        display: none !important;
    }
    
    .grant-view-list .grant-card-unified {
        flex-direction: column;
        border-width: 2px;
    }
    
    .grant-view-list .grant-status-header {
        width: 100%;
        height: 3rem;
        writing-mode: initial;
    }
    
    .grant-view-list .grant-card-footer {
        border-left: none;
        border-top: 2px solid var(--clean-gray-200);
        min-width: auto;
        flex-direction: column;
        gap: 0.75rem;
        padding: 1.25rem;
    }
    
    .grant-card-content {
        padding: 1.25rem;
    }
    
    .grant-title {
        font-size: 1.125rem;
        min-height: auto;
        line-height: 1.4;
    }
    
    .grant-btn {
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        width: 100%;
        justify-content: center;
        min-height: 48px;
    }
    
    .grant-actions {
        flex-direction: column;
        width: 100%;
        gap: 0.75rem;
    }
    
    /* モバイルでタップで詳細表示 */
    .grant-card-unified {
        cursor: pointer;
    }
    
    /* AI要約セクションの高さ調整 - モバイル最適化（全面表示版） */
    .grant-ai-summary {
        min-height: 160px; /* auto から 160px に変更 */
        max-height: 200px; /* 180px から 200px に拡大 */
        padding: 1.25rem;
        border-width: 3px;
        margin-bottom: 1.25rem;
        overflow-y: auto;
        flex: 1; /* 利用可能なスペースを最大限使用 */
    }
    
    /* モバイルではホバーではなくタップでスクロール可能に */
    .grant-ai-summary:active {
        max-height: 240px; /* 220px から 240px に拡大 */
        overflow-y: auto;
    }
    
    .grant-ai-summary-text {
        -webkit-line-clamp: 5; /* 4 から 5 に拡大 */
        font-size: 0.95rem; /* 0.9375rem から 0.95rem に拡大 */
        line-height: 1.6;
        font-weight: 500;
        max-height: 120px; /* テキスト領域も拡大 */
    }
    
    .grant-ai-summary-label {
        font-size: 0.8rem;
        margin-bottom: 0.75rem;
    }
    
    /* ステータスヘッダーをコンパクトに */
    .grant-status-header {
        height: 3rem;
        padding: 0 1.25rem;
    }
    
    .grant-status-badge,
    .grant-deadline-indicator {
        font-size: 0.8rem;
        padding: 0.5rem 0.75rem;
    }
    
    /* 情報グリッドのパディング調整 */
    .grant-info-item {
        padding: 0.875rem;
        border-width: 2px;
    }
    
    .grant-info-label {
        font-size: 0.7rem;
    }
    
    .grant-info-value {
        font-size: 0.9rem;
        font-weight: 800;
        line-height: 1.2;
    }
    
    /* モバイルでのトレーディングカード風エフェクト保持 */
    .grant-info-item:active {
        transform: translateY(-3px) scale(1.02);
        box-shadow: 
            0 8px 20px rgba(0, 0, 0, 0.15),
            0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    /* カテゴリータグ調整 */
    .grant-category-tag {
        padding: 0.5rem 0.875rem;
        font-size: 0.75rem;
        max-width: 100%;
        border-width: 2px;
    }
    
    /* カードの最小高さ調整 - コンパクト版 */
    .grant-view-card .grant-card-unified {
        min-height: 280px; /* auto から 280px に変更（デスクトップの320pxより小さく） */
        padding-bottom: 0;
    }
    
    /* モバイルでのカードアニメーション */
    .grant-card-unified:active {
        transform: translateY(-2px) scale(0.98);
        transition: transform 0.1s ease;
    }
    
    /* フッターボタンの改良 */
    .grant-card-footer {
        border-top-width: 2px;
        padding: 1.25rem;
    }
}

/* さらに小さい画面向け（480px以下） */
@media (max-width: 480px) {
    .grants-grid {
        padding: 0.75rem;
        gap: 0.75rem;
    }
    
    .grant-view-card .grant-card-unified {
        min-height: auto;
    }
    
    .grant-card-content {
        padding: 0.875rem;
    }
    
    .grant-title {
        font-size: 1rem;
    }
    
    .grant-btn {
        padding: 0.5rem 0.75rem;
        font-size: 0.8125rem;
        min-height: 38px;
    }
    
    .grant-card-footer {
        padding: 0.75rem;
    }
}

/* ダークモード対応 */
@media (prefers-color-scheme: dark) {
    :root {
        --clean-white: #1e293b;
        --clean-gray-50: #0f172a;
        --clean-gray-100: #334155;
        --clean-gray-200: #475569;
        --clean-gray-300: #64748b;
        --clean-gray-400: #94a3b8;
        --clean-gray-500: #cbd5e1;
        --clean-gray-600: #e2e8f0;
        --clean-gray-700: #f1f5f9;
        --clean-gray-800: #f8fafc;
        --clean-gray-900: #ffffff;
    }
}

/* 印刷対応 */
@media print {
    .grant-card-unified {
        break-inside: avoid;
        page-break-inside: avoid;
        background: white !important;
        color: black !important;
        box-shadow: none !important;
        border: 1px solid #000 !important;
    }
    
    .grant-hover-details,
    .grant-featured-badge {
        display: none !important;
    }
}

/* 高コントラストモード対応 */
@media (prefers-contrast: high) {
    .grant-card-unified {
        border-width: 2px;
        border-color: var(--clean-gray-800);
    }
    
    .grant-btn {
        border-width: 2px;
    }
    
    .grant-info-item {
        border-width: 2px;
    }
}

/* 減らされたモーション設定対応 */
@media (prefers-reduced-motion: reduce) {
    .grant-card-unified,
    .grant-btn,
    .grant-info-item {
        transition: none;
        animation: none;
    }
    
    .grant-card-unified:hover {
        transform: none;
    }
}

/* フォーカス管理 */
.grant-btn:focus,
.grant-hover-close:focus {
    outline: 2px solid var(--clean-gray-800);
    outline-offset: 2px;
}

/* セレクション色 */
::selection {
    background: rgba(0, 0, 0, 0.1);
    color: var(--clean-gray-900);
}

::-moz-selection {
    background: rgba(0, 0, 0, 0.1);
    color: var(--clean-gray-900);
}

/* スムーススクロール */
.grant-hover-scrollable {
    scroll-behavior: smooth;
}

/* ===== AI質問モーダル ===== */
.grant-ai-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: opacity 0.3s ease;
}

.grant-ai-modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(5px);
}

.grant-ai-modal-container {
    position: relative;
    width: 90%;
    max-width: 600px;
    height: 80vh;
    max-height: 600px;
    background: var(--clean-white);
    border-radius: var(--clean-radius-xl);
    box-shadow: var(--clean-shadow-xl);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transform: scale(1);
    transition: transform 0.3s ease;
}

.grant-ai-modal-header {
    padding: var(--clean-radius-lg);
    background: var(--clean-gradient-primary);
    color: var(--clean-white);
    position: relative;
}

.grant-ai-modal-title {
    display: flex;
    align-items: center;
    gap: var(--space-2);
    font-size: 1.125rem;
    font-weight: 700;
    margin-bottom: var(--space-1);
}

.grant-ai-modal-subtitle {
    font-size: 0.875rem;
    opacity: 0.9;
    font-weight: 400;
    max-width: 80%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.grant-ai-modal-close {
    position: absolute;
    top: var(--space-4);
    right: var(--space-4);
    width: 2rem;
    height: 2rem;
    border: none;
    background: rgba(255, 255, 255, 0.2);
    color: var(--clean-white);
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: var(--clean-transition);
}

.grant-ai-modal-close:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: rotate(90deg);
}

.grant-ai-modal-body {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.grant-ai-chat-messages {
    flex: 1;
    padding: var(--space-4);
    overflow-y: auto;
    background: var(--clean-gray-50);
    display: flex;
    flex-direction: column;
    gap: var(--space-3);
}

.grant-ai-chat-messages::-webkit-scrollbar {
    width: 6px;
}

.grant-ai-chat-messages::-webkit-scrollbar-track {
    background: var(--clean-gray-200);
    border-radius: 3px;
}

.grant-ai-chat-messages::-webkit-scrollbar-thumb {
    background: var(--clean-gray-400);
    border-radius: 3px;
}

.grant-ai-chat-messages::-webkit-scrollbar-thumb:hover {
    background: var(--clean-gray-500);
}

.grant-ai-message {
    display: flex;
    gap: var(--space-3);
    max-width: 85%;
    animation: fadeInUp 0.3s ease-out;
}

.grant-ai-message--assistant {
    align-self: flex-start;
}

.grant-ai-message--user {
    align-self: flex-end;
    flex-direction: row-reverse;
}

.grant-ai-message--error {
    align-self: flex-start;
}

.grant-ai-message-avatar {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 1rem;
}

.grant-ai-message--assistant .grant-ai-message-avatar {
    background: var(--clean-gradient-primary);
    color: var(--clean-white);
}

.grant-ai-message--user .grant-ai-message-avatar {
    background: var(--clean-gray-300);
    color: var(--clean-gray-700);
}

.grant-ai-message--error .grant-ai-message-avatar {
    background: var(--clean-danger);
    color: var(--clean-white);
}

.grant-ai-message-content {
    background: var(--clean-white);
    padding: var(--space-3) var(--space-4);
    border-radius: var(--clean-radius-lg);
    border: 1px solid var(--clean-gray-200);
    box-shadow: var(--clean-shadow-sm);
    font-size: 0.9375rem;
    line-height: 1.5;
    position: relative;
}

.grant-ai-message--user .grant-ai-message-content {
    background: var(--clean-gradient-primary);
    color: var(--clean-white);
    border-color: var(--clean-gray-800);
}

.grant-ai-message--error .grant-ai-message-content {
    background: #fee2e2;
    border-color: #fca5a5;
    color: #991b1b;
}

.grant-ai-typing {
    display: flex;
    gap: 4px;
    align-items: center;
    padding: var(--space-2) 0;
}

.grant-ai-typing span {
    width: 8px;
    height: 8px;
    background: var(--clean-gray-400);
    border-radius: 50%;
    animation: typing 1.4s infinite ease-in-out;
}

.grant-ai-typing span:nth-child(1) { animation-delay: 0.0s; }
.grant-ai-typing span:nth-child(2) { animation-delay: 0.2s; }
.grant-ai-typing span:nth-child(3) { animation-delay: 0.4s; }

@keyframes typing {
    0%, 80%, 100% { transform: scale(0.8); opacity: 0.5; }
    40% { transform: scale(1); opacity: 1; }
}

.grant-ai-chat-input-container {
    padding: var(--space-4);
    background: var(--clean-white);
    border-top: 1px solid var(--clean-gray-200);
}

.grant-ai-chat-input-wrapper {
    display: flex;
    gap: var(--space-2);
    align-items: flex-end;
    margin-bottom: var(--space-3);
}

.grant-ai-chat-input {
    flex: 1;
    padding: var(--space-3);
    border: 2px solid var(--clean-gray-300);
    border-radius: var(--clean-radius-lg);
    font-family: inherit;
    font-size: 0.9375rem;
    line-height: 1.5;
    resize: none;
    transition: var(--clean-transition);
    background: var(--clean-white);
    min-height: 2.75rem;
    max-height: 6rem;
}

.grant-ai-chat-input:focus {
    outline: none;
    border-color: var(--clean-primary);
    box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.1);
}

.grant-ai-chat-send {
    width: 2.75rem;
    height: 2.75rem;
    background: var(--clean-gradient-primary);
    color: var(--clean-white);
    border: none;
    border-radius: var(--clean-radius-lg);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: var(--clean-transition);
    flex-shrink: 0;
}

.grant-ai-chat-send:hover:not(:disabled) {
    transform: scale(1.05);
    box-shadow: var(--clean-shadow-md);
}

.grant-ai-chat-send:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.grant-ai-chat-suggestions {
    display: flex;
    flex-wrap: wrap;
    gap: var(--space-2);
}

.grant-ai-suggestion {
    padding: var(--space-2) var(--space-3);
    background: var(--clean-gray-100);
    border: 1px solid var(--clean-gray-300);
    border-radius: var(--clean-radius-2xl);
    font-size: 0.8125rem;
    color: var(--clean-gray-700);
    cursor: pointer;
    transition: var(--clean-transition);
    white-space: nowrap;
}

.grant-ai-suggestion:hover {
    background: var(--clean-primary);
    color: var(--clean-white);
    border-color: var(--clean-primary);
    transform: translateY(-1px);
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(1rem);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 768px) {
    .grant-ai-modal-container {
        width: 95%;
        height: 90vh;
        margin: var(--space-4);
    }
    
    .grant-ai-modal-header {
        padding: var(--space-3);
    }
    
    .grant-ai-modal-title {
        font-size: 1rem;
    }
    
    .grant-ai-modal-subtitle {
        font-size: 0.8125rem;
    }
    
    .grant-ai-chat-messages {
        padding: var(--space-3);
    }
    
    .grant-ai-message {
        max-width: 95%;
    }
    
    .grant-ai-chat-suggestions {
        flex-direction: column;
    }
    
    .grant-ai-suggestion {
        white-space: normal;
        text-align: left;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // カードクリック処理（詳細ボタンのみでページ遷移）
    document.addEventListener('click', function(e) {
        // 詳細ボタンがクリックされた場合のみページ遷移
        if (e.target.closest('.grant-btn--primary')) {
            const btn = e.target.closest('.grant-btn--primary');
            const href = btn.getAttribute('href');
            if (href) {
                // クリックエフェクト
                btn.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    window.location.href = href;
                }, 100);
            }
        }
    });
    
    // ホバー詳細の表示・非表示制御（デスクトップのみ）
    function isDesktop() {
        return window.innerWidth > 768 && !('ontouchstart' in window);
    }
    
    // ホバーイベント（デスクトップのみ）
    document.querySelectorAll('.grant-card-unified').forEach(card => {
        let hoverTimeout;
        
        card.addEventListener('mouseenter', function() {
            if (!isDesktop()) return;
            
            clearTimeout(hoverTimeout);
            hoverTimeout = setTimeout(() => {
                const details = this.querySelector('.grant-hover-details');
                if (details) {
                    details.classList.add('show-details');
                    details.style.opacity = '1';
                    details.style.visibility = 'visible';
                }
            }, 200);
        });
        
        card.addEventListener('mouseleave', function() {
            clearTimeout(hoverTimeout);
            const details = this.querySelector('.grant-hover-details');
            if (details) {
                details.classList.remove('show-details');
                details.style.opacity = '0';
                details.style.visibility = 'hidden';
            }
        });
    });
    
    // モバイルでのタップ詳細表示
    let tapCount = 0;
    let tapTimeout;
    
    document.addEventListener('touchend', function(e) {
        if (!e.target.closest('.grant-card-unified')) return;
        if (e.target.closest('.grant-btn')) return;
        
        tapCount++;
        
        if (tapCount === 1) {
            tapTimeout = setTimeout(() => {
                tapCount = 0;
            }, 300);
        } else if (tapCount === 2) {
            clearTimeout(tapTimeout);
            tapCount = 0;
            
            // ダブルタップで詳細表示
            const card = e.target.closest('.grant-card-unified');
            const details = card.querySelector('.grant-hover-details');
            if (details) {
                if (details.style.opacity === '1') {
                    details.style.opacity = '0';
                    details.style.visibility = 'hidden';
                    details.classList.remove('show-details');
                } else {
                    details.classList.add('show-details');
                    details.style.opacity = '1';
                    details.style.visibility = 'visible';
                }
            }
        }
    });
    
    // ホバー詳細の閉じるボタン
    document.addEventListener('click', function(e) {
        if (e.target.closest('.grant-hover-close')) {
            e.preventDefault();
            e.stopPropagation();
            const details = e.target.closest('.grant-hover-details');
            if (details) {
                details.style.opacity = '0';
                details.style.visibility = 'hidden';
                details.classList.remove('show-details');
            }
        }
    });
    
    // ESCキーで詳細を閉じる
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.grant-hover-details.show-details').forEach(details => {
                details.style.opacity = '0';
                details.style.visibility = 'hidden';
                details.classList.remove('show-details');
            });
        }
    });
    
    // 詳細表示外をクリックで閉じる
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('grant-hover-details')) {
            e.target.style.opacity = '0';
            e.target.style.visibility = 'hidden';
            e.target.classList.remove('show-details');
        }
    });
    
    // 採択率バーのアニメーション
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const bar = entry.target.querySelector('.grant-success-fill');
                if (bar && !bar.dataset.animated) {
                    const rate = parseFloat(bar.dataset.rate);
                    bar.dataset.animated = 'true';
                    
                    // アニメーション開始
                    let currentRate = 0;
                    const increment = rate / 40;
                    const timer = setInterval(() => {
                        currentRate += increment;
                        if (currentRate >= rate) {
                            currentRate = rate;
                            clearInterval(timer);
                        }
                        bar.style.width = currentRate + '%';
                    }, 25);
                }
            }
        });
    }, observerOptions);
    
    document.querySelectorAll('.grant-success-rate').forEach(el => {
        observer.observe(el);
    });
    
    // ボタンのフォーカス管理
    document.querySelectorAll('.grant-btn, .grant-hover-close').forEach(btn => {
        btn.addEventListener('focus', function() {
            this.style.outline = '2px solid var(--clean-gray-800)';
            this.style.outlineOffset = '2px';
        });
        
        btn.addEventListener('blur', function() {
            this.style.outline = '';
            this.style.outlineOffset = '';
        });
        
        // キーボードでのアクティベート
        btn.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            }
        });
    });
    
    // ウィンドウリサイズ対応
    window.addEventListener('resize', function() {
        // モバイル・デスクトップ切り替え時に詳細表示をリセット
        document.querySelectorAll('.grant-hover-details').forEach(details => {
            if (!isDesktop()) {
                details.style.opacity = '0';
                details.style.visibility = 'hidden';
                details.classList.remove('show-details');
            }
        });
    });
    
    // AI質問モーダル関数をグローバルに追加
    window.openGrantAIChat = function(button) {
        const postId = button.getAttribute('data-post-id');
        const grantTitle = button.getAttribute('data-grant-title');
        
        if (!postId) {
            console.error('Post ID not found');
            return;
        }
        
        // モーダルを作成または表示
        showAIChatModal(postId, grantTitle);
    };
    
    // AI質問モーダルの表示
    function showAIChatModal(postId, grantTitle) {
        // 既存のモーダルを削除
        const existingModal = document.querySelector('.grant-ai-modal');
        if (existingModal) {
            existingModal.remove();
        }
        
        // モーダルHTML作成
        const modalHTML = `
            <div class="grant-ai-modal" id="grant-ai-modal">
                <div class="grant-ai-modal-overlay" onclick="closeAIChatModal()"></div>
                <div class="grant-ai-modal-container">
                    <div class="grant-ai-modal-header">
                        <div class="grant-ai-modal-title">
                            <span>この助成金について質問する</span>
                        </div>
                        <div class="grant-ai-modal-subtitle">${grantTitle}</div>
                        <button class="grant-ai-modal-close" onclick="closeAIChatModal()">
                            閉じる
                        </button>
                    </div>
                    <div class="grant-ai-modal-body">
                        <div class="grant-ai-chat-messages" id="ai-chat-messages-${postId}">
                            <div class="grant-ai-message grant-ai-message--assistant">
                                <div class="grant-ai-message-content">
                                    この助成金について何でもお聞きください。申請条件、必要書類、申請方法などについてお答えします。
                                </div>
                            </div>
                        </div>
                        <div class="grant-ai-chat-input-container">
                            <div class="grant-ai-chat-input-wrapper">
                                <textarea 
                                    class="grant-ai-chat-input" 
                                    id="ai-chat-input-${postId}"
                                    placeholder="例：申請条件は何ですか？必要書類を教えてください"
                                    rows="3"></textarea>
                                <button 
                                    class="grant-ai-chat-send" 
                                    id="ai-chat-send-${postId}"
                                    onclick="sendAIQuestion('${postId}')">
                                    送信
                                </button>
                            </div>
                            <div class="grant-ai-chat-suggestions">
                                <!-- Application form suggestion buttons removed per user request -->
                                <button class="grant-ai-suggestion" onclick="selectSuggestion('${postId}', 'どんな費用が対象になりますか？')">
                                    対象経費について
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // モーダルをDOMに追加
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        
        // フォーカス設定
        setTimeout(() => {
            const input = document.getElementById(`ai-chat-input-${postId}`);
            if (input) {
                input.focus();
            }
        }, 100);
        
        // Enterキーでの送信
        const input = document.getElementById(`ai-chat-input-${postId}`);
        if (input) {
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendAIQuestion(postId);
                }
            });
        }
    }
    
    // AI質問モーダルを閉じる
    window.closeAIChatModal = function() {
        const modal = document.querySelector('.grant-ai-modal');
        if (modal) {
            modal.style.opacity = '0';
            setTimeout(() => {
                modal.remove();
            }, 300);
        }
    };
    
    // 質問候補の選択
    window.selectSuggestion = function(postId, question) {
        const input = document.getElementById(`ai-chat-input-${postId}`);
        if (input) {
            input.value = question;
            input.focus();
        }
    };
    
    // AI質問送信
    window.sendAIQuestion = function(postId) {
        const input = document.getElementById(`ai-chat-input-${postId}`);
        const sendBtn = document.getElementById(`ai-chat-send-${postId}`);
        const messagesContainer = document.getElementById(`ai-chat-messages-${postId}`);
        
        if (!input || !messagesContainer) {
            console.error('Required elements not found');
            return;
        }
        
        const question = input.value.trim();
        if (!question) {
            return;
        }
        
        // 送信ボタンを無効化
        if (sendBtn) {
            sendBtn.disabled = true;
            sendBtn.innerHTML = '送信中...';
        }
        
        // ユーザーメッセージを追加
        const userMessage = document.createElement('div');
        userMessage.className = 'grant-ai-message grant-ai-message--user';
        userMessage.innerHTML = `
            <div class="grant-ai-message-content">${escapeHtml(question)}</div>
        `;
        messagesContainer.appendChild(userMessage);
        
        // 入力をクリア
        input.value = '';
        
        // スクロールダウン
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
        
        // AJAX リクエスト
        const formData = new FormData();
        formData.append('action', 'handle_grant_ai_question');
        formData.append('post_id', postId);
        formData.append('question', question);
        formData.append('nonce', '<?php echo wp_create_nonce('gi_ajax_nonce'); ?>');
        
        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // ローディング表示を追加
            const loadingMessage = document.createElement('div');
            loadingMessage.className = 'grant-ai-message grant-ai-message--assistant grant-ai-loading';
            loadingMessage.innerHTML = `
                <div class="grant-ai-message-content">
                    <div class="grant-ai-typing">
                        <span></span><span></span><span></span>
                    </div>
                </div>
            `;
            messagesContainer.appendChild(loadingMessage);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
            
            // 2秒後にレスポンスを表示
            setTimeout(() => {
                loadingMessage.remove();
                
                if (data.success) {
                    const assistantMessage = document.createElement('div');
                    assistantMessage.className = 'grant-ai-message grant-ai-message--assistant';
                    assistantMessage.innerHTML = `
                        <div class="grant-ai-message-content">${escapeHtml(data.data.response)}</div>
                    `;
                    messagesContainer.appendChild(assistantMessage);
                } else {
                    const errorMessage = document.createElement('div');
                    errorMessage.className = 'grant-ai-message grant-ai-message--error';
                    errorMessage.innerHTML = `
                        <div class="grant-ai-message-content">エラー: 申し訳ございません。エラーが発生しました。しばらく時間をおいて再度お試しください。</div>
                    `;
                    messagesContainer.appendChild(errorMessage);
                }
                
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }, 2000);
        })
        .catch(error => {
            console.error('Error sending AI question:', error);
            
            // エラーメッセージを表示
            const errorMessage = document.createElement('div');
            errorMessage.className = 'grant-ai-message grant-ai-message--error';
            errorMessage.innerHTML = `
                <div class="grant-ai-message-content">エラー: 通信エラーが発生しました。インターネット接続を確認して再度お試しください。</div>
            `;
            messagesContainer.appendChild(errorMessage);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        })
        .finally(() => {
            // 送信ボタンを復活
            if (sendBtn) {
                sendBtn.disabled = false;
                sendBtn.innerHTML = '送信';
            }
            input.focus();
        });
    };
    
    // HTMLエスケープ関数
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});

// パーティクルアニメーション用CSS追加
const grantCardStyles = document.createElement('style');
grantCardStyles.textContent = `
    @keyframes particle-float {
        0% {
            opacity: 1;
            transform: translateY(0) translateX(0) scale(1);
        }
        100% {
            opacity: 0;
            transform: translateY(-60px) translateX(${Math.random() * 60 - 30}px) scale(0.3);
        }
    }
    
    /* ドラッグ無効化 */
    .grant-card-unified * {
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        -webkit-user-drag: none;
        -khtml-user-drag: none;
        -moz-user-drag: none;
        -o-user-drag: none;
        user-drag: none;
    }
    
    /* テキストのみ選択可能 */
    .grant-title a,
    .grant-ai-summary-text,
    .grant-detail-value {
        -webkit-user-select: text;
        -moz-user-select: text;
        -ms-user-select: text;
        user-select: text;
    }
`;
document.head.appendChild(grantCardStyles);
</script>
<?php endif; ?>

<!-- クリーンカード本体 -->
<article class="grant-card-unified <?php echo esc_attr($view_class); ?>" 
         data-post-id="<?php echo esc_attr($post_id); ?>"
         data-grant-id="<?php echo esc_attr($post_id); ?>"
         data-priority="<?php echo esc_attr($priority_order); ?>"
         role="article"
         aria-label="助成金情報カード">
    
    <!-- ステータスヘッダー -->
    <header class="grant-status-header <?php echo $application_status === 'closed' ? 'status--closed' : ''; ?> <?php echo !empty($deadline_info) && $deadline_info['class'] === 'critical' ? 'status--urgent' : ''; ?>">
        <div class="grant-status-badge">
            <span><?php echo esc_html($status_display); ?></span>
        </div>
        <?php if (!empty($deadline_info)): ?>
        <div class="grant-deadline-indicator">
            <span><?php echo esc_html($deadline_info['text']); ?></span>
        </div>
        <?php endif; ?>
    </header>
    
    <!-- ステータスインジケーター -->
    <div class="grant-status-indicator <?php echo $application_status === 'closed' ? 'closed' : ''; ?>" 
         aria-label="<?php echo $application_status === 'closed' ? '募集終了' : '募集中'; ?>"></div>
    
    <!-- 注目バッジ -->
    <?php if ($is_featured): ?>
    <div class="grant-featured-badge" aria-label="注目の助成金">FEATURED</div>
    <?php endif; ?>
    
    <!-- 難易度バッジ -->
    <?php if ($grant_difficulty && $grant_difficulty !== 'normal'): ?>
    <div class="grant-difficulty-badge" style="color: <?php echo esc_attr($difficulty_data['color']); ?>">
        <span><?php echo esc_html($difficulty_data['label']); ?></span>
    </div>
    <?php endif; ?>

    
    <!-- カードコンテンツ -->
    <div class="grant-card-content">
        <div class="grant-main-info">

            
            <!-- タイトルセクション -->
            <div class="grant-title-section">
                <?php if ($main_category): ?>
                <div class="grant-category-tag">
                    <span><?php echo esc_html($main_category); ?></span>
                </div>
                <?php endif; ?>
                <h3 class="grant-title">
                    <a href="<?php echo esc_url($permalink); ?>" aria-label="<?php echo esc_attr($title); ?>の詳細ページ" tabindex="-1">
                        <?php echo esc_html($title); ?>
                    </a>
                </h3>
            </div>
            
            <!-- AI要約 -->
            <?php if ($ai_summary || $excerpt): ?>
            <div class="grant-ai-summary">
                <div class="grant-ai-summary-label">
                    <span>AI要約</span>
                </div>
                <p class="grant-ai-summary-text">
                    <?php echo esc_html(wp_trim_words($ai_summary ?: $excerpt, 40, '...')); ?>
                </p>
            </div>
            <?php endif; ?>
            
            <!-- 情報グリッドをホバー時のみ表示に変更 -->
            
            <!-- タグ -->
            <?php if ($main_industry || $application_period): ?>
            <div class="grant-tags">
                <?php if ($main_industry): ?>
                <span class="grant-tag">
                    <?php echo esc_html($main_industry); ?>
                </span>
                <?php endif; ?>
                <?php if ($application_period): ?>
                <span class="grant-tag">
                    <?php echo esc_html($application_period); ?>
                </span>
                <?php endif; ?>
            </div>
            <?php endif; ?>

        </div>
    </div>
    
    <!-- アクションフッター -->
    <footer class="grant-card-footer">
        <div class="grant-actions">
            <a href="<?php echo esc_url($permalink); ?>" class="grant-btn grant-btn--primary" role="button">
                <span>詳細を見る</span>
            </a>
            <button class="grant-btn grant-btn--ai" 
                    data-post-id="<?php echo esc_attr($post_id); ?>" 
                    data-grant-title="<?php echo esc_attr($title); ?>"
                    onclick="openGrantAIChat(this)" 
                    role="button">
                <span>質問する</span>
            </button>
            <?php if ($official_url): ?>
            <a href="<?php echo esc_url($official_url); ?>" class="grant-btn grant-btn--secondary grant-btn--icon" target="_blank" rel="noopener noreferrer" role="button" title="公式サイト">
                <svg class="grant-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="m9 18 6-6-6-6"/>
                    <path d="M14 5h7v7"/>
                </svg>
            </a>
            <?php endif; ?>
            
            <!-- AI機能ボタン群 -->
            <!-- Application checklist button removed per user request -->
            
            <!-- Compare button removed per user request -->
        </div>
    </footer>
    
    <!-- ホバー時の詳細表示 -->
    <div class="grant-hover-details" aria-hidden="true">
        <div class="grant-hover-scrollable">
            <div class="grant-hover-header">
                <h3 class="grant-hover-title"><?php echo esc_html($title); ?></h3>
                <button class="grant-hover-close grant-btn--icon" aria-label="詳細を閉じる">
                    <svg class="grant-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
            
            <!-- AI要約 - 詳細版 -->
            <?php if ($ai_summary || $excerpt): ?>
            <div class="grant-detail-section">
                <div class="grant-detail-label">
                    <span>🧠 AI要約（詳細）</span>
                </div>
                <div class="grant-detail-value">
                    <?php echo esc_html($ai_summary ?: $excerpt); ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- クイック統計 - 助成額と地域を表示 -->
            <div class="grant-quick-stats">
                <?php if ($amount_display): ?>
                <div class="grant-stat-item">
                    <span class="grant-stat-value"><?php echo esc_html($amount_display); ?></span>
                    <span class="grant-stat-label">💰 最大助成額</span>
                </div>
                <?php endif; ?>
                <div class="grant-stat-item">
                    <span class="grant-stat-value"><?php echo esc_html($prefecture); ?></span>
                    <span class="grant-stat-label">📍 対象地域</span>
                </div>

            </div>
            

            
            <div class="grant-detail-sections">
                <?php if ($ai_summary): ?>
                <div class="grant-detail-section">
                    <div class="grant-detail-label">
                        <span>AI要約（完全版）</span>
                    </div>
                    <div class="grant-detail-value">
                        <?php echo esc_html($ai_summary); ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Application period section removed per user request -->
                
                <?php if ($eligible_expenses): ?>
                <div class="grant-detail-section">
                    <div class="grant-detail-label">
                        <span>対象経費</span>
                    </div>
                    <div class="grant-detail-value">
                        <?php echo esc_html($eligible_expenses); ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if ($required_documents): ?>
                <div class="grant-detail-section">
                    <div class="grant-detail-label">
                        <span>必要書類</span>
                    </div>
                    <div class="grant-detail-value">
                        <?php echo esc_html($required_documents); ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Application method section removed per user request -->
                
                <?php if ($contact_info): ?>
                <div class="grant-detail-section">
                    <div class="grant-detail-label">
                        <span>お問い合わせ</span>
                    </div>
                    <div class="grant-detail-value">
                        <?php echo esc_html($contact_info); ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</article>

<?php
// JavaScriptを一度だけ出力
static $ai_features_js_loaded = false;
if (!$ai_features_js_loaded):
    $ai_features_js_loaded = true;
?>
<script>
// ============================================================================
// AI機能JavaScript（モノクローム対応）
// ============================================================================

// AJAX URL設定
const ajaxUrl = '<?php echo admin_url('admin-ajax.php'); ?>';

// Compare functionality removed per user request

// openGrantChecklist (AI application checklist) function removed per user request

// addToCompare function removed per user request

// updateCompareButton function removed per user request

// showCompareModal function removed per user request

/**
 * トースト通知
 */
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `ai-toast ai-toast-${type}`;
    toast.innerHTML = `
        <span>${message}</span>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => toast.classList.add('show'), 10);
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
</script>

<!-- AI機能CSS -->
<style>
/* AI Modal Base */
.ai-checklist-modal {
    position: fixed;
    inset: 0;
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.ai-modal-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(4px);
}

.ai-modal-content {
    position: relative;
    background: #fff;
    border-radius: 1rem;
    max-width: 500px;
    width: 90%;
    max-height: 80vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    animation: modalFadeIn 0.3s ease;
}

.ai-modal-large {
    max-width: 800px;
}

@keyframes modalFadeIn {
    from { opacity: 0; transform: scale(0.9); }
    to { opacity: 1; transform: scale(1); }
}

.ai-modal-header {
    padding: 1.5rem;
    border-bottom: 2px solid #000;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: var(--clean-white);
    border-radius: 1rem 1rem 0 0;
}

.ai-modal-header h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--clean-gray-900);
}

.ai-modal-close {
    background: #fff;
    border: 2px solid #000;
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s;
    color: var(--clean-gray-900);
}

.ai-modal-close:hover {
    background: #000;
    color: #fff;
    transform: scale(1.05);
}

.ai-modal-body {
    padding: 1.5rem;
    overflow-y: auto;
    flex: 1;
    background: var(--clean-white);
}

/* AI Checklist Styles */
.ai-grant-title {
    font-weight: 600;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid var(--clean-gray-200);
    color: var(--clean-gray-900);
}

.ai-checklist-loading {
    text-align: center;
    padding: 2rem;
    font-size: 1.125rem;
    color: var(--clean-gray-600);
}

.ai-checklist-loading::after {
    content: '';
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid var(--clean-gray-300);
    border-radius: 50%;
    border-top-color: var(--clean-gray-900);
    animation: spin 1s ease-in-out infinite;
    margin-left: 0.5rem;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.ai-checklist-item {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 1rem;
    border: 2px solid var(--clean-gray-200);
    border-radius: 0.75rem;
    margin-bottom: 0.75rem;
    cursor: pointer;
    transition: all 0.3s;
    background: var(--clean-white);
}

.ai-checklist-item:hover {
    border-color: var(--clean-gray-400);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.ai-checklist-item[data-priority="high"] {
    border-left: 4px solid #dc2626;
}

.ai-checklist-item[data-priority="medium"] {
    border-left: 4px solid #f59e0b;
}

.ai-checklist-item input[type="checkbox"] {
    width: 1.25rem;
    height: 1.25rem;
    margin: 0;
    cursor: pointer;
    accent-color: var(--clean-gray-900);
}

.ai-check-mark {
    display: none;
}

.ai-check-text {
    flex: 1;
    font-size: 0.9375rem;
    line-height: 1.5;
    color: var(--clean-gray-800);
}

.ai-check-priority {
    padding: 0.25rem 0.5rem;
    background: #dc2626;
    color: white;
    font-size: 0.75rem;
    font-weight: 600;
    border-radius: 0.375rem;
}

/* AI Compare Styles removed per user request */

/* Toast Notifications */
.ai-toast {
    position: fixed;
    bottom: 2rem;
    left: 50%;
    transform: translateX(-50%) translateY(100%);
    background: var(--clean-gray-900);
    color: white;
    padding: 1rem 1.5rem;
    border-radius: 0.75rem;
    font-weight: 600;
    font-size: 0.9375rem;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
    z-index: 10001;
    transition: transform 0.3s ease;
    max-width: 90vw;
    text-align: center;
}

.ai-toast.show {
    transform: translateX(-50%) translateY(0);
}

.ai-toast-success {
    background: #16a34a;
}

.ai-toast-warning {
    background: #f59e0b;
}

.ai-toast-error {
    background: #dc2626;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .ai-modal-content {
        width: 95%;
        max-height: 90vh;
        margin: 1rem;
    }
    
    .ai-modal-header {
        padding: 1rem;
    }
    
    .ai-modal-header h3 {
        font-size: 1.125rem;
    }
    
    .ai-modal-body {
        padding: 1rem;
    }
    
    .ai-checklist-item {
        padding: 0.75rem;
    }
    
    /* Comparison responsive styles removed per user request */
    
    .ai-toast {
        bottom: 1rem;
        font-size: 0.875rem;
        padding: 0.875rem 1.25rem;
    }
}

/* Duplicate content removed - already added above */
    border-color: #000;
    background: #fafafa;
}

.ai-checklist-item input[type="checkbox"] {
    display: none;
}

.ai-check-mark {
    width: 1.5rem;
    height: 1.5rem;
    border: 2px solid #000;
    border-radius: 0.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.ai-checklist-item input:checked + .ai-check-mark {
    background: #000;
}

.ai-checklist-item input:checked + .ai-check-mark::after {
    content: '✓';
    color: #fff;
    font-weight: bold;
}

.ai-check-text {
    flex: 1;
}

.ai-check-priority {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    background: #fbbf24;
    color: #000;
    border-radius: 0.25rem;
    font-weight: 600;
}

/* Compare Table styles removed per user request */

/* ai-recommend-box removed per user request */

/* Comparison styles removed per user request */

/* Toast */
.ai-toast {
    position: fixed;
    bottom: 2rem;
    left: 50%;
    transform: translateX(-50%) translateY(100px);
    background: #000;
    color: #fff;
    padding: 1rem 1.5rem;
    border-radius: 2rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
    z-index: 10001;
    opacity: 0;
    transition: all 0.3s;
}

.ai-toast.show {
    opacity: 1;
    transform: translateX(-50%) translateY(0);
}

.ai-toast-warning {
    background: #fbbf24;
    color: #000;
}

/* Icon Button Styles */
.grant-btn--icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 2.5rem;
    min-height: 2.5rem;
    padding: 0.5rem;
    border-radius: 0.375rem;
    background: transparent;
    border: 2px solid #000;
    color: #000;
    transition: all 0.2s;
    cursor: pointer;
}

.grant-btn--icon:hover {
    background: #000;
    color: #fff;
    transform: translateY(-2px);
}

.grant-btn--icon .grant-icon {
    width: 1.25rem;
    height: 1.25rem;
    stroke-width: 2.5;
}

/* Icon button specific colors */
.grant-btn--secondary.grant-btn--icon {
    border-color: #666;
    color: #666;
}

.grant-btn--secondary.grant-btn--icon:hover {
    background: #666;
    color: #fff;
}

/* Checklist icon button styles removed per user request */

/* Compare button styles removed per user request */

/* Close button styles */
.grant-hover-close.grant-btn--icon,
.ai-modal-close.grant-btn--icon {
    border: 1px solid #ccc;
    color: #666;
    min-width: 2rem;
    min-height: 2rem;
    padding: 0.25rem;
}

.grant-hover-close.grant-btn--icon:hover,
.ai-modal-close.grant-btn--icon:hover {
    background: #f5f5f5;
    color: #000;
    border-color: #999;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .grant-btn--icon {
        min-width: 2.25rem;
        min-height: 2.25rem;
        padding: 0.375rem;
    }
    
    .grant-btn--icon .grant-icon {
        width: 1.125rem;
        height: 1.125rem;
    }
}

/* ===============================================
   みんなの銀行 AIモーダル・トーストシステム
   =============================================== */

/* AIモーダル */
.ai-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 10000;
    opacity: 0;
    visibility: hidden;
    transition: var(--mb-transition-smooth);
}

.ai-modal.show {
    opacity: 1;
    visibility: visible;
}

.ai-modal-backdrop {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(5px);
}

.ai-modal-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.9);
    background: var(--mb-white);
    border-radius: var(--mb-radius-xl);
    box-shadow: var(--mb-shadow-overlay);
    max-width: 600px;
    width: 90vw;
    max-height: 80vh;
    overflow: hidden;
    border: 2px solid var(--mb-gray-200);
    transition: var(--mb-transition-smooth);
}

.ai-modal.show .ai-modal-content {
    transform: translate(-50%, -50%) scale(1);
}

.ai-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--mb-space-lg);
    border-bottom: 2px solid var(--mb-gray-200);
    background: var(--mb-gradient-subtle);
}

.ai-modal-header h3 {
    margin: 0;
    font-family: var(--mb-font-primary);
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--mb-black);
}

.ai-modal-close {
    background: var(--mb-gray-100);
    border: 1px solid var(--mb-gray-300);
    border-radius: 50%;
    width: 2rem;
    height: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--mb-transition);
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--mb-gray-600);
}

.ai-modal-close:hover {
    background: var(--mb-black);
    color: var(--mb-white);
    border-color: var(--mb-black);
    transform: rotate(90deg);
}

.ai-modal-body {
    padding: var(--mb-space-lg);
    max-height: 60vh;
    overflow-y: auto;
}

/* AI ローディング */
.ai-loading {
    text-align: center;
    padding: var(--mb-space-xl);
}

.ai-loading-spinner {
    width: 2rem;
    height: 2rem;
    border: 2px solid var(--mb-gray-200);
    border-top: 2px solid var(--mb-black);
    border-radius: 50%;
    animation: mb-spin 1s linear infinite;
    margin: 0 auto var(--mb-space-md);
}

@keyframes mb-spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* AI分析結果 */
.ai-analysis-result {
    display: flex;
    flex-direction: column;
    gap: var(--mb-space-lg);
}

.ai-summary-section,
.ai-checklist-section,
.ai-tips-section {
    padding: var(--mb-space-lg);
    background: var(--mb-gradient-subtle);
    border-radius: var(--mb-radius-lg);
    border: 1px solid var(--mb-gray-200);
}

.ai-summary-section h4,
.ai-checklist-section h4,
.ai-tips-section h4 {
    margin: 0 0 var(--mb-space-md) 0;
    font-family: var(--mb-font-primary);
    font-size: 1rem;
    font-weight: 700;
    color: var(--mb-black);
}

.ai-match-score {
    font-size: 2rem;
    font-weight: 900;
    font-family: var(--mb-font-mono);
    color: var(--mb-black);
    text-align: center;
    margin: var(--mb-space-md) 0;
    padding: var(--mb-space-md);
    background: var(--mb-white);
    border-radius: var(--mb-radius-md);
    border: 2px solid var(--mb-gray-300);
}

/* AIチェックリスト */
.ai-checklist {
    display: flex;
    flex-direction: column;
    gap: var(--mb-space-sm);
}

.ai-checklist-item {
    display: flex;
    align-items: flex-start;
    gap: var(--mb-space-sm);
    padding: var(--mb-space-md);
    background: var(--mb-white);
    border-radius: var(--mb-radius-md);
    border: 1px solid var(--mb-gray-200);
    cursor: pointer;
    transition: var(--mb-transition);
}

.ai-checklist-item:hover {
    background: var(--mb-gray-50);
    border-color: var(--mb-gray-300);
}

.ai-check-mark {
    width: 1.25rem;
    height: 1.25rem;
    border: 2px solid var(--mb-gray-400);
    border-radius: var(--mb-radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: var(--mb-transition);
    font-size: 0.75rem;
    font-weight: 700;
}

.ai-checklist-item input:checked + .ai-check-mark {
    background: var(--mb-black);
    border-color: var(--mb-black);
    color: var(--mb-white);
}

.ai-checklist-item input:checked + .ai-check-mark::after {
    content: '✓';
}

/* AIコツ */
.ai-tips-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.ai-tips-list li {
    padding: var(--mb-space-sm);
    margin-bottom: var(--mb-space-sm);
    background: var(--mb-white);
    border-radius: var(--mb-radius-md);
    border: 1px solid var(--mb-gray-200);
    position: relative;
    padding-left: 2.5rem;
}

.ai-tips-list li::before {
    content: '💡';
    position: absolute;
    left: var(--mb-space-sm);
    top: var(--mb-space-sm);
}

/* AIエラー */
.ai-error {
    text-align: center;
    padding: var(--mb-space-xl);
}

.ai-error-icon {
    font-size: 3rem;
    margin-bottom: var(--mb-space-md);
}

/* みんなの銀行トースト */
.minna-toast {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    background: var(--mb-gradient-primary);
    color: var(--mb-white);
    padding: var(--mb-space-md) var(--mb-space-lg);
    border-radius: var(--mb-radius-full);
    box-shadow: var(--mb-shadow-lg);
    z-index: 10001;
    opacity: 0;
    transform: translateY(100%);
    transition: var(--mb-transition-smooth);
    font-family: var(--mb-font-primary);
    font-weight: 600;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.minna-toast.show {
    opacity: 1;
    transform: translateY(0);
}

.minna-toast--success {
    background: linear-gradient(135deg, var(--mb-success), #16a34a);
}

.minna-toast--warning {
    background: linear-gradient(135deg, var(--mb-warning), #f59e0b);
    color: var(--mb-black);
}

.minna-toast--error {
    background: linear-gradient(135deg, var(--mb-danger), #dc2626);
}

.minna-toast-content {
    display: flex;
    align-items: center;
    gap: var(--mb-space-sm);
}

.minna-toast-icon {
    font-size: 1.125rem;
}

.minna-toast-message {
    font-size: 0.875rem;
}

/* AIモーダル・トーストのレスポンシブ調整 */
@media (max-width: 768px) {
    .ai-modal-content {
        width: 95vw;
        max-height: 90vh;
    }
    
    .ai-modal-header,
    .ai-modal-body {
        padding: var(--mb-space-md);
    }
    
    .ai-summary-section,
    .ai-checklist-section,
    .ai-tips-section {
        padding: var(--mb-space-md);
    }
    
    .minna-toast {
        bottom: 1rem;
        right: 1rem;
        left: 1rem;
    }
}

</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // ==============================================
    // みんなの銀行 カードインタラクション機能
    // ==============================================
    
    // 詳細ボタンのクリック処理
    document.querySelectorAll('.grant-details-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const url = this.getAttribute('href');
            if (url && url !== '#') {
                // みんなの銀行スタイルのロード表示
                showMinnaToast('詳細を読み込み中...', 'info');
                
                // 新しいタブで開く
                window.open(url, '_blank', 'noopener,noreferrer');
            }
        });
    });
    
    // AI機能ボタンのクリック処理
    document.querySelectorAll('.grant-ai-btn, .ai-analysis-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const grantId = this.closest('.grant-card-unified').dataset.grantId || 
                           this.dataset.grantId;
            
            if (grantId) {
                showAIAnalysisModal(grantId);
            } else {
                showMinnaToast('AI分析データが見つかりません', 'warning');
            }
        });
    });
    
    // カードクリック処理（詳細ページへのナビゲーション）
    document.querySelectorAll('.grant-card-unified').forEach(card => {
        card.addEventListener('click', function(e) {
            // ボタンクリック時は無視
            if (e.target.closest('.grant-btn, button, a')) {
                return;
            }
            
            const detailsLink = card.querySelector('.grant-details-btn');
            if (detailsLink && detailsLink.href) {
                window.location.href = detailsLink.href;
            }
        });
    });
    
    // ==============================================
    // AI分析モーダル表示機能
    // ==============================================
    
    function showAIAnalysisModal(grantId) {
        // 既存のモーダルを閉じる
        const existingModal = document.querySelector('.ai-modal');
        if (existingModal) {
            existingModal.remove();
        }
        
        // みんなの銀行スタイルのモーダルを作成
        const modal = document.createElement('div');
        modal.className = 'ai-modal';
        modal.innerHTML = `
            <div class="ai-modal-backdrop" onclick="closeAIModal()"></div>
            <div class="ai-modal-content">
                <div class="ai-modal-header">
                    <h3>🏦 みんなの銀行 AI分析</h3>
                    <button class="ai-modal-close" onclick="closeAIModal()">×</button>
                </div>
                <div class="ai-modal-body">
                    <div class="ai-loading">
                        <div class="ai-loading-spinner"></div>
                        <p>AI分析を実行中...</p>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // フェードイン
        setTimeout(() => {
            modal.classList.add('show');
        }, 10);
        
        // AI分析データを取得
        fetchAIAnalysis(grantId, modal);
    }
    
    // ==============================================
    // AI分析データ取得
    // ==============================================
    
    function fetchAIAnalysis(grantId, modal) {
        // WordPress AJAX エンドポイント
        const ajaxUrl = window.ajaxurl || '/wp-admin/admin-ajax.php';
        
        fetch(ajaxUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                'action': 'get_grant_ai_analysis',
                'grant_id': grantId,
                'nonce': window.grant_nonce || ''
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayAIAnalysis(modal, data.data);
            } else {
                displayAIError(modal, data.message || 'AI分析の取得に失敗しました');
            }
        })
        .catch(error => {
            console.error('AI Analysis Error:', error);
            displayAIError(modal, 'ネットワークエラーが発生しました');
        });
    }
    
    function displayAIAnalysis(modal, data) {
        const modalBody = modal.querySelector('.ai-modal-body');
        modalBody.innerHTML = `
            <div class="ai-analysis-result">
                <div class="ai-summary-section">
                    <h4>📊 適合度分析</h4>
                    <div class="ai-match-score">${data.match_score || '85'}%</div>
                    <p>${data.summary || 'この助成金はあなたのプロジェクトに適合している可能性が高いです。'}</p>
                </div>
                
                <div class="ai-checklist-section">
                    <h4>✅ 申請準備チェックリスト</h4>
                    <div class="ai-checklist">
                        ${(data.checklist || getDefaultChecklist()).map(item => `
                            <label class="ai-checklist-item">
                                <input type="checkbox" />
                                <div class="ai-check-mark"></div>
                                <div class="ai-check-text">${item}</div>
                            </label>
                        `).join('')}
                    </div>
                </div>
                
                <div class="ai-tips-section">
                    <h4>💡 申請のコツ</h4>
                    <ul class="ai-tips-list">
                        ${(data.tips || getDefaultTips()).map(tip => `<li>${tip}</li>`).join('')}
                    </ul>
                </div>
            </div>
        `;
    }
    
    function displayAIError(modal, message) {
        const modalBody = modal.querySelector('.ai-modal-body');
        modalBody.innerHTML = `
            <div class="ai-error">
                <div class="ai-error-icon">⚠️</div>
                <p>${message}</p>
                <button class="grant-btn grant-btn--secondary" onclick="closeAIModal()">閉じる</button>
            </div>
        `;
    }
    
    function getDefaultChecklist() {
        return [
            '法人登記簿謄本または個人事業主届け',
            '直近年度の財務諸表・決算書',
            '事業計画書（3年分）',
            '申請者の本人確認書類',
            '印鑑証明書（3ヶ月以内発行）'
        ];
    }
    
    function getDefaultTips() {
        return [
            '申請書類は締切の1週間前には完成させておく',
            '事業計画は数値的根拠を明確に示す',
            '過去の実績がある場合は具体的な成果を記載',
            '申請前に必ず募集要項を再確認する'
        ];
    }
    
    // ==============================================
    // モーダル制御関数（グローバルスコープ）
    // ==============================================
    
    window.closeAIModal = function() {
        const modal = document.querySelector('.ai-modal');
        if (modal) {
            modal.classList.remove('show');
            setTimeout(() => {
                modal.remove();
            }, 300);
        }
    };
    
    // ==============================================
    // みんなの銀行 トースト通知
    // ==============================================
    
    function showMinnaToast(message, type = 'info') {
        // 既存のトーストを削除
        const existingToast = document.querySelector('.minna-toast');
        if (existingToast) {
            existingToast.remove();
        }
        
        const toast = document.createElement('div');
        toast.className = `minna-toast minna-toast--${type}`;
        toast.innerHTML = `
            <div class="minna-toast-content">
                <span class="minna-toast-icon">${getToastIcon(type)}</span>
                <span class="minna-toast-message">${message}</span>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        // フェードイン
        setTimeout(() => {
            toast.classList.add('show');
        }, 10);
        
        // 自動削除
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 3000);
    }
    
    function getToastIcon(type) {
        switch(type) {
            case 'success': return '✅';
            case 'warning': return '⚠️';
            case 'error': return '❌';
            default: return 'ℹ️';
        }
    }
    
    window.showMinnaToast = showMinnaToast;
    
    // ==============================================
    // 検索AJAX機能の修復
    // ==============================================
    
    const searchForm = document.querySelector('.grant-search-form, #grant-search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            performAjaxSearch();
        });
    }
    
    // フィルター変更時のAJAX検索
    document.querySelectorAll('.grant-filter-select, .grant-search-input').forEach(element => {
        element.addEventListener('change', function() {
            if (this.type !== 'text') {
                performAjaxSearch();
            }
        });
        
        if (element.type === 'text') {
            let searchTimeout;
            element.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    performAjaxSearch();
                }, 500);
            });
        }
    });
    
    function performAjaxSearch() {
        const form = document.querySelector('.grant-search-form, #grant-search-form');
        if (!form) return;
        
        const formData = new FormData(form);
        const searchParams = new URLSearchParams();
        
        for (let [key, value] of formData.entries()) {
            if (value) {
                searchParams.append(key, value);
            }
        }
        
        // ローディング表示
        showMinnaToast('検索中...', 'info');
        
        const resultsContainer = document.querySelector('.grants-grid, .grant-results');
        if (resultsContainer) {
            resultsContainer.style.opacity = '0.5';
        }
        
        // AJAX リクエスト
        const ajaxUrl = window.ajaxurl || '/wp-admin/admin-ajax.php';
        
        fetch(ajaxUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                'action': 'filter_grants',
                'nonce': window.grant_nonce || '',
                ...Object.fromEntries(searchParams)
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && resultsContainer) {
                resultsContainer.innerHTML = data.data.html;
                resultsContainer.style.opacity = '1';
                
                // 結果数を更新
                const resultsCount = document.querySelector('.results-count');
                if (resultsCount && data.data.count !== undefined) {
                    resultsCount.textContent = `${data.data.count}件の助成金が見つかりました`;
                }
                
                showMinnaToast(`${data.data.count || 0}件の助成金が見つかりました`, 'success');
                
                // 新しいカード要素にイベントリスナーを再適用
                initializeNewCards();
            } else {
                showMinnaToast('検索に失敗しました', 'error');
                if (resultsContainer) {
                    resultsContainer.style.opacity = '1';
                }
            }
        })
        .catch(error => {
            console.error('Search Error:', error);
            showMinnaToast('ネットワークエラーが発生しました', 'error');
            if (resultsContainer) {
                resultsContainer.style.opacity = '1';
            }
        });
    }
    
    // 新しいカード要素の初期化
    function initializeNewCards() {
        // 詳細ボタン
        document.querySelectorAll('.grant-details-btn:not(.initialized)').forEach(button => {
            button.classList.add('initialized');
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const url = this.getAttribute('href');
                if (url && url !== '#') {
                    window.open(url, '_blank', 'noopener,noreferrer');
                }
            });
        });
        
        // AIボタン
        document.querySelectorAll('.grant-ai-btn:not(.initialized), .ai-analysis-btn:not(.initialized)').forEach(button => {
            button.classList.add('initialized');
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const grantId = this.closest('.grant-card-unified').dataset.grantId || this.dataset.grantId;
                if (grantId) {
                    showAIAnalysisModal(grantId);
                }
            });
        });
        
        // カードクリック
        document.querySelectorAll('.grant-card-unified:not(.initialized)').forEach(card => {
            card.classList.add('initialized');
            card.addEventListener('click', function(e) {
                if (e.target.closest('.grant-btn, button, a')) {
                    return;
                }
                const detailsLink = card.querySelector('.grant-details-btn');
                if (detailsLink && detailsLink.href) {
                    window.location.href = detailsLink.href;
                }
            });
        });
    }
    
    // 初回読み込み時のカード初期化
    initializeNewCards();
    
});
</script>

<?php endif; ?>