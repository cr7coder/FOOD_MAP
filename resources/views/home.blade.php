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
<div id="reelsModal" class="reels-modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 9999; background: rgba(8, 8, 12, 0.96); backdrop-filter: blur(12px); align-items: center; justify-content: center; transition: opacity 0.3s;">
    <!-- Close button -->
    <button onclick="closeReelsModal()" style="position: absolute; top: 25px; right: 25px; background: rgba(255,255,255,0.1); border: none; font-size: 1.5rem; color: #fff; width: 45px; height: 45px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; z-index: 10002; transition: all 0.25s;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">✕</button>
    
    <!-- Reel Container Frame -->
    <div style="position: relative; width: 380px; height: 90%; max-height: 720px; background: #000; border-radius: 20px; overflow: hidden; box-shadow: 0 15px 50px rgba(0,0,0,0.8); display: flex; flex-direction: column; border: 1.5px solid rgba(255,255,255,0.1); z-index: 10001;" id="reelsPhoneFrame">
        
        <!-- Navigation arrows (Desktop helper) -->
        <button onclick="prevReel()" style="position: absolute; left: -60px; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); width: 44px; height: 44px; border-radius: 50%; color: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; transition: all 0.25s; border: none;" class="nav-arrow-desktop">▲</button>
        <button onclick="nextReel()" style="position: absolute; right: -60px; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); width: 44px; height: 44px; border-radius: 50%; color: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; transition: all 0.25s; border: none;" class="nav-arrow-desktop">▼</button>

        <!-- Player wrapper -->
        <div id="reelPlayerWrapper" style="width: 100%; height: 100%; position: relative;">
            <!-- Player content dynamically loaded by JS -->
            <div id="reelLoadingSpinner" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); display: flex; flex-direction: column; align-items: center; gap: 12px; color: var(--text-muted);">
                <div style="width: 40px; height: 40px; border: 4px solid rgba(255,255,255,0.1); border-top-color: var(--primary); border-radius: 50%; animation: spin 1s infinite linear;"></div>
                <span style="font-size: 0.85rem; font-family: var(--font-heading);">Đang tải Tour...</span>
            </div>
        </div>

        <!-- Float Interactive Overlay -->
        <div style="position: absolute; bottom: 0; left: 0; width: 100%; padding: 20px; background: linear-gradient(transparent, rgba(0,0,0,0.9) 70%); display: flex; flex-direction: column; gap: 15px; pointer-events: none; z-index: 1000;">
            
            <!-- Video Info details -->
            <div style="color: #fff; pointer-events: auto;">
                <h4 id="reelVideoTitle" style="font-size: 0.95rem; font-family: var(--font-heading); font-weight: 700; line-height: 1.4; text-shadow: 0 2px 4px rgba(0,0,0,0.8); margin-bottom: 4px;">Tiêu đề Video</h4>
                <div style="display: flex; align-items: center; gap: 6px;">
                    <span style="font-size: 0.72rem; background: rgba(0,242,254,0.15); color: #00f2fe; padding: 2px 8px; border-radius: 20px; border: 1px solid rgba(0,242,254,0.3); font-weight: 800; font-family: var(--font-heading);" id="reelVideoTypeBadge">TikTok</span>
                </div>
            </div>

            <!-- Restaurant Card (Sync with map) -->
            <div class="glass-panel" style="padding: 10px 12px; border-radius: 12px; display: flex; align-items: center; justify-content: space-between; border: 1px solid rgba(255,255,255,0.15); background: rgba(15,15,20,0.75); backdrop-filter: blur(8px); pointer-events: auto;" id="reelEateryCard">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <img id="reelEateryImg" src="" style="width: 38px; height: 38px; border-radius: 8px; object-fit: cover; border: 1px solid rgba(255,255,255,0.1);">
                    <div style="display: flex; flex-direction: column;">
                        <span id="reelEateryName" style="font-size: 0.82rem; font-weight: 700; color: #fff; line-height: 1.2;">Tên quán ăn</span>
                        <span id="reelEateryCommune" style="font-size: 0.68rem; color: rgba(255,255,255,0.6); margin-top: 1px;">Xã Đông Anh</span>
                    </div>
                </div>
                <button onclick="reelFocusEatery()" style="background: var(--primary-grad); border: none; border-radius: 8px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; color: #fff; cursor: pointer; font-size: 0.95rem; box-shadow: 0 0 10px rgba(255,126,41,0.5); transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'" title="Xem trên Bản đồ">📍</button>
            </div>

        </div>

        <!-- Floating Sidebar Actions (Hearts, Share) -->
        <div style="position: absolute; right: 12px; bottom: 130px; display: flex; flex-direction: column; align-items: center; gap: 18px; z-index: 1000; pointer-events: auto;">
            <!-- Heart like action -->
            <div style="display: flex; flex-direction: column; align-items: center; gap: 4px; cursor: pointer;" onclick="likeCurrentReel()">
                <div style="width: 44px; height: 44px; border-radius: 50%; background: rgba(255,255,255,0.12); display: flex; align-items: center; justify-content: center; font-size: 1.4rem; color: #ff3366; backdrop-filter: blur(8px); transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.15)'" onmouseout="this.style.transform='scale(1)'" id="reelHeartBtn">❤️</div>
                <span id="reelLikesCount" style="font-size: 0.72rem; color: #fff; font-weight: 700; text-shadow: 0 2px 4px rgba(0,0,0,0.8);">1,250</span>
            </div>
            <!-- Share video link -->
            <div style="display: flex; flex-direction: column; align-items: center; gap: 4px; cursor: pointer;" onclick="shareCurrentReel()">
                <div style="width: 44px; height: 44px; border-radius: 50%; background: rgba(255,255,255,0.12); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: #fff; backdrop-filter: blur(8px); transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.15)'" onmouseout="this.style.transform='scale(1)'">🔗</div>
                <span style="font-size: 0.68rem; color: #fff; font-weight: 600; text-shadow: 0 2px 4px rgba(0,0,0,0.8);">Chia sẻ</span>
            </div>
        </div>

        <!-- Floating hearts container (pulsing hearts rising) -->
        <div id="reelsHeartsContainer" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; overflow: hidden; z-index: 999;"></div>

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
            zoomControl: false // Chúng ta sẽ tùy chỉnh vị trí nút zoom
        }).setView([21.1352, 105.8458], 13);
        
        L.control.zoom({ position: 'bottomright' }).addTo(map);

        // 3. Sử dụng Tileset phù hợp chế độ Sáng/Tối
        let currentTheme = localStorage.getItem('theme') || 'light';
        let tileUrl = currentTheme === 'light' 
            ? 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png'
            : 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png';
            
        let activeTileLayer = L.tileLayer(tileUrl, {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 20
        }).addTo(map);

        // Lắng nghe sự kiện đổi chế độ Sáng/Tối để đổi lớp nền bản đồ tức thì
        document.addEventListener('theme-changed', function(e) {
            const nextTheme = e.detail.theme;
            const nextTileUrl = nextTheme === 'light'
                ? 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png'
                : 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png';
            
            map.removeLayer(activeTileLayer);
            activeTileLayer = L.tileLayer(nextTileUrl, {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
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

                    // Nội dung popup hiển thị nhanh
                    const popupContent = `
                        <div class="map-popup-card">
                            <img src="${eat.image_path ? eat.image_path : 'https://images.unsplash.com/photo-1591814468924-caf88d1232e1?auto=format&fit=crop&w=300&q=80'}" class="map-popup-img">
                            <h4 class="map-popup-title">${eat.name}</h4>
                            <p style="font-size: 0.8rem; color: var(--text-muted); margin: 2px 0;">📍 Xã ${communeName}</p>
                            <div class="map-popup-footer">
                                <span class="rating-stars">⭐ ${ratingVal}</span>
                                <a href="/dia-diem/dac-san/${eat.slug}" class="btn-primary" style="padding: 4px 10px; font-size: 0.75rem; border-radius: 6px; font-family: var(--font-heading);">Xem quán</a>
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
    let currentReelIndex = 0;
    let touchStartY = 0;
    let touchEndY = 0;

    // Helper functions to extract IDs
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

    window.openReelsModal = function() {
        const modal = document.getElementById('reelsModal');
        modal.style.display = 'flex';
        modal.style.opacity = '1';
        
        // Show spinner initially
        document.getElementById('reelPlayerWrapper').innerHTML = `
            <div id="reelLoadingSpinner" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); display: flex; flex-direction: column; align-items: center; gap: 12px; color: var(--text-muted);">
                <div style="width: 40px; height: 40px; border: 4px solid rgba(255,255,255,0.1); border-top-color: var(--primary); border-radius: 50%; animation: spin 1s infinite linear;"></div>
                <span style="font-size: 0.85rem; font-family: var(--font-heading);">Đang tải Tour...</span>
            </div>
        `;

        fetch('/api/videos')
            .then(res => res.json())
            .then(data => {
                reelsList = data;
                if (reelsList.length === 0) {
                    document.getElementById('reelPlayerWrapper').innerHTML = `
                        <div style="text-align: center; color: #fff; padding: 40px 20px; font-family: var(--font-heading); width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px;">
                            <span style="font-size: 2.5rem;">🎬</span>
                            <h4 style="font-size: 1.1rem; font-weight: 700;">Chưa có Video Review nào!</h4>
                            <p style="font-size: 0.8rem; opacity: 0.7; max-width: 250px;">Hãy đăng nhập trang quản trị để thêm video review đầu tiên cho đặc sản của bạn.</p>
                        </div>
                    `;
                    return;
                }
                currentReelIndex = 0;
                loadReel(currentReelIndex);
                setupSwipeGestures();
            })
            .catch(err => {
                console.error("Lỗi lấy dữ liệu video:", err);
                document.getElementById('reelPlayerWrapper').innerHTML = `
                    <div style="text-align: center; color: #fff; padding: 20px;">
                        <span>⚠️ Không thể tải dữ liệu.</span>
                    </div>
                `;
            });
    };

    window.closeReelsModal = function() {
        const modal = document.getElementById('reelsModal');
        modal.style.opacity = '0';
        setTimeout(() => {
            modal.style.display = 'none';
            // Clear content to stop any playing videos/iframes immediately
            document.getElementById('reelPlayerWrapper').innerHTML = '';
        }, 300);
    };

    window.loadReel = function(index) {
        if (index < 0 || index >= reelsList.length) return;
        currentReelIndex = index;
        const reel = reelsList[index];

        // Update titles, badges, and likes
        document.getElementById('reelVideoTitle').innerText = reel.title;
        
        const typeBadge = document.getElementById('reelVideoTypeBadge');
        typeBadge.innerText = reel.video_type.toUpperCase();
        if (reel.video_type === 'tiktok') {
            typeBadge.style.background = 'rgba(0, 242, 254, 0.15)';
            typeBadge.style.color = '#00f2fe';
            typeBadge.style.borderColor = 'rgba(0, 242, 254, 0.3)';
        } else if (reel.video_type === 'youtube_shorts') {
            typeBadge.style.background = 'rgba(255, 0, 0, 0.15)';
            typeBadge.style.color = '#ff0000';
            typeBadge.style.borderColor = 'rgba(255, 0, 0, 0.3)';
        } else {
            typeBadge.style.background = 'rgba(255, 126, 41, 0.15)';
            typeBadge.style.color = 'var(--primary)';
            typeBadge.style.borderColor = 'rgba(255, 126, 41, 0.3)';
        }

        document.getElementById('reelLikesCount').innerText = reel.likes_count.toLocaleString();
        
        // Update eatery card info
        document.getElementById('reelEateryName').innerText = reel.eatery.name;
        document.getElementById('reelEateryCommune').innerText = `📍 ${reel.eatery.address}`;
        document.getElementById('reelEateryImg').src = reel.eatery.image_path || 'https://images.unsplash.com/photo-1591814468924-caf88d1232e1?auto=format&fit=crop&w=100&q=80';

        // Load media content safely
        const wrapper = document.getElementById('reelPlayerWrapper');
        wrapper.innerHTML = ''; // Clear previous player

        if (reel.video_type === 'tiktok') {
            const videoId = getTikTokVideoId(reel.video_url);
            if (videoId) {
                wrapper.innerHTML = `<iframe src="https://www.tiktok.com/embed/v2/${videoId}" style="width: 100%; height: 100%; border: none; background: #000;" allowfullscreen allow="autoplay; encrypted-media;"></iframe>`;
            } else {
                wrapper.innerHTML = `<iframe src="${reel.video_url}" style="width: 100%; height: 100%; border: none; background: #000;" allowfullscreen allow="autoplay;"></iframe>`;
            }
        } else if (reel.video_type === 'youtube_shorts') {
            const shortsId = getYouTubeShortsId(reel.video_url);
            if (shortsId) {
                wrapper.innerHTML = `<iframe src="https://www.youtube.com/embed/${shortsId}?autoplay=1&mute=0&loop=1&playlist=${shortsId}&controls=0" style="width: 100%; height: 100%; border: none; background: #000;" allowfullscreen allow="autoplay; encrypted-media;"></iframe>`;
            } else {
                wrapper.innerHTML = `<iframe src="${reel.video_url}" style="width: 100%; height: 100%; border: none; background: #000;" allowfullscreen allow="autoplay;"></iframe>`;
            }
        } else {
            // Local direct mp4 storage file
            wrapper.innerHTML = `<video src="${reel.video_url}" autoplay loop muted playsinline controls style="width: 100%; height: 100%; object-fit: cover; background: #000;"></video>`;
        }
    };

    window.prevReel = function() {
        if (currentReelIndex > 0) {
            loadReel(currentReelIndex - 1);
        }
    };

    window.nextReel = function() {
        if (currentReelIndex < reelsList.length - 1) {
            loadReel(currentReelIndex + 1);
        }
    };

    // Mobile Swipe Gestures
    function setupSwipeGestures() {
        const frame = document.getElementById('reelsPhoneFrame');
        if (!frame) return;

        frame.addEventListener('touchstart', e => {
            touchStartY = e.changedTouches[0].screenY;
        }, { passive: true });

        frame.addEventListener('touchend', e => {
            touchEndY = e.changedTouches[0].screenY;
            handleSwipeDirection();
        }, { passive: true });
    }

    function handleSwipeDirection() {
        const swipeThreshold = 50;
        const diff = touchStartY - touchEndY;
        if (Math.abs(diff) > swipeThreshold) {
            if (diff > 0) {
                nextReel();
            } else {
                prevReel();
            }
        }
    }

    // Like Action & Floating Heart Animation
    window.likeCurrentReel = function() {
        if (reelsList.length === 0) return;
        const reel = reelsList[currentReelIndex];

        createFloatingHeart();

        reel.likes_count++;
        document.getElementById('reelLikesCount').innerText = reel.likes_count.toLocaleString();
        
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        fetch(`/api/videos/${reel.id}/like`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                reel.likes_count = data.likes_count;
                document.getElementById('reelLikesCount').innerText = data.likes_count.toLocaleString();
            }
        })
        .catch(err => console.error("Lỗi tăng lượt thích:", err));
    };

    function createFloatingHeart() {
        const container = document.getElementById('reelsHeartsContainer');
        if (!container) return;

        const heart = document.createElement('div');
        heart.innerHTML = '❤️';
        heart.style.position = 'absolute';
        heart.style.bottom = '130px';
        heart.style.right = '20px';
        heart.style.fontSize = (Math.random() * 15 + 20) + 'px';
        heart.style.opacity = '1';
        heart.style.pointerEvents = 'none';
        heart.style.transition = 'all 1s cubic-bezier(0.18, 0.89, 0.32, 1.28)';
        
        const drift = (Math.random() * 80 - 40);
        container.appendChild(heart);

        setTimeout(() => {
            heart.style.transform = `translate(${drift}px, -200px) scale(1.5)`;
            heart.style.opacity = '0';
        }, 50);

        setTimeout(() => {
            heart.remove();
        }, 1050);
    }

    // Copy Reel link
    window.shareCurrentReel = function() {
        if (reelsList.length === 0) return;
        const reel = reelsList[currentReelIndex];
        const shareUrl = window.location.origin + '/dia-diem/dac-san/' + reel.eatery.slug;
        
        navigator.clipboard.writeText(shareUrl)
            .then(() => {
                alert(`🔗 Đã sao chép liên kết chia sẻ của quán "${reel.eatery.name}" vào khay nhớ tạm!`);
            })
            .catch(err => console.error("Không thể sao chép:", err));
    };

    // Synced Map Trigger: Close modal and fly to eatery on Leaflet map
    window.reelFocusEatery = function() {
        if (reelsList.length === 0) return;
        const reel = reelsList[currentReelIndex];
        
        closeReelsModal();
        focusOnEatery(reel.eatery.latitude, reel.eatery.longitude, reel.eatery.slug);
    };
</script>
@endsection
