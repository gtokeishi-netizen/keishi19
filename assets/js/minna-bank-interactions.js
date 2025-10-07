/**
 * Minna Bank Style JavaScript - Professional Interactions
 * File: minna-bank-interactions.js
 * 
 * みんなの銀行スタイル - プロフェッショナル・インタラクション
 * 高級感のあるアニメーション・スムーズな操作性
 * 
 * @version 1.0.0
 */

(function() {
    'use strict';

    // ===== MINNA BANK INTERACTION CONTROLLER ===== //
    const MinnaBank = {
        // Configuration
        config: {
            animationDuration: 300,
            debounceDelay: 300,
            loadingDelay: 150,
            apiEndpoint: '/wp-json/grant-insight/v1/',
            pageSize: 12,
            maxRetries: 3
        },

        // State management
        state: {
            isLoading: false,
            currentPage: 1,
            totalPages: 1,
            activeFilters: {},
            searchQuery: '',
            sortBy: 'date_desc',
            viewMode: 'grid',
            lastRequest: null
        },

        // Initialize all interactions
        init() {
            this.bindEvents();
            this.initializeFilters();
            this.initializeSearch();
            this.initializeViewControls();
            this.initializeScrollEffects();
            this.initializeAccessibility();
            this.loadInitialState();
            
            // Add smooth loading animations
            this.addLoadingAnimations();
            
            console.log('🏦 Minna Bank Style initialized');
        },

        // ===== EVENT BINDING ===== //
        bindEvents() {
            // Search input with debounce
            const searchInput = document.getElementById('minna-search-input');
            if (searchInput) {
                searchInput.addEventListener('input', this.debounce(
                    (e) => this.handleSearchInput(e), 
                    this.config.debounceDelay
                ));
                
                searchInput.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        this.executeSearch();
                    }
                });
            }

            // Filter selects
            document.querySelectorAll('.minna-select').forEach(select => {
                select.addEventListener('change', (e) => this.handleFilterChange(e));
            });

            // Filter pills
            document.querySelectorAll('.minna-filter-pill').forEach(pill => {
                pill.addEventListener('click', (e) => this.handleFilterPill(e));
            });

            // Status bars
            document.querySelectorAll('.minna-status-bar').forEach(bar => {
                bar.addEventListener('click', (e) => this.handleStatusBarClick(e));
            });

            // View controls
            document.querySelectorAll('.minna-view-btn').forEach(btn => {
                btn.addEventListener('click', (e) => this.handleViewChange(e));
            });

            // Grant cards
            document.addEventListener('click', (e) => {
                if (e.target.closest('.minna-grant-card')) {
                    this.handleGrantCardClick(e);
                }
            });

            // Apply filters button
            const applyBtn = document.querySelector('[onclick*="applyFilters"]');
            if (applyBtn) {
                applyBtn.removeAttribute('onclick');
                applyBtn.addEventListener('click', () => this.applyFilters());
            }

            // Clear filters button
            const clearBtn = document.querySelector('[onclick*="clearAllFilters"]');
            if (clearBtn) {
                clearBtn.removeAttribute('onclick');
                clearBtn.addEventListener('click', () => this.clearAllFilters());
            }

            // Scroll events for sticky elements
            window.addEventListener('scroll', this.throttle(
                () => this.handleScroll(), 
                16
            ));

            // Resize events
            window.addEventListener('resize', this.throttle(
                () => this.handleResize(), 
                100
            ));
        },

        // ===== SEARCH FUNCTIONALITY ===== //
        handleSearchInput(e) {
            this.state.searchQuery = e.target.value;
            this.showSearchSuggestions(this.state.searchQuery);
        },

        executeSearch() {
            if (this.state.isLoading) return;
            
            this.showLoadingState();
            this.updateURL();
            this.fetchResults();
        },

        showSearchSuggestions(query) {
            if (query.length < 2) return;
            
            // AI-powered search suggestions (placeholder)
            const suggestions = [
                'DX推進関連の助成金',
                '環境配慮型ビジネス支援',
                '中小企業デジタル化補助金',
                '働き方改革推進支援'
            ];
            
            // Implementation would show suggestions dropdown
            console.log('Search suggestions for:', query);
        },

        // ===== FILTER MANAGEMENT ===== //
        initializeFilters() {
            // Load filters from URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            
            urlParams.forEach((value, key) => {
                if (key !== 'paged' && value) {
                    this.state.activeFilters[key] = value;
                }
            });

            this.updateFilterUI();
        },

        handleFilterChange(e) {
            const { name, value } = e.target;
            
            if (value) {
                this.state.activeFilters[name || 'filter'] = value;
            } else {
                delete this.state.activeFilters[name || 'filter'];
            }

            this.updateFilterPills();
        },

        handleFilterPill(e) {
            e.preventDefault();
            const pill = e.currentTarget;
            const filterType = pill.dataset.filter;
            
            // Toggle active state
            const isActive = pill.classList.contains('active');
            
            // Remove active from all pills of same group
            document.querySelectorAll('.minna-filter-pill').forEach(p => {
                if (p.dataset.filter === filterType) {
                    p.classList.remove('active');
                }
            });
            
            if (!isActive) {
                pill.classList.add('active');
                this.applyQuickFilter(filterType);
            } else {
                this.clearQuickFilter(filterType);
            }
        },

        applyQuickFilter(filterType) {
            switch (filterType) {
                case 'active':
                    this.state.activeFilters.status = 'active';
                    break;
                case 'featured':
                    this.state.activeFilters.featured = '1';
                    break;
                case 'large-amount':
                    this.state.activeFilters.amount = '1000+';
                    break;
                case 'deadline-soon':
                    this.state.activeFilters.deadline = 'soon';
                    break;
                default:
                    this.clearAllFilters();
                    return;
            }
            
            this.executeSearch();
        },

        clearQuickFilter(filterType) {
            switch (filterType) {
                case 'active':
                    delete this.state.activeFilters.status;
                    break;
                case 'featured':
                    delete this.state.activeFilters.featured;
                    break;
                case 'large-amount':
                    delete this.state.activeFilters.amount;
                    break;
                case 'deadline-soon':
                    delete this.state.activeFilters.deadline;
                    break;
            }
            
            this.executeSearch();
        },

        clearAllFilters() {
            this.state.activeFilters = {};
            this.state.searchQuery = '';
            this.state.currentPage = 1;
            
            // Reset UI
            document.getElementById('minna-search-input').value = '';
            document.querySelectorAll('.minna-select').forEach(select => {
                select.value = '';
            });
            
            document.querySelectorAll('.minna-filter-pill').forEach(pill => {
                pill.classList.remove('active');
            });
            
            // Activate "すべて" pill
            const allPill = document.querySelector('.minna-filter-pill[data-filter="all"]');
            if (allPill) {
                allPill.classList.add('active');
            }
            
            this.executeSearch();
        },

        updateFilterPills() {
            // Update filter pill states based on current filters
            document.querySelectorAll('.minna-filter-pill').forEach(pill => {
                const filterType = pill.dataset.filter;
                let isActive = false;
                
                switch (filterType) {
                    case 'active':
                        isActive = this.state.activeFilters.status === 'active';
                        break;
                    case 'featured':
                        isActive = this.state.activeFilters.featured === '1';
                        break;
                    case 'large-amount':
                        isActive = this.state.activeFilters.amount === '1000+';
                        break;
                }
                
                pill.classList.toggle('active', isActive);
            });
        },

        applyFilters() {
            this.showLoadingState();
            this.updateURL();
            this.fetchResults();
        },

        // ===== STATUS BAR INTERACTIONS ===== //
        handleStatusBarClick(e) {
            const statusBar = e.currentTarget;
            const statusType = statusBar.dataset.status || 
                             statusBar.getAttribute('onclick')?.match(/'([^']+)'/)?.[1];
            
            if (statusType) {
                this.applyQuickFilter(statusType);
                
                // Add visual feedback
                statusBar.style.transform = 'scale(0.98)';
                setTimeout(() => {
                    statusBar.style.transform = '';
                }, 150);
            }
        },

        // ===== VIEW CONTROLS ===== //
        initializeViewControls() {
            const gridBtn = document.querySelector('.minna-view-btn[onclick*="grid"]');
            const listBtn = document.querySelector('.minna-view-btn[onclick*="list"]');
            
            if (gridBtn) {
                gridBtn.removeAttribute('onclick');
                gridBtn.addEventListener('click', () => this.switchView('grid'));
            }
            
            if (listBtn) {
                listBtn.removeAttribute('onclick');
                listBtn.addEventListener('click', () => this.switchView('list'));
            }
        },

        handleViewChange(e) {
            const viewType = e.currentTarget.dataset.view;
            if (viewType) {
                this.switchView(viewType);
            }
        },

        switchView(viewType) {
            if (this.state.viewMode === viewType) return;
            
            this.state.viewMode = viewType;
            
            // Update button states
            document.querySelectorAll('.minna-view-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            const activeBtn = document.querySelector(`.minna-view-btn[data-view="${viewType}"]`);
            if (activeBtn) {
                activeBtn.classList.add('active');
            }
            
            // Update layout
            this.updateViewLayout();
            this.updateURL();
        },

        updateViewLayout() {
            const container = document.getElementById('minna-grants-container');
            if (!container) return;
            
            const currentGrid = container.querySelector('.minna-grants-grid');
            const currentList = container.querySelector('.minna-grants-list');
            
            if (this.state.viewMode === 'grid') {
                if (currentList) {
                    currentList.className = 'minna-grants-grid';
                }
            } else {
                if (currentGrid) {
                    currentGrid.className = 'minna-grants-list';
                }
            }
        },

        // ===== GRANT CARD INTERACTIONS ===== //
        handleGrantCardClick(e) {
            const card = e.target.closest('.minna-grant-card');
            const link = card.getAttribute('onclick')?.match(/href='([^']+)'/)?.[1] ||
                        card.dataset.href;
            
            if (link) {
                // Add loading animation
                card.style.opacity = '0.7';
                card.style.transform = 'scale(0.98)';
                
                // Navigate with smooth transition
                setTimeout(() => {
                    window.location.href = link;
                }, 200);
            }
        },

        // ===== DATA FETCHING ===== //
        async fetchResults() {
            if (this.state.isLoading) return;
            
            this.state.isLoading = true;
            
            try {
                const params = this.buildQueryParams();
                const url = `${window.location.pathname}?${params.toString()}`;
                
                // Use history API for smooth navigation
                if (this.state.lastRequest !== url) {
                    window.history.pushState(null, '', url);
                    this.state.lastRequest = url;
                }
                
                // Simulate API call (in real implementation, would fetch from server)
                await this.simulateLoading();
                
                // Update results (placeholder)
                this.updateResults();
                
            } catch (error) {
                console.error('Error fetching results:', error);
                this.showErrorState();
            } finally {
                this.state.isLoading = false;
                this.hideLoadingState();
            }
        },

        buildQueryParams() {
            const params = new URLSearchParams();
            
            // Add search query
            if (this.state.searchQuery) {
                params.set('s', this.state.searchQuery);
            }
            
            // Add active filters
            Object.entries(this.state.activeFilters).forEach(([key, value]) => {
                params.set(key, value);
            });
            
            // Add view mode
            if (this.state.viewMode !== 'grid') {
                params.set('view', this.state.viewMode);
            }
            
            // Add pagination
            if (this.state.currentPage > 1) {
                params.set('paged', this.state.currentPage);
            }
            
            return params;
        },

        async simulateLoading() {
            // Simulate network delay
            await new Promise(resolve => 
                setTimeout(resolve, Math.random() * 1000 + 500)
            );
        },

        updateResults() {
            // Placeholder for results update
            console.log('Results updated with state:', this.state);
        },

        // ===== LOADING STATES ===== //
        showLoadingState() {
            const container = document.getElementById('minna-grants-container');
            if (!container) return;
            
            // Add loading overlay
            const overlay = document.createElement('div');
            overlay.className = 'minna-loading-overlay';
            overlay.innerHTML = `
                <div class="minna-loading">
                    <div class="minna-spinner"></div>
                    <span>検索中...</span>
                </div>
            `;
            
            container.style.position = 'relative';
            container.appendChild(overlay);
            
            // Disable form elements
            document.querySelectorAll('.minna-select, .minna-input, .minna-btn').forEach(el => {
                el.disabled = true;
            });
        },

        hideLoadingState() {
            const overlay = document.querySelector('.minna-loading-overlay');
            if (overlay) {
                overlay.remove();
            }
            
            // Re-enable form elements
            document.querySelectorAll('.minna-select, .minna-input, .minna-btn').forEach(el => {
                el.disabled = false;
            });
        },

        showErrorState() {
            const container = document.getElementById('minna-grants-container');
            if (!container) return;
            
            container.innerHTML = `
                <div class="minna-error-state">
                    <div class="minna-error-icon"></div>
                    <h3>エラーが発生しました</h3>
                    <p>検索処理中にエラーが発生しました。しばらく時間をおいて再度お試しください。</p>
                    <button class="minna-btn minna-btn-primary" onclick="location.reload()">
                        ページを再読み込み
                    </button>
                </div>
            `;
        },

        // ===== SCROLL EFFECTS ===== //
        initializeScrollEffects() {
            // Sticky header behavior
            const stickyElement = document.querySelector('.minna-advanced-filters');
            if (stickyElement) {
                this.initializeStickyBehavior(stickyElement);
            }
            
            // Scroll-to-top button
            this.initializeScrollToTop();
        },

        initializeStickyBehavior(element) {
            const observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            element.classList.remove('is-stuck');
                        } else {
                            element.classList.add('is-stuck');
                        }
                    });
                },
                { threshold: 0.1 }
            );
            
            observer.observe(element);
        },

        initializeScrollToTop() {
            const scrollBtn = document.createElement('button');
            scrollBtn.className = 'minna-scroll-to-top';
            scrollBtn.innerHTML = '<div class="minna-icon minna-icon-chevron-up"></div>';
            scrollBtn.setAttribute('aria-label', 'ページトップへ戻る');
            
            scrollBtn.addEventListener('click', () => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
            
            document.body.appendChild(scrollBtn);
            
            // Show/hide based on scroll position
            window.addEventListener('scroll', () => {
                if (window.scrollY > 1000) {
                    scrollBtn.classList.add('visible');
                } else {
                    scrollBtn.classList.remove('visible');
                }
            });
        },

        handleScroll() {
            // Handle scroll-based animations
            const elements = document.querySelectorAll('.minna-grant-card');
            elements.forEach(el => {
                if (this.isElementInViewport(el)) {
                    el.classList.add('animate-in');
                }
            });
        },

        handleResize() {
            // Handle responsive adjustments
            this.updateViewLayout();
        },

        // ===== ACCESSIBILITY ===== //
        initializeAccessibility() {
            // Keyboard navigation
            document.addEventListener('keydown', (e) => {
                this.handleKeyboardNavigation(e);
            });
            
            // ARIA labels and states
            this.updateAriaStates();
            
            // Focus management
            this.initializeFocusManagement();
        },

        handleKeyboardNavigation(e) {
            // Escape key to clear search/filters
            if (e.key === 'Escape') {
                const activeElement = document.activeElement;
                if (activeElement && activeElement.matches('.minna-input')) {
                    activeElement.blur();
                }
            }
            
            // Tab navigation improvements
            if (e.key === 'Tab') {
                this.updateFocusIndicators();
            }
        },

        updateAriaStates() {
            // Update ARIA attributes based on current state
            document.querySelectorAll('.minna-filter-pill').forEach(pill => {
                const isActive = pill.classList.contains('active');
                pill.setAttribute('aria-pressed', isActive.toString());
            });
        },

        initializeFocusManagement() {
            // Ensure proper focus indicators
            document.addEventListener('focusin', (e) => {
                e.target.classList.add('has-focus');
            });
            
            document.addEventListener('focusout', (e) => {
                e.target.classList.remove('has-focus');
            });
        },

        updateFocusIndicators() {
            // Enhanced focus indicators for better accessibility
            setTimeout(() => {
                const focusedElement = document.activeElement;
                if (focusedElement) {
                    focusedElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest'
                    });
                }
            }, 0);
        },

        // ===== ANIMATION SYSTEM ===== //
        addLoadingAnimations() {
            // Add CSS for loading animations
            const style = document.createElement('style');
            style.textContent = `
                .minna-loading-overlay {
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(255, 255, 255, 0.8);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 1000;
                    backdrop-filter: blur(2px);
                }
                
                .minna-scroll-to-top {
                    position: fixed;
                    bottom: 2rem;
                    right: 2rem;
                    width: 3rem;
                    height: 3rem;
                    background: var(--minna-primary);
                    color: var(--minna-white);
                    border: none;
                    border-radius: 50%;
                    cursor: pointer;
                    opacity: 0;
                    visibility: hidden;
                    transition: all 0.3s ease;
                    z-index: 1000;
                    box-shadow: var(--minna-shadow-lg);
                }
                
                .minna-scroll-to-top.visible {
                    opacity: 1;
                    visibility: visible;
                }
                
                .minna-scroll-to-top:hover {
                    background: var(--minna-gray-800);
                    transform: translateY(-2px);
                }
                
                .minna-grant-card {
                    opacity: 0;
                    transform: translateY(20px);
                    transition: all 0.6s ease;
                }
                
                .minna-grant-card.animate-in {
                    opacity: 1;
                    transform: translateY(0);
                }
                
                .minna-advanced-filters.is-stuck {
                    box-shadow: var(--minna-shadow-lg);
                }
                
                .has-focus {
                    outline: 2px solid var(--minna-info);
                    outline-offset: 2px;
                }
                
                @media (prefers-reduced-motion: reduce) {
                    .minna-grant-card {
                        opacity: 1;
                        transform: none;
                        transition: none;
                    }
                }
            `;
            document.head.appendChild(style);
        },

        // ===== UTILITY FUNCTIONS ===== //
        debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        throttle(func, limit) {
            let inThrottle;
            return function() {
                const args = arguments;
                const context = this;
                if (!inThrottle) {
                    func.apply(context, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            };
        },

        isElementInViewport(el) {
            const rect = el.getBoundingClientRect();
            return (
                rect.top >= 0 &&
                rect.left >= 0 &&
                rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
                rect.right <= (window.innerWidth || document.documentElement.clientWidth)
            );
        },

        updateURL() {
            const params = this.buildQueryParams();
            const url = `${window.location.pathname}${params.toString() ? '?' + params.toString() : ''}`;
            window.history.replaceState(null, '', url);
        },

        loadInitialState() {
            // Load state from URL on page load
            const urlParams = new URLSearchParams(window.location.search);
            
            // Load search query
            const search = urlParams.get('s');
            if (search) {
                this.state.searchQuery = search;
                const searchInput = document.getElementById('minna-search-input');
                if (searchInput) {
                    searchInput.value = search;
                }
            }
            
            // Load view mode
            const view = urlParams.get('view');
            if (view) {
                this.state.viewMode = view;
                this.updateViewLayout();
            }
            
            // Load page number
            const page = urlParams.get('paged');
            if (page) {
                this.state.currentPage = parseInt(page);
            }
            
            // Animate in existing cards
            setTimeout(() => {
                document.querySelectorAll('.minna-grant-card').forEach((card, index) => {
                    setTimeout(() => {
                        card.classList.add('animate-in');
                    }, index * 100);
                });
            }, 100);
        }
    };

    // ===== GLOBAL FUNCTIONS FOR BACKWARD COMPATIBILITY ===== //
    window.clearAllFilters = () => MinnaBank.clearAllFilters();
    window.applyFilters = () => MinnaBank.applyFilters();
    window.filterByStatus = (status) => MinnaBank.applyQuickFilter('active');
    window.filterByAmount = (amount) => MinnaBank.applyQuickFilter('large-amount');
    window.filterByFeatured = () => MinnaBank.applyQuickFilter('featured');
    window.filterByDeadline = () => MinnaBank.applyQuickFilter('deadline-soon');
    window.filterByDifficulty = (level) => MinnaBank.applyQuickFilter('easy');
    window.switchView = (view) => MinnaBank.switchView(view);
    window.openAIOptimization = () => {
        alert('AI最適化機能は近日公開予定です。現在の検索条件を基に最適な助成金をご提案します。');
    };

    // ===== INITIALIZATION ===== //
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => MinnaBank.init());
    } else {
        MinnaBank.init();
    }

    // Export for external use
    window.MinnaBank = MinnaBank;

})();