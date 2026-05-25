@extends('layouts.app')

@section('title', 'Tìm kiếm địa điểm - Bản đồ số Ẩm thực Đông Anh')

@section('content')
<style>
    /* 📱 RESPONSIVE & MOBILE-NATIVE UPGRADES FOR SEARCH & MAP split-page */
    .search-detail-grid {
        display: grid;
        grid-template-columns: 280px 1fr !important;
        gap: 24px;
    }
    
    @media (max-width: 992px) {
        .search-detail-grid {
            grid-template-columns: 1fr !important;
            gap: 20px;
        }
        
        /* On mobile, place the map at the very top of the main area for quick exploration */
        .sidebar-filter {
            order: 2; /* Move filters below map */
        }
        .main-map-area {
            order: 1; /* Move map area to top */
        }
    }
    
    @media (max-width: 576px) {
        /* Premium Card Stack for mobile view */
        .eatery-card {
            flex-direction: column !important;
            align-items: stretch !important;
            padding: 12px !important;
            gap: 12px !important;
            border-radius: 16px !important;
        }
        
        .eatery-img-wrapper {
            width: 100% !important;
            height: 160px !important;
            border-radius: 12px !important;
            overflow: hidden;
        }
        
        .eatery-img {
            border-radius: 12px !important;
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
        }
        
        .eatery-info {
            padding: 0 !important;
        }
        
        .eatery-title {
            font-size: 1.05rem !important;
        }
        
        .eatery-desc {
            font-size: 0.78rem !important;
            margin: 6px 0 !important;
            -webkit-line-clamp: 3 !important; /* Allow slightly more text on stacked layouts */
        }
        
        .sidebar-tab-btn {
            font-size: 0.74rem !important;
            padding: 8px 4px !important;
            gap: 4px !important;
        }
        
        /* Optimize map height for smaller phone displays */
        .search-map-container {
            height: 280px !important;
        }
    }
</style>

<section class="hero-banner" style="padding: 30px 0 15px;">
    <div class="container">
        <h1 style="font-size: 1.8rem; margin-bottom: 8px; font-family: var(--font-heading);">
            🔍 Kết quả Tìm kiếm & Bản đồ số
        </h1>
        <p style="color: var(--text-muted); font-size: 0.95rem;">
            Bộ lọc nâng cao giúp tìm kiếm chính xác nhà hàng, quán ăn, đặc sản theo nhu cầu
        </p>
    </div>
</section>

<!-- Split Filter and Map Layout -->
<div class="container" style="padding-bottom: 60px;">
    <div class="detail-grid search-detail-grid">
        
        <!-- Left Sidebar Filter Control -->
        <aside class="sidebar-filter">
            <div class="glass-panel" style="padding: 20px;">
                <!-- Sidebar Tab Navigation -->
                <div class="sidebar-tabs" style="display: none;">
                    <button type="button" class="sidebar-tab-btn active" id="tabBtn-filters" onclick="switchSidebarTab('filters')">🔍 Bộ lọc</button>
                </div>
                
                <!-- Tab 1: Advanced Filters & GPS Radius Search -->
                <div id="tab-filters" class="sidebar-tab-content active">
                    <h3 style="font-size: 1rem; margin-bottom: 12px; color: var(--primary); display: flex; align-items: center; gap: 8px;">
                        <span>⚙️</span> Bộ lọc địa điểm
                    </h3>
                    
                    <!-- GPS Simulation Status -->
                    <div class="gps-badge">
                        <span class="pulse-dot" id="gpsPulseDot"></span>
                        <span id="gpsStatusText">📍 GPS: Đang giả lập (Cổ Loa)</span>
                    </div>
                    <div class="review-form-group" style="margin-top: 5px; margin-bottom: 15px;">
                        <button type="button" class="btn-accent" onclick="simulateRealGPS()" style="width: 100%; justify-content: center; font-size: 0.8rem; padding: 6px 12px; border-radius: 8px;">
                            ⚡ Định vị vị trí thực tế
                        </button>
                    </div>

                    <!-- Radius Filter Slider -->
                    <div class="range-slider-container">
                        <div class="range-slider-header">
                            <span style="font-size: 0.82rem; color: var(--text-muted);">Bán kính tìm kiếm</span>
                            <span id="radiusValText" style="color: var(--primary); font-size: 0.85rem; font-weight: 700;">5.0 km</span>
                        </div>
                        <input type="range" id="radiusSearchSlider" min="1" max="10" step="0.5" value="5" class="custom-range-slider" oninput="updateRadiusFilter(this.value)">
                    </div>
                    
                    <form action="/tim-kiem" method="GET" id="filterForm">
                        <!-- Text Keyword Input -->
                        <div class="review-form-group">
                            <label class="review-form-label" style="font-size: 0.82rem; font-weight: 600; color: var(--text-muted);">Từ khóa tìm kiếm</label>
                            <input type="text" name="q" id="sidebarSearchInput" value="{{ $keyword }}" class="form-input" placeholder="Tên quán, món ăn..." style="font-size: 0.85rem;">
                        </div>
                        
                        <!-- Category Select Dropdown -->
                        <div class="review-form-group">
                            <label class="review-form-label" style="font-size: 0.82rem; font-weight: 600; color: var(--text-muted);">Danh mục ẩm thực</label>
                            <select name="category_id" class="form-input" style="font-size: 0.85rem;">
                                <option value="">-- Tất cả danh mục --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ $catId == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->icon }} {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Commune Select Dropdown -->
                        <div class="review-form-group">
                            <label class="review-form-label" style="font-size: 0.82rem; font-weight: 600; color: var(--text-muted);">Khu vực (Xã / Thị trấn)</label>
                            <select name="commune_id" class="form-input" style="font-size: 0.85rem;">
                                <option value="">-- Tất cả khu vực --</option>
                                @foreach($communes as $com)
                                    <option value="{{ $com->id }}" {{ $comId == $com->id ? 'selected' : '' }}>
                                        📍 Xã {{ $com->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Open Only Checkbox -->
                        <div class="review-form-group" style="display: flex; align-items: center; gap: 8px; margin-top: 15px; margin-bottom: 10px;">
                            <input type="checkbox" id="openOnlyCheckbox" onchange="filterSearchPage()" style="width: 16px; height: 16px; accent-color: var(--primary); cursor: pointer;">
                            <label for="openOnlyCheckbox" style="font-size: 0.82rem; font-weight: 600; cursor: pointer; user-select: none;">
                                🟢 Chỉ quán Đang mở cửa
                            </label>
                        </div>
                        
                        <button type="submit" class="btn-primary" style="width: 100%; justify-content: center; margin-top: 10px; font-size: 0.85rem;">
                            Áp dụng bộ lọc
                        </button>
                        
                        <a href="/tim-kiem" class="btn-secondary" style="width: 100%; justify-content: center; margin-top: 10px; font-size: 0.85rem; padding: 8px 0; border-radius: 12px;">
                            Xóa bộ lọc
                        </a>
                    </form>
                </div>


            </div>
        </aside>
        
        <!-- Right Main View (Map on Top, Results below) -->
        <div class="main-map-area">
            <!-- Results Leaflet Map -->
            <div class="glass-panel search-map-container" style="height: 380px; overflow: hidden; margin-bottom: 24px; border: 1px solid var(--border-glow); position: relative;">
                <div id="searchMap" style="width: 100%; height: 100%;"></div>
            </div>
            
            <!-- Result Cards Feed -->
            <h2 style="font-size: 1.2rem; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                <span>📋</span> Kết quả tìm kiếm
                <span id="searchCountSpan" style="font-size: 0.85rem; color: var(--text-muted); font-weight: normal; margin-left: auto;">
                    Tìm thấy {{ $eateries->count() }} địa điểm phù hợp
                </span>
            </h2>
            
            @if($eateries->count() > 0)
                <div style="display: grid; grid-template-columns: 1fr; gap: 16px;" id="searchCardsContainer">
                    @foreach($eateries as $eat)
                        <div class="eatery-card glass-panel" 
                             data-slug="{{ $eat->slug }}"
                             data-name="{{ $eat->name }}"
                             data-address="{{ $eat->address }}"
                             data-desc="{{ $eat->description }}"
                             data-category="{{ $eat->category_id }}"
                             data-commune="{{ $eat->commune_id }}"
                             onclick="focusSearchMap({{ $eat->latitude }}, {{ $eat->longitude }}, '{{ $eat->slug }}')">
                            <div class="eatery-img-wrapper" style="width: 120px; height: 120px;">
                                <img src="{{ $eat->image_path ?: 'https://images.unsplash.com/photo-1591814468924-caf88d1232e1?auto=format&fit=crop&w=300&q=80' }}" class="eatery-img" alt="{{ $eat->name }}">
                            </div>
                            <div class="eatery-info">
                                <div class="eatery-header">
                                    <div>
                                        <span class="tag-badge" style="padding: 2px 8px; font-size: 0.7rem; margin-right: 6px;">{{ $eat->category->icon }} {{ $eat->category->name }}</span>
                                        <h3 class="eatery-title" style="display: inline-block; font-size: 1.15rem; margin-top: 4px;">{{ $eat->name }}</h3>
                                    </div>
                                    <div class="rating-stars">
                                        <span>⭐</span> {{ $eat->average_rating }}
                                    </div>
                                </div>
                                <p class="eatery-desc" style="-webkit-line-clamp: 2;">{{ $eat->description }}</p>
                                <div class="eatery-footer" style="margin-top: 4px;">
                                    <div class="eatery-meta-item">
                                        <span>📍</span> {{ $eat->address }}
                                    </div>
                                    <div class="eatery-meta-item" style="color: var(--primary); font-weight: 700; font-family: var(--font-heading);">
                                        {{ $eat->price_range }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="glass-panel" style="padding: 60px 40px; text-align: center; color: var(--text-muted);">
                    <p style="font-size: 1.3rem; margin-bottom: 8px; color: var(--text-main);">😔 Rất tiếc, không tìm thấy kết quả</p>
                    <p style="font-size: 0.9rem;">Thử tìm kiếm với từ khóa khác (ví dụ: *"Bún Mạch Tràng"*, *"Cafe"*, *"Lẩu"*) hoặc điều chỉnh lại danh mục và khu vực xã lọc.</p>
                    <a href="/tim-kiem" class="btn-primary" style="margin-top: 20px; font-size: 0.9rem;">Xem toàn bộ địa điểm</a>
                </div>
            @endif
        </div>
        
    </div>
</div>

<!-- ==========================================================================
     PREMIUM MODALS & OVERLAYS FOR CUSTOMER INTERACTIVE DISCOVERY
     ========================================================================== -->

<div id="reelsModal" class="reels-overlay" style="display: none;">
    <!-- Navigation arrows (Desktop helper) - Placed outside overflow: hidden container -->
    <button id="searchPrevReelBtn" onclick="searchPrevReel()" style="position: absolute; left: calc(50% - 250px); top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.25); width: 48px; height: 48px; border-radius: 50%; color: #fff; cursor: pointer; display: none; align-items: center; justify-content: center; font-size: 1.4rem; transition: all 0.25s; z-index: 100000; box-shadow: 0 4px 12px rgba(0,0,0,0.5);" class="nav-arrow-desktop">◀</button>
    <button id="searchNextReelBtn" onclick="searchNextReel()" style="position: absolute; right: calc(50% - 250px); top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.25); width: 48px; height: 48px; border-radius: 50%; color: #fff; cursor: pointer; display: none; align-items: center; justify-content: center; font-size: 1.4rem; transition: all 0.25s; z-index: 100000; box-shadow: 0 4px 12px rgba(0,0,0,0.5);" class="nav-arrow-desktop">▶</button>

    <div class="reels-container glass-panel">
        <div class="reels-video-wrapper">
            <!-- Dynamic video / iframe player container -->
            <div id="reelPlayerWrapper" style="width: 100%; height: 100%; position: absolute; inset: 0; z-index: 1;"></div>
            
            <div class="reels-header-controls" style="z-index: 10;">
                <span class="reels-badge-live">🎥 REVIEW THỰC TẾ</span>
                <button class="reels-close-btn" style="pointer-events: auto;" onclick="closeReelsModal()">✕</button>
            </div>
            
            <div class="reels-overlay-info" style="z-index: 10;">
                <h3 class="reels-eatery-name" id="reelsEateryName">Bún Mạch Tràng Cổ Loa</h3>
                <p class="reels-desc" id="reelsVideoDesc">Sợi bún tròn mướt màu ngà tự nhiên, xào chung với thịt heo tươi ngon và nghệ vàng thơm lừng. Không gian Cổ Loa cực chill!</p>
                <span class="reels-signature-tag">🌟 Món đặc trưng: Bún Mạch Tràng Trộn Nghệ</span>
            </div>
            
            <!-- Double click/Tap overlay to fly hearts -->
            <div style="position: absolute; inset: 0; z-index: 5; pointer-events: auto;" onclick="triggerDoubleTapHeart(event)"></div>
        </div>
        
        <!-- Right Action sidebar (TikTok Style) -->
        <div class="reels-side-actions">
            <button type="button" class="reels-action-btn" id="reelsLikeBtn" onclick="toggleReelsLike()">
                <span style="font-size: 1.8rem; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.6));">❤️</span>
                <span class="reels-action-label" id="reelsLikeCount">3.8K</span>
            </button>
            
            <button type="button" class="reels-action-btn" onclick="alert('Đã thêm quán ăn này vào Danh sách Yêu thích của bạn!')">
                <span style="font-size: 1.6rem; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.6));">⭐</span>
                <span class="reels-action-label">4.8</span>
            </button>
            
            <button type="button" class="reels-action-btn" onclick="navigator.clipboard.writeText(window.location.href); alert('Đã sao chép liên kết chia sẻ review của quán!');">
                <span style="font-size: 1.6rem; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.6));">🔗</span>
                <span class="reels-action-label">Chia sẻ</span>
            </button>
            
            <div class="reels-music-disc">🍜</div>
        </div>
    </div>
</div>

<!-- 2. Gamified Voucher Unboxing Lucky Draw Modal -->
<div id="voucherGiftModal" class="premium-modal-overlay" style="display: none;">
    <div class="premium-modal-card glass-panel">
        <button class="reels-close-btn" style="position: absolute; top: 15px; right: 15px; border-radius: 50%; width: 30px; height: 30px;" onclick="closeVoucherModal()">✕</button>
        
        <h2 style="font-family: var(--font-heading); color: var(--primary); font-size: 1.4rem; font-weight: 800; margin-bottom: 5px;">🎉 Chúc mừng bạn đã bắt được!</h2>
        <p style="font-size: 0.8rem; color: var(--text-muted); line-height: 1.4;">Chạm trực tiếp vào hộp quà thần kỳ bên dưới để unbox và rinh voucher giảm giá TA-FOOD cực nóng!</p>
        
        <div class="gift-box-large" id="unboxingGiftBox" onclick="unboxVoucherGift()">🎁</div>
        
        <div id="unboxingResultDiv" style="display: none;">
            <div class="scratch-voucher-card">
                <div style="font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700; letter-spacing: 1px;">VOUCHER ĐỘT PHÁ HỆ SINH THÁI</div>
                <div class="scratch-voucher-title" id="scratchVoucherCode">TAFOOD50</div>
                <p style="font-size: 0.82rem; color: var(--text-main); font-weight: 600; margin: 4px 0;" id="scratchVoucherDetails">Giảm ngay 50.000đ cho đơn hàng từ 150.000đ</p>
                <p style="font-size: 0.7rem; color: var(--text-muted);">Áp dụng đặt món tức thì tại toàn bộ quán ngon trên Đông Anh Food Map!</p>
            </div>
            
            <button type="button" class="btn-accent" onclick="claimVoucher()" style="margin-top: 20px; justify-content: center; width: 100%; border-radius: 10px;">
                📥 Lưu Voucher vào Ví ưu đãi
            </button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Khởi tạo các marker tìm kiếm từ PHP
    const eateries = @json($eateries);
    let searchMap;
    let markers = {};

    // Hàm lấy thông số màn hình động để cấu hình UI tương đối (relative specs)
    function getScreenSpecs() {
        const width = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
        const height = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
        return {
            viewportWidth: width,
            viewportHeight: height,
            isMobile: width <= 768,
            isTablet: width > 768 && width <= 992,
            isDesktop: width > 992,
            orientation: height > width ? 'portrait' : 'landscape'
        };
    }

    document.addEventListener("DOMContentLoaded", function() {
        // Căn chỉnh chiều cao container của bản đồ tương đối với chiều cao màn hình người dùng
        const adjustMapContainerHeight = () => {
            const specs = getScreenSpecs();
            const mapContainer = document.querySelector('.search-map-container');
            if (mapContainer) {
                if (specs.isMobile) {
                    const relativeHeight = Math.max(260, Math.min(380, specs.viewportHeight * 0.35));
                    mapContainer.style.setProperty('height', `${relativeHeight}px`, 'important');
                } else if (specs.isTablet) {
                    mapContainer.style.setProperty('height', '340px', 'important');
                } else {
                    mapContainer.style.setProperty('height', '400px', 'important');
                }
            }
        };

        // Chạy căn chỉnh chiều cao lần đầu
        adjustMapContainerHeight();

        const specs = getScreenSpecs();
        const initialZoom = specs.isMobile ? 11.5 : 12.5;

        // Thiết lập map Leaflet
        searchMap = L.map('searchMap', {
            zoomControl: false
        }).setView([21.1352, 105.8458], initialZoom);

        // Lắng nghe sự kiện resize để cập nhật kích thước bản đồ
        window.addEventListener('resize', () => {
            adjustMapContainerHeight();
            if (searchMap) {
                searchMap.invalidateSize();
            }
        });
        
        L.control.zoom({ position: 'bottomright' }).addTo(searchMap);

        // 3. Sử dụng Tileset bản đồ màu sáng mặc định (Voyager)
        let activeTileLayer = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 20
        }).addTo(searchMap);

        const group = [];

        // Khởi tạo GPS Giả lập và Vòng tròn Bán kính tìm kiếm của Khách hàng
        initUserLocation();

        // Vẽ các marker signature dạng đĩa ăn tròn neon tuyệt đẹp
        eateries.forEach(function(eat) {
            if (eat.latitude && eat.longitude) {
                let categoryColor = '#ff7e29';
                if (eat.category.slug === 'bun-pho') categoryColor = '#ff3366';
                if (eat.category.slug === 'lau-nuong') categoryColor = '#ff3300';
                if (eat.category.slug === 'quan-cafe') categoryColor = '#20b2aa';
                if (eat.category.slug === 'khach-san-nha-nghi') categoryColor = '#9d4edd';
                if (eat.category.slug === 'dac-san-dia-phuong') categoryColor = '#38b000';
                if (eat.category.slug === 'cho-truyen-thong') categoryColor = '#e63946';
                if (eat.category.slug === 'cho-dan-sinh') categoryColor = '#f77f00';
                if (eat.category.slug === 'sieu-thi') categoryColor = '#4361ee';
                if (eat.category.slug === 'cua-hang-tien-ich') categoryColor = '#7209b7';

                // Thay marker thường bằng Marker tròn cao cấp lồng ảnh món signature
                const customIcon = L.divIcon({
                    html: `<div style="background-image: url('${eat.image_path ? eat.image_path : 'https://images.unsplash.com/photo-1591814468924-caf88d1232e1?auto=format&fit=crop&w=120&q=80'}'); background-size: cover; background-position: center; width: 34px; height: 34px; border-radius: 50%; border: 2.5px solid ${categoryColor}; box-shadow: 0 0 10px rgba(0,0,0,0.5); position: relative;"><div style="position: absolute; bottom: -4px; right: -4px; background: ${categoryColor}; width: 16px; height: 16px; border-radius: 50%; border: 1px solid white; display: flex; align-items: center; justify-content: center; font-size: 0.65rem;">${eat.category.icon}</div></div>`,
                    className: 'custom-leaflet-marker',
                    iconSize: [34, 34],
                    iconAnchor: [17, 17]
                });

                // Signature dish simulated mapping for video reels popup
                const signatureDishName = eat.category.slug === 'bun-pho' ? 'Phở Bò Tái Lăn Cao Lỗ' : 
                                         (eat.category.slug === 'dac-san-dia-phuong' ? 'Bún Mạch Tràng Trộn Nghệ' : 
                                         (eat.category.slug === 'lau-nuong' ? 'Lẩu Nướng Sườn Sụn Cổ Loa' : 'Món Ngon Đặc Trưng'));

                // Get approved video reviews for this eatery
                const approvedVideos = eat.review_videos || [];
                const hasVideo = approvedVideos.length > 0;
                const buttonText = hasVideo ? `🎥 Xem Video Review (${approvedVideos.length})` : '🎥 Đông Anh Food Tour';

                const popupContent = `
                    <div class="map-popup-card">
                        <img src="${eat.image_path ? eat.image_path : 'https://images.unsplash.com/photo-1591814468924-caf88d1232e1?auto=format&fit=crop&w=300&q=80'}" class="map-popup-img">
                        <h4 class="map-popup-title">${eat.name}</h4>
                        <p style="font-size: 0.76rem; color: var(--text-muted); margin: 2px 0;">📍 ${eat.address}</p>
                        
                        <!-- Premium Video Review Modal trigger button -->
                        <button type="button" class="btn-accent" onclick="openReelsModal('${eat.slug}', '${eat.name.replace(/'/g, "\\'")}', '${signatureDishName}', '${eat.image_path}')" style="width: 100%; justify-content: center; font-size: 0.75rem; padding: 5px 0; border-radius: 6px; margin: 6px 0; gap: 4px;">
                            ${buttonText}
                        </button>
                        
                        <div class="map-popup-footer">
                            <span class="rating-stars" style="font-size: 0.8rem;">⭐ ${parseFloat(eat.rating).toFixed(1)}</span>
                            <a href="/dia-diem/dac-san/${eat.slug}" class="btn-primary" style="padding: 4px 10px; font-size: 0.72rem; border-radius: 6px;">Chi tiết</a>
                        </div>
                    </div>
                `;

                const marker = L.marker([eat.latitude, eat.longitude], { icon: customIcon })
                    .bindPopup(popupContent)
                    .addTo(searchMap);
                
                markers[eat.slug] = marker;
                group.push([eat.latitude, eat.longitude]);
            }
        });

        // Tự động zoom ôm trọn kết quả ban đầu tương đối theo thiết bị
        if (group.length > 0) {
            const specs = getScreenSpecs();
            const boundsPadding = specs.isMobile ? [15, 15] : [40, 40];
            searchMap.fitBounds(group, { padding: boundsPadding });
        }

        // 6. Tính năng lọc Real-Time Sidebar nâng cao (Từ khóa + Category + Commune + Bán kính GPS + Trạng thái đóng mở)
        const sidebarSearchInput = document.getElementById("sidebarSearchInput");
        const categorySelect = document.querySelector("select[name='category_id']");
        const communeSelect = document.querySelector("select[name='commune_id']");
        const openOnlyCheckbox = document.getElementById("openOnlyCheckbox");
        const cards = document.querySelectorAll('#searchCardsContainer .eatery-card');
        const countSpan = document.getElementById("searchCountSpan");

        const removeSign = (str) => {
            return str.normalize("NFD")
                      .replace(/[\u0300-\u036f]/g, "")
                      .replace(/đ/g, "d")
                      .replace(/Đ/g, "D");
        };

        function filterSearchPage() {
            // Không áp dụng lọc khi đang xem Food Trail đặc biệt
            if (activeTrailPolyline) return;

            const query = sidebarSearchInput.value.trim().toLowerCase();
            const queryNoSign = removeSign(query);
            const catId = categorySelect.value;
            const comId = communeSelect.value;
            const openOnlyChecked = openOnlyCheckbox ? openOnlyCheckbox.checked : false;
            
            let matchCount = 0;
            const group = [];

            cards.forEach(card => {
                const name = card.getAttribute('data-name') || '';
                const address = card.getAttribute('data-address') || '';
                const desc = card.getAttribute('data-desc') || '';
                const slug = card.getAttribute('data-slug') || '';
                const cardCatId = card.getAttribute('data-category') || '';
                const cardComId = card.getAttribute('data-commune') || '';

                // Tra cứu thông tin gốc trong mảng eateries để lấy tọa độ & giờ mở cửa
                const eateryInfo = eateries.find(e => e.slug === slug);
                const eatLat = eateryInfo ? parseFloat(eateryInfo.latitude) : null;
                const eatLng = eateryInfo ? parseFloat(eateryInfo.longitude) : null;
                const openingHours = eateryInfo ? eateryInfo.opening_hours : "07:00 - 22:00";

                const matchesQuery = query === '' || name.toLowerCase().includes(query) || address.toLowerCase().includes(query) || desc.toLowerCase().includes(query) || removeSign(name.toLowerCase()).includes(queryNoSign) || removeSign(address.toLowerCase()).includes(queryNoSign) || removeSign(desc.toLowerCase()).includes(queryNoSign);
                const matchesCategory = catId === '' || cardCatId === catId;
                const matchesCommune = comId === '' || cardComId === comId;
                
                // Lọc theo khoảng cách bán kính thực tế (GPS)
                const matchesRadius = !eatLat || !eatLng || getDistance(userLocation.lat, userLocation.lng, eatLat, eatLng) <= currentRadius;
                
                // Lọc theo giờ mở cửa thời gian thực
                const matchesOpen = !openOnlyChecked || isEateryOpen(openingHours);

                if (matchesQuery && matchesCategory && matchesCommune && matchesRadius && matchesOpen) {
                    card.style.setProperty('display', 'flex', 'important');
                    matchCount++;
                    if (markers[slug]) {
                        markers[slug].addTo(searchMap);
                        const latLng = markers[slug].getLatLng();
                        group.push([latLng.lat, latLng.lng]);
                    }
                } else {
                    card.style.setProperty('display', 'none', 'important');
                    if (markers[slug]) {
                        searchMap.removeLayer(markers[slug]);
                    }
                }
            });

            // Cập nhật số lượng kết quả
            if (countSpan) {
                countSpan.innerText = `Tìm thấy ${matchCount} địa điểm phù hợp`;
            }

            // Tự động zoom map để ôm trọn các kết quả còn hiển thị tương đối theo thiết bị
            if (group.length > 0) {
                const specs = getScreenSpecs();
                const boundsPadding = specs.isMobile ? [15, 15] : [40, 40];
                searchMap.fitBounds(group, { padding: boundsPadding });
            }

            // Hiển thị hoặc ẩn thông báo không tìm thấy kết quả
            let noResultDiv = document.getElementById('searchPageNoResults');
            if (matchCount === 0) {
                if (!noResultDiv) {
                    noResultDiv = document.createElement('div');
                    noResultDiv.id = 'searchPageNoResults';
                    noResultDiv.className = 'glass-panel';
                    noResultDiv.style.padding = '60px 40px';
                    noResultDiv.style.textAlign = 'center';
                    noResultDiv.style.color = 'var(--text-muted)';
                    noResultDiv.style.width = '100%';
                    noResultDiv.innerHTML = `
                        <p style="font-size: 1.3rem; margin-bottom: 8px; color: var(--text-main);">😔 Rất tiếc, không tìm thấy kết quả</p>
                        <p style="font-size: 0.9rem;">Thử tìm kiếm với từ khóa khác hoặc điều chỉnh lại các bộ lọc bên trái.</p>
                        <button onclick="clearSearchPageFilters()" class="btn-primary" style="margin-top: 20px; font-size: 0.9rem; cursor: pointer;">Xóa bộ lọc</button>
                    `;
                    document.getElementById('searchCardsContainer').parentNode.appendChild(noResultDiv);
                } else {
                    noResultDiv.style.display = 'block';
                }
            } else {
                if (noResultDiv) {
                    noResultDiv.style.display = 'none';
                }
            }
        }

        window.clearSearchPageFilters = function() {
            sidebarSearchInput.value = '';
            categorySelect.value = '';
            communeSelect.value = '';
            filterSearchPage();
        };

        // Lọc real-time khi gõ từ khóa
        sidebarSearchInput.addEventListener('input', filterSearchPage);
        
        // Lọc real-time khi thay đổi dropdown danh mục
        categorySelect.addEventListener('change', filterSearchPage);

        // Lọc real-time khi thay đổi dropdown xã/thị trấn
        communeSelect.addEventListener('change', filterSearchPage);

        // Chặn reload trang khi submit form, thay vào đó lọc real-time cực kỳ mượt mà
        const filterForm = document.getElementById('filterForm');
        if (filterForm) {
            filterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                filterSearchPage();
            });
        }
    });

    // Bấm card cuộn bản đồ đến marker tương ứng và cuộn màn hình lên mượt mà
    function focusSearchMap(lat, lng, slug) {
        // Cuộn màn hình lên vị trí bản đồ mượt mà
        const mapContainer = document.getElementById('searchMap');
        if (mapContainer) {
            mapContainer.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }

        if (searchMap && markers[slug]) {
            searchMap.flyTo([lat, lng], 15, {
                animate: true,
                duration: 1.2
            });
            setTimeout(() => {
                markers[slug].openPopup();
            }, 1000);
        }
    }

    // ==========================================================================
    // TABS, GPS, HAVERSINE, TRAILS, VOUCHER, & VIDEO REELS CORE SYSTEM
    // ==========================================================================

    // 1. Sidebar Tab Switcher
    window.switchSidebarTab = function(tabName) {
        document.querySelectorAll('.sidebar-tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.sidebar-tab-content').forEach(content => content.classList.remove('active'));
        
        document.getElementById('tabBtn-' + tabName).classList.add('active');
        document.getElementById('tab-' + tabName).classList.add('active');
        
        // Cleanups when leaving tab
        if (tabName !== 'trails') {
            clearActiveTrail();
        }
        if (tabName !== 'vouchers') {
            clearVoucherHunt();
        }
    };

    // 2. Simulated GPS Location & Radius Circle Layer
    let userLocation = L.latLng(21.1182, 105.8394); // Mặc định ở Thôn Mạch Tràng, Cổ Loa, Đông Anh
    let userMarker = null;
    let userRadiusCircle = null;
    let currentRadius = 5.0; // km

    function initUserLocation() {
        if (!searchMap) return;
        
        const userIcon = L.divIcon({
            html: `<div style="background: var(--accent); width: 18px; height: 18px; border-radius: 50%; border: 3px solid #ffffff; box-shadow: 0 0 12px var(--accent); position: relative;"><div style="position: absolute; inset: -4px; border-radius: 50%; border: 2px solid var(--accent); animation: pulseGps 1.8s infinite;"></div></div>`,
            className: 'user-location-marker',
            iconSize: [18, 18],
            iconAnchor: [9, 9]
        });
        
        userMarker = L.marker(userLocation, { icon: userIcon, draggable: true }).addTo(searchMap);
        userMarker.bindTooltip("<b>Vị trí của bạn (Kéo thả để di chuyển)</b>", { permanent: false, direction: 'top' });
        
        userMarker.on('dragend', function(e) {
            userLocation = e.target.getLatLng();
            updateRadiusCircle();
            filterSearchPage();
        });
        
        updateRadiusCircle();
    }

    function updateRadiusCircle() {
        if (!searchMap) return;
        if (userRadiusCircle) {
            searchMap.removeLayer(userRadiusCircle);
        }
        
        userRadiusCircle = L.circle(userLocation, {
            radius: currentRadius * 1000, // mét
            color: 'var(--accent)',
            fillColor: 'var(--accent)',
            fillOpacity: 0.06,
            weight: 1.5,
            dashArray: '5 5'
        }).addTo(searchMap);
    }

    window.updateRadiusFilter = function(value) {
        currentRadius = parseFloat(value);
        document.getElementById('radiusValText').innerText = currentRadius.toFixed(1) + ' km';
        updateRadiusCircle();
        filterSearchPage();
    };

    window.simulateRealGPS = function() {
        if (navigator.geolocation) {
            document.getElementById('gpsStatusText').innerText = "📡 Đang định vị GPS...";
            document.getElementById('gpsPulseDot').style.backgroundColor = 'var(--primary)';
            
            navigator.geolocation.getCurrentPosition(function(position) {
                userLocation = L.latLng(position.coords.latitude, position.coords.longitude);
                document.getElementById('gpsStatusText').innerText = "📍 GPS: Vị trí thực tế";
                document.getElementById('gpsPulseDot').style.backgroundColor = 'var(--accent)';
                
                if (userMarker) userMarker.setLatLng(userLocation);
                searchMap.setView(userLocation, 14);
                updateRadiusCircle();
                filterSearchPage();
            }, function(error) {
                alert("Không lấy được GPS. Hệ thống tiếp tục sử dụng vị trí giả lập tại Cổ Loa.");
                document.getElementById('gpsStatusText').innerText = "📍 GPS: Cổ Loa (Giả lập)";
                document.getElementById('gpsPulseDot').style.backgroundColor = 'var(--accent)';
            });
        } else {
            alert("Trình duyệt không hỗ trợ Geolocation.");
        }
    };

    // 3. Haversine Distance Calculator
    function getDistance(lat1, lon1, lat2, lon2) {
        const R = 6371; // km
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = 
            Math.sin(dLat/2) * Math.sin(dLat/2) +
            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * 
            Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }

    // 4. Real-time Opening Hours Check
    function isEateryOpen(openingHours) {
        if (!openingHours) return true;
        const hours = openingHours.trim().toLowerCase();
        if (hours.includes('24/7') || hours.includes('cả ngày') || hours.includes('24/24')) return true;
        
        try {
            const timeParts = hours.match(/(\d{2}):(\d{2})\s*-\s*(\d{2}):(\d{2})/);
            if (!timeParts) return true;
            
            const now = new Date();
            const currentMin = now.getHours() * 60 + now.getMinutes();
            
            const startMin = parseInt(timeParts[1]) * 60 + parseInt(timeParts[2]);
            const endMin = parseInt(timeParts[3]) * 60 + parseInt(timeParts[4]);
            
            if (endMin < startMin) { // Qua đêm
                return currentMin >= startMin || currentMin <= endMin;
            }
            return currentMin >= startMin && currentMin <= endMin;
        } catch (e) {
            return true;
        }
    }

    // 5. Pre-designed Food Trails
    let activeTrailPolyline = null;
    let activeTrailMarkers = [];

    window.selectFoodTrail = function(trailId) {
        clearActiveTrail();
        
        document.querySelectorAll('.trail-card').forEach(c => c.classList.remove('active'));
        document.getElementById('trail-' + trailId).classList.add('active');
        document.getElementById('clearTrailBtn').style.display = 'block';
        
        let trailCoords = [];
        let trailEateries = [];
        
        if (trailId === 'colo-specialty') {
            // Đặc sản Cổ Loa
            trailEateries = [
                eateries.find(e => e.slug === 'bun-mach-trang-co-loa'),
                eateries.find(e => e.slug === 'tiem-lau-nuong-co-loa-hoi-quan'),
                eateries.find(e => e.slug === 'ca-phe-gio-vinh-ngoc')
            ];
        } else if (trailId === 'downtown-crawl') {
            // Phố thị Cao Lỗ
            trailEateries = [
                eateries.find(e => e.slug === 'pho-bo-gia-truyen-cao-lo'),
                eateries.find(e => e.slug === 'khach-san-dong-anh-luxury-hotel'),
                eateries.find(e => e.slug === 'ca-phe-gio-vinh-ngoc')
            ];
        }
        
        trailEateries = trailEateries.filter(e => e !== undefined && e.latitude !== null);
        if (trailEateries.length === 0) return;
        
        // Ẩn các marker thông thường
        for (let slug in markers) {
            searchMap.removeLayer(markers[slug]);
        }
        
        // Vẽ checkpoint và nối Polyline
        trailEateries.forEach((eat, index) => {
            trailCoords.push([eat.latitude, eat.longitude]);
            
            const checkpointIcon = L.divIcon({
                html: `<div class="checkpoint-number-marker">${index + 1}</div>`,
                className: 'checkpoint-marker-wrap',
                iconSize: [28, 28],
                iconAnchor: [14, 14]
            });
            
            const popupContent = `
                <div class="map-popup-card">
                    <div style="background: var(--primary); color: #fff; padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 700; width: max-content; margin-bottom: 5px;">STOP ${index + 1}</div>
                    <h4 class="map-popup-title">${eat.name}</h4>
                    <p style="font-size: 0.76rem; color: var(--text-muted); margin: 2px 0;">📍 ${eat.address}</p>
                    <div class="map-popup-footer" style="margin-top: 6px;">
                        <a href="/dia-diem/dac-san/${eat.slug}" class="btn-primary" style="padding: 4px 10px; font-size: 0.72rem; border-radius: 6px;">Xem chi tiết</a>
                    </div>
                </div>
            `;
            
            const marker = L.marker([eat.latitude, eat.longitude], { icon: checkpointIcon })
                .bindPopup(popupContent)
                .addTo(searchMap);
                
            activeTrailMarkers.push(marker);
        });
        
        activeTrailPolyline = L.polyline(trailCoords, {
            color: 'var(--primary)',
            weight: 4.5,
            opacity: 0.9,
            dashArray: '8 8',
            lineCap: 'round',
            lineJoin: 'round'
        }).addTo(searchMap);
        
        const specs = getScreenSpecs();
        const trailPadding = specs.isMobile ? [20, 20] : [50, 50];
        searchMap.fitBounds(activeTrailPolyline.getBounds(), { padding: trailPadding });
        
        // Hiển thị hộp điều khiển nổi trên bản đồ
        const floatingInfo = document.createElement('div');
        floatingInfo.id = 'floatingTrailInfo';
        floatingInfo.className = 'trail-info-popup glass-panel';
        floatingInfo.innerHTML = `
            <h4 style="color: var(--primary); font-size: 0.85rem; margin-bottom: 2px; text-transform: uppercase; font-weight: 800; letter-spacing: 0.5px;">⚡ Lộ trình đang xem</h4>
            <div style="font-size: 0.9rem; font-weight: 700; color: var(--text-main);">${trailId === 'colo-specialty' ? 'Food Tour Đặc sản Cổ Loa' : 'Khám Phá Phố Thị Cao Lỗ'}</div>
            <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 4px; display: flex; gap: 10px;">
                <span>⏱️ ${trailId === 'colo-specialty' ? '2.5' : '1.5'} giờ hành trình</span>
                <span>📏 ${trailId === 'colo-specialty' ? '4.8' : '3.2'} km</span>
            </div>
        `;
        document.getElementById('searchMap').appendChild(floatingInfo);
    };

    window.clearActiveTrail = function() {
        if (activeTrailPolyline) {
            searchMap.removeLayer(activeTrailPolyline);
            activeTrailPolyline = null;
        }
        
        activeTrailMarkers.forEach(m => searchMap.removeLayer(m));
        activeTrailMarkers = [];
        
        const floatingInfo = document.getElementById('floatingTrailInfo');
        if (floatingInfo) floatingInfo.remove();
        
        document.querySelectorAll('.trail-card').forEach(c => c.classList.remove('active'));
        document.getElementById('clearTrailBtn').style.display = 'none';
        
        filterSearchPage();
    };

    // 6. Voucher Gamification Scan Radar & Unboxing
    let isHunting = false;
    let radarEffectLayer = null;
    let voucherMarkers = [];

    window.toggleVoucherHunt = function() {
        if (isHunting) {
            clearVoucherHunt();
            return;
        }
        
        isHunting = true;
        document.getElementById('startRadarBtn').innerText = "🛑 Dừng quét radar";
        document.getElementById('startRadarBtn').classList.replace('btn-primary', 'btn-secondary');
        document.getElementById('huntStatusDiv').style.display = 'block';
        
        // Radar Pulse layer
        const radarIcon = L.divIcon({
            html: `<div class="radar-pulse-effect"></div>`,
            className: 'radar-pulse-wrap',
            iconSize: [200, 200],
            iconAnchor: [100, 100]
        });
        
        radarEffectLayer = L.marker(userLocation, { icon: radarIcon }).addTo(searchMap);
        
        // Spawn 3 hộp quà ngẫu nhiên
        spawnGiftBoxes(3);
    };

    function spawnGiftBoxes(count) {
        voucherMarkers.forEach(m => searchMap.removeLayer(m));
        voucherMarkers = [];
        
        const mockVouchers = [
            { code: "TAFOOD50", detail: "Giảm ngay 50.000đ cho đơn từ 150.000đ" },
            { code: "HE2026", detail: "Giảm ngay 20.000đ cho mọi đơn đặt món" },
            { code: "COLOA10", detail: "Giảm 10% tối đa 30.000đ khi đi Foodtour Cổ Loa" }
        ];
        
        for (let i = 0; i < count; i++) {
            // Tọa độ ngẫu nhiên xung quanh GPS của người dùng
            const r = 0.015 * Math.random(); // trong bán kính 1.5km
            const theta = Math.random() * 2 * Math.PI;
            const giftLat = userLocation.lat + r * Math.sin(theta);
            const giftLng = userLocation.lng + r * Math.cos(theta);
            
            const giftIcon = L.divIcon({
                html: `<div class="giftbox-bounce-marker">🎁</div>`,
                className: 'giftbox-marker-wrap',
                iconSize: [34, 34],
                iconAnchor: [17, 17]
            });
            
            const voucher = mockVouchers[i % mockVouchers.length];
            
            const marker = L.marker([giftLat, giftLng], { icon: giftIcon }).addTo(searchMap);
            marker.bindTooltip("<b>Hộp quà Voucher! Bấm để mở</b>", { permanent: false, direction: 'top' });
            
            marker.on('click', function() {
                triggerUnboxingModal(voucher, marker);
            });
            
            voucherMarkers.push(marker);
        }
    }

    window.clearVoucherHunt = function() {
        isHunting = false;
        document.getElementById('startRadarBtn').innerText = "🎯 Bắt đầu săn Hộp Quà";
        document.getElementById('startRadarBtn').classList.replace('btn-secondary', 'btn-primary');
        document.getElementById('huntStatusDiv').style.display = 'none';
        
        if (radarEffectLayer) {
            searchMap.removeLayer(radarEffectLayer);
            radarEffectLayer = null;
        }
        
        voucherMarkers.forEach(m => searchMap.removeLayer(m));
        voucherMarkers = [];
    };

    let selectedVoucher = null;
    let targetGiftMarker = null;

    function triggerUnboxingModal(voucher, marker) {
        selectedVoucher = voucher;
        targetGiftMarker = marker;
        
        document.getElementById('unboxingResultDiv').style.display = 'none';
        document.getElementById('unboxingGiftBox').style.display = 'inline-block';
        document.getElementById('unboxingGiftBox').className = 'gift-box-large';
        
        document.getElementById('voucherGiftModal').style.display = 'flex';
    }

    window.closeVoucherModal = function() {
        document.getElementById('voucherGiftModal').style.display = 'none';
    };

    window.unboxVoucherGift = function() {
        const box = document.getElementById('unboxingGiftBox');
        box.className = 'gift-box-large shake';
        
        setTimeout(() => {
            box.style.display = 'none';
            document.getElementById('scratchVoucherCode').innerText = selectedVoucher.code;
            document.getElementById('scratchVoucherDetails').innerText = selectedVoucher.detail;
            document.getElementById('unboxingResultDiv').style.display = 'block';
            
            generateConfetti();
            
            if (targetGiftMarker && searchMap) {
                searchMap.removeLayer(targetGiftMarker);
                voucherMarkers = voucherMarkers.filter(m => m !== targetGiftMarker);
            }
        }, 1200);
    };

    window.claimVoucher = function() {
        alert("🎉 Chúc mừng! Voucher " + selectedVoucher.code + " đã được thêm thành công vào ví ưu đãi TA-FOOD của bạn!");
        closeVoucherModal();
    };

    function generateConfetti() {
        const container = document.getElementById('voucherGiftModal');
        const colors = ['#ff7e29', '#f04e23', '#20b2aa', '#ff3366', '#ffc107'];
        
        for (let i = 0; i < 50; i++) {
            const confetti = document.createElement('div');
            confetti.className = 'confetti-particle';
            confetti.style.left = Math.random() * 100 + '%';
            confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
            confetti.style.animationDelay = Math.random() * 1.5 + 's';
            confetti.style.transform = `rotate(${Math.random() * 360}deg)`;
            
            container.appendChild(confetti);
            setTimeout(() => confetti.remove(), 3500);
        }
    }

    // 7. Immersive TikTok Reels Video Player
    let currentLikeCount = 3800;
    let isLiked = false;
    let currentEateryReels = [];
    let currentReelIndex = 0;
    let currentEateryName = '';
    let currentSpecialtyName = '';

    // Helper functions to extract video IDs
    function getTikTokVideoId(url) {
        if (url.includes('vt.tiktok.com')) {
            return null; // Let iframe render standard fallback
        }
        const matches = url.match(/\/video\/(\d+)/i);
        if (matches && matches[1]) return matches[1];
        const shortMatches = url.match(/\/v\/(\d+)/i);
        if (shortMatches && shortMatches[1]) return shortMatches[1];
        return null;
    }

    function getYouTubeShortsId(url) {
        const regExp = /^.*(shorts\/|youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
        const match = url.match(regExp);
        return (match && match[1] && match[2] && match[2].length === 11) ? match[2] : (match && match[1] && match[1].length === 11 ? match[1] : null);
    }

    window.openReelsModal = function(eaterySlug, eateryName, specialtyName, imagePath) {
        currentEateryName = eateryName;
        currentSpecialtyName = specialtyName;
        
        // Find eatery and its approved videos
        const eat = eateries.find(e => e.slug === eaterySlug);
        let approvedVideos = [];
        if (eat && eat.review_videos) {
            approvedVideos = eat.review_videos;
        }
        
        if (approvedVideos.length === 0) {
            // Fallback mock video if no specific video is linked
            currentEateryReels = [{
                video_url: 'https://assets.mixkit.co/videos/preview/mixkit-chef-preparing-a-fresh-vegetable-salad-32860-large.mp4',
                video_type: 'local'
            }];
        } else {
            currentEateryReels = approvedVideos;
        }
        
        currentReelIndex = 0;
        document.getElementById('reelsModal').style.display = 'flex';
        
        playReelAtIndex(currentReelIndex);
    };

    function playReelAtIndex(index) {
        if (index < 0 || index >= currentEateryReels.length) return;
        
        const reel = currentEateryReels[index];
        document.getElementById('reelsEateryName').innerText = currentEateryName;
        
        // Dynamic description including video count
        const videoIndicator = currentEateryReels.length > 1 ? `[Video ${index + 1}/${currentEateryReels.length}] ` : '';
        document.getElementById('reelsVideoDesc').innerText = `${videoIndicator}Khám phá món ngon tại "${currentEateryName}". Đặc sản "${currentSpecialtyName}" đang làm nức lòng thực khách gần xa bởi hương vị đậm chất truyền thống Đông Anh!`;
        document.querySelector('.reels-signature-tag').innerText = `🌟 Món đặc trưng: ${currentSpecialtyName}`;
        
        // Update navigation arrows visibility
        const prevBtn = document.getElementById('searchPrevReelBtn');
        const nextBtn = document.getElementById('searchNextReelBtn');
        if (prevBtn && nextBtn) {
            prevBtn.style.display = index > 0 ? 'flex' : 'none';
            nextBtn.style.display = index < currentEateryReels.length - 1 ? 'flex' : 'none';
        }
        
        const wrapper = document.getElementById('reelPlayerWrapper');
        wrapper.innerHTML = ''; // Clear previous player
        
        let videoUrl = reel.video_url;
        let videoType = reel.video_type;

        if (videoType === 'tiktok') {
            const videoId = getTikTokVideoId(videoUrl);
            if (videoId) {
                wrapper.innerHTML = `<iframe src="https://www.tiktok.com/embed/v2/${videoId}" style="width: 100%; height: 100%; border: none; background: #000; pointer-events: auto;" allowfullscreen allow="autoplay; encrypted-media;"></iframe>`;
            } else {
                wrapper.innerHTML = `<iframe src="${videoUrl}" style="width: 100%; height: 100%; border: none; background: #000; pointer-events: auto;" allowfullscreen allow="autoplay;"></iframe>`;
            }
        } else if (videoType === 'youtube_shorts') {
            const shortsId = getYouTubeShortsId(videoUrl);
            if (shortsId) {
                wrapper.innerHTML = `<iframe src="https://www.youtube.com/embed/${shortsId}?autoplay=1" style="width: 100%; height: 100%; border: none; background: #000; pointer-events: auto;" allowfullscreen allow="autoplay; encrypted-media;"></iframe>`;
            } else {
                wrapper.innerHTML = `<iframe src="${videoUrl}" style="width: 100%; height: 100%; border: none; background: #000; pointer-events: auto;" allowfullscreen allow="autoplay;"></iframe>`;
            }
        } else {
            // Local direct mp4 storage file
            wrapper.innerHTML = `<video src="${videoUrl}" autoplay loop muted playsinline controls style="width: 100%; height: 100%; object-fit: cover; background: #000; pointer-events: auto;"></video>`;
        }
    }

    window.searchPrevReel = function() {
        if (currentReelIndex > 0) {
            currentReelIndex--;
            playReelAtIndex(currentReelIndex);
        }
    };

    window.searchNextReel = function() {
        if (currentReelIndex < currentEateryReels.length - 1) {
            currentReelIndex++;
            playReelAtIndex(currentReelIndex);
        }
    };

    window.closeReelsModal = function() {
        document.getElementById('reelPlayerWrapper').innerHTML = ''; // Clear player
        document.getElementById('reelsModal').style.display = 'none';
    };

    window.toggleReelsLike = function() {
        const likeBtn = document.getElementById('reelsLikeBtn');
        const countSpan = document.getElementById('reelsLikeCount');
        
        if (isLiked) {
            isLiked = false;
            currentLikeCount--;
            likeBtn.querySelector('span').style.color = '#fff';
        } else {
            isLiked = true;
            currentLikeCount++;
            likeBtn.querySelector('span').style.color = '#ff3366';
            
            // Tim bay từ tâm
            spawnSingleHeart(window.innerWidth / 2, window.innerHeight / 2);
        }
        countSpan.innerText = (currentLikeCount / 1000).toFixed(1) + 'K';
    };

    window.triggerDoubleTapHeart = function(event) {
        spawnSingleHeart(event.clientX, event.clientY);
        if (!isLiked) {
            toggleReelsLike();
        }
    };

    function spawnSingleHeart(x, y) {
        const container = document.getElementById('reelsModal');
        const heart = document.createElement('div');
        heart.className = 'floating-heart';
        heart.innerHTML = '❤️';
        heart.style.left = x + 'px';
        heart.style.top = y + 'px';
        
        const dx = (Math.random() * 120 - 60) + 'px';
        const rot = (Math.random() * 70 - 35) + 'deg';
        heart.style.setProperty('--dx', dx);
        heart.style.setProperty('--rot', rot);
        
        container.appendChild(heart);
        setTimeout(() => heart.remove(), 1000);
    }

</script>
@endsection
