@extends('layouts.app')

@section('title', 'Bản đồ số Ẩm thực Đông Anh - Số hóa Ẩm thực Địa phương')

@section('content')
<!-- Hero banner with active typewriter effect -->
<section class="hero-banner">
    <div class="container" style="max-width: 800px;">
        <h1 style="font-size: 2.8rem; margin-bottom: 12px; background: var(--primary-grad); -webkit-background-clip: text; -webkit-text-fill-color: transparent; padding: 8px 0; line-height: 1.3;">
            Bản đồ số Ẩm thực Đông Anh
        </h1>
        <p style="color: var(--text-muted); font-size: 1.1rem; margin-bottom: 30px; font-weight: 500;">
            Số hóa quán ăn, nhà hàng, quán cà phê, khách sạn và di sản đặc sản tại Đông Anh
        </p>
        
        <!-- Search bar with suggestions -->
        <div class="search-container">
            <form action="/tim-kiem" method="GET" class="search-form" id="searchForm">
                <div class="search-box glass-panel">
                    <span style="font-size: 1.3rem; margin-left: 16px;">🔍</span>
                    <input type="text" name="q" id="searchInput" class="search-input" placeholder="Tìm kiếm 'Bún chả', 'Cafe Vĩnh Ngọc', 'Lẩu gần Cổ Loa'..." autocomplete="off">
                    <button type="submit" class="btn-primary" style="border-radius: 40px; padding: 12px 24px; font-weight: 600; box-shadow: 0 4px 15px rgba(255,126,41,0.3);">Tìm kiếm</button>
                </div>
            </form>
            <div id="suggestionDropdown" class="autocomplete-suggestions glass-panel"></div>
        </div>
    </div>
</section>

<!-- Categories Slider -->
<div class="container" style="margin-bottom: 24px;">
    <div class="categories-slider">
        <a href="/" class="category-card glass-panel {{ !$selectedCatSlug ? 'active' : '' }}">
            <span class="cat-icon">🗺️</span>
            <span class="cat-name">Tất cả</span>
        </a>
        @foreach($categories as $cat)
            <a href="/?cat={{ $cat->slug }}" class="category-card glass-panel {{ $selectedCatSlug === $cat->slug ? 'active' : '' }} {{ $cat->slug === 'dac-san-dia-phuong' ? 'specialty-highlight-card' : '' }}">
                <span class="cat-icon">{{ $cat->icon }}</span>
                <span class="cat-name">{{ $cat->name }}</span>
            </a>
        @endforeach
    </div>
</div>

<!-- Interactive Split Screen Layout -->
<section class="split-screen" style="border-top: 1px solid var(--border-glow);">
    
    <!-- Left column: Scrollable feed of eateries -->
    <div class="split-list">
        <div id="listHeaderContainer">
            @if($selectedCatSlug === 'dac-san-dia-phuong')
                <div style="margin-bottom: 20px; border-bottom: 1.5px dashed rgba(212, 175, 55, 0.3); padding-bottom: 16px;">
                    <span class="heritage-badge" style="margin-bottom: 8px; font-size: 0.7rem; font-weight: 800; letter-spacing: 1.5px; border: 1px solid rgba(212, 175, 55, 0.4); background: rgba(212, 175, 55, 0.1); color: #ffb300; padding: 4px 10px; border-radius: 20px; display: inline-block;">🏛️ BẢN ĐỒ DI SẢN SỐ CỐ ĐÔ</span>
                    <h2 style="font-size: 1.6rem; font-family: var(--font-heading); font-weight: 800; margin: 4px 0 6px 0; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                        <span style="background: linear-gradient(135deg, #d97706 0%, #b45309 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Không Gian Số Di Sản Ẩm Thực</span>
                        <span id="resultsCountSpan" style="font-size: 0.85rem; color: var(--text-muted); font-weight: normal;">
                            ({{ $eateries->count() }} di sản)
                        </span>
                    </h2>
                    <p style="font-size: 0.88rem; color: var(--text-muted); line-height: 1.5; margin: 0;">
                        Khám phá nguồn gốc lịch sử, câu chuyện làng nghề và gặp gỡ các nghệ nhân gìn giữ tinh hoa ẩm thực Kinh Bắc qua hàng ngàn năm dựng nước & giữ nước.
                    </p>
                </div>
            @else
                <h2 style="font-size: 1.25rem; margin: 6px 0 0 0; font-family: var(--font-heading); font-weight: 700; line-height: 1.4; color: var(--text-main);">
                    <span style="margin-right: 4px;">📍</span> 
                    @if($selectedCatSlug)
                        Danh sách: <span style="color: var(--primary);">{{ $categories->where('slug', $selectedCatSlug)->first()->name }}</span>
                    @else
                        Địa điểm nổi bật tại Đông Anh
                    @endif
                    <span id="resultsCountSpan" style="font-size: 0.8rem; color: var(--text-muted); font-weight: normal; margin-left: 6px; display: inline-block; white-space: nowrap;">
                        ({{ $eateries->count() }} kết quả)
                    </span>
                </h2>
            @endif
        </div>
        
        <div id="eateriesListContainer" style="display: flex; flex-direction: column; gap: 24px; width: 100%;">
            @if($eateries->count() > 0)
                @foreach($eateries as $eat)
                    <div class="eatery-card glass-panel" 
                         data-slug="{{ $eat->slug }}"
                         data-name="{{ $eat->name }}"
                         data-address="{{ $eat->address }}"
                         data-desc="{{ $eat->description }}"
                         data-commune="{{ $eat->commune->name }}"
                         data-category="{{ $eat->category->slug }}"
                         onclick="focusOnEatery({{ $eat->latitude }}, {{ $eat->longitude }}, '{{ $eat->slug }}')">
                        <div class="eatery-img-wrapper">
                            <img src="{{ $eat->image_path ?: 'https://images.unsplash.com/photo-1591814468924-caf88d1232e1?auto=format&fit=crop&w=300&q=80' }}" class="eatery-img" alt="{{ $eat->name }}">
                            <div style="position: absolute; top: 8px; left: 8px; max-width: calc(100% - 16px); display: flex; align-items: center; gap: 4px; font-size: 0.68rem; font-weight: 700; color: #ffffff; background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(4px); padding: 4px 8px; border-radius: 6px; box-shadow: 0 2px 10px rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1);">
                                <span>{{ $eat->category->icon }}</span>
                                <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $eat->category->name }}</span>
                            </div>
                        </div>
                        <div class="eatery-info">
                            <div class="eatery-header">
                                <h3 class="eatery-title">{{ $eat->name }}</h3>
                                <div class="rating-stars">
                                    <span>⭐</span> {{ $eat->average_rating }}
                                </div>
                            </div>
                            <p class="eatery-desc">{{ $eat->description }}</p>
                            <div class="eatery-footer">
                                <div class="eatery-meta-item">
                                    <span>📍</span> {{ $eat->commune->name }}
                                </div>
                                <div class="eatery-meta-item" style="color: var(--primary); font-weight: 600;">
                                    {{ $eat->price_range }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="glass-panel" style="padding: 40px; text-align: center; color: var(--text-muted); width: 100%;">
                    <p style="font-size: 1.2rem; margin-bottom: 8px;">😔 Không tìm thấy địa điểm nào phù hợp</p>
                    <p style="font-size: 0.9rem;">Hãy thử lọc danh mục khác hoặc xóa bộ lọc để khám phá lại toàn bộ Đông Anh!</p>
                    <a href="/" class="btn-primary" style="margin-top: 16px; padding: 8px 16px; text-decoration: none; display: inline-block;">Xem tất cả</a>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Right column: Premium Leaflet map -->
    <div class="split-map-container" style="position: relative;">
        <div id="map"></div>
        
        <!-- Floating Tóp Tóp Reels FAB removed -->
    </div>
</section>

<!-- Fullscreen Reels Modal -->
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
            
            <div class="reels-overlay-info" style="z-index: 10; bottom: 20px; left: 16px; right: 80px;">
                <h3 class="reels-eatery-name" id="reelsEateryName" style="font-size: 1.05rem; font-weight: 700; text-shadow: 0 2px 4px rgba(0,0,0,0.95); margin: 0; font-family: var(--font-heading); color: #ffffff;">Bún Mạch Tràng Cổ Loa</h3>
                <p class="reels-desc" id="reelsVideoDesc" style="display: none !important;"></p>
                <span class="reels-signature-tag" style="display: none !important;"></span>
            </div>
            
            <!-- Double click/Tap overlay to fly hearts -->
            <div id="reelTapOverlay" style="position: absolute; inset: 0; z-index: 5; pointer-events: auto;" onclick="triggerDoubleTapHeart(event)"></div>
        </div>
        
        <!-- Right Action sidebar (Premium Glassmorphic Cinema style) -->
        <div class="reels-side-actions" style="right: 14px; bottom: 50px; gap: 14px;">
            <div style="display: flex; flex-direction: column; align-items: center;">
                <button type="button" class="reels-action-btn" id="reelsLikeBtn" onclick="toggleReelsLike()" style="background: rgba(255, 255, 255, 0.12); width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 4px 15px rgba(0,0,0,0.3);" onmouseover="this.style.transform='scale(1.15) translateY(-2px)'; this.style.backgroundColor='rgba(255, 255, 255, 0.25)';" onmouseout="this.style.transform='scale(1)'; this.style.backgroundColor='rgba(255, 255, 255, 0.12)';">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="#ff3366" stroke="#ff3366" stroke-width="2" style="filter: drop-shadow(0 0 4px rgba(255, 51, 102, 0.6));"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                </button>
                <span class="reels-action-label" id="reelsLikeCount" style="margin-top: 6px; font-weight: 700; text-shadow: 0 2px 4px rgba(0,0,0,0.8); font-size: 0.72rem;">3.8K</span>
            </div>
            
            <div style="display: flex; flex-direction: column; align-items: center;">
                <button type="button" class="reels-action-btn" onclick="alert('Đã thêm quán ăn này vào Danh sách Yêu thích của bạn!')" style="background: rgba(255, 255, 255, 0.12); width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 4px 15px rgba(0,0,0,0.3);" onmouseover="this.style.transform='scale(1.15) translateY(-2px)'; this.style.backgroundColor='rgba(255, 255, 255, 0.25)';" onmouseout="this.style.transform='scale(1)'; this.style.backgroundColor='rgba(255, 255, 255, 0.12)';">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="#ffb800" stroke="#ffb800" stroke-width="2" style="filter: drop-shadow(0 0 4px rgba(255, 184, 0, 0.6));"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                </button>
                <span class="reels-action-label" style="margin-top: 6px; font-weight: 700; text-shadow: 0 2px 4px rgba(0,0,0,0.8); font-size: 0.72rem;">4.8</span>
            </div>
            
            <div style="display: flex; flex-direction: column; align-items: center;">
                <button type="button" class="reels-action-btn" onclick="navigator.clipboard.writeText(window.location.href); alert('Đã sao chép liên kết chia sẻ review của quán!');" style="background: rgba(255, 255, 255, 0.12); width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 4px 15px rgba(0,0,0,0.3);" onmouseover="this.style.transform='scale(1.15) translateY(-2px)'; this.style.backgroundColor='rgba(255, 255, 255, 0.25)';" onmouseout="this.style.transform='scale(1)'; this.style.backgroundColor='rgba(255, 255, 255, 0.12)';">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="filter: drop-shadow(0 0 3px rgba(255,255,255,0.4));"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                </button>
                <span class="reels-action-label" style="margin-top: 6px; font-weight: 700; text-shadow: 0 2px 4px rgba(0,0,0,0.8); font-size: 0.72rem;">Chia sẻ</span>
            </div>
            
            <div class="reels-music-disc" style="border: 2px solid var(--primary); background: radial-gradient(circle, #f04e23 30%, #000 70%); font-size: 0.95rem; box-shadow: 0 0 10px rgba(240, 78, 35, 0.5);">🍜</div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // 1. Khởi tạo dữ liệu JSON của các quán ăn được truyền từ PHP Controller
    const eateries = @json($eateries);
    let map;
    let markers = {};

    // Hàm tự động cuộn thẻ danh mục được chọn vào chính giữa thanh trượt trơn tru (Google Maps/Airbnb Style)
    function centerActiveCategoryCard(cardElement) {
        const slider = document.querySelector('.categories-slider');
        if (!slider || !cardElement) return;

        const sliderWidth = slider.clientWidth;
        const sliderRect = slider.getBoundingClientRect();
        const cardRect = cardElement.getBoundingClientRect();
        
        // Vị trí thực tế của thẻ so với điểm bắt đầu của nội dung trượt (kể cả khi đã cuộn)
        const relativeLeft = cardRect.left - sliderRect.left + slider.scrollLeft;
        const cardWidth = cardElement.clientWidth;

        // Tính toán khoảng cách để đưa thẻ về giữa
        const targetScrollLeft = relativeLeft - (sliderWidth / 2) + (cardWidth / 2);

        slider.scrollTo({
            left: targetScrollLeft,
            behavior: 'smooth'
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        // 2. Thiết lập bản đồ Leaflet tâm vị trí Đông Anh (huyện lỵ)
        map = L.map('map', {
            zoomControl: false, // Chúng ta sẽ tùy chỉnh vị trí nút zoom
            zoomSnap: 0.5,       // Bước zoom 0.5 giúp phản hồi nhanh nhạy
            zoomDelta: 0.5,      // Độ nhảy zoom mỗi lần cuộn
            wheelPxPerZoomLevel: 60, // Tốc độ zoom tiêu chuẩn nhanh & mượt
            zoomAnimation: true,
            fadeAnimation: true,
            markerZoomAnimation: true
        }).setView([21.1352, 105.8458], 13);
        
        L.control.zoom({ position: 'bottomright' }).addTo(map);

        // 3. Sử dụng Tileset phù hợp chế độ Sáng/Tối (Sử dụng Google Maps chính thức cho bản đồ sáng)
        let currentTheme = localStorage.getItem('theme') || 'dark';
        let tileUrl = currentTheme === 'light' 
            ? 'https://mt1.google.com/vt/lyrs=m&hl=vi&x={x}&y={y}&z={z}'
            : 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png';
            
        let activeTileLayer = L.tileLayer(tileUrl, {
            attribution: currentTheme === 'light' ? '&copy; Google Maps' : '&copy; OpenStreetMap &copy; CARTO',
            subdomains: 'abcd',
            maxZoom: 20
        }).addTo(map);

        // Lắng nghe sự kiện đổi chế độ Sáng/Tối để đổi lớp nền bản đồ tức thì
        document.addEventListener('theme-changed', function(e) {
            const nextTheme = e.detail.theme;
            const nextTileUrl = nextTheme === 'light'
                ? 'https://mt1.google.com/vt/lyrs=m&hl=vi&x={x}&y={y}&z={z}'
                : 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png';
            
            map.removeLayer(activeTileLayer);
            activeTileLayer = L.tileLayer(nextTileUrl, {
                attribution: nextTheme === 'light' ? '&copy; Google Maps' : '&copy; OpenStreetMap &copy; CARTO',
                subdomains: 'abcd',
                maxZoom: 20
            }).addTo(map);
        });

        // 4. Định nghĩa hàm vẽ các địa điểm lên Bản đồ (Hỗ trợ gọi lại khi lọc AJAX)
        window.renderEateryMarkers = function(eateriesList) {
            // Xóa toàn bộ markers cũ trên bản đồ
            Object.values(markers).forEach(marker => {
                map.removeLayer(marker);
            });
            markers = {};

            eateriesList.forEach(function(eat) {
                if (eat.latitude && eat.longitude) {
                    // Biểu tượng Marker tùy chỉnh dựa trên danh mục để người dùng phân biệt trực quan
                    let categoryColor = '#ff7e29'; // Mặc định cam
                    const catSlug = eat.category ? (eat.category.slug || eat.category) : '';
                    
                    if (catSlug === 'bun-pho') categoryColor = '#ff3366';
                    else if (catSlug === 'lau-nuong') categoryColor = '#ff3300';
                    else if (catSlug === 'quan-cafe') categoryColor = '#20b2aa';
                    else if (catSlug === 'khach-san-nha-nghi') categoryColor = '#9d4edd';
                    else if (catSlug === 'dac-san-dia-phuong') categoryColor = '#38b000';
                    else if (catSlug === 'cho-truyen-thong') categoryColor = '#e63946';
                    else if (catSlug === 'cho-dan-sinh') categoryColor = '#f77f00';
                    else if (catSlug === 'sieu-thi') categoryColor = '#4361ee';
                    else if (catSlug === 'cua-hang-tien-ich') categoryColor = '#7209b7';

                    const catIcon = eat.category ? (eat.category.icon || '📍') : '📍';
                    const catName = eat.category ? (eat.category.name || '') : '';
                    const communeName = eat.commune ? (eat.commune.name || eat.commune) : '';
                    const ratingVal = eat.average_rating || (eat.rating ? parseFloat(eat.rating).toFixed(1) : '5.0');

                    const customIcon = L.divIcon({
                        html: `<div style="background-color: ${categoryColor}; width: 28px; height: 28px; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 10px rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">${catIcon}</div>`,
                        className: 'custom-leaflet-marker',
                        iconSize: [28, 28],
                        iconAnchor: [14, 14]
                    });

                    const signatureDishName = catSlug === 'bun-pho' ? 'Phở Bò Tái Lăn Cao Lỗ' : 
                                             (catSlug === 'dac-san-dia-phuong' ? 'Bún Mạch Tràng Trộn Nghệ' : 
                                             (catSlug === 'lau-nuong' ? 'Lẩu Nướng Sườn Sụn Cổ Loa' : 'Món Ngon Đặc Trưng'));

                    // Nội dung popup hiển thị nhanh
                    const approvedVideos = eat.review_videos || eat.reviewVideos || [];
                    const hasVideo = approvedVideos.length > 0;
                    const videoBtn = hasVideo 
                        ? `<button onclick="openReelsModal('${eat.slug}', '${eat.name.replace(/'/g, "\\'")}', '${signatureDishName}', '${eat.image_path}')" class="btn-secondary" style="padding: 4px 10px; font-size: 0.75rem; border-radius: 6px; font-family: var(--font-heading); background: rgba(255, 126, 41, 0.08); border-color: rgba(255, 126, 41, 0.25); color: var(--primary); display: inline-flex; align-items: center; gap: 4px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='rgba(255, 126, 41, 0.15)'" onmouseout="this.style.background='rgba(255, 126, 41, 0.08)'">🎬 Video</button>`
                        : '';

                    const popupContent = `
                        <div class="map-popup-card">
                            <img src="${eat.image_path ? eat.image_path : 'https://images.unsplash.com/photo-1591814468924-caf88d1232e1?auto=format&fit=crop&w=300&q=80'}" class="map-popup-img">
                            <h4 class="map-popup-title">${eat.name}</h4>
                            <p style="font-size: 0.8rem; color: var(--text-muted); margin: 2px 0;">📍 Xã ${communeName}</p>
                            <div class="map-popup-footer">
                                <span class="rating-stars">⭐ ${ratingVal}</span>
                                <div style="display: flex; gap: 6px; align-items: center;">
                                    ${videoBtn}
                                    <a href="/dia-diem/dac-san/${eat.slug}" class="btn-primary" style="padding: 4px 10px; font-size: 0.75rem; border-radius: 6px; font-family: var(--font-heading);">Xem quán</a>
                                </div>
                            </div>
                        </div>
                    `;

                    const marker = L.marker([eat.latitude, eat.longitude], { icon: customIcon })
                        .bindPopup(popupContent)
                        .addTo(map);
                        
                    markers[eat.slug] = marker;
                }
            });
        };

        // Vẽ danh sách quán ăn ban đầu lên Bản đồ
        window.renderEateryMarkers(eateries);

        // 5. Logic tìm kiếm gõ real-time cực nhạy + Autocomplete gợi ý
        const searchInput = document.getElementById("searchInput");
        const suggestionDropdown = document.getElementById("suggestionDropdown");
        const cards = document.querySelectorAll('.split-list .eatery-card');
        const countSpan = document.getElementById("resultsCountSpan");
        
        // Hàm chuyển đổi tiếng Việt có dấu thành không dấu để tìm kiếm thông minh hơn
        const removeSign = (str) => {
            return str.normalize("NFD")
                      .replace(/[\u0300-\u036f]/g, "")
                      .replace(/đ/g, "d")
                      .replace(/Đ/g, "D");
        };

        function filterEateries(query) {
            query = query.trim().toLowerCase();
            const queryNoSign = removeSign(query);
            let matchCount = 0;

            cards.forEach(card => {
                const name = card.getAttribute('data-name') || '';
                const address = card.getAttribute('data-address') || '';
                const desc = card.getAttribute('data-desc') || '';
                const slug = card.getAttribute('data-slug') || '';
                
                const textToSearch = `${name} ${address} ${desc}`.toLowerCase();
                const textNoSign = removeSign(textToSearch);
                
                const isMatch = textToSearch.includes(query) || textNoSign.includes(queryNoSign);
                
                if (isMatch || query === '') {
                    card.style.setProperty('display', 'flex', 'important');
                    matchCount++;
                    if (markers[slug]) {
                        markers[slug].addTo(map);
                    }
                } else {
                    card.style.setProperty('display', 'none', 'important');
                    if (markers[slug]) {
                        map.removeLayer(markers[slug]);
                    }
                }
            });

            // Cập nhật số lượng kết quả hiển thị
            if (countSpan) {
                countSpan.innerText = `(${matchCount} kết quả)`;
            }

            // Hiển thị hoặc ẩn phần thông báo không tìm thấy kết quả
            let noResultDiv = document.getElementById('noResultsPlaceholder');
            if (matchCount === 0) {
                if (!noResultDiv) {
                    noResultDiv = document.createElement('div');
                    noResultDiv.id = 'noResultsPlaceholder';
                    noResultDiv.className = 'glass-panel';
                    noResultDiv.style.padding = '40px';
                    noResultDiv.style.textAlign = 'center';
                    noResultDiv.style.color = 'var(--text-muted)';
                    noResultDiv.style.width = '100%';
                    noResultDiv.innerHTML = `
                        <p style="font-size: 1.2rem; margin-bottom: 8px; color: var(--text-main);">😔 Không tìm thấy địa điểm nào phù hợp</p>
                        <p style="font-size: 0.9rem;">Hãy thử từ khóa khác hoặc xóa ô tìm kiếm để hiển thị lại toàn bộ!</p>
                        <button onclick="clearSearch()" class="btn-primary" style="margin-top: 16px; padding: 8px 16px; cursor: pointer;">Xem tất cả</button>
                    `;
                    document.querySelector('.split-list').appendChild(noResultDiv);
                } else {
                    noResultDiv.style.display = 'block';
                }
            } else {
                if (noResultDiv) {
                    noResultDiv.style.display = 'none';
                }
            }
        }

        window.clearSearch = function() {
            searchInput.value = '';
            suggestionDropdown.style.display = 'none';
            filterEateries('');
        };

        // Lọc real-time khi đang gõ
        searchInput.addEventListener("input", function() {
            const query = this.value;
            filterEateries(query);
            
            if (query.trim().length < 2) {
                suggestionDropdown.style.display = "none";
                return;
            }

            fetch(`/tim-kiem?q=${encodeURIComponent(query)}&ajax=suggest`)
                .then(res => res.json())
                .then(data => {
                    suggestionDropdown.innerHTML = "";
                    if (data.length === 0) {
                        suggestionDropdown.style.display = "none";
                        return;
                    }

                    data.forEach(item => {
                        const div = document.createElement("div");
                        div.className = "suggestion-item";
                        div.innerHTML = `
                            <div>
                                <div class="suggestion-title">${item.name}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">📍 ${item.address}</div>
                            </div>
                            <span class="suggestion-type">Xem chi tiết ➔</span>
                        `;
                        div.onclick = function() {
                            window.location.href = `/dia-diem/dac-san/${item.slug}`;
                        };
                        suggestionDropdown.appendChild(div);
                    });

                    suggestionDropdown.style.display = "block";
                });
        });

        // Chặn reload trang khi submit Form tìm kiếm trang chủ, thực hiện lọc ngay tại chỗ cực nhạy
        const searchForm = document.getElementById('searchForm');
        if (searchForm) {
            searchForm.addEventListener('submit', function(e) {
                e.preventDefault();
                filterEateries(searchInput.value);
                suggestionDropdown.style.display = "none";
            });
        }

        // Đóng dropdown khi click ngoài
        document.addEventListener("click", function(e) {
            if (e.target !== searchInput && e.target !== suggestionDropdown) {
                suggestionDropdown.style.display = "none";
            }
        });

        // 9. Lắng nghe click danh mục để lọc AJAX (Không reload trang, mượt mà kiểu SPA)
        const catCards = document.querySelectorAll('.category-card');
        catCards.forEach(card => {
            card.addEventListener('click', function(e) {
                e.preventDefault();
                
                const href = this.getAttribute('href');
                const urlParams = new URLSearchParams(href.split('?')[1] || '');
                const slug = urlParams.get('cat') || '';
                
                // Đánh dấu nút đang chọn
                catCards.forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                
                // Tự động cuộn thẻ được click vào chính giữa thanh trượt ngang
                centerActiveCategoryCard(this);
                
                // Gọi bộ lọc AJAX
                if (window.filterCategoryAjax) {
                    window.filterCategoryAjax(slug, href);
                }
            });
        });

        // Tự động cuộn thẻ danh mục đang active vào chính giữa khi nạp trang lần đầu
        const activeCard = document.querySelector('.category-card.active');
        if (activeCard) {
            setTimeout(() => {
                centerActiveCategoryCard(activeCard);
            }, 300);
        }

        // 7. Tự động cuộn xuống danh sách quán ăn khi người dùng lọc theo Danh mục trên Mobile
        @if(request()->has('cat'))
        setTimeout(() => {
            if (window.innerWidth <= 768) {
                const splitList = document.querySelector('.split-list');
                if (splitList) {
                    window.scrollTo({
                        top: splitList.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            }
        }, 500);
        @endif

    });

    // 6. Hàm đồng bộ click card bên trái -> di chuyển camera map qua phải và mở popup marker tương ứng
    function focusOnEatery(lat, lng, slug) {
        if (map && markers[slug]) {
            let targetLat = lat;
            // Trên mobile, dịch chuyển tâm bản đồ lên phía bắc (lat + offset) để đẩy marker xuống phía dưới,
            // giúp phần popup hiển thị trọn vẹn trong khung bản đồ nhỏ (320px) không bị che khuất ở cạnh trên.
            if (window.innerWidth <= 768) {
                targetLat = lat + 0.0018;
            }

            map.flyTo([targetLat, lng], 16, {
                animate: true,
                duration: 1.2
            });
            setTimeout(() => {
                markers[slug].openPopup();
            }, 1000);
            
            // Cuộn màn hình lên vị trí bản đồ trên mobile chuẩn xác ngay dưới thanh Header sticky (64px)
            if (window.innerWidth <= 992) {
                const mapContainer = document.querySelector('.split-map-container');
                if (mapContainer) {
                    window.scrollTo({
                        top: mapContainer.offsetTop - 64,
                        behavior: 'smooth'
                    });
                }
            }
        }
    }



    // 10. Logic lọc danh mục qua AJAX mượt mà (SPA style, không reload trang!)
    window.filterCategoryAjax = function(slug, href) {
        const eateriesContainer = document.getElementById('eateriesListContainer');
        const headerContainer = document.getElementById('listHeaderContainer');
        
        if (!eateriesContainer) return;
        
        // Thêm hiệu ứng mờ mượt khi tải
        eateriesContainer.style.opacity = '0.4';
        eateriesContainer.style.transition = 'opacity 0.2s ease';
        
        // Tạo URL request API
        const ajaxUrl = href + (href.includes('?') ? '&' : '?') + 'ajax=1';
        
        fetch(ajaxUrl)
            .then(res => res.json())
            .then(data => {
                // Cập nhật URL trình duyệt (không reload trang)
                history.pushState(null, '', href);
                
                // Vẽ lại markers trên bản đồ
                if (window.renderEateryMarkers) {
                    window.renderEateryMarkers(data.eateries);
                }
                
                // Cập nhật tiêu đề header của danh sách
                if (headerContainer) {
                    if (slug === 'dac-san-dia-phuong') {
                        headerContainer.innerHTML = `
                            <div style="margin-bottom: 20px; border-bottom: 1.5px dashed rgba(212, 175, 55, 0.3); padding-bottom: 16px;">
                                <span class="heritage-badge" style="margin-bottom: 8px; font-size: 0.7rem; font-weight: 800; letter-spacing: 1.5px; border: 1px solid rgba(212, 175, 55, 0.4); background: rgba(212, 175, 55, 0.1); color: #ffb300; padding: 4px 10px; border-radius: 20px; display: inline-block;">🏛️ BẢN ĐỒ DI SẢN SỐ CỐ ĐÔ</span>
                                <h2 style="font-size: 1.6rem; font-family: var(--font-heading); font-weight: 800; margin: 4px 0 6px 0; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                                    <span style="background: linear-gradient(135deg, #d97706 0%, #b45309 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Không Gian Số Di Sản Ẩm Thực</span>
                                    <span id="resultsCountSpan" style="font-size: 0.85rem; color: var(--text-muted); font-weight: normal;">
                                        (${data.eateries.length} di sản)
                                    </span>
                                </h2>
                                <p style="font-size: 0.88rem; color: var(--text-muted); line-height: 1.5; margin: 0;">
                                    Khám phá nguồn gốc lịch sử, câu chuyện làng nghề và gặp gỡ các nghệ nhân gìn giữ tinh hoa ẩm thực Kinh Bắc qua hàng ngàn năm dựng nước & giữ nước.
                                </p>
                            </div>
                        `;
                    } else {
                        const activeCard = document.querySelector('.category-card.active');
                        const catName = activeCard ? activeCard.querySelector('.cat-name').innerText : 'Tất cả';
                        const titleText = slug ? `Danh sách: <span style="color: var(--primary);">${catName}</span>` : `Địa điểm nổi bật tại Đông Anh`;
                        
                        headerContainer.innerHTML = `
                            <h2 style="font-size: 1.25rem; margin: 6px 0 0 0; font-family: var(--font-heading); font-weight: 700; line-height: 1.4; color: var(--text-main);">
                                <span style="margin-right: 4px;">📍</span> 
                                ${titleText}
                                <span id="resultsCountSpan" style="font-size: 0.8rem; color: var(--text-muted); font-weight: normal; margin-left: 6px; display: inline-block; white-space: nowrap;">
                                    (${data.eateries.length} kết quả)
                                </span>
                            </h2>
                        `;
                    }
                }
                
                // Re-render danh sách quán ăn
                if (data.eateries.length > 0) {
                    let cardsHtml = '';
                    data.eateries.forEach(eat => {
                        const imgUrl = eat.image_path ? eat.image_path : 'https://images.unsplash.com/photo-1591814468924-caf88d1232e1?auto=format&fit=crop&w=300&q=80';
                        const ratingVal = eat.average_rating || (eat.rating ? parseFloat(eat.rating).toFixed(1) : '5.0');
                        const communeName = eat.commune ? (eat.commune.name || eat.commune) : '';
                        const categoryIcon = eat.category ? (eat.category.icon || '') : '';
                        const categoryName = eat.category ? (eat.category.name || '') : '';
                        const categorySlug = eat.category ? (eat.category.slug || '') : '';

                        cardsHtml += `
                            <div class="eatery-card glass-panel" 
                                 data-slug="${eat.slug}"
                                 data-name="${eat.name}"
                                 data-address="${eat.address}"
                                 data-desc="${eat.description}"
                                 data-commune="${communeName}"
                                 data-category="${categorySlug}"
                                 style="animation: fadeIn 0.4s ease forwards;"
                                 onclick="focusOnEatery(${eat.latitude}, ${eat.longitude}, '${eat.slug}')">
                                <div class="eatery-img-wrapper">
                                    <img src="${imgUrl}" class="eatery-img" alt="${eat.name}">
                                    <div style="position: absolute; top: 8px; left: 8px; max-width: calc(100% - 16px); display: flex; align-items: center; gap: 4px; font-size: 0.68rem; font-weight: 700; color: #ffffff; background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(4px); padding: 4px 8px; border-radius: 6px; box-shadow: 0 2px 10px rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1);">
                                        <span>${categoryIcon}</span>
                                        <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${categoryName}</span>
                                    </div>
                                </div>
                                <div class="eatery-info">
                                    <div class="eatery-header">
                                        <h3 class="eatery-title">${eat.name}</h3>
                                        <div class="rating-stars">
                                            <span>⭐</span> ${ratingVal}
                                        </div>
                                    </div>
                                    <p class="eatery-desc">${eat.description}</p>
                                    <div class="eatery-footer">
                                        <div class="eatery-meta-item">
                                            <span>📍</span> ${communeName}
                                        </div>
                                        <div class="eatery-meta-item" style="color: var(--primary); font-weight: 600;">
                                            ${eat.price_range}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    eateriesContainer.innerHTML = cardsHtml;
                } else {
                    eateriesContainer.innerHTML = `
                        <div class="glass-panel" style="padding: 40px; text-align: center; color: var(--text-muted); width: 100%;">
                            <p style="font-size: 1.2rem; margin-bottom: 8px; color: var(--text-main);">😔 Không tìm thấy địa điểm nào phù hợp</p>
                            <p style="font-size: 0.9rem;">Hãy thử lọc danh mục khác hoặc xóa bộ lọc để khám phá lại toàn bộ Đông Anh!</p>
                            <a href="/" class="btn-primary" style="margin-top: 16px; padding: 8px 16px; text-decoration: none; display: inline-block;">Xem tất cả</a>
                        </div>
                    `;
                    // Tự động gắn sự kiện click cho nút "Xem tất cả" vừa tạo mới qua ajax
                    const viewAllBtn = eateriesContainer.querySelector('a');
                    if (viewAllBtn) {
                        viewAllBtn.addEventListener('click', function(evt) {
                            evt.preventDefault();
                            const allCatCard = document.querySelector('.category-card[href="/"]');
                            if (allCatCard) allCatCard.click();
                        });
                    }
                }
                
                // Mở lại độ mờ
                eateriesContainer.style.opacity = '1';
                
                // Tự động cuộn xuống danh sách quán ăn trên di động
                if (window.innerWidth <= 768) {
                    const splitList = document.querySelector('.split-list');
                    if (splitList) {
                        window.scrollTo({
                            top: splitList.offsetTop - 80,
                            behavior: 'smooth'
                        });
                    }
                }
            })
            .catch(err => {
                console.error("AJAX loading error:", err);
                eateriesContainer.style.opacity = '1';
            });
    };

    // ==========================================================================
    // DYNAMIC TIKTOK REELS-STYLE PLAYER FOR TÓP TÓP FOOD TOUR
    // ==========================================================================
    let reelsList = [];
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

        // Dynamically hide/show the right side action panel for YouTube videos
        const sideActions = document.querySelector('.reels-side-actions');
        const tapOverlay = document.getElementById('reelTapOverlay');
        const isIframe = videoType === 'youtube_shorts' || videoType === 'tiktok' || videoUrl.includes('youtube.com') || videoUrl.includes('youtu.be');
        
        if (sideActions) {
            if (isIframe) {
                sideActions.style.display = 'none';
            } else {
                sideActions.style.display = 'flex';
            }
        }

        // Allow touch, click, zoom gestures to go directly to YouTube/TikTok player by setting pointer-events: none
        if (tapOverlay) {
            if (isIframe) {
                tapOverlay.style.pointerEvents = 'none';
            } else {
                tapOverlay.style.pointerEvents = 'auto';
            }
        }

        if (videoType === 'tiktok') {
            const videoId = getTikTokVideoId(videoUrl);
            if (videoId) {
                wrapper.innerHTML = `<iframe src="https://www.tiktok.com/embed/v2/${videoId}" style="width: 100%; height: 100%; border: none; background: #000; pointer-events: auto;" allowfullscreen allow="autoplay; encrypted-media;"></iframe>`;
            } else {
                wrapper.innerHTML = `<iframe src="${videoUrl}" style="width: 100%; height: 100%; border: none; background: #000; pointer-events: auto;" allowfullscreen allow="autoplay;"></iframe>`;
            }
        } else if (videoType === 'youtube_shorts' || videoUrl.includes('youtube.com') || videoUrl.includes('youtu.be')) {
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
