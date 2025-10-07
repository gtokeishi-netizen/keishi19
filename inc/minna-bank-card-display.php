<?php
/**
 * Minna Bank Style - Grant Card Display Functions
 *
 * みんなの銀行スタイル専用カードレンダリングシステム
 * プロフェッショナル・モノクロデザイン・白黒アイコンのみ
 * 
 * @package Grant_Insight_Minna_Bank
 * @version 1.0.0
 */

// セキュリティチェック
if (!defined('ABSPATH')) {
    exit;
}

/**
 * =============================================================================
 * Minna Bank Style Grant Card Renderer
 * =============================================================================
 */
class MinnaBankCardRenderer {
    
    private static $instance = null;
    private $user_favorites_cache = null;
    
    /**
     * シングルトンパターンのインスタンス取得
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * プライベートコンストラクタ
     */
    private function __construct() {
        $this->load_dependencies();
    }
    
    /**
     * 依存関数の確認
     */
    private function load_dependencies() {
        // 必要な関数が読み込まれているか確認
        $required_functions = [
            'gi_safe_get_meta',
            'gi_get_formatted_deadline',
            'gi_map_application_status_ui',
            'gi_get_user_favorites'
        ];
        
        foreach ($required_functions as $function) {
            if (!function_exists($function)) {
                error_log("Minna Bank Card Renderer: Required function {$function} not found");
            }
        }
    }
    
    /**
     * Minna Bank スタイル グリッドカードレンダリング
     * 
     * @param WP_Post $post 助成金投稿オブジェクト
     * @param array $options レンダリングオプション
     * @return string HTMLカード
     */
    public function render_grid_card($post, $options = []) {
        if (!$post || !is_object($post)) {
            return '';
        }
        
        // デフォルトオプション
        $default_options = [
            'show_favorite' => true,
            'show_featured' => true,
            'show_category' => true,
            'show_prefecture' => true,
            'lazy_load' => true,
            'animate' => true
        ];
        
        $options = array_merge($default_options, $options);
        
        // 投稿データ取得
        $post_id = $post->ID;
        $title = get_the_title($post_id);
        $permalink = get_permalink($post_id);
        
        // ACFフィールド取得（フォールバック付き）
        $organization = function_exists('get_field') ? get_field('organization', $post_id) : get_post_meta($post_id, 'organization', true);
        $max_amount = function_exists('get_field') ? get_field('max_amount', $post_id) : get_post_meta($post_id, 'max_amount', true);
        $deadline_date = function_exists('get_field') ? get_field('deadline_date', $post_id) : get_post_meta($post_id, 'deadline_date', true);
        $adoption_rate = function_exists('get_field') ? floatval(get_field('adoption_rate', $post_id)) : floatval(get_post_meta($post_id, 'adoption_rate', true));
        $application_status = function_exists('get_field') ? get_field('application_status', $post_id) : get_post_meta($post_id, 'application_status', true);
        $is_featured = function_exists('get_field') ? get_field('is_featured', $post_id) : get_post_meta($post_id, 'is_featured', true);
        
        // タクソノミー取得
        $categories = wp_get_post_terms($post_id, 'grant_category', ['fields' => 'names']);
        $prefectures = wp_get_post_terms($post_id, 'grant_prefecture', ['fields' => 'names']);
        
        // 締切情報の処理
        $deadline_info = $this->process_deadline_info($deadline_date);
        
        // ステータス情報の処理
        $status_info = $this->process_status_info($application_status);
        
        // お気に入り状態
        $is_favorite = $this->is_user_favorite($post_id);
        
        ob_start();
        ?>
        
        <article class="minna-grant-card <?php echo $options['animate'] ? 'animate-card' : ''; ?>" 
                 data-post-id="<?php echo esc_attr($post_id); ?>"
                 data-href="<?php echo esc_url($permalink); ?>">
            
            <?php if ($options['show_featured'] && $is_featured): ?>
            <!-- Featured Badge -->
            <div class="minna-featured-badge">
                <div class="minna-icon minna-icon-star minna-icon-xs"></div>
                <span>おすすめ</span>
            </div>
            <?php endif; ?>
            
            <?php if ($options['show_favorite']): ?>
            <!-- Favorite Button -->
            <button class="minna-favorite-btn <?php echo $is_favorite ? 'active' : ''; ?>"
                    data-post-id="<?php echo esc_attr($post_id); ?>"
                    aria-label="<?php echo $is_favorite ? 'お気に入りから削除' : 'お気に入りに追加'; ?>">
                <div class="minna-icon minna-icon-heart minna-icon-sm"></div>
            </button>
            <?php endif; ?>
            
            <!-- Card Header -->
            <div class="minna-grant-card-header">
                <?php if ($options['show_category'] && !empty($categories)): ?>
                <div class="minna-category-badge">
                    <div class="minna-icon minna-icon-target minna-icon-xs"></div>
                    <span><?php echo esc_html($categories[0]); ?></span>
                </div>
                <?php endif; ?>
                
                <h3 class="minna-grant-title"><?php echo esc_html($title); ?></h3>
                
                <div class="minna-grant-organization">
                    <div class="minna-icon minna-icon-building minna-icon-sm"></div>
                    <span><?php echo esc_html($organization ?: '実施機関未指定'); ?></span>
                </div>
            </div>
            
            <!-- Card Body -->
            <div class="minna-grant-card-body">
                <div class="minna-grant-info-grid">
                    
                    <!-- Amount Information -->
                    <div class="minna-info-item">
                        <div class="minna-info-icon">
                            <div class="minna-icon minna-icon-money minna-icon-lg"></div>
                        </div>
                        <div class="minna-info-content">
                            <div class="minna-info-label">助成金額</div>
                            <div class="minna-info-value">
                                <?php echo $max_amount ? esc_html($max_amount) : '金額応相談'; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Deadline Information -->
                    <div class="minna-info-item">
                        <div class="minna-info-icon">
                            <div class="minna-icon minna-icon-calendar minna-icon-lg"></div>
                        </div>
                        <div class="minna-info-content">
                            <div class="minna-info-label">申請締切</div>
                            <div class="minna-info-value <?php echo esc_attr($deadline_info['class']); ?>">
                                <?php echo esc_html($deadline_info['text']); ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Success Rate Information -->
                    <div class="minna-info-item">
                        <div class="minna-info-icon">
                            <div class="minna-icon minna-icon-chart minna-icon-lg"></div>
                        </div>
                        <div class="minna-info-content">
                            <div class="minna-info-label">採択率</div>
                            <div class="minna-info-value">
                                <?php echo $adoption_rate > 0 ? number_format($adoption_rate, 1) . '%' : '未公開'; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Location Information -->
                    <?php if ($options['show_prefecture'] && !empty($prefectures)): ?>
                    <div class="minna-info-item">
                        <div class="minna-info-icon">
                            <div class="minna-icon minna-icon-location minna-icon-lg"></div>
                        </div>
                        <div class="minna-info-content">
                            <div class="minna-info-label">対象地域</div>
                            <div class="minna-info-value">
                                <?php echo esc_html($prefectures[0]); ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                </div>
            </div>
            
            <!-- Card Footer -->
            <div class="minna-grant-card-footer">
                <div class="minna-status-indicator">
                    <div class="minna-status-dot <?php echo esc_attr($status_info['class']); ?>"></div>
                    <span class="minna-status-text"><?php echo esc_html($status_info['label']); ?></span>
                </div>
                
                <div class="minna-card-actions">
                    <button class="minna-btn minna-btn-sm minna-btn-outline" 
                            onclick="event.stopPropagation(); window.open('<?php echo esc_url($permalink); ?>', '_blank')">
                        <div class="minna-icon minna-icon-link minna-icon-xs"></div>
                        詳細
                    </button>
                </div>
            </div>
            
        </article>
        
        <?php
        return ob_get_clean();
    }
    
    /**
     * Minna Bank スタイル リストカードレンダリング
     * 
     * @param WP_Post $post 助成金投稿オブジェクト
     * @param array $options レンダリングオプション
     * @return string HTMLカード
     */
    public function render_list_card($post, $options = []) {
        if (!$post || !is_object($post)) {
            return '';
        }
        
        // リストビュー用のオプション
        $default_options = [
            'show_description' => true,
            'show_tags' => true,
            'compact' => false
        ];
        
        $options = array_merge($default_options, $options);
        
        // 投稿データ取得
        $post_id = $post->ID;
        $title = get_the_title($post_id);
        $permalink = get_permalink($post_id);
        $excerpt = get_the_excerpt($post_id);
        
        // ACFフィールド取得
        $organization = function_exists('get_field') ? get_field('organization', $post_id) : get_post_meta($post_id, 'organization', true);
        $max_amount = function_exists('get_field') ? get_field('max_amount', $post_id) : get_post_meta($post_id, 'max_amount', true);
        $deadline_date = function_exists('get_field') ? get_field('deadline_date', $post_id) : get_post_meta($post_id, 'deadline_date', true);
        $adoption_rate = function_exists('get_field') ? floatval(get_field('adoption_rate', $post_id)) : floatval(get_post_meta($post_id, 'adoption_rate', true));
        $application_status = function_exists('get_field') ? get_field('application_status', $post_id) : get_post_meta($post_id, 'application_status', true);
        
        // タクソノミー取得
        $categories = wp_get_post_terms($post_id, 'grant_category', ['fields' => 'names']);
        $prefectures = wp_get_post_terms($post_id, 'grant_prefecture', ['fields' => 'names']);
        
        // 情報処理
        $deadline_info = $this->process_deadline_info($deadline_date);
        $status_info = $this->process_status_info($application_status);
        $is_favorite = $this->is_user_favorite($post_id);
        
        ob_start();
        ?>
        
        <article class="minna-grant-list-card <?php echo $options['compact'] ? 'compact' : ''; ?>" 
                 data-post-id="<?php echo esc_attr($post_id); ?>"
                 onclick="window.location.href='<?php echo esc_url($permalink); ?>'">
            
            <div class="minna-list-card-main">
                <div class="minna-list-card-header">
                    <div class="minna-list-title-area">
                        <h3 class="minna-list-card-title"><?php echo esc_html($title); ?></h3>
                        <div class="minna-list-organization">
                            <div class="minna-icon minna-icon-building minna-icon-sm"></div>
                            <span><?php echo esc_html($organization ?: '実施機関未指定'); ?></span>
                        </div>
                    </div>
                    
                    <div class="minna-list-card-meta">
                        <div class="minna-list-meta-item">
                            <div class="minna-icon minna-icon-money minna-icon-sm"></div>
                            <span><?php echo $max_amount ? esc_html($max_amount) : '金額応相談'; ?></span>
                        </div>
                        
                        <div class="minna-list-meta-item">
                            <div class="minna-icon minna-icon-calendar minna-icon-sm"></div>
                            <span class="<?php echo esc_attr($deadline_info['class']); ?>">
                                <?php echo esc_html($deadline_info['text']); ?>
                            </span>
                        </div>
                        
                        <?php if ($adoption_rate > 0): ?>
                        <div class="minna-list-meta-item">
                            <div class="minna-icon minna-icon-chart minna-icon-sm"></div>
                            <span><?php echo number_format($adoption_rate, 1); ?>%</span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if ($options['show_description'] && $excerpt): ?>
                <div class="minna-list-card-excerpt">
                    <p><?php echo esc_html(wp_trim_words($excerpt, 30)); ?></p>
                </div>
                <?php endif; ?>
                
                <?php if ($options['show_tags'] && (!empty($categories) || !empty($prefectures))): ?>
                <div class="minna-list-card-tags">
                    <?php foreach (array_slice($categories, 0, 2) as $category): ?>
                        <span class="minna-tag minna-tag-category"><?php echo esc_html($category); ?></span>
                    <?php endforeach; ?>
                    
                    <?php foreach (array_slice($prefectures, 0, 1) as $prefecture): ?>
                        <span class="minna-tag minna-tag-location"><?php echo esc_html($prefecture); ?></span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="minna-list-card-actions">
                <div class="minna-status-indicator">
                    <div class="minna-status-dot <?php echo esc_attr($status_info['class']); ?>"></div>
                    <span><?php echo esc_html($status_info['label']); ?></span>
                </div>
                
                <button class="minna-favorite-btn <?php echo $is_favorite ? 'active' : ''; ?>"
                        data-post-id="<?php echo esc_attr($post_id); ?>"
                        onclick="event.stopPropagation();"
                        aria-label="<?php echo $is_favorite ? 'お気に入りから削除' : 'お気に入りに追加'; ?>">
                    <div class="minna-icon minna-icon-heart minna-icon-sm"></div>
                </button>
                
                <div class="minna-icon minna-icon-chevron-right minna-icon-sm"></div>
            </div>
            
        </article>
        
        <?php
        return ob_get_clean();
    }
    
    /**
     * 締切情報の処理
     * 
     * @param string $deadline_date 締切日
     * @return array 処理された締切情報
     */
    private function process_deadline_info($deadline_date) {
        if (!$deadline_date) {
            return [
                'text' => '随時募集',
                'class' => 'status-ongoing',
                'days_left' => null
            ];
        }
        
        $deadline_timestamp = strtotime($deadline_date);
        $current_timestamp = current_time('timestamp');
        $days_left = ceil(($deadline_timestamp - $current_timestamp) / DAY_IN_SECONDS);
        
        if ($days_left < 0) {
            return [
                'text' => '募集終了',
                'class' => 'status-closed',
                'days_left' => $days_left
            ];
        } elseif ($days_left <= 7) {
            return [
                'text' => "残り{$days_left}日",
                'class' => 'status-urgent',
                'days_left' => $days_left
            ];
        } elseif ($days_left <= 30) {
            return [
                'text' => "残り{$days_left}日",
                'class' => 'status-warning',
                'days_left' => $days_left
            ];
        } else {
            return [
                'text' => date('Y/n/j', $deadline_timestamp),
                'class' => 'status-normal',
                'days_left' => $days_left
            ];
        }
    }
    
    /**
     * ステータス情報の処理
     * 
     * @param string $status ステータス
     * @return array 処理されたステータス情報
     */
    private function process_status_info($status) {
        $status_map = [
            'open' => ['label' => '募集中', 'class' => 'status-open'],
            'active' => ['label' => '募集中', 'class' => 'status-open'],
            'upcoming' => ['label' => '募集予定', 'class' => 'status-upcoming'],
            'closed' => ['label' => '募集終了', 'class' => 'status-closed'],
            'paused' => ['label' => '一時停止', 'class' => 'status-paused']
        ];
        
        return $status_map[$status] ?? ['label' => '詳細確認', 'class' => 'status-unknown'];
    }
    
    /**
     * お気に入り状態の確認
     * 
     * @param int $post_id 投稿ID
     * @return bool お気に入り状態
     */
    private function is_user_favorite($post_id) {
        if ($this->user_favorites_cache === null) {
            $this->user_favorites_cache = function_exists('gi_get_user_favorites') 
                ? gi_get_user_favorites() 
                : [];
        }
        
        return in_array($post_id, $this->user_favorites_cache);
    }
    
    /**
     * カードのCSS出力
     * 
     * @return string CSS
     */
    public function get_card_styles() {
        ob_start();
        ?>
        <style>
        /* ===== MINNA BANK GRANT CARD STYLES ===== */
        
        /* Grid Card Styles */
        .minna-grant-card {
            background: var(--minna-white);
            border: 2px solid var(--minna-gray-200);
            border-radius: var(--minna-radius-xl);
            overflow: hidden;
            transition: var(--minna-transition-normal);
            cursor: pointer;
            position: relative;
        }
        
        .minna-grant-card:hover {
            border-color: var(--minna-gray-400);
            box-shadow: var(--minna-shadow-xl);
            transform: translateY(-4px);
        }
        
        .minna-featured-badge {
            position: absolute;
            top: var(--minna-space-3);
            left: var(--minna-space-3);
            background: var(--minna-primary);
            color: var(--minna-white);
            padding: var(--minna-space-1) var(--minna-space-3);
            border-radius: var(--minna-radius-full);
            font-size: var(--minna-text-xs);
            font-weight: var(--minna-weight-semibold);
            z-index: 10;
            display: flex;
            align-items: center;
            gap: var(--minna-space-1);
        }
        
        .minna-favorite-btn {
            position: absolute;
            top: var(--minna-space-3);
            right: var(--minna-space-3);
            width: 2rem;
            height: 2rem;
            background: var(--minna-white);
            border: 2px solid var(--minna-gray-200);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--minna-transition-fast);
            z-index: 10;
        }
        
        .minna-favorite-btn:hover {
            border-color: var(--minna-primary);
            transform: scale(1.1);
        }
        
        .minna-favorite-btn.active {
            background: var(--minna-primary);
            border-color: var(--minna-primary);
        }
        
        .minna-favorite-btn.active .minna-icon {
            filter: brightness(0) invert(1);
        }
        
        .minna-grant-card-header {
            padding: var(--minna-space-6);
            background: linear-gradient(135deg, var(--minna-gray-50) 0%, var(--minna-white) 100%);
            border-bottom: 1px solid var(--minna-gray-200);
        }
        
        .minna-category-badge {
            display: inline-flex;
            align-items: center;
            gap: var(--minna-space-1);
            background: var(--minna-gray-100);
            color: var(--minna-gray-700);
            padding: var(--minna-space-1) var(--minna-space-3);
            border-radius: var(--minna-radius-full);
            font-size: var(--minna-text-xs);
            font-weight: var(--minna-weight-medium);
            margin-bottom: var(--minna-space-3);
        }
        
        .minna-grant-title {
            font-size: var(--minna-text-lg);
            font-weight: var(--minna-weight-bold);
            color: var(--minna-gray-900);
            line-height: var(--minna-leading-tight);
            margin-bottom: var(--minna-space-3);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .minna-grant-organization {
            display: flex;
            align-items: center;
            gap: var(--minna-space-2);
            font-size: var(--minna-text-sm);
            color: var(--minna-gray-600);
            font-weight: var(--minna-weight-medium);
        }
        
        .minna-grant-card-body {
            padding: var(--minna-space-6);
        }
        
        .minna-grant-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--minna-space-5);
        }
        
        .minna-info-item {
            display: flex;
            align-items: flex-start;
            gap: var(--minna-space-3);
        }
        
        .minna-info-icon {
            width: 2.5rem;
            height: 2.5rem;
            background: var(--minna-gray-100);
            border-radius: var(--minna-radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        
        .minna-info-content {
            flex: 1;
        }
        
        .minna-info-label {
            font-size: var(--minna-text-xs);
            color: var(--minna-gray-500);
            font-weight: var(--minna-weight-medium);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: var(--minna-space-1);
        }
        
        .minna-info-value {
            font-size: var(--minna-text-sm);
            font-weight: var(--minna-weight-semibold);
            color: var(--minna-gray-900);
            line-height: var(--minna-leading-tight);
        }
        
        .minna-grant-card-footer {
            padding: var(--minna-space-4) var(--minna-space-6);
            background: var(--minna-gray-50);
            border-top: 1px solid var(--minna-gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .minna-status-indicator {
            display: flex;
            align-items: center;
            gap: var(--minna-space-2);
        }
        
        .minna-status-dot {
            width: 0.5rem;
            height: 0.5rem;
            border-radius: 50%;
            flex-shrink: 0;
        }
        
        .minna-status-dot.status-open { background: var(--minna-success); }
        .minna-status-dot.status-upcoming { background: var(--minna-info); }
        .minna-status-dot.status-closed { background: var(--minna-gray-400); }
        .minna-status-dot.status-paused { background: var(--minna-warning); }
        .minna-status-dot.status-unknown { background: var(--minna-gray-300); }
        
        .minna-status-text {
            font-size: var(--minna-text-xs);
            font-weight: var(--minna-weight-medium);
            color: var(--minna-gray-600);
        }
        
        .minna-card-actions {
            display: flex;
            gap: var(--minna-space-2);
        }
        
        /* Status Classes */
        .status-urgent { color: var(--minna-danger); font-weight: var(--minna-weight-bold); }
        .status-warning { color: var(--minna-warning); font-weight: var(--minna-weight-semibold); }
        .status-normal { color: var(--minna-gray-700); }
        .status-ongoing { color: var(--minna-success); }
        .status-closed { color: var(--minna-gray-500); }
        
        /* List Card Styles */
        .minna-grant-list-card {
            background: var(--minna-white);
            border: 2px solid var(--minna-gray-200);
            border-radius: var(--minna-radius-xl);
            padding: var(--minna-space-6);
            display: flex;
            align-items: center;
            gap: var(--minna-space-6);
            transition: var(--minna-transition-normal);
            cursor: pointer;
        }
        
        .minna-grant-list-card:hover {
            border-color: var(--minna-gray-400);
            box-shadow: var(--minna-shadow-lg);
            transform: translateY(-2px);
        }
        
        .minna-list-card-main {
            flex: 1;
        }
        
        .minna-list-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: var(--minna-space-4);
            margin-bottom: var(--minna-space-3);
        }
        
        .minna-list-title-area {
            flex: 1;
        }
        
        .minna-list-card-title {
            font-size: var(--minna-text-lg);
            font-weight: var(--minna-weight-bold);
            color: var(--minna-gray-900);
            margin-bottom: var(--minna-space-2);
            line-height: var(--minna-leading-tight);
        }
        
        .minna-list-organization {
            display: flex;
            align-items: center;
            gap: var(--minna-space-2);
            font-size: var(--minna-text-sm);
            color: var(--minna-gray-600);
        }
        
        .minna-list-card-meta {
            display: flex;
            gap: var(--minna-space-4);
            align-items: center;
        }
        
        .minna-list-meta-item {
            display: flex;
            align-items: center;
            gap: var(--minna-space-1);
            font-size: var(--minna-text-sm);
            font-weight: var(--minna-weight-medium);
            color: var(--minna-gray-700);
            white-space: nowrap;
        }
        
        .minna-list-card-excerpt {
            margin-bottom: var(--minna-space-3);
        }
        
        .minna-list-card-excerpt p {
            color: var(--minna-gray-600);
            line-height: var(--minna-leading-relaxed);
            margin: 0;
        }
        
        .minna-list-card-tags {
            display: flex;
            gap: var(--minna-space-2);
            flex-wrap: wrap;
        }
        
        .minna-tag {
            padding: var(--minna-space-1) var(--minna-space-3);
            background: var(--minna-gray-100);
            color: var(--minna-gray-700);
            border-radius: var(--minna-radius-full);
            font-size: var(--minna-text-xs);
            font-weight: var(--minna-weight-medium);
        }
        
        .minna-tag-category {
            background: var(--minna-primary);
            color: var(--minna-white);
        }
        
        .minna-tag-location {
            background: var(--minna-info);
            color: var(--minna-white);
        }
        
        .minna-list-card-actions {
            display: flex;
            align-items: center;
            gap: var(--minna-space-3);
            flex-shrink: 0;
        }
        
        .minna-list-card-actions .minna-favorite-btn {
            position: static;
        }
        
        /* Compact List Style */
        .minna-grant-list-card.compact {
            padding: var(--minna-space-4);
        }
        
        .minna-grant-list-card.compact .minna-list-card-header {
            margin-bottom: var(--minna-space-2);
        }
        
        .minna-grant-list-card.compact .minna-list-card-title {
            font-size: var(--minna-text-base);
        }
        
        /* Animation */
        .animate-card {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.6s ease-out forwards;
        }
        
        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .minna-grant-info-grid {
                grid-template-columns: 1fr;
                gap: var(--minna-space-3);
            }
            
            .minna-list-card-header {
                flex-direction: column;
                align-items: stretch;
                gap: var(--minna-space-3);
            }
            
            .minna-list-card-meta {
                flex-wrap: wrap;
                gap: var(--minna-space-2);
            }
            
            .minna-grant-list-card {
                flex-direction: column;
                align-items: stretch;
            }
            
            .minna-list-card-actions {
                justify-content: space-between;
            }
        }
        
        /* Accessibility */
        @media (prefers-reduced-motion: reduce) {
            .animate-card {
                animation: none;
                opacity: 1;
                transform: none;
            }
            
            .minna-grant-card,
            .minna-grant-list-card,
            .minna-favorite-btn {
                transition: none;
            }
        }
        </style>
        <?php
        return ob_get_clean();
    }
}

/**
 * =============================================================================
 * Helper Functions for Template Use
 * =============================================================================
 */

/**
 * Minna Bank スタイル グリッドカードを表示
 * 
 * @param WP_Post $post 投稿オブジェクト
 * @param array $options オプション
 */
function minna_display_grant_grid_card($post, $options = []) {
    $renderer = MinnaBankCardRenderer::getInstance();
    echo $renderer->render_grid_card($post, $options);
}

/**
 * Minna Bank スタイル リストカードを表示
 * 
 * @param WP_Post $post 投稿オブジェクト
 * @param array $options オプション
 */
function minna_display_grant_list_card($post, $options = []) {
    $renderer = MinnaBankCardRenderer::getInstance();
    echo $renderer->render_list_card($post, $options);
}

/**
 * Minna Bank カードのCSSを出力
 */
function minna_output_card_styles() {
    $renderer = MinnaBankCardRenderer::getInstance();
    echo $renderer->get_card_styles();
}

/**
 * カード表示タイプに応じた出力
 * 
 * @param WP_Post $post 投稿オブジェクト
 * @param string $view_type 表示タイプ（grid/list）
 * @param array $options オプション
 */
function minna_display_grant_card($post, $view_type = 'grid', $options = []) {
    $renderer = MinnaBankCardRenderer::getInstance();
    
    if ($view_type === 'list') {
        echo $renderer->render_list_card($post, $options);
    } else {
        echo $renderer->render_grid_card($post, $options);
    }
}

// CSS自動出力（必要に応じて）
add_action('wp_head', function() {
    if (is_post_type_archive('grant') || is_singular('grant')) {
        minna_output_card_styles();
    }
}, 15);