/*!
 * Minna Bank Style - WordPress Grant System
 * みんなの銀行スタイル統合JavaScript
 * 完全プロフェッショナル・デザイン対応
 * 
 * @version 2.0.0
 * @date 2025-10-07
 * @design Minna Bank Professional Style
 */

/**
 * =============================================================================
 * MINNA BANK STYLE - メイン名前空間
 * みんなの銀行デザインシステム統合JavaScript
 * プロフェッショナル・モノクロームデザイン対応
 * =============================================================================
 */
const MinnaBankGrants = {
    // バージョン情報
    version: '2.0.0',
    
    // 設定オブジェクト
    config: {
        debounceDelay: 250,
        toastDuration: 4000,
        scrollTrackingInterval: 100,
        apiEndpoint: '/wp-admin/admin-ajax.php',
        searchMinLength: 1,
        maxComparisonItems: 5,
        animationDuration: 300,
        mobileBreakpoint: 768,
        filterCategories: {
            grant_category: 'カテゴリー',
            grant_prefecture: '都道府県', 
            grant_municipality: '市区町村',
            deadline_status: '締切状況',
            amount_range: '金額範囲',
            status: 'ステータス'
        }
    },

    // 初期化フラグ
    initialized: false,
    
    // 状態管理
    state: {
        lastScrollY: 0,
        headerHeight: 0,
        isScrolling: false,
        activeFilters: new Map(),
        comparisonItems: [],
        touchStartY: 0,
        touchEndY: 0
    },

    // DOM要素キャッシュ
    elements: {},

    /**
     * ==========================================================================
     * 初期化システム
     * ==========================================================================
     */
    init() {
        if (this.initialized) return;
        
        // Minna Bank Style 初期化開始ログ
        console.log('🏦 Minna Bank Grant System - Initializing...');
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setupAll());
        } else {
            this.setupAll();
        }
    },

    /**
     * 全機能のセットアップ
     */
    setupAll() {
        try {
            this.cacheElements();
            this.setupUtils();
            this.setupMinnaBankCore();
            this.setupSearch();
            this.setupFilters();
            this.setupComparison();
            this.setupMobile();
            this.setupAccessibility();
            this.setupPerformance();
            this.setupAnimations();
            this.setupForms();
            this.setupMinnaBankInteractions();
            this.setupAdvancedFeatures();
            
            this.initialized = true;
            console.log('🏦 Minna Bank Grant System - Successfully Initialized');
            this.debug('Minna Bank Grant System initialized successfully');
        } catch (error) {
            console.error('🚨 Minna Bank initialization error:', error);
        }
    },

    /**
     * DOM要素のキャッシュ
     */
    cacheElements() {
        this.elements = {
            // 検索関連
            searchInputs: document.querySelectorAll('.gi-search-input, #grant-search, .search-input'),
            searchContainer: document.querySelector('.gi-search-container, .search-container'),
            searchSuggestions: null, // 動的作成
            
            // フィルター関連
            filterButtons: document.querySelectorAll('.gi-filter-chip, .filter-button, .filter-chip'),
            filterTrigger: document.querySelector('.gi-filter-trigger, #filter-toggle'),
            
            // コンテンツ関連
            grantsGrid: document.querySelector('.gi-grants-grid, .grants-grid, #grants-container'),
            
            // UI要素
            header: document.querySelector('.gi-mobile-header, .site-header'),
            body: document.body,
            
            // 比較関連
            comparisonBar: null // 動的作成
        };
    },

    /**
     * ==========================================================================
     * みんなの銀行コア機能
     * プロフェッショナルデザインシステム基盤
     * ==========================================================================
     */
    setupMinnaBankCore() {
        // デザインシステムの初期化
        this.initDesignSystem();
        
        // プロフェッショナル・インタラクションの設定
        this.setupProfessionalInteractions();
        
        // モノクローム・UIの初期化
        this.initMonochromeInterface();
        
        // 銀行スタイル・レスポンシブ対応
        this.setupBankingResponsive();
        
        this.debug('Minna Bank core system initialized');
    },

    /**
     * デザインシステムの初期化
     */
    initDesignSystem() {
        // プロフェッショナル・カラーパレットの適用
        document.documentElement.style.setProperty('--mb-transition', '0.2s cubic-bezier(0.4, 0, 0.2, 1)');
        document.documentElement.style.setProperty('--mb-shadow', '0 2px 8px rgba(0, 0, 0, 0.1)');
        document.documentElement.style.setProperty('--mb-shadow-hover', '0 4px 16px rgba(0, 0, 0, 0.15)');
        
        // Body クラスの設定
        this.elements.body.classList.add('minna-bank-style', 'professional-interface');
        
        // フォントシステムの適用
        this.applyProfessionalFonts();
    },

    /**
     * プロフェッショナルフォントシステム
     */
    applyProfessionalFonts() {
        const fontCSS = `
            :root {
                --mb-font-primary: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                --mb-font-mono: "SF Mono", Monaco, "Roboto Mono", Consolas, monospace;
                --mb-font-weight-light: 300;
                --mb-font-weight-normal: 400;
                --mb-font-weight-medium: 500;
                --mb-font-weight-bold: 600;
            }
        `;
        
        if (!document.querySelector('#minna-bank-fonts')) {
            const style = document.createElement('style');
            style.id = 'minna-bank-fonts';
            style.textContent = fontCSS;
            document.head.appendChild(style);
        }
    },

    /**
     * プロフェッショナル・インタラクション
     */
    setupProfessionalInteractions() {
        // ホバー効果の統一
        this.setupUnifiedHoverEffects();
        
        // フォーカス・インジケーターの改善
        this.setupProfessionalFocus();
        
        // クリック・フィードバック
        this.setupClickFeedback();
        
        // ローディング・インジケーター
        this.setupLoadingIndicators();
    },

    /**
     * 統一ホバー効果
     */
    setupUnifiedHoverEffects() {
        const hoverElements = '.mb-card, .mb-button, .mb-filter-chip, .mb-grant-card, .grant-card';
        
        document.addEventListener('mouseenter', (e) => {
            if (e.target.matches(hoverElements)) {
                e.target.classList.add('mb-hover-active');
            }
        }, true);

        document.addEventListener('mouseleave', (e) => {
            if (e.target.matches(hoverElements)) {
                e.target.classList.remove('mb-hover-active');
            }
        }, true);
    },

    /**
     * プロフェッショナル・フォーカス
     */
    setupProfessionalFocus() {
        // キーボードナビゲーション時のみフォーカス表示
        let isKeyboardNavigation = false;
        
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
                isKeyboardNavigation = true;
                this.elements.body.classList.add('mb-keyboard-navigation');
            }
        });
        
        document.addEventListener('mousedown', () => {
            isKeyboardNavigation = false;
            this.elements.body.classList.remove('mb-keyboard-navigation');
        });
    },

    /**
     * クリック・フィードバック
     */
    setupClickFeedback() {
        document.addEventListener('click', (e) => {
            const button = e.target.closest('button, .mb-button, .btn');
            if (!button) return;
            
            // リップル効果
            this.createRippleEffect(button, e);
            
            // ハプティック・フィードバック（対応デバイスのみ）
            if (navigator.vibrate) {
                navigator.vibrate(10);
            }
        });
    },

    /**
     * リップル効果の作成
     */
    createRippleEffect(element, event) {
        const rect = element.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = event.clientX - rect.left - size / 2;
        const y = event.clientY - rect.top - size / 2;
        
        const ripple = document.createElement('div');
        ripple.className = 'mb-ripple-effect';
        ripple.style.cssText = `
            position: absolute;
            width: ${size}px;
            height: ${size}px;
            left: ${x}px;
            top: ${y}px;
            background: rgba(0, 0, 0, 0.1);
            border-radius: 50%;
            transform: scale(0);
            animation: mbRipple 0.6s ease-out;
            pointer-events: none;
        `;
        
        // 相対位置の設定
        if (getComputedStyle(element).position === 'static') {
            element.style.position = 'relative';
        }
        element.style.overflow = 'hidden';
        
        element.appendChild(ripple);
        
        setTimeout(() => {
            if (ripple.parentNode) {
                ripple.parentNode.removeChild(ripple);
            }
        }, 600);
    },

    /**
     * モノクローム・インターフェース
     */
    initMonochromeInterface() {
        // アイコン・システムの初期化
        this.initIconSystem();
        
        // グレー・スケール・パレットの適用
        this.applyGrayscalePalette();
        
        // プロフェッショナル・コンポーネントの初期化
        this.initProfessionalComponents();
    },

    /**
     * アイコンシステムの初期化
     */
    initIconSystem() {
        // SVGアイコンの動的読み込み
        this.loadSVGIcons();
        
        // アイコン・ユーティリティの設定
        this.setupIconUtilities();
    },

    /**
     * SVGアイコンの読み込み
     */
    loadSVGIcons() {
        // Font Awesomeの代替としてSVGアイコンを設定
        const iconMap = {
            'search': '<svg viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>',
            'filter': '<svg viewBox="0 0 24 24"><path d="M3 17v2h6v-2H3zM3 5v2h10V5H3zm10 16v-2h8v-2h-8v-2h-2v6h2zM7 9v2H3v2h4v2h2V9H7zm14 4v-2H11v2h10zm-6-4h2V7h4V5h-4V3h-2v6z"/></svg>',
            'bookmark': '<svg viewBox="0 0 24 24"><path d="M17 3H7c-1.1 0-1.99.9-1.99 2L5 21l7-3 7 3V5c0-1.1-.9-2-2-2z"/></svg>',
            'heart': '<svg viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>',
            'arrow-up': '<svg viewBox="0 0 24 24"><path d="M7.41 15.41L12 10.83l4.59 4.58L18 14l-6-6-6 6z"/></svg>',
            'close': '<svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>',
            'menu': '<svg viewBox="0 0 24 24"><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/></svg>',
            'calendar': '<svg viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>',
            'location': '<svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>',
            'money': '<svg viewBox="0 0 24 24"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>',
            'check': '<svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>',
            'info': '<svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>'
        };
        
        // アイコンをページに注入
        Object.keys(iconMap).forEach(iconName => {
            const elements = document.querySelectorAll(`[data-icon="${iconName}"]`);
            elements.forEach(el => {
                el.innerHTML = iconMap[iconName];
                el.classList.add('mb-icon', `mb-icon-${iconName}`);
            });
        });
    },

    /**
     * 銀行スタイル・レスポンシブ
     */
    setupBankingResponsive() {
        // ブレークポイント管理
        this.handleResponsiveBreakpoints();
        
        // モバイル・バンキング・UI
        this.setupMobileBankingUI();
        
        // タブレット最適化
        this.setupTabletOptimization();
    },

    /**
     * レスポンシブ・ブレークポイント
     */
    handleResponsiveBreakpoints() {
        const updateBreakpoint = () => {
            const width = window.innerWidth;
            this.elements.body.classList.remove('mb-mobile', 'mb-tablet', 'mb-desktop');
            
            if (width < 768) {
                this.elements.body.classList.add('mb-mobile');
                this.state.currentBreakpoint = 'mobile';
            } else if (width < 1024) {
                this.elements.body.classList.add('mb-tablet');
                this.state.currentBreakpoint = 'tablet';
            } else {
                this.elements.body.classList.add('mb-desktop');
                this.state.currentBreakpoint = 'desktop';
            }
            
            this.debug(`Breakpoint changed to: ${this.state.currentBreakpoint}`);
        };
        
        updateBreakpoint();
        window.addEventListener('resize', this.throttle(updateBreakpoint, 250));
    },

    /**
     * ==========================================================================
     * ユーティリティ関数群
     * ==========================================================================
     */
    setupUtils() {
        // HTMLエスケープ関数
        this.escapeHtml = function(text) {
            if (!text) return '';
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        };

        // デバウンス関数
        this.debounce = function(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func.apply(this, args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        };

        // スロットル関数
        this.throttle = function(func, limit) {
            let inThrottle;
            return function(...args) {
                if (!inThrottle) {
                    func.apply(this, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            };
        };

        // トースト通知関数
        this.showToast = function(message, type = 'info') {
            // 既存のトーストを削除
            const existingToast = document.querySelector('.gi-toast, .ui-notification');
            if (existingToast) {
                existingToast.remove();
            }
            
            const toast = document.createElement('div');
            toast.className = `gi-toast gi-toast-${type}`;
            toast.innerHTML = `
                <div class="gi-toast-content">
                    <span class="gi-toast-message">${this.escapeHtml(message)}</span>
                    <button class="gi-toast-close" aria-label="閉じる">×</button>
                </div>
            `;
            
            document.body.appendChild(toast);
            
            // アニメーション
            requestAnimationFrame(() => {
                toast.classList.add('gi-toast-show');
            });
            
            // 閉じるボタン
            toast.querySelector('.gi-toast-close').addEventListener('click', () => {
                this.hideToast(toast);
            });
            
            // 自動削除
            setTimeout(() => {
                this.hideToast(toast);
            }, this.config.toastDuration);
            
            return toast;
        };

        // トースト非表示
        this.hideToast = function(toast) {
            toast.classList.remove('gi-toast-show');
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        };

        // AJAX関数（統一API）
        this.ajax = function(action, data = {}, options = {}) {
            const url = options.url || this.config.apiEndpoint;
            
            const requestData = {
                action: action,
                nonce: window.gi_ajax?.nonce || options.nonce,
                ...data
            };

            return fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                    ...options.headers
                },
                body: new URLSearchParams(requestData).toString(),
                ...options
            }).then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            });
        };

        // デバッグ関数
        this.debug = function(message, ...args) {
            if (window.location.hostname === 'localhost' || window.location.search.includes('debug=1')) {
                console.log(`[Grant Insight] ${message}`, ...args);
            }
        };
    },

    /**
     * ==========================================================================
     * 検索機能（統合版）
     * ==========================================================================
     */
    setupSearch() {
        if (!this.elements.searchInputs.length) return;

        this.elements.searchInputs.forEach(input => {
            // 検索入力のデバウンス処理
            const debouncedSearch = this.debounce((value) => {
                if (value.length >= this.config.searchMinLength) {
                    this.performSearch(value);
                    this.showSearchSuggestions(value);
                } else {
                    this.hideSearchSuggestions();
                }
            }, this.config.debounceDelay);

            // 入力イベント
            input.addEventListener('input', (e) => {
                debouncedSearch(e.target.value);
            });

            // エンターキーでの検索実行
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    this.executeSearch(e.target.value);
                }
                
                // キーボードナビゲーション
                if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                    this.handleSuggestionNavigation(e);
                }

                if (e.key === 'Escape') {
                    this.hideSearchSuggestions();
                }
            });

            // フォーカス時の処理
            input.addEventListener('focus', () => {
                this.state.lastFocusedInput = input;
                if (input.value.length >= this.config.searchMinLength) {
                    this.showSearchSuggestions(input.value);
                }
            });

            // フォーカス外時の処理
            input.addEventListener('blur', () => {
                setTimeout(() => this.hideSearchSuggestions(), 150);
            });
        });
    },

    /**
     * 検索実行
     */
    performSearch(query) {
        this.ajax('gi_search_grants', { query })
            .then(response => {
                if (response.success) {
                    this.updateSearchResults(response.data);
                } else {
                    this.showToast(response.data || '検索中にエラーが発生しました', 'error');
                }
            })
            .catch(error => {
                console.error('Search error:', error);
                this.showToast('検索中にエラーが発生しました', 'error');
            });
    },

    /**
     * 検索候補表示
     */
    showSearchSuggestions(query) {
        this.ajax('gi_get_search_suggestions', { query })
            .then(response => {
                if (response.success) {
                    this.renderSearchSuggestions(response.data);
                }
            })
            .catch(error => {
                this.debug('Search suggestions error:', error);
            });
    },

    /**
     * 検索候補のレンダリング
     */
    renderSearchSuggestions(suggestions) {
        if (!suggestions || !suggestions.length) {
            this.hideSearchSuggestions();
            return;
        }

        let container = this.elements.searchSuggestions;
        if (!container) {
            container = document.createElement('div');
            container.className = 'gi-search-suggestions';
            this.elements.searchSuggestions = container;
            
            if (this.elements.searchContainer) {
                this.elements.searchContainer.appendChild(container);
            } else {
                // フォールバック：最初の検索入力の親に追加
                const firstInput = this.elements.searchInputs[0];
                if (firstInput && firstInput.parentNode) {
                    firstInput.parentNode.appendChild(container);
                }
            }
        }

        container.innerHTML = suggestions.map((item, index) => `
            <div class="gi-suggestion-item" 
                 data-value="${this.escapeHtml(item.value)}"
                 data-index="${index}">
                <i class="fas fa-search gi-suggestion-icon"></i>
                <span class="gi-suggestion-text">${this.escapeHtml(item.label)}</span>
            </div>
        `).join('');

        container.style.display = 'block';
        container.classList.add('gi-suggestions-active');

        // クリックイベントの設定
        container.querySelectorAll('.gi-suggestion-item').forEach(item => {
            item.addEventListener('click', (e) => {
                const value = e.currentTarget.dataset.value;
                this.executeSearch(value);
                this.hideSearchSuggestions();
            });
        });
    },

    /**
     * 検索候補のキーボードナビゲーション
     */
    handleSuggestionNavigation(e) {
        const container = this.elements.searchSuggestions;
        if (!container || !container.classList.contains('gi-suggestions-active')) return;

        const items = container.querySelectorAll('.gi-suggestion-item');
        if (!items.length) return;

        const currentActive = container.querySelector('.gi-suggestion-active');
        let newIndex = 0;

        if (currentActive) {
            const currentIndex = parseInt(currentActive.dataset.index);
            if (e.key === 'ArrowDown') {
                newIndex = (currentIndex + 1) % items.length;
            } else if (e.key === 'ArrowUp') {
                newIndex = currentIndex > 0 ? currentIndex - 1 : items.length - 1;
            }
            currentActive.classList.remove('gi-suggestion-active');
        }

        e.preventDefault();
        items[newIndex].classList.add('gi-suggestion-active');
    },

    /**
     * 検索実行
     */
    executeSearch(query) {
        const input = this.elements.searchInputs[0];
        if (input) {
            input.value = query;
        }
        
        // 検索結果ページに移動またはAJAXで結果更新
        const currentPath = window.location.pathname;
        if (currentPath === '/' || currentPath.includes('grants')) {
            this.performSearch(query);
        } else {
            window.location.href = `/grants/?search=${encodeURIComponent(query)}`;
        }
        
        this.hideSearchSuggestions();
    },

    /**
     * 検索候補を隠す
     */
    hideSearchSuggestions() {
        const container = this.elements.searchSuggestions;
        if (container) {
            container.classList.remove('gi-suggestions-active');
            setTimeout(() => {
                container.style.display = 'none';
            }, 150);
        }
    },

    /**
     * ==========================================================================
     * フィルター機能（統合版）
     * ==========================================================================
     */
    setupFilters() {
        // フィルターボタンのイベント
        this.elements.filterButtons.forEach(button => {
            button.addEventListener('click', () => {
                this.toggleFilter(button);
            });
        });

        // フィルター表示ボタン
        if (this.elements.filterTrigger) {
            this.elements.filterTrigger.addEventListener('click', () => {
                this.showFilterBottomSheet();
            });
        }

        // デリゲートイベント（動的要素用）
        document.addEventListener('click', (e) => {
            // 比較実行
            if (e.target.matches('.execute-comparison, .gi-btn-filter-apply')) {
                e.preventDefault();
                this.handleFilterApply(e.target);
            }

            // 比較クリア
            if (e.target.matches('.clear-comparison, .gi-btn-filter-clear')) {
                e.preventDefault();
                this.clearFilters();
            }

            // フィルターシート閉じる
            if (e.target.matches('.gi-filter-sheet-close')) {
                this.hideFilterBottomSheet();
            }
        });
    },

    /**
     * フィルター切り替え
     */
    toggleFilter(button) {
        const filterType = button.dataset.filter || button.dataset.type;
        const filterValue = button.dataset.value;
        
        if (!filterType || !filterValue) return;

        button.classList.toggle('active');
        button.classList.toggle('selected'); // 互換性のため
        
        const filterKey = `${filterType}-${filterValue}`;
        
        if (button.classList.contains('active')) {
            this.state.activeFilters.set(filterKey, {
                type: filterType,
                value: filterValue,
                label: button.textContent.trim()
            });
        } else {
            this.state.activeFilters.delete(filterKey);
        }

        // リアルタイムフィルタリング
        this.applyFilters();
    },

    /**
     * フィルター適用
     */
    applyFilters() {
        const filters = this.buildFilterObject();
        
        this.ajax('gi_filter_grants', { filters })
            .then(response => {
                if (response.success) {
                    this.updateSearchResults(response.data);
                    const count = response.data.total || response.data.count || 0;
                    this.showToast(`${count}件の助成金が見つかりました`, 'success');
                    this.updateURL(filters);
                } else {
                    this.showToast(response.data || 'フィルター処理中にエラーが発生しました', 'error');
                }
            })
            .catch(error => {
                console.error('Filter error:', error);
                this.showToast('フィルター処理中にエラーが発生しました', 'error');
            });

        this.hideFilterBottomSheet();
    },

    /**
     * フィルターオブジェクトの構築
     */
    buildFilterObject() {
        const filters = {};
        
        this.state.activeFilters.forEach(filter => {
            if (!filters[filter.type]) {
                filters[filter.type] = [];
            }
            filters[filter.type].push(filter.value);
        });

        return filters;
    },

    /**
     * URLの更新（履歴管理）
     */
    updateURL(filters) {
        const params = new URLSearchParams();
        
        Object.keys(filters).forEach(type => {
            if (filters[type].length > 0) {
                params.set(type, filters[type].join(','));
            }
        });
        
        const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
        window.history.pushState({}, '', newUrl);
    },

    /**
     * フィルタークリア
     */
    clearFilters() {
        this.state.activeFilters.clear();
        
        // UI状態のリセット
        document.querySelectorAll('.gi-filter-chip.active, .filter-button.active, .filter-chip.selected').forEach(button => {
            button.classList.remove('active', 'selected');
        });

        this.applyFilters();
    },

    /**
     * フィルター適用ハンドラー
     */
    handleFilterApply(target) {
        if (target.classList.contains('execute-comparison')) {
            this.executeComparison();
        } else {
            this.applyFilters();
        }
    },

    /**
     * ==========================================================================
     * 比較機能
     * ==========================================================================
     */
    setupComparison() {
        // 比較チェックボックスのイベント（デリゲート）
        document.addEventListener('change', (e) => {
            if (e.target.matches('.grant-compare-checkbox')) {
                const grantId = e.target.dataset.grantId;
                const grantTitle = e.target.dataset.grantTitle || e.target.closest('.grant-card')?.querySelector('.card-title, .grant-card-title')?.textContent?.trim();
                
                if (e.target.checked) {
                    this.addComparisonItem(grantId, grantTitle);
                } else {
                    this.removeComparisonItem(grantId);
                }
            }
        });

        // ローカルストレージから復元
        this.loadComparisonFromStorage();
    },

    /**
     * 比較アイテム追加
     */
    addComparisonItem(id, title) {
        if (this.state.comparisonItems.length >= this.config.maxComparisonItems) {
            this.showToast(`比較は最大${this.config.maxComparisonItems}件までです`, 'warning');
            
            // チェックボックスを解除
            const checkbox = document.querySelector(`[data-grant-id="${id}"]`);
            if (checkbox) checkbox.checked = false;
            return false;
        }
        
        if (this.state.comparisonItems.find(item => item.id === id)) {
            return false; // 既に追加済み
        }
        
        this.state.comparisonItems.push({ id, title: title || `助成金 ID: ${id}` });
        this.updateComparisonWidget();
        this.saveComparisonToStorage();
        this.showToast('比較リストに追加しました', 'success');
        
        return true;
    },

    /**
     * 比較アイテム削除
     */
    removeComparisonItem(id) {
        this.state.comparisonItems = this.state.comparisonItems.filter(item => item.id !== id);
        this.updateComparisonWidget();
        this.saveComparisonToStorage();
        
        // チェックボックスの状態を更新
        const checkbox = document.querySelector(`[data-grant-id="${id}"]`);
        if (checkbox) checkbox.checked = false;
    },

    /**
     * 比較ウィジェット更新
     */
    updateComparisonWidget() {
        if (this.state.comparisonItems.length === 0) {
            this.hideComparisonWidget();
            return;
        }
        
        this.elements.body.classList.add('has-comparison-bar');
        
        let container = this.elements.comparisonBar;
        if (!container) {
            container = document.createElement('div');
            container.className = 'gi-comparison-bar';
            this.elements.comparisonBar = container;
            this.elements.body.appendChild(container);
        }

        container.innerHTML = `
            <div class="gi-comparison-bar-inner">
                <div class="gi-comparison-items">
                    ${this.state.comparisonItems.map(item => `
                        <div class="gi-comparison-item" data-id="${item.id}">
                            <span class="gi-item-title">${this.escapeHtml(item.title)}</span>
                            <button class="gi-remove-item" data-id="${item.id}" aria-label="削除">×</button>
                        </div>
                    `).join('')}
                </div>
                <div class="gi-comparison-actions">
                    <button class="execute-comparison gi-btn gi-btn-primary">
                        比較する (${this.state.comparisonItems.length}件)
                    </button>
                    <button class="clear-comparison gi-btn gi-btn-secondary">クリア</button>
                </div>
            </div>
        `;
        
        container.classList.add('gi-comparison-active');

        // 削除ボタンのイベント
        container.querySelectorAll('.gi-remove-item').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = e.target.dataset.id;
                this.removeComparisonItem(id);
            });
        });
    },

    /**
     * 比較ウィジェット非表示
     */
    hideComparisonWidget() {
        if (this.elements.comparisonBar) {
            this.elements.comparisonBar.classList.remove('gi-comparison-active');
            this.elements.body.classList.remove('has-comparison-bar');
        }
    },

    /**
     * 比較実行
     */
    executeComparison() {
        if (this.state.comparisonItems.length < 2) {
            this.showToast('比較するには2件以上選択してください', 'warning');
            return;
        }
        
        const ids = this.state.comparisonItems.map(item => item.id).join(',');
        window.location.href = `/compare?grants=${ids}`;
    },

    /**
     * 比較データの保存
     */
    saveComparisonToStorage() {
        try {
            localStorage.setItem('grant_comparison', JSON.stringify(this.state.comparisonItems));
        } catch (e) {
            this.debug('Failed to save comparison data:', e);
        }
    },

    /**
     * 比較データの読み込み
     */
    loadComparisonFromStorage() {
        try {
            const saved = localStorage.getItem('grant_comparison');
            if (saved) {
                this.state.comparisonItems = JSON.parse(saved);
                this.updateComparisonWidget();
                
                // チェックボックスの状態を復元
                this.state.comparisonItems.forEach(item => {
                    const checkbox = document.querySelector(`[data-grant-id="${item.id}"]`);
                    if (checkbox) checkbox.checked = true;
                });
            }
        } catch (e) {
            this.debug('Failed to load comparison data:', e);
        }
    },

    /**
     * ==========================================================================
     * モバイル最適化機能
     * ==========================================================================
     */
    setupMobile() {
        this.setupMobileHeader();
        this.setupTouchOptimizations();
        this.setupCardInteractions();
        this.setupMobileMenu();
    },

    /**
     * モバイルヘッダーのセットアップ
     */
    setupMobileHeader() {
        if (!this.elements.header && window.innerWidth <= 768) {
            this.elements.header = this.createMobileHeader();
        }
        
        if (this.elements.header) {
            this.state.headerHeight = this.elements.header.offsetHeight;
            
            // スマートヘッダー表示/非表示
            const scrollHandler = this.throttle(() => {
                const currentScrollY = window.scrollY;
                const scrollDelta = Math.abs(currentScrollY - this.state.lastScrollY);
                
                if (scrollDelta < 10) return;
                
                if (currentScrollY > this.state.lastScrollY && currentScrollY > this.state.headerHeight) {
                    this.elements.header.classList.add('gi-header-hidden');
                } else {
                    this.elements.header.classList.remove('gi-header-hidden');
                }
                
                this.state.lastScrollY = currentScrollY;
            }, 10);
            
            window.addEventListener('scroll', scrollHandler, { passive: true });
        }
    },

    /**
     * モバイルヘッダーの作成
     */
    createMobileHeader() {
        const header = document.createElement('div');
        header.className = 'gi-mobile-header';
        header.innerHTML = `
            <div class="gi-mobile-header-content">
                <a href="/" class="gi-logo-mobile">助成金検索</a>
                <div class="gi-search-container-mobile">
                    <input type="text" class="gi-search-input" placeholder="助成金を検索...">
                </div>
                <button class="gi-filter-trigger" aria-label="フィルター">
                    <i class="fas fa-sliders-h"></i>
                </button>
            </div>
        `;
        
        document.body.insertBefore(header, document.body.firstChild);
        
        // 新しい検索入力を要素キャッシュに追加
        const newSearchInput = header.querySelector('.gi-search-input');
        if (newSearchInput) {
            // 既存の検索設定を適用
            this.setupSearchForElement(newSearchInput);
        }
        
        return header;
    },

    /**
     * 単一要素への検索設定（モバイルヘッダー用）
     */
    setupSearchForElement(input) {
        const debouncedSearch = this.debounce((value) => {
            if (value.length >= this.config.searchMinLength) {
                this.showSearchSuggestions(value);
            }
        }, this.config.debounceDelay);

        input.addEventListener('input', (e) => debouncedSearch(e.target.value));
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.executeSearch(e.target.value);
            }
        });
    },

    /**
     * モバイルメニュー
     */
    setupMobileMenu() {
        // モバイルメニュートグル
        document.addEventListener('click', (e) => {
            if (e.target.matches('.mobile-menu-toggle, .gi-menu-toggle')) {
                this.elements.body.classList.toggle('gi-mobile-menu-open');
                e.target.classList.toggle('gi-menu-active');
            }

            // メニュー外クリックで閉じる
            if (!e.target.closest('.gi-mobile-menu, .mobile-menu, .mobile-menu-toggle, .gi-menu-toggle')) {
                this.elements.body.classList.remove('gi-mobile-menu-open');
                document.querySelectorAll('.mobile-menu-toggle, .gi-menu-toggle').forEach(toggle => {
                    toggle.classList.remove('gi-menu-active');
                });
            }
        });
    },

    /**
     * タッチ最適化
     */
    setupTouchOptimizations() {
        // タッチデバイス検出
        const isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
        
        if (isTouchDevice) {
            this.elements.body.classList.add('gi-touch-device');
            
            // タッチフィードバック
            this.setupTouchFeedback();
            
            // プルトゥリフレッシュ
            this.setupPullToRefresh();
        }
    },

    /**
     * タッチフィードバック
     */
    setupTouchFeedback() {
        const touchElements = document.querySelectorAll('button, .btn, .gi-filter-chip, .category-card, .grant-card');
        
        touchElements.forEach(element => {
            element.addEventListener('touchstart', () => {
                element.classList.add('gi-touch-active');
            });

            element.addEventListener('touchend', () => {
                setTimeout(() => {
                    element.classList.remove('gi-touch-active');
                }, 150);
            });
        });
    },

    /**
     * カードインタラクション
     */
    setupCardInteractions() {
        document.addEventListener('click', (e) => {
            const card = e.target.closest('.gi-grant-card-enhanced, .grant-card, .category-card');
            if (!card) return;

            // ボタンやリンク以外をクリックした場合、詳細ページに移動
            if (!e.target.matches('button, .btn, a, input, .gi-bookmark-btn')) {
                const link = card.querySelector('a[href]');
                if (link) {
                    window.location.href = link.href;
                }
            }
        });
    },

    /**
     * プルトゥリフレッシュ
     */
    setupPullToRefresh() {
        let startY = 0;
        let currentY = 0;
        let isRefreshing = false;

        document.addEventListener('touchstart', (e) => {
            if (window.scrollY === 0 && !isRefreshing) {
                startY = e.touches[0].clientY;
            }
        }, { passive: true });

        document.addEventListener('touchmove', (e) => {
            if (window.scrollY === 0 && startY > 0) {
                currentY = e.touches[0].clientY;
                const pullDistance = currentY - startY;
                
                if (pullDistance > 100 && !isRefreshing) {
                    this.showPullToRefreshIndicator();
                }
            }
        }, { passive: true });

        document.addEventListener('touchend', () => {
            if (currentY - startY > 100 && !isRefreshing) {
                this.triggerRefresh();
            }
            startY = 0;
            currentY = 0;
        });
    },

    /**
     * リフレッシュ実行
     */
    triggerRefresh() {
        this.showToast('更新中...', 'info');
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    },

    /**
     * プルトゥリフレッシュインジケーター表示
     */
    showPullToRefreshIndicator() {
        // 実装は簡略化（必要に応じて詳細実装）
        this.debug('Pull to refresh triggered');
    },

    /**
     * ==========================================================================
     * アニメーション・スクロール効果
     * ==========================================================================
     */
    setupAnimations() {
        this.setupScrollAnimations();
        this.setupSmoothScroll();
        this.setupBackToTop();
    },

    /**
     * スクロールアニメーション
     */
    setupScrollAnimations() {
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('gi-animated', 'gi-fade-in');
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });
            
            const animateElements = document.querySelectorAll('.category-card, .grant-card, .prefecture-item');
            animateElements.forEach(el => {
                el.classList.add('gi-animate-on-scroll');
                observer.observe(el);
            });
        }
    },

    /**
     * スムーズスクロール
     */
    setupSmoothScroll() {
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a[href^="#"]');
            if (!link) return;

            const targetId = link.getAttribute('href');
            const target = document.querySelector(targetId);
            
            if (target) {
                e.preventDefault();
                const headerOffset = this.state.headerHeight || 80;
                const targetPosition = target.offsetTop - headerOffset;
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    },

    /**
     * トップへ戻るボタン
     */
    setupBackToTop() {
        let backToTopButton = document.querySelector('.gi-back-to-top, .back-to-top');
        
        if (!backToTopButton) {
            backToTopButton = document.createElement('button');
            backToTopButton.className = 'gi-back-to-top';
            backToTopButton.innerHTML = '<i class="fas fa-arrow-up"></i>';
            backToTopButton.setAttribute('aria-label', 'ページトップへ戻る');
            document.body.appendChild(backToTopButton);
        }
        
        // スクロール監視
        const scrollHandler = this.throttle(() => {
            if (window.scrollY > 300) {
                backToTopButton.classList.add('gi-back-to-top-visible');
            } else {
                backToTopButton.classList.remove('gi-back-to-top-visible');
            }
        }, 100);
        
        window.addEventListener('scroll', scrollHandler, { passive: true });
        
        // クリックイベント
        backToTopButton.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    },

    /**
     * ==========================================================================
     * フォーム拡張
     * ==========================================================================
     */
    setupForms() {
        this.setupFormValidation();
        this.setupFormEnhancements();
    },

    /**
     * フォームバリデーション
     */
    setupFormValidation() {
        document.addEventListener('submit', (e) => {
            const form = e.target.closest('form');
            if (!form || form.classList.contains('gi-no-validation')) return;

            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            let firstInvalidField = null;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('gi-field-error');
                    
                    if (!firstInvalidField) {
                        firstInvalidField = field;
                    }
                } else {
                    field.classList.remove('gi-field-error');
                }
            });

            if (!isValid) {
                e.preventDefault();
                this.showToast('必須項目を入力してください', 'error');
                
                if (firstInvalidField) {
                    firstInvalidField.focus();
                }
            }
        });

        // エラー状態のクリア
        document.addEventListener('input', (e) => {
            if (e.target.matches('input, textarea, select')) {
                e.target.classList.remove('gi-field-error');
            }
        });
    },

    /**
     * フォーム拡張機能
     */
    setupFormEnhancements() {
        // 自動保存（下書き機能）
        this.setupAutoSave();
        
        // ファイル選択の改善
        this.setupFileInputs();
    },

    /**
     * 自動保存機能
     */
    setupAutoSave() {
        const autoSaveFields = document.querySelectorAll('[data-autosave]');
        
        autoSaveFields.forEach(field => {
            const saveKey = field.dataset.autosave;
            
            // 保存されたデータを復元
            const savedValue = localStorage.getItem(`gi_autosave_${saveKey}`);
            if (savedValue && !field.value) {
                field.value = savedValue;
            }
            
            // 変更時に自動保存
            const saveHandler = this.debounce(() => {
                try {
                    localStorage.setItem(`gi_autosave_${saveKey}`, field.value);
                    this.debug(`Auto-saved: ${saveKey}`);
                } catch (e) {
                    this.debug('Auto-save error:', e);
                }
            }, 1000);
            
            field.addEventListener('input', saveHandler);
        });
    },

    /**
     * ファイル入力の改善
     */
    setupFileInputs() {
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', (e) => {
                const files = e.target.files;
                if (files.length > 0) {
                    const fileNames = Array.from(files).map(file => file.name).join(', ');
                    this.showToast(`選択されたファイル: ${fileNames}`, 'info');
                }
            });
        });
    },

    /**
     * ==========================================================================
     * みんなの銀行インタラクション機能
     * プロフェッショナル・インタラクション・システム
     * ==========================================================================
     */
    setupMinnaBankInteractions() {
        // フィルター・インタラクション
        this.setupAdvancedFiltering();
        
        // カード・インタラクション
        this.setupProfessionalCards();
        
        // ステータス・バー・システム
        this.setupStatusBars();
        
        // プロフェッショナル・ナビゲーション
        this.setupProfessionalNavigation();
        
        this.debug('Minna Bank interactions initialized');
    },

    /**
     * 高度フィルタリング・システム
     */
    setupAdvancedFiltering() {
        // トップフィルターシステム
        this.initTopFilterSystem();
        
        // インテリジェント・フィルター
        this.setupIntelligentFilters();
        
        // フィルター・プリセット
        this.setupFilterPresets();
    },

    /**
     * トップフィルターシステムの初期化
     */
    initTopFilterSystem() {
        const topFilters = document.querySelector('.mb-top-filters');
        if (!topFilters) return;
        
        // フィルターカテゴリーの設定
        Object.keys(this.config.filterCategories).forEach(category => {
            const filterGroup = topFilters.querySelector(`[data-filter-group="${category}"]`);
            if (filterGroup) {
                this.setupFilterGroup(filterGroup, category);
            }
        });
        
        // フィルター同期
        this.syncFilterStates();
    },

    /**
     * フィルターグループの設定
     */
    setupFilterGroup(group, category) {
        const chips = group.querySelectorAll('.mb-filter-chip');
        
        chips.forEach(chip => {
            chip.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleFilterChipClick(chip, category);
            });
            
            // キーボード対応
            chip.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.handleFilterChipClick(chip, category);
                }
            });
        });
    },

    /**
     * フィルターチップのクリック処理
     */
    handleFilterChipClick(chip, category) {
        const value = chip.dataset.value;
        const isMultiSelect = chip.closest('[data-multi-select="true"]');
        
        if (!isMultiSelect) {
            // 単一選択：同じグループの他を無効化
            const siblings = chip.parentNode.querySelectorAll('.mb-filter-chip');
            siblings.forEach(sibling => {
                if (sibling !== chip) {
                    sibling.classList.remove('active');
                }
            });
        }
        
        // 現在のチップの状態切り替え
        chip.classList.toggle('active');
        
        // フィルター状態の更新
        this.updateFilterState(category, value, chip.classList.contains('active'));
        
        // ステータスバーの更新
        this.updateStatusBars();
        
        // フィルター適用
        this.applyAdvancedFilters();
        
        // アニメーション効果
        this.animateFilterChange(chip);
    },

    /**
     * フィルター状態の更新
     */
    updateFilterState(category, value, isActive) {
        if (!this.state.advancedFilters) {
            this.state.advancedFilters = new Map();
        }
        
        if (!this.state.advancedFilters.has(category)) {
            this.state.advancedFilters.set(category, new Set());
        }
        
        const categoryFilters = this.state.advancedFilters.get(category);
        
        if (isActive) {
            categoryFilters.add(value);
        } else {
            categoryFilters.delete(value);
        }
        
        this.debug(`Filter updated: ${category} = ${Array.from(categoryFilters)}`);
    },

    /**
     * 高度フィルター適用
     */
    applyAdvancedFilters() {
        const filters = this.buildAdvancedFilterObject();
        
        // ローディング状態の表示
        this.showFilterLoading();
        
        this.ajax('mb_filter_grants_advanced', { filters })
            .then(response => {
                if (response.success) {
                    this.updateGrantsDisplay(response.data);
                    this.updateFilterSummary(response.data);
                    this.showFilterSuccess(response.data.total);
                } else {
                    this.showFilterError(response.data);
                }
            })
            .catch(error => {
                console.error('Advanced filter error:', error);
                this.showFilterError('フィルター処理中にエラーが発生しました');
            })
            .finally(() => {
                this.hideFilterLoading();
            });
    },

    /**
     * 高度フィルターオブジェクトの構築
     */
    buildAdvancedFilterObject() {
        const filters = {};
        
        if (this.state.advancedFilters) {
            this.state.advancedFilters.forEach((values, category) => {
                if (values.size > 0) {
                    filters[category] = Array.from(values);
                }
            });
        }
        
        return filters;
    },

    /**
     * プロフェッショナル・カード・システム
     */
    setupProfessionalCards() {
        // カード・ホバー・エフェクト
        this.setupCardHoverEffects();
        
        // カード・クイック・アクション
        this.setupCardQuickActions();
        
        // カード・詳細・プレビュー
        this.setupCardPreview();
        
        // カード・ブックマーク・システム
        this.setupCardBookmarks();
    },

    /**
     * カード・ホバー・エフェクト
     */
    setupCardHoverEffects() {
        document.addEventListener('mouseenter', (e) => {
            const card = e.target.closest('.mb-grant-card, .grant-card');
            if (!card) return;
            
            // ホバー・アニメーション
            card.style.transform = 'translateY(-2px)';
            card.style.boxShadow = 'var(--mb-shadow-hover)';
            
            // 関連情報の表示
            this.showCardHoverInfo(card);
        }, true);

        document.addEventListener('mouseleave', (e) => {
            const card = e.target.closest('.mb-grant-card, .grant-card');
            if (!card) return;
            
            // ホバー・リセット
            card.style.transform = '';
            card.style.boxShadow = '';
            
            // 関連情報を隠す
            this.hideCardHoverInfo(card);
        }, true);
    },

    /**
     * カード・ホバー・情報表示
     */
    showCardHoverInfo(card) {
        const hoverInfo = card.querySelector('.mb-card-hover-info');
        if (hoverInfo) {
            hoverInfo.classList.add('visible');
        }
        
        // 類似助成金のプリローディング
        const grantId = card.dataset.grantId;
        if (grantId) {
            this.preloadSimilarGrants(grantId);
        }
    },

    /**
     * カード・クイック・アクション
     */
    setupCardQuickActions() {
        document.addEventListener('click', (e) => {
            // ブックマーク・ボタン
            if (e.target.matches('.mb-bookmark-btn, .bookmark-btn')) {
                e.preventDefault();
                this.handleBookmarkClick(e.target);
            }
            
            // クイック・比較
            if (e.target.matches('.mb-quick-compare, .quick-compare')) {
                e.preventDefault();
                this.handleQuickCompare(e.target);
            }
            
            // 詳細プレビュー
            if (e.target.matches('.mb-preview-btn, .preview-btn')) {
                e.preventDefault();
                this.showGrantPreview(e.target);
            }
        });
    },

    /**
     * ブックマーク・ハンドラー
     */
    handleBookmarkClick(button) {
        const grantId = button.dataset.grantId || button.closest('.grant-card').dataset.grantId;
        const isBookmarked = button.classList.contains('bookmarked');
        
        // アニメーション効果
        button.style.transform = 'scale(0.8)';
        setTimeout(() => {
            button.style.transform = '';
        }, 150);
        
        // ブックマーク状態の切り替え
        this.toggleBookmark(grantId, !isBookmarked)
            .then(success => {
                if (success) {
                    button.classList.toggle('bookmarked');
                    const action = isBookmarked ? '削除しました' : '追加しました';
                    this.showMinnaBankToast(`ブックマークを${action}`, 'success');
                }
            });
    },

    /**
     * ブックマーク切り替え
     */
    toggleBookmark(grantId, add) {
        return this.ajax('mb_toggle_bookmark', { grant_id: grantId, add })
            .then(response => {
                return response.success;
            })
            .catch(error => {
                this.showMinnaBankToast('ブックマークの更新に失敗しました', 'error');
                return false;
            });
    },

    /**
     * ステータス・バー・システム
     */
    setupStatusBars() {
        // ホリゾンタル・ステータス・バーの初期化
        this.initHorizontalStatusBars();
        
        // ステータス・インジケーター
        this.setupStatusIndicators();
        
        // プログレス・バー
        this.setupProgressBars();
    },

    /**
     * ホリゾンタル・ステータス・バーの初期化
     */
    initHorizontalStatusBars() {
        const statusBars = document.querySelectorAll('.mb-status-bar-horizontal');
        
        statusBars.forEach(bar => {
            this.setupSingleStatusBar(bar);
        });
    },

    /**
     * 単一ステータス・バーの設定
     */
    setupSingleStatusBar(bar) {
        const items = bar.querySelectorAll('.mb-status-item');
        
        items.forEach((item, index) => {
            // アニメーション遅延の設定
            item.style.animationDelay = `${index * 0.1}s`;
            
            // インタラクティブ要素の設定
            if (item.classList.contains('interactive')) {
                this.setupInteractiveStatusItem(item);
            }
        });
        
        // バー全体のアニメーション
        this.animateStatusBar(bar);
    },

    /**
     * インタラクティブ・ステータス・アイテム
     */
    setupInteractiveStatusItem(item) {
        item.addEventListener('click', () => {
            const action = item.dataset.action;
            const value = item.dataset.value;
            
            switch (action) {
                case 'filter':
                    this.applyQuickFilter(value);
                    break;
                case 'sort':
                    this.applySorting(value);
                    break;
                case 'view':
                    this.changeViewMode(value);
                    break;
            }
            
            // アクティブ状態の管理
            item.parentNode.querySelectorAll('.active').forEach(active => {
                active.classList.remove('active');
            });
            item.classList.add('active');
        });
    },

    /**
     * ステータス・バー更新
     */
    updateStatusBars() {
        const statusBars = document.querySelectorAll('.mb-status-bar-horizontal');
        
        statusBars.forEach(bar => {
            this.updateSingleStatusBar(bar);
        });
    },

    /**
     * 単一ステータス・バー更新
     */
    updateSingleStatusBar(bar) {
        const filters = this.buildAdvancedFilterObject();
        const activeCount = Object.keys(filters).length;
        
        // アクティブ・フィルター数の表示
        const filterCount = bar.querySelector('.mb-filter-count');
        if (filterCount) {
            filterCount.textContent = activeCount;
            filterCount.classList.toggle('has-filters', activeCount > 0);
        }
        
        // ステータス・インジケーターの更新
        const indicators = bar.querySelectorAll('.mb-status-indicator');
        indicators.forEach(indicator => {
            this.updateStatusIndicator(indicator, filters);
        });
    },

    /**
     * プロフェッショナル・ナビゲーション
     */
    setupProfessionalNavigation() {
        // ブレッドクラム・システム
        this.setupBreadcrumbs();
        
        // ページネーション・システム
        this.setupProfessionalPagination();
        
        // ナビゲーション・ショートカット
        this.setupNavigationShortcuts();
    },

    /**
     * ブレッドクラム・システム
     */
    setupBreadcrumbs() {
        const breadcrumbs = document.querySelector('.mb-breadcrumbs');
        if (!breadcrumbs) return;
        
        // 動的ブレッドクラム生成
        this.generateBreadcrumbs();
        
        // ブレッドクラム・ナビゲーション
        breadcrumbs.addEventListener('click', (e) => {
            const link = e.target.closest('.mb-breadcrumb-link');
            if (link && link.dataset.path) {
                this.navigateToBreadcrumb(link.dataset.path);
            }
        });
    },

    /**
     * ==========================================================================
     * 高度機能システム
     * ==========================================================================
     */
    setupAdvancedFeatures() {
        // インテリジェント検索
        this.setupIntelligentSearch();
        
        // パーソナライゼーション
        this.setupPersonalization();
        
        // 分析・トラッキング
        this.setupAnalytics();
        
        // A/Bテスト・システム
        this.setupABTesting();
        
        this.debug('Advanced features initialized');
    },

    /**
     * インテリジェント検索
     */
    setupIntelligentSearch() {
        // 検索候補の改善
        this.enhanceSearchSuggestions();
        
        // 検索履歴
        this.setupSearchHistory();
        
        // 関連検索
        this.setupRelatedSearches();
    },

    /**
     * 検索候補の改善
     */
    enhanceSearchSuggestions() {
        // 既存の検索機能を拡張
        const originalShowSuggestions = this.showSearchSuggestions.bind(this);
        
        this.showSearchSuggestions = (query) => {
            // インテリジェント候補の取得
            this.getIntelligentSuggestions(query)
                .then(suggestions => {
                    this.renderIntelligentSuggestions(suggestions);
                })
                .catch(() => {
                    // フォールバック：元の候補システム
                    originalShowSuggestions(query);
                });
        };
    },

    /**
     * インテリジェント候補の取得
     */
    getIntelligentSuggestions(query) {
        return this.ajax('mb_get_intelligent_suggestions', { 
            query,
            user_history: this.getUserSearchHistory(),
            current_filters: this.buildAdvancedFilterObject()
        });
    },

    /**
     * パーソナライゼーション
     */
    setupPersonalization() {
        // ユーザー・プリファレンス
        this.loadUserPreferences();
        
        // 閲覧履歴ベースの推奨
        this.setupPersonalizedRecommendations();
        
        // カスタマイズ可能UI
        this.setupCustomizableInterface();
    },

    /**
     * みんなの銀行トースト通知
     */
    showMinnaBankToast(message, type = 'info', duration = null) {
        const toastDuration = duration || this.config.toastDuration;
        
        // 既存のトーストを削除
        document.querySelectorAll('.mb-toast').forEach(toast => toast.remove());
        
        const toast = document.createElement('div');
        toast.className = `mb-toast mb-toast-${type}`;
        
        // アイコンの設定
        const iconMap = {
            'success': '✓',
            'error': '✕',
            'warning': '⚠',
            'info': 'ℹ'
        };
        
        toast.innerHTML = `
            <div class="mb-toast-content">
                <div class="mb-toast-icon">${iconMap[type] || iconMap.info}</div>
                <div class="mb-toast-message">${this.escapeHtml(message)}</div>
                <button class="mb-toast-close" aria-label="閉じる">×</button>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        // アニメーション
        requestAnimationFrame(() => {
            toast.classList.add('mb-toast-show');
        });
        
        // クローズボタン
        toast.querySelector('.mb-toast-close').addEventListener('click', () => {
            this.hideMinnaBankToast(toast);
        });
        
        // 自動削除
        setTimeout(() => {
            this.hideMinnaBankToast(toast);
        }, toastDuration);
        
        return toast;
    },

    /**
     * みんなの銀行トースト非表示
     */
    hideMinnaBankToast(toast) {
        toast.classList.remove('mb-toast-show');
        toast.classList.add('mb-toast-hide');
        
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    },

    /**
     * フィルター・ローディング表示
     */
    showFilterLoading() {
        const container = this.elements.grantsGrid || document.querySelector('.grants-grid');
        if (!container) return;
        
        // ローディング・オーバーレイ
        const overlay = document.createElement('div');
        overlay.className = 'mb-loading-overlay';
        overlay.innerHTML = `
            <div class="mb-loading-spinner">
                <div class="mb-spinner"></div>
                <p class="mb-loading-text">検索中...</p>
            </div>
        `;
        
        container.style.position = 'relative';
        container.appendChild(overlay);
        
        // アニメーション
        requestAnimationFrame(() => {
            overlay.classList.add('mb-loading-active');
        });
    },

    /**
     * フィルター・ローディング非表示
     */
    hideFilterLoading() {
        const overlay = document.querySelector('.mb-loading-overlay');
        if (overlay) {
            overlay.classList.remove('mb-loading-active');
            setTimeout(() => {
                if (overlay.parentNode) {
                    overlay.parentNode.removeChild(overlay);
                }
            }, 300);
        }
    },

    /**
     * アニメーション・ユーティリティ
     */
    animateFilterChange(element) {
        element.style.transform = 'scale(0.95)';
        element.style.transition = 'transform 0.1s ease';
        
        setTimeout(() => {
            element.style.transform = '';
        }, 100);
    },

    animateStatusBar(bar) {
        const items = bar.querySelectorAll('.mb-status-item');
        
        items.forEach((item, index) => {
            setTimeout(() => {
                item.classList.add('mb-animated', 'mb-fade-in-up');
            }, index * 50);
        });
    },

    /**
     * ==========================================================================
     * アクセシビリティ・パフォーマンス
     * ==========================================================================
     */
    setupAccessibility() {
        this.setupKeyboardNavigation();
        this.setupFocusManagement();
        this.setupARIALabels();
    },

    /**
     * キーボードナビゲーション
     */
    setupKeyboardNavigation() {
        document.addEventListener('keydown', (e) => {
            // Escapeキー
            if (e.key === 'Escape') {
                this.hideSearchSuggestions();
                this.hideFilterBottomSheet();
                this.closeModals();
            }
            
            // Ctrl+K で検索フォーカス
            if (e.ctrlKey && e.key === 'k') {
                e.preventDefault();
                const searchInput = this.elements.searchInputs[0];
                if (searchInput) {
                    searchInput.focus();
                }
            }
        });
    },

    /**
     * フォーカス管理
     */
    setupFocusManagement() {
        // タブトラップの実装
        this.setupTabTrap();
        
        // フォーカス可視化
        this.setupFocusVisibility();
    },

    /**
     * タブトラップ
     */
    setupTabTrap() {
        document.addEventListener('keydown', (e) => {
            if (e.key !== 'Tab') return;

            const modal = document.querySelector('.gi-modal-active, .gi-filter-bottom-sheet.active');
            if (!modal) return;

            const focusableElements = modal.querySelectorAll(
                'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
            );
            
            if (focusableElements.length === 0) return;

            const firstElement = focusableElements[0];
            const lastElement = focusableElements[focusableElements.length - 1];

            if (e.shiftKey) {
                if (document.activeElement === firstElement) {
                    e.preventDefault();
                    lastElement.focus();
                }
            } else {
                if (document.activeElement === lastElement) {
                    e.preventDefault();
                    firstElement.focus();
                }
            }
        });
    },

    /**
     * フォーカス可視化
     */
    setupFocusVisibility() {
        // マウス使用時はフォーカスアウトラインを無効化
        document.addEventListener('mousedown', () => {
            this.elements.body.classList.add('gi-using-mouse');
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
                this.elements.body.classList.remove('gi-using-mouse');
            }
        });
    },

    /**
     * ARIA ラベルの設定
     */
    setupARIALabels() {
        // 動的コンテンツのARIAラベル
        const updateARIALabels = () => {
            // 結果数の通知
            const resultsContainer = this.elements.grantsGrid;
            if (resultsContainer) {
                const count = resultsContainer.querySelectorAll('.grant-card').length;
                resultsContainer.setAttribute('aria-label', `${count}件の助成金が表示されています`);
            }
            
            // 比較アイテム数の通知
            if (this.elements.comparisonBar) {
                const count = this.state.comparisonItems.length;
                this.elements.comparisonBar.setAttribute('aria-label', `${count}件の助成金が比較リストに追加されています`);
            }
        };

        // 初期設定
        updateARIALabels();

        // 変更時に更新
        const observer = new MutationObserver(updateARIALabels);
        if (this.elements.grantsGrid) {
            observer.observe(this.elements.grantsGrid, { childList: true });
        }
    },

    /**
     * パフォーマンス最適化
     */
    setupPerformance() {
        this.setupLazyLoading();
        this.setupInfiniteScroll();
        this.setupImageOptimization();
    },

    /**
     * 遅延読み込み
     */
    setupLazyLoading() {
        const images = document.querySelectorAll('img[data-src]');
        if (images.length === 0 || !('IntersectionObserver' in window)) return;

        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.add('gi-image-loaded');
                    img.classList.remove('gi-image-loading');
                    imageObserver.unobserve(img);
                }
            });
        }, {
            rootMargin: '50px'
        });

        images.forEach(img => {
            img.classList.add('gi-image-loading');
            imageObserver.observe(img);
        });
    },

    /**
     * 無限スクロール
     */
    setupInfiniteScroll() {
        let page = 2;
        let isLoading = false;
        let hasMore = true;

        const loadMoreHandler = this.throttle(() => {
            if (isLoading || !hasMore) return;

            const scrollTop = window.pageYOffset;
            const windowHeight = window.innerHeight;
            const documentHeight = document.documentElement.scrollHeight;

            if (scrollTop + windowHeight >= documentHeight - 1000) {
                isLoading = true;
                
                this.ajax('gi_load_more_grants', { page })
                    .then(response => {
                        if (response.success && response.data.grants && response.data.grants.length > 0) {
                            const container = this.elements.grantsGrid;
                            if (container) {
                                const newCards = response.data.grants.map(grant => 
                                    this.renderGrantCard(grant)
                                ).join('');
                                container.insertAdjacentHTML('beforeend', newCards);
                                
                                // 新しいカードにイベントを設定
                                this.setupNewCardEvents(container);
                            }
                            page++;
                        } else {
                            hasMore = false;
                        }
                    })
                    .catch(error => {
                        console.error('Load more error:', error);
                        hasMore = false;
                    })
                    .finally(() => {
                        isLoading = false;
                    });
            }
        }, 200);

        window.addEventListener('scroll', loadMoreHandler, { passive: true });
    },

    /**
     * 新しいカードイベントの設定
     */
    setupNewCardEvents(container) {
        // 新しく追加された画像の遅延読み込み
        const newImages = container.querySelectorAll('img[data-src]:not(.gi-image-loading)');
        newImages.forEach(img => {
            img.classList.add('gi-image-loading');
            // 既存の画像オブザーバーがあれば再利用
        });

        // 新しいチェックボックスの状態復元
        this.state.comparisonItems.forEach(item => {
            const checkbox = container.querySelector(`[data-grant-id="${item.id}"]:not([data-restored])`);
            if (checkbox) {
                checkbox.checked = true;
                checkbox.dataset.restored = 'true';
            }
        });
    },

    /**
     * 画像最適化
     */
    setupImageOptimization() {
        // WebP対応チェック
        const supportsWebP = this.checkWebPSupport();
        
        if (supportsWebP) {
            this.elements.body.classList.add('gi-supports-webp');
        }
    },

    /**
     * WebP対応チェック
     */
    checkWebPSupport() {
        try {
            return document.createElement('canvas').toDataURL('image/webp').indexOf('data:image/webp') === 0;
        } catch (e) {
            return false;
        }
    },

    /**
     * ==========================================================================
     * UI更新・レンダリング
     * ==========================================================================
     */

    /**
     * 検索結果の更新
     */
    updateSearchResults(data) {
        const container = this.elements.grantsGrid;
        if (!container) return;

        if (data.grants && data.grants.length > 0) {
            container.innerHTML = data.grants.map(grant => this.renderGrantCard(grant)).join('');
            this.setupNewCardEvents(container);
        } else {
            container.innerHTML = `
                <div class="gi-no-results">
                    <div class="gi-no-results-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>該当する助成金が見つかりませんでした</h3>
                    <p>検索条件を変更して再度お試しください。</p>
                </div>
            `;
        }

        // 結果数の更新
        const countElement = document.querySelector('.gi-results-count, .results-count');
        if (countElement && data.total !== undefined) {
            countElement.textContent = `${data.total}件の助成金`;
        }
    },

    /**
     * 助成金カードのレンダリング
     */
    renderGrantCard(grant) {
        return `
            <div class="gi-grant-card-enhanced grant-card" data-grant-id="${grant.id}">
                <div class="gi-card-image-container">
                    <img src="${grant.image || '/assets/images/default-grant.jpg'}" 
                         alt="${this.escapeHtml(grant.title)}" 
                         class="gi-card-image"
                         loading="lazy">
                    <div class="gi-card-badges">
                        ${grant.is_new ? '<span class="gi-card-badge gi-badge-new">新着</span>' : ''}
                        ${grant.is_featured ? '<span class="gi-card-badge gi-badge-featured">注目</span>' : ''}
                    </div>
                    <div class="gi-card-compare">
                        <label class="gi-compare-checkbox-container">
                            <input type="checkbox" 
                                   class="grant-compare-checkbox"
                                   data-grant-id="${grant.id}"
                                   data-grant-title="${this.escapeHtml(grant.title)}">
                            <span class="gi-compare-checkbox-label">比較</span>
                        </label>
                    </div>
                </div>
                <div class="gi-card-content">
                    <h3 class="gi-card-title">${this.escapeHtml(grant.title)}</h3>
                    <div class="gi-card-meta">
                        <div class="gi-card-amount">${grant.amount ? `${grant.amount}円` : '金額未定'}</div>
                        <div class="gi-card-organization">${this.escapeHtml(grant.organization || '')}</div>
                        <div class="gi-card-deadline">${grant.deadline ? `締切: ${grant.deadline}` : ''}</div>
                    </div>
                    ${grant.excerpt ? `<p class="gi-card-excerpt">${this.escapeHtml(grant.excerpt)}</p>` : ''}
                    <div class="gi-card-actions">
                        <a href="${grant.url || '#'}" class="gi-btn gi-btn-primary gi-card-cta">詳細を見る</a>
                        <button class="gi-btn gi-btn-secondary gi-bookmark-btn" 
                                data-grant-id="${grant.id}"
                                aria-label="ブックマーク">
                            <i class="fas fa-bookmark"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
    },

    /**
     * ==========================================================================
     * フィルターUI（ボトムシート）
     * ==========================================================================
     */

    /**
     * フィルターボトムシート表示
     */
    showFilterBottomSheet() {
        let sheet = document.querySelector('.gi-filter-bottom-sheet');
        
        if (!sheet) {
            sheet = this.createFilterBottomSheet();
            document.body.appendChild(sheet);
        }
        
        // オーバーレイ
        const overlay = document.createElement('div');
        overlay.className = 'gi-filter-overlay';
        overlay.addEventListener('click', () => this.hideFilterBottomSheet());
        document.body.appendChild(overlay);
        
        // アニメーション
        requestAnimationFrame(() => {
            sheet.classList.add('gi-filter-sheet-active');
            overlay.classList.add('gi-overlay-active');
            this.elements.body.classList.add('gi-filter-sheet-open');
        });
    },

    /**
     * フィルターボトムシート非表示
     */
    hideFilterBottomSheet() {
        const sheet = document.querySelector('.gi-filter-bottom-sheet');
        const overlay = document.querySelector('.gi-filter-overlay');
        
        if (sheet) {
            sheet.classList.remove('gi-filter-sheet-active');
        }
        
        if (overlay) {
            overlay.classList.remove('gi-overlay-active');
        }
        
        this.elements.body.classList.remove('gi-filter-sheet-open');
        
        setTimeout(() => {
            if (sheet && sheet.parentNode) {
                sheet.parentNode.removeChild(sheet);
            }
            if (overlay && overlay.parentNode) {
                overlay.parentNode.removeChild(overlay);
            }
        }, 300);
    },

    /**
     * フィルターボトムシートの作成
     */
    createFilterBottomSheet() {
        const sheet = document.createElement('div');
        sheet.className = 'gi-filter-bottom-sheet';
        sheet.innerHTML = `
            <div class="gi-filter-sheet-header">
                <h3 class="gi-filter-sheet-title">フィルター</h3>
                <button class="gi-filter-sheet-close gi-btn-icon" aria-label="閉じる">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="gi-filter-sheet-content">
                <div class="gi-filter-group">
                    <div class="gi-filter-group-title">カテゴリー</div>
                    <div class="gi-filter-options">
                        <button class="gi-filter-option" data-filter="category" data-value="business">
                            <span>事業助成</span>
                        </button>
                        <button class="gi-filter-option" data-filter="category" data-value="research">
                            <span>研究助成</span>
                        </button>
                        <button class="gi-filter-option" data-filter="category" data-value="education">
                            <span>教育助成</span>
                        </button>
                    </div>
                </div>
                <div class="gi-filter-group">
                    <div class="gi-filter-group-title">都道府県</div>
                    <div class="gi-filter-options">
                        <button class="gi-filter-option" data-filter="prefecture" data-value="tokyo">
                            <span>東京都</span>
                        </button>
                        <button class="gi-filter-option" data-filter="prefecture" data-value="osaka">
                            <span>大阪府</span>
                        </button>
                        <button class="gi-filter-option" data-filter="prefecture" data-value="kanagawa">
                            <span>神奈川県</span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="gi-filter-sheet-footer">
                <button class="gi-btn gi-btn-secondary gi-btn-filter-clear">クリア</button>
                <button class="gi-btn gi-btn-primary gi-btn-filter-apply">適用</button>
            </div>
        `;

        // フィルターオプションのイベント
        sheet.querySelectorAll('.gi-filter-option').forEach(option => {
            option.addEventListener('click', () => {
                option.classList.toggle('gi-filter-option-selected');
            });
        });

        return sheet;
    },

    /**
     * ==========================================================================
     * ユーティリティ・ヘルパー
     * ==========================================================================
     */

    /**
     * モーダルを閉じる
     */
    closeModals() {
        // 各種モーダルやポップアップを閉じる
        this.hideSearchSuggestions();
        this.hideFilterBottomSheet();
        
        // 他のモーダルがあれば追加
        document.querySelectorAll('.gi-modal-active, .gi-popup-active').forEach(modal => {
            modal.classList.remove('gi-modal-active', 'gi-popup-active');
        });
    }
};

/**
 * =============================================================================
 * 自動初期化
 * =============================================================================
 */

// みんなの銀行スタイル初期化実行
MinnaBankGrants.init();

// グローバルアクセス用（後方互換性とデバッグ用）
window.MinnaBankGrants = MinnaBankGrants;
window.GrantInsight = MinnaBankGrants; // 後方互換性

/**
 * =============================================================================
 * CSS-in-JS スタイル（最小限）
 * =============================================================================
 */

// みんなの銀行スタイル・動的CSS追加
document.addEventListener('DOMContentLoaded', () => {
    const styleSheet = document.createElement('style');
    styleSheet.id = 'minna-bank-dynamic-styles';
    styleSheet.textContent = `
        /* みんなの銀行Toast通知スタイル */
        .mb-toast {
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 10000;
            max-width: 420px;
            background: var(--mb-white);
            border-radius: 12px;
            box-shadow: var(--mb-shadow-hover);
            transform: translateX(100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid var(--mb-gray-200);
        }
        
        .mb-toast-show {
            transform: translateX(0);
        }
        
        .mb-toast-hide {
            transform: translateX(100%) scale(0.95);
            opacity: 0;
        }
        
        .mb-toast-content {
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .mb-toast-icon {
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: var(--mb-font-weight-bold);
            font-size: 16px;
        }
        
        .mb-toast-message {
            flex: 1;
            font-weight: var(--mb-font-weight-medium);
            color: var(--mb-gray-800);
            font-size: 14px;
            line-height: 1.4;
        }
        
        .mb-toast-close {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: var(--mb-gray-500);
            padding: 4px;
            border-radius: 4px;
            transition: var(--mb-transition);
        }
        
        .mb-toast-close:hover {
            background-color: var(--mb-gray-100);
            color: var(--mb-gray-700);
        }
        
        /* トーストタイプ別スタイル */
        .mb-toast-success .mb-toast-icon { color: var(--mb-success-600); }
        .mb-toast-error .mb-toast-icon { color: var(--mb-error-600); }
        .mb-toast-warning .mb-toast-icon { color: var(--mb-warning-600); }
        .mb-toast-info .mb-toast-icon { color: var(--mb-primary-600); }
        
        /* ローディング・オーバーレイ */
        .mb-loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 100;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .mb-loading-active {
            opacity: 1;
        }
        
        .mb-loading-spinner {
            text-align: center;
        }
        
        .mb-spinner {
            width: 32px;
            height: 32px;
            border: 3px solid var(--mb-gray-200);
            border-top: 3px solid var(--mb-primary-600);
            border-radius: 50%;
            animation: mbSpin 1s linear infinite;
            margin: 0 auto 12px;
        }
        
        .mb-loading-text {
            font-size: 14px;
            color: var(--mb-gray-600);
            font-weight: var(--mb-font-weight-medium);
        }
        
        /* リップル効果 */
        .mb-ripple-effect {
            position: absolute;
            border-radius: 50%;
            background: rgba(0, 0, 0, 0.1);
            transform: scale(0);
            animation: mbRipple 0.6s ease-out;
            pointer-events: none;
        }
        
        /* アニメーション */
        .mb-animate-on-scroll {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity 0.6s cubic-bezier(0.4, 0, 0.2, 1), transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .mb-animated {
            opacity: 1;
            transform: translateY(0);
        }
        
        .mb-fade-in-up {
            animation: mbFadeInUp 0.6s ease-out forwards;
        }
        
        /* ホバー効果 */
        .mb-hover-active {
            transform: translateY(-2px);
            box-shadow: var(--mb-shadow-hover);
            transition: var(--mb-transition);
        }
        
        /* タッチフィードバック */
        .mb-touch-active {
            transform: scale(0.98);
            opacity: 0.9;
            transition: var(--mb-transition);
        }
        
        /* フォーカス管理 */
        .mb-keyboard-navigation *:focus {
            outline: 2px solid var(--mb-primary-500);
            outline-offset: 2px;
        }
        
        .mb-using-mouse *:focus {
            outline: none;
        }
        
        /* エラー状態 */
        .mb-field-error {
            border-color: var(--mb-error-500) !important;
            box-shadow: 0 0 0 3px rgba(220, 38, 127, 0.1);
        }
        
        /* カード・ホバー情報 */
        .mb-card-hover-info {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: var(--mb-white);
            border: 1px solid var(--mb-gray-200);
            border-radius: 0 0 12px 12px;
            padding: 16px;
            opacity: 0;
            transform: translateY(-8px);
            transition: var(--mb-transition);
            z-index: 10;
        }
        
        .mb-card-hover-info.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* ブックマーク・アニメーション */
        .mb-bookmark-btn.bookmarked {
            color: var(--mb-primary-600);
            transform: scale(1.1);
        }
        
        /* ステータス・バー・アニメーション */
        .mb-status-item {
            opacity: 0;
            transform: translateY(20px);
        }
        
        .mb-status-item.mb-animated {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* キーフレーム・アニメーション */
        @keyframes mbSpin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @keyframes mbRipple {
            0% {
                transform: scale(0);
                opacity: 1;
            }
            100% {
                transform: scale(4);
                opacity: 0;
            }
        }
        
        @keyframes mbFadeInUp {
            0% {
                opacity: 0;
                transform: translateY(24px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* レスポンシブ・ブレークポイント対応 */
        @media (max-width: 768px) {
            .mb-toast {
                right: 16px;
                left: 16px;
                max-width: none;
                transform: translateY(-100%);
                top: 16px;
            }
            
            .mb-toast-show {
                transform: translateY(0);
            }
            
            .mb-toast-hide {
                transform: translateY(-100%) scale(0.95);
            }
        }
        
        /* アクセシビリティ・改善 */
        @media (prefers-reduced-motion: reduce) {
            .mb-toast,
            .mb-loading-overlay,
            .mb-ripple-effect,
            .mb-animate-on-scroll,
            .mb-hover-active,
            .mb-touch-active,
            .mb-card-hover-info,
            .mb-status-item {
                transition: none;
                animation: none;
            }
        }
        
        /* ダークモード対応 */
        @media (prefers-color-scheme: dark) {
            .mb-toast {
                background: var(--mb-gray-800);
                border-color: var(--mb-gray-700);
            }
            
            .mb-toast-message {
                color: var(--mb-gray-100);
            }
            
            .mb-toast-close {
                color: var(--mb-gray-400);
            }
            
            .mb-toast-close:hover {
                background-color: var(--mb-gray-700);
                color: var(--mb-gray-200);
            }
            
            .mb-loading-overlay {
                background: rgba(17, 24, 39, 0.9);
            }
            
            .mb-loading-text {
                color: var(--mb-gray-300);
            }
        }
    `;
    document.head.appendChild(styleSheet);
});

/**
 * =============================================================================
 * エクスポート（モジュール対応）
 * =============================================================================
 */

// ES6モジュール対応
if (typeof module !== 'undefined' && module.exports) {
    module.exports = MinnaBankGrants;
}

// AMD対応
if (typeof define === 'function' && define.amd) {
    define(() => MinnaBankGrants);
}