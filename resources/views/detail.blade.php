@extends('layouts.app')

<!-- Tối ưu hóa SEO: Tiêu đề động chính xác theo yêu cầu khách hàng -->
@section('title', $eatery->name . ' - ' . $eatery->category->name . ' ngon tại ' . $eatery->commune->name . ', Đông Anh')

<!-- Tối ưu hóa SEO: Thẻ mô tả Meta tự sinh chân thực -->
@section('meta_description', 'Khám phá ' . $eatery->name . ' tại ' . $eatery->address . ', Xã ' . $eatery->commune->name . ', Đông Anh. Số điện thoại liên hệ: ' . $eatery->phone . '. Thực đơn món đặc sắc: ' . $eatery->dishes->take(2)->pluck('name')->implode(', ') . '. Xem đánh giá khách hàng và bản đồ hướng dẫn đường đi chi tiết.')

@section('og_image', $eatery->image_path ?: 'https://images.unsplash.com/photo-1591814468924-caf88d1232e1?auto=format&fit=crop&w=800&q=80')

<!-- Tối ưu hóa SEO Google: Nhúng mã JSON-LD Schema.org động sinh từ Controller -->
@section('seo_schema')
    {!! $jsonLd !!}
@endsection

@section('content')
<!-- Detail Hero Background Banner -->
<section class="detail-hero" style="background-image: url('{{ $eatery->image_path ?: 'https://images.unsplash.com/photo-1591814468924-caf88d1232e1?auto=format&fit=crop&w=1200&q=80' }}')">
    <div class="container detail-hero-content">
        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 12px;">
            <span class="tag-badge-accent" style="display: inline-block; font-size: 0.85rem; color: var(--text-main); background: var(--bg-card); border-color: var(--border-glow); backdrop-filter: blur(8px);">
                {{ $eatery->category->icon }} {{ $eatery->category->name }}
            </span>
            @if($eatery->foodSafetyCertificate)
                <a href="#trust-hub-section" onclick="scrollToTrustHub(event)" class="tag-badge-accent" style="display: inline-flex; align-items: center; gap: 6px; font-size: 0.85rem; color: #ffffff; background: #2ecc71; border: 1px solid #27ae60; border-radius: 30px; padding: 6px 14px; cursor: pointer; text-decoration: none; font-weight: 800; box-shadow: 0 4px 15px rgba(46, 204, 113, 0.4); transition: all 0.3s; animation: pulse-shield 2.5s infinite;" onmouseover="this.style.background='#27ae60'; this.style.transform='scale(1.05)';" onmouseout="this.style.background='#2ecc71'; this.style.transform='none';">
                    🛡️ <span style="color: #ffffff;">Xác minh An Toàn</span>
                </a>
            @endif
        </div>
        <h1 style="font-size: 2.8rem; font-weight: 800; margin-bottom: 8px; font-family: var(--font-heading); color: var(--text-main);">
            {{ $eatery->name }}
        </h1>
        <p style="font-size: 1.1rem; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
            <span style="font-size: 1.2rem;">📍</span> {{ $eatery->address }}
        </p>
    </div>
</section>

<!-- Detail Main Layout Grid -->
<div class="container">
    <div class="detail-grid">
        
        <!-- Left Side: Core Info, Menu, and Reviews -->
        <div>
            <!-- Giới thiệu hoặc Không gian di sản văn hóa ẩm thực -->
            @php
                $dossier = $eatery->heritage_dossier;
            @endphp

            @if($dossier)
                <!-- Premium Digital Museum Showcase for Heritage Specialties -->
                <div class="detail-section glass-panel heritage-museum-card" style="padding: 28px; margin-bottom: 40px;">
                    
                    <!-- Traditional Vietnamese Decorative Pattern Overlay -->
                    <div class="heritage-pattern-overlay"></div>

                    <div style="position: relative; z-index: 2;">
                        <!-- Header of Heritage Dossier -->
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 24px; border-bottom: 1px dashed rgba(212, 175, 55, 0.25); padding-bottom: 20px;">
                            <div style="flex: 1; min-width: 0;">

                                <h2 style="font-size: 2rem; font-family: var(--font-heading); color: var(--text-main); font-weight: 800; margin-top: 8px; margin-bottom: 4px;">
                                    Hồ Sơ Di Sản: {{ $eatery->name }}
                                </h2>
                                <p style="font-style: italic; color: var(--primary); font-size: 0.95rem; font-weight: 600; display: flex; align-items: center; gap: 6px;">
                                    <span>🌾</span> {{ $dossier['heritage_year'] }}
                                </p>
                            </div>
                            
                            <!-- OCOP Star Badge Badge -->
                            <div class="ocop-star-badge" style="flex-shrink: 0;">
                                <span style="font-weight: 900; font-size: 0.75rem; color: var(--primary); display: block; letter-spacing: 1px;">CHỨNG NHẬN OCOP</span>
                                <div style="color: #ffc107; font-size: 1.2rem; margin-top: 4px; display: flex; gap: 2px; justify-content: flex-end;">
                                    @for($i=1; $i<=5; $i++)
                                        <span style="{{ $i <= $dossier['ocop_stars'] ? 'color: #ffb300; text-shadow: 0 0 10px rgba(255, 179, 0, 0.5);' : 'color: var(--border-glow);' }}">★</span>
                                    @endfor
                                </div>
                                <span style="font-size: 0.7rem; color: var(--text-muted); display: block; margin-top: 4px;">{{ $dossier['ocop_stars'] }} Sao Cấp Quốc Gia</span>
                            </div>
                        </div>

                        <!-- Smart AI Voice Storytelling Component -->
                        <div class="audio-storyteller-widget glass-panel" style="background: rgba(212, 175, 55, 0.04); border: 1px solid rgba(212, 175, 55, 0.2); padding: 16px 20px; border-radius: 16px; margin-bottom: 30px; display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap;">
                            <div style="display: flex; align-items: center; gap: 16px; min-width: 280px; flex: 1;">
                                <button id="playAudioBtn" class="audio-play-btn" aria-label="Play narrative audio" title="Nghe kể câu chuyện di sản" style="outline: none;">
                                    <span class="play-icon" id="playBtnIcon">🔊</span>
                                </button>
                                <div>
                                    <strong style="color: #ffb300; display: block; font-size: 1rem;">🎧 Nghe kể chuyện di sản</strong>
                                    <span style="font-size: 0.85rem; color: var(--text-muted);" id="audioStatusText">Bấm để lắng nghe giọng đọc AI thuyết minh văn hóa món ăn</span>
                                </div>
                            </div>
                            
                            <!-- Equalizer Visualizer -->
                            <div class="equalizer-container" id="audioEqualizer">
                                <div class="eq-bar"></div>
                                <div class="eq-bar"></div>
                                <div class="eq-bar"></div>
                                <div class="eq-bar"></div>
                                <div class="eq-bar"></div>
                                <div class="eq-bar"></div>
                            </div>
                        </div>

                        <!-- Heritage Tab Content -->
                        <div class="heritage-tabs-container">
                            <div class="heritage-tab-buttons">
                                <button class="heritage-tab-btn active" data-tab="tab-story">🏛️ Nguồn Gốc & Câu Chuyện</button>
                                <button class="heritage-tab-btn" data-tab="tab-artisans">👨‍🍳 Nghệ Nhân Truyền Nghề</button>
                                <button class="heritage-tab-btn" data-tab="tab-ingredients">🌾 Bí Quyết & Nguyên Liệu</button>
                                <button class="heritage-tab-btn" data-tab="tab-timeline">📜 Hành Trình Di Sản</button>
                            </div>

                            <!-- Tab 1: Nguồn gốc -->
                            <div id="tab-story" class="heritage-tab-content active-content">
                                <p style="font-size: 1.05rem; line-height: 1.8; color: var(--text-main); margin-bottom: 20px;">
                                    {{ $dossier['story'] }}
                                </p>
                                <div style="background: rgba(255,255,255,0.01); border-left: 3px solid #ffb300; padding: 16px; border-radius: 4px; font-size: 0.95rem; line-height: 1.6;">
                                    {{ $eatery->description }}
                                </div>
                            </div>

                            <!-- Tab 2: Nghệ nhân -->
                            <div id="tab-artisans" class="heritage-tab-content">
                                <div style="display: flex; gap: 20px; align-items: center; flex-wrap: wrap;">
                                    <div style="flex: 1; min-width: 260px;">
                                        <h4 style="color: var(--primary); font-size: 1.15rem; margin-bottom: 12px; font-weight: 700;">Gặp Gỡ Những Người Giữ Lửa Di Sản</h4>
                                        <p style="font-size: 1.02rem; line-height: 1.8; color: var(--text-main); font-style: italic; background: var(--bg-btn-secondary); padding: 20px; border-radius: 12px; border: 1px dashed var(--border-glow-hover); margin: 0;">
                                            {{ $dossier['artisans'] }}
                                        </p>
                                    </div>
                                </div>
                            </div>
 
                            <!-- Tab 3: Nguyên liệu -->
                            <div id="tab-ingredients" class="heritage-tab-content">
                                <h4 style="color: var(--primary); font-size: 1.15rem; margin-bottom: 16px; font-weight: 700;">Bảng Thành Phần Bản Địa Thuần Khiết</h4>
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px;">
                                    @foreach($dossier['ingredients'] as $ingredient)
                                        <div class="glass-panel" style="padding: 14px 20px; background: rgba(212, 175, 55, 0.03); border: 1px solid rgba(212, 175, 55, 0.15); display: flex; align-items: center; gap: 12px;">
                                            <span style="font-size: 1.3rem;">✨</span>
                                            <span style="font-size: 0.95rem; font-weight: 600; color: var(--text-main);">{{ $ingredient }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
 
                            <!-- Tab 4: Timeline -->
                            <div id="tab-timeline" class="heritage-tab-content">
                                <div class="heritage-timeline">
                                    @foreach($dossier['timeline'] as $item)
                                        <div class="heritage-timeline-item">
                                            <div class="heritage-timeline-badge">{{ $item['year'] }}</div>
                                            <div class="heritage-timeline-content glass-panel" style="margin-left: 20px;">
                                                <p style="font-size: 0.95rem; margin: 0; line-height: 1.6; color: var(--text-main);">{{ $item['event'] }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
 
                        <!-- "Bạn có biết?" Trivia Widget -->
                        <div class="trivia-widget glass-panel" style="background: linear-gradient(135deg, rgba(212, 175, 55, 0.08) 0%, rgba(255, 111, 0, 0.03) 100%); border: 1px solid rgba(212, 175, 55, 0.25); padding: 24px; border-radius: 16px; margin-top: 32px; display: flex; gap: 16px; align-items: flex-start;">
                            <span style="font-size: 2.2rem; filter: drop-shadow(0 0 10px rgba(255,179,0,0.6));">💡</span>
                            <div>
                                <h4 style="font-size: 1.1rem; color: var(--primary); font-weight: 700; margin-bottom: 6px;">BẠN CÓ BIẾT?</h4>
                                <p style="font-size: 0.95rem; line-height: 1.7; color: var(--text-main); margin: 0;">
                                    {{ $dossier['fun_fact'] }}
                                </p>
                            </div>
                        </div>
                        

                    </div>
                </div>
            @else
                <div class="detail-section glass-panel" style="padding: 28px; margin-bottom: 40px;">
                    <h2 class="section-title"><span>📝</span> Giới thiệu về quán</h2>
                    <p style="font-size: 1rem; color: var(--text-main); line-height: 1.8;">
                        {{ $eatery->description }}
                    </p>
                </div>
            @endif

            <!-- CSS đặc thù cho Hệ thống Minh bạch Thực phẩm sạch (Trust Hub) -->
            <style>
                .trust-tab-btn {
                    background: transparent;
                    border: none;
                    outline: none;
                    color: var(--text-muted);
                    font-weight: 600;
                    font-size: 0.9rem;
                    padding: 8px 16px;
                    border-radius: 8px;
                    cursor: pointer;
                    transition: all 0.3s ease;
                }
                .trust-tab-btn.active {
                    background: var(--bg-btn-secondary);
                    color: var(--accent);
                    box-shadow: 0 0 15px rgba(32, 178, 170, 0.15), inset 0 0 0 1px rgba(32, 178, 170, 0.3);
                }
                .trust-tab-btn:hover:not(.active) {
                    color: var(--text-main);
                    background: rgba(255, 255, 255, 0.02);
                }
                .trust-tab-content {
                    display: none;
                    animation: fadeInTrust 0.4s ease forwards;
                }
                .trust-tab-content.active-content {
                    display: block;
                }
                @keyframes fadeInTrust {
                    from { opacity: 0; transform: translateY(8px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                @keyframes pulse-trust {
                    0% { transform: scale(1); filter: drop-shadow(0 0 4px rgba(32, 178, 170, 0.4)); }
                    50% { transform: scale(1.05); filter: drop-shadow(0 0 12px rgba(32, 178, 170, 0.7)); }
                    100% { transform: scale(1); filter: drop-shadow(0 0 4px rgba(32, 178, 170, 0.4)); }
                }
                @keyframes pulse-shield {
                    0% { box-shadow: 0 0 0 0 rgba(46, 204, 113, 0.4); }
                    70% { box-shadow: 0 0 0 10px rgba(46, 204, 113, 0); }
                    100% { box-shadow: 0 0 0 0 rgba(46, 204, 113, 0); }
                }
            </style>



            <!-- Premium Pop-up Lightbox for Document Scan Image Viewer -->
            <div id="trustLightbox" class="lightbox-overlay" onclick="closeTrustLightbox()" style="display: none; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.9); z-index: 99999; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s ease;">
                <div style="position: absolute; top: 20px; right: 20px; font-size: 2rem; color: white; cursor: pointer; font-weight: bold; text-shadow: 0 0 10px rgba(0,0,0,0.8);" onclick="closeTrustLightbox()">&times;</div>
                <div class="lightbox-content" style="max-width: 90%; max-height: 85%; transform: scale(0.9); transition: transform 0.3s ease; text-align: center;" onclick="event.stopPropagation()">
                    <img id="trustLightboxImg" src="" style="max-width: 100%; max-height: 75vh; border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); border: 2px solid rgba(255,255,255,0.1);">
                    <h4 id="trustLightboxCaption" style="color: white; margin-top: 16px; font-family: var(--font-heading); font-size: 1.1rem; text-shadow: 0 2px 4px rgba(0,0,0,0.8);"></h4>
                    <div style="font-size: 0.8rem; color: #aaa; margin-top: 8px; letter-spacing: 0.5px;">✓ Bản quét bảo mật số hóa Đông Anh (Đã xác thực)</div>
                </div>
            </div>

            <!-- Thực đơn món ăn -->
            <div class="detail-section glass-panel" style="padding: 28px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
                    <h2 class="section-title" style="margin-bottom: 0;"><span>📖</span> Thực đơn & Món ăn đặc trưng</h2>
                    @if($eatery->dishes->count() > 0)
                        <button onclick="openFullMenuModal()" class="btn-secondary" style="font-size: 0.85rem; padding: 8px 18px; border-radius: 8px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; background: rgba(255, 126, 41, 0.05); border-color: rgba(255, 126, 41, 0.2); color: var(--primary);">
                            📖 Xem toàn bộ thực đơn
                        </button>
                    @endif
                </div>
                
                @if($eatery->dishes->count() > 0)
                    <div style="position: relative; width: 100%;">
                        <!-- Slider Navigation Arrows -->
                        <button id="slideMenuPrev" class="menu-slider-btn prev-btn" onclick="scrollMenuSlider(-1)" aria-label="Previous slide">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="display: block;"><polyline points="15 18 9 12 15 6"></polyline></svg>
                        </button>
                        <button id="slideMenuNext" class="menu-slider-btn next-btn" onclick="scrollMenuSlider(1)" aria-label="Next slide">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="display: block;"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </button>
                        
                        <div class="menu-slider-wrapper" id="menuSliderWrapper">
                            <div class="menu-slider-content" id="menuSliderContent">
                                @foreach($eatery->dishes as $dish)
                                    <div class="dish-card glass-panel" style="background: rgba(255,255,255,0.02); flex: 0 0 calc(50% - 10px); min-width: 290px;">
                                        <img src="{{ $dish->image_path ?: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=150&q=80' }}" class="dish-img" alt="{{ $dish->name }}">
                                        <div class="dish-info" style="flex: 1;">
                                            <div>
                                                @if($dish->is_signature)
                                                    <span class="tag-badge" style="padding: 1px 6px; font-size: 0.65rem; font-weight: 700; margin-bottom: 4px; display: inline-block;">★ Món đặc trưng</span>
                                                @endif
                                                <h3 class="dish-name">{{ $dish->name }}</h3>
                                                <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 4px; line-height: 1.4;">{{ $dish->description }}</p>
                                            </div>
                                            <span class="dish-price">{{ number_format($dish->price, 0, ',', '.') }}đ</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <p style="color: var(--text-muted); font-style: italic; text-align: center;">Chưa cập nhật thực đơn chi tiết cho địa điểm này.</p>
                @endif
            </div>

            <!-- Styles cho Slider thực đơn -->
            <style>
                .menu-slider-wrapper {
                    overflow-x: auto;
                    scroll-behavior: smooth;
                    width: 100%;
                    padding: 10px 0;
                    scrollbar-width: none; /* Firefox */
                }
                .menu-slider-wrapper::-webkit-scrollbar {
                    display: none; /* Safari and Chrome */
                }
                .menu-slider-content {
                    display: flex;
                    gap: 20px;
                    transition: all 0.4s ease;
                }
                .menu-slider-btn {
                    position: absolute;
                    top: 50%;
                    transform: translateY(-50%);
                    width: 48px;
                    height: 48px;
                    border-radius: 50%;
                    background: rgba(255, 255, 255, 0.08);
                    border: 1px solid rgba(255, 255, 255, 0.15);
                    color: var(--text-main);
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 10;
                    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                    backdrop-filter: blur(12px);
                    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.25);
                }
                .menu-slider-btn:hover {
                    background: var(--primary-grad);
                    border-color: rgba(255, 255, 255, 0.25);
                    color: #ffffff;
                    box-shadow: 0 10px 25px rgba(255, 111, 0, 0.45);
                    transform: translateY(-50%) scale(1.08);
                }
                .menu-slider-btn:active {
                    transform: translateY(-50%) scale(0.95);
                }
                .menu-slider-btn.prev-btn {
                    left: -24px;
                }
                .menu-slider-btn.next-btn {
                    right: -24px;
                }
                
                /* Modal tab buttons */
                .modal-tab-btn {
                    background: rgba(255, 255, 255, 0.03);
                    border: 1px solid var(--border-glow);
                    color: var(--text-muted);
                    padding: 8px 18px;
                    border-radius: 30px;
                    font-size: 0.82rem;
                    font-weight: 700;
                    cursor: pointer;
                    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
                    white-space: nowrap;
                    display: inline-flex;
                    align-items: center;
                    gap: 6px;
                }
                .modal-tab-btn:hover {
                    background: rgba(255, 255, 255, 0.08);
                    color: var(--text-main);
                    transform: translateY(-1px);
                }
                .modal-tab-btn.active {
                    background: var(--primary-grad);
                    border-color: rgba(255, 255, 255, 0.15);
                    color: #ffffff;
                    box-shadow: 0 4px 15px rgba(255, 111, 0, 0.3);
                }

                @media (max-width: 768px) {
                    .menu-slider-btn {
                        display: none;
                    }
                    .menu-slider-content .dish-card {
                        flex: 0 0 85% !important;
                    }
                }
            </style>

            <!-- Full Menu Detail Modal -->
            <div id="fullMenuModal" style="display: none; position: fixed; inset: 0; z-index: 99999; background: rgba(0, 0, 0, 0.75); backdrop-filter: blur(12px); align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s ease;">
                <div class="lightbox-content" style="background: var(--bg-card); border: 1px solid var(--border-glow); width: 90%; max-width: 780px; max-height: 85vh; border-radius: 24px; box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5); overflow: hidden; transform: scale(0.9); transition: transform 0.3s ease; display: flex; flex-direction: column; position: relative;">
                    <!-- Modal Header -->
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed var(--border-glow); padding: 20px 24px; background: rgba(255,255,255,0.01);">
                        <h3 style="margin: 0; font-size: 1.4rem; font-weight: 800; color: var(--text-main); display: flex; align-items: center; gap: 10px; font-family: var(--font-heading);">
                            📖 Thực Đơn Chi Tiết - {{ $eatery->name }}
                        </h3>
                        <button onclick="closeFullMenuModal()" style="background: transparent; border: none; font-size: 1.5rem; color: var(--text-muted); cursor: pointer; transition: color 0.2s;" onmouseover="this.style.color='#f04e23'" onmouseout="this.style.color='var(--text-muted)'">✕</button>
                    </div>
                    
                    <!-- Modal Content (Scrollable List of Dishes) -->
                    <div style="overflow-y: auto; padding: 24px; flex: 1; display: flex; flex-direction: column; gap: 16px;">
                        <!-- Thanh tìm kiếm & Lọc nhanh -->
                        <div style="position: relative; margin-bottom: 4px;">
                            <span style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1.1rem; pointer-events: none;">🔍</span>
                            <input type="text" id="dishSearchInput" oninput="filterModalDishes()" placeholder="Tìm món ăn ngon theo tên hoặc mô tả..." style="width: 100%; padding: 12px 16px 12px 46px; background: rgba(255,255,255,0.03); border: 1.5px solid var(--border-glow); border-radius: 14px; color: var(--text-main); font-size: 0.88rem; outline: none; transition: all 0.3s;" onfocus="this.style.borderColor='var(--primary)'; this.style.boxShadow='0 0 12px rgba(255, 126, 41, 0.15)'" onblur="this.style.borderColor='var(--border-glow)'; this.style.boxShadow='none'">
                        </div>

                        <!-- Bộ lọc nhóm món ăn -->
                        <div style="display: flex; gap: 8px; overflow-x: auto; padding-bottom: 8px; scrollbar-width: none; -ms-overflow-style: none;">
                            <button class="modal-tab-btn active" onclick="filterModalTab('all')" data-tab="all">📋 Tất cả ({{ $eatery->dishes->count() }})</button>
                            @if($eatery->dishes->where('is_signature', true)->count() > 0)
                                <button class="modal-tab-btn" onclick="filterModalTab('signature')" data-tab="signature">★ Món đặc trưng ({{ $eatery->dishes->where('is_signature', true)->count() }})</button>
                            @endif
                            <button class="modal-tab-btn" onclick="filterModalTab('best-price')" data-tab="best-price">💰 Giá tiết kiệm (≤ 200k)</button>
                        </div>

                        <!-- Grid hiển thị món ăn -->
                        <div id="modalMenuGrid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            @foreach($eatery->dishes as $dish)
                                <div class="dish-card glass-panel" 
                                     data-name="{{ strtolower($dish->name) }}" 
                                     data-desc="{{ strtolower($dish->description) }}" 
                                     data-signature="{{ $dish->is_signature ? 'true' : 'false' }}" 
                                     data-price="{{ $dish->price }}"
                                     style="background: rgba(255,255,255,0.02); display: flex; gap: 16px; padding: 16px; border-radius: 12px; border: 1px solid var(--border-glow); transition: opacity 0.25s ease, transform 0.25s ease; opacity: 1; transform: translateY(0);">
                                    <img src="{{ $dish->image_path ?: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=150&q=80' }}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; flex-shrink: 0;" alt="{{ $dish->name }}">
                                    <div style="display: flex; flex-direction: column; justify-content: space-between; flex: 1;">
                                        <div>
                                            @if($dish->is_signature)
                                                <span class="tag-badge" style="padding: 1px 6px; font-size: 0.65rem; font-weight: 700; margin-bottom: 4px; display: inline-block;">★ Món đặc trưng</span>
                                            @endif
                                            <h4 style="font-weight: 600; font-size: 0.95rem; color: var(--text-main); margin: 0;">{{ $dish->name }}</h4>
                                            <p style="font-size: 0.78rem; color: var(--text-muted); margin: 4px 0 0 0; line-height: 1.4;">{{ $dish->description }}</p>
                                        </div>
                                        <span style="color: var(--primary); font-weight: 700; font-size: 0.95rem; font-family: var(--font-heading); margin-top: 4px;">{{ number_format($dish->price, 0, ',', '.') }}đ</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div style="border-top: 1px dashed var(--border-glow); padding: 16px 24px; background: rgba(255,255,255,0.01); display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 0.85rem; color: var(--text-muted);">Đang hiển thị: <strong style="color: var(--accent);" id="modalVisibleCount">{{ $eatery->dishes->count() }} món</strong></span>
                        <button onclick="closeFullMenuModal()" class="btn-primary" style="font-size: 0.85rem; padding: 8px 20px; border-radius: 8px; cursor: pointer;">Đóng thực đơn</button>
                    </div>
                </div>
            </div>
            
            <!-- Đánh giá bình luận -->
            <div class="detail-section glass-panel" style="padding: 28px;">
                <h2 class="section-title" id="reviewsSection">
                    <span>💬</span> Đánh giá từ thực khách
                    <span style="font-size: 0.9rem; color: var(--text-muted); font-weight: normal;">
                        ({{ $eatery->reviews->count() }} nhận xét)
                    </span>
                </h2>
                
                <!-- Hiển thị thông báo thành công khi gửi đánh giá -->
                @if(session('success'))
                    <div class="glass-panel" style="background: rgba(32, 178, 170, 0.1); border-color: var(--accent); padding: 14px 20px; border-radius: 8px; color: var(--accent); margin-bottom: 20px; font-size: 0.95rem;">
                        {{ session('success') }}
                    </div>
                @endif
                
                <!-- Form Đánh giá mới -->
                <div class="review-submit glass-panel" style="background: rgba(255,255,255,0.02);">
                    <h3 style="font-size: 1.1rem; margin-bottom: 16px;">✍️ Gửi nhận xét của bạn</h3>
                    <form action="/dia-diem/reviews/{{ $eatery->id }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="review-form-group">
                            <label class="review-form-label">Tên của bạn</label>
                            <input type="text" name="user_name" class="form-input" required placeholder="Nhập tên..." value="{{ session('user_name') ?: '' }}">
                        </div>
                        
                        <div class="review-form-group">
                            <label class="review-form-label">Chấm điểm sao (1 - 5 sao)</label>
                            <div class="stars-rating-select" id="starsSelector">
                                <span data-value="1">☆</span>
                                <span data-value="2">☆</span>
                                <span data-value="3">☆</span>
                                <span data-value="4">☆</span>
                                <span data-value="5">☆</span>
                            </div>
                            <input type="hidden" name="rating" id="ratingInput" value="">
                        </div>
                        
                        <div class="review-form-group">
                            <label class="review-form-label">Nhận xét của bạn</label>
                            <textarea name="comment" class="form-input" rows="4" placeholder="Nhập cảm nhận của bạn về món ăn, không gian, dịch vụ quán..." style="resize: vertical;"></textarea>
                        </div>
                        
                        <div class="review-form-group">
                            <label class="review-form-label">Thêm Ảnh / Video ngắn (Tùy chọn)</label>
                            <input type="file" name="media[]" class="form-input" multiple accept="image/*,video/*">
                        </div>
                        
                        <button type="submit" class="btn-primary" style="margin-top: 8px;">Gửi đánh giá</button>
                    </form>
                </div>
                
                <!-- Danh sách bình luận -->
                @if($eatery->reviews->count() > 0)
                    <div class="review-list" style="display: flex; flex-direction: column; gap: 24px; margin-top: 24px;">
                        @foreach($eatery->reviews->take(5) as $rev)
                            <div class="review-card glass-panel" style="padding: 24px; border-radius: 20px; border: 1px solid var(--border-glow); background: rgba(255, 255, 255, 0.015); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.015); transition: all 0.3s ease; display: flex; flex-direction: column; gap: 16px;">
                                <!-- User Info Header -->
                                <div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
                                    <div style="display: flex; align-items: center; gap: 14px;">
                                        <!-- Avatar with gradient -->
                                        <div style="width: 46px; height: 46px; border-radius: 50%; background: linear-gradient(135deg, var(--primary) 0%, #ff8b3d 100%); color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.1rem; box-shadow: 0 4px 15px rgba(255, 126, 41, 0.2); text-transform: uppercase;">
                                            {{ substr($rev->user_name, 0, 2) }}
                                        </div>
                                        
                                        <!-- Name & Stars -->
                                        <div>
                                            <span style="font-weight: 700; color: var(--text-main); font-size: 1.05rem; display: block;">{{ $rev->user_name }}</span>
                                            <div style="color: #ffb03a; font-size: 0.95rem; margin-top: 2px; display: flex; gap: 3px;">
                                                @for($i=1; $i<=5; $i++)
                                                    @if($i <= $rev->rating)
                                                        <span style="color: #ffb03a; text-shadow: 0 0 8px rgba(255, 176, 58, 0.4);">★</span>
                                                    @else
                                                        <span style="color: var(--border-glow);">★</span>
                                                    @endif
                                                @endfor
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Date Badge -->
                                    <span style="font-size: 0.8rem; color: var(--text-muted); background: rgba(255,255,255,0.04); padding: 4px 12px; border-radius: 30px; border: 1px solid var(--border-glow); display: inline-flex; align-items: center; gap: 4px;">
                                        📅 {{ $rev->created_at->format('d/m/Y H:i') }}
                                    </span>
                                </div>

                                <!-- Review Comment Text -->
                                <p style="font-size: 0.98rem; color: var(--text-main); line-height: 1.7; margin: 0; white-space: pre-line; font-weight: 450;">{{ $rev->comment }}</p>
                                
                                <!-- Attached Media (Photos/Videos) -->
                                @if($rev->media && $rev->media->count() > 0)
                                    <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 4px;">
                                        @foreach($rev->media as $mediaItem)
                                            @if($mediaItem->file_type === 'image')
                                                <div style="position: relative; width: 100px; height: 100px; border-radius: 12px; overflow: hidden; border: 1.5px solid var(--border-glow); box-shadow: 0 4px 12px rgba(0,0,0,0.12); cursor: pointer; transition: all 0.25s ease;" onmouseover="this.style.transform='scale(1.05)'; this.style.borderColor='var(--primary)'" onmouseout="this.style.transform='none'; this.style.borderColor='var(--border-glow)'">
                                                    <img src="{{ $mediaItem->file_path }}" alt="Review Image" style="width: 100%; height: 100%; object-fit: cover;">
                                                </div>
                                            @else
                                                <div style="position: relative; width: 140px; height: 100px; border-radius: 12px; overflow: hidden; border: 1.5px solid var(--border-glow); box-shadow: 0 4px 12px rgba(0,0,0,0.12);">
                                                    <video src="{{ $mediaItem->file_path }}" style="width: 100%; height: 100%; object-fit: cover;" controls></video>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Official Seller Reply Box -->
                                @if($rev->seller_reply)
                                    <div class="seller-reply-bubble" style="margin-top: 8px; padding: 18px 22px; background: rgba(255, 126, 41, 0.04); border: 1.5px solid rgba(255, 126, 41, 0.15); border-radius: 18px; font-size: 0.9rem; box-shadow: 0 8px 24px rgba(255, 126, 41, 0.02); position: relative;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; flex-wrap: wrap; gap: 8px;">
                                            <strong style="color: var(--primary); display: flex; align-items: center; gap: 8px; font-size: 0.92rem; font-weight: 800;">
                                                <span style="font-size: 1.2rem;">🏪</span> Phản hồi từ chủ quán
                                            </strong>
                                            <span style="font-size: 0.72rem; color: var(--text-muted); font-weight: 600;">Chủ cửa hàng</span>
                                        </div>
                                        <p style="margin: 0; color: var(--text-main); line-height: 1.65; font-style: italic; font-weight: 500;">
                                            "{{ $rev->seller_reply }}"
                                        </p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    @if($eatery->reviews->count() > 5)
                        <div style="display: flex; justify-content: center; margin-top: 24px;">
                            <button onclick="openAllReviewsModal()" class="btn-secondary" style="font-size: 0.95rem; padding: 12px 28px; border-radius: 12px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; background: rgba(255, 126, 41, 0.05); border: 1.5px solid rgba(255, 126, 41, 0.2); color: var(--primary); transition: all 0.3s ease; outline: none;" onmouseover="this.style.background='rgba(255, 126, 41, 0.1)'; this.style.transform='translateY(-2px)'" onmouseout="this.style.background='rgba(255, 126, 41, 0.05)'; this.style.transform='none'">
                                💬 Xem tất cả đánh giá & phản hồi ({{ $eatery->reviews->count() }} nhận xét)
                            </button>
                        </div>
                    @endif
                @else
                    <p style="color: var(--text-muted); font-style: italic; text-align: center; padding: 20px 0;">Chưa có đánh giá nào. Hãy là người đầu tiên chia sẻ cảm nhận về địa điểm này!</p>
                @endif
            </div>

            <!-- Bridge Text and upgraded Trust Hub Card -->
            <div style="margin-top: 40px; margin-bottom: 20px; padding: 0 10px; display: flex; align-items: flex-start; gap: 12px; background: rgba(32, 178, 170, 0.03); border: 1px dashed rgba(32, 178, 170, 0.2); padding: 16px; border-radius: 12px;">
                <span style="font-size: 1.5rem; filter: drop-shadow(0 0 5px rgba(32, 178, 170, 0.5));">🛡️</span>
                <span style="font-size: 0.9rem; line-height: 1.6; color: var(--text-muted); font-style: italic;">
                    Nhằm đảm bảo sức khỏe cộng đồng và bảo tồn tinh hoa ẩm thực địa phương, nhà hàng tự nguyện công khai toàn bộ hồ sơ nguồn gốc thực phẩm dưới sự giám sát chặt chẽ của các cơ quan chức năng huyện Đông Anh.
                </span>
            </div>

            <!-- Báo cáo Minh bạch An toàn & Truy xuất nguồn gốc thực phẩm sạch -->
            <div id="trust-hub-section" class="detail-section glass-panel trust-hub-card" style="padding: 28px; margin-bottom: 40px; position: relative;">
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 24px; border-bottom: 1px dashed rgba(32, 178, 170, 0.25); padding-bottom: 16px;">
                    <h2 class="section-title" style="margin: 0; border: none; padding: 0;">
                        <span style="display: inline-block; filter: drop-shadow(0 0 8px rgba(32, 178, 170, 0.5));">🛡️</span> Minh Bạch An Toàn & Truy Xuất Số
                    </h2>
                    @if($eatery->foodSafetyCertificate)
                        <span class="trust-badge" style="background: rgba(32, 178, 170, 0.1); border: 1px solid var(--accent); color: var(--accent); font-weight: 700; font-size: 0.8rem; padding: 4px 12px; border-radius: 20px;">
                            ✓ ĐÃ XÁC MINH CSDL
                        </span>
                    @else
                        <span class="trust-badge" style="background: rgba(255, 193, 7, 0.1); border: 1px solid #ffc107; color: #ffc107; font-weight: 700; font-size: 0.8rem; padding: 4px 12px; border-radius: 20px;">
                            ⚠ ĐANG CHỜ CẬP NHẬT
                        </span>
                    @endif
                </div>

                @if($eatery->foodSafetyCertificate || $eatery->foodSupplyContracts->count() > 0 || $eatery->purchaseInvoices->count() > 0 || $eatery->dailyFoodLogs->count() > 0)
                    <!-- Khiên An toàn Vàng kim / Xanh ngọc (Trust Shield Banner) -->
                    <div class="trust-shield-banner glass-panel" style="background: linear-gradient(135deg, rgba(32, 178, 170, 0.08) 0%, rgba(0, 150, 136, 0.02) 100%); border: 1px solid rgba(32, 178, 170, 0.25); padding: 18px 24px; border-radius: 16px; margin-bottom: 24px; display: flex; gap: 16px; align-items: center;">
                        <div style="font-size: 2.2rem; animation: pulse-trust 2s infinite;">🛡️</div>
                        <div>
                            <h4 style="font-size: 1.05rem; color: var(--accent); font-weight: 700; margin-bottom: 4px; text-transform: uppercase;">Cơ sở Đủ Điều Kiện An Toàn Thực Phẩm</h4>
                            <p style="font-size: 0.88rem; line-height: 1.6; color: var(--text-main); margin: 0;">
                                Nhà hàng đã công khai toàn bộ hồ sơ pháp lý, hợp đồng cung cấp thực phẩm sạch và nhật ký kiểm tra hàng ngày trên hệ thống dữ liệu số của huyện Đông Anh.
                            </p>
                        </div>
                    </div>

                    <div class="trust-tabs-container">
                        <div class="trust-tab-buttons" style="display: flex; gap: 8px; margin-bottom: 20px; overflow-x: auto; padding-bottom: 8px; border-bottom: 1px solid var(--border-glow);">
                            <button class="trust-tab-btn active" data-trust-tab="trust-cert" style="white-space: nowrap;">🛡️ Chứng Nhận ATTP</button>
                            <button class="trust-tab-btn" data-trust-tab="trust-contracts" style="white-space: nowrap;">📜 Hợp Đồng Cung Ứng</button>
                            <button class="trust-tab-btn" data-trust-tab="trust-invoices" style="white-space: nowrap;">🧾 Hóa Đơn Mua Hàng</button>
                            <button class="trust-tab-btn" data-trust-tab="trust-logs" style="white-space: nowrap;">📅 Nhật Ký An Toàn</button>
                        </div>

                        <!-- Tab 1: Giấy chứng nhận VSATTP -->
                        <div id="trust-cert" class="trust-tab-content active-content">
                            @if($eatery->foodSafetyCertificate)
                                <div style="display: flex; gap: 20px; flex-wrap: wrap; align-items: flex-start;">
                                    <div>
                                        <div class="cert-image-preview" style="position: relative; width: 140px; height: 190px; border-radius: 8px; overflow: hidden; border: 1px solid var(--border-glow); cursor: pointer;" onclick="openTrustLightbox('{{ $eatery->foodSafetyCertificate->image_path }}', 'Giấy chứng nhận VSATTP số {{ $eatery->foodSafetyCertificate->certificate_number }}')">
                                            <img src="{{ $eatery->foodSafetyCertificate->image_path }}" style="width: 100%; height: 100%; object-fit: cover;">
                                            <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s;" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0">
                                                <span style="font-size: 1.5rem;">🔍</span>
                                            </div>
                                        </div>
                                        <div style="font-size: 0.7rem; color: var(--text-muted); margin-top: 8px; text-align: center; max-width: 140px; line-height: 1.3;">
                                            Nhấp vào ảnh để xem chi tiết hoặc tải về
                                        </div>
                                    </div>
                                    <div style="flex: 1; min-width: 250px;">
                                        <h4 style="color: var(--accent); font-size: 1.15rem; margin-bottom: 12px; font-weight: 700;">Giấy Chứng Nhận Đủ Điều Kiện ATTP</h4>
                                        <table style="width: 100%; font-size: 0.9rem; border-collapse: collapse;">
                                            <tr style="border-bottom: 1px dashed var(--border-glow);">
                                                <td style="padding: 8px 0; color: var(--text-muted); width: 140px;">Số chứng chỉ:</td>
                                                <td style="padding: 8px 0; font-weight: 600; color: var(--text-main);">{{ $eatery->foodSafetyCertificate->certificate_number }}</td>
                                            </tr>
                                            <tr style="border-bottom: 1px dashed var(--border-glow);">
                                                <td style="padding: 8px 0; color: var(--text-muted);">Cơ quan cấp:</td>
                                                <td style="padding: 8px 0; font-weight: 600; color: var(--text-main);">{{ $eatery->foodSafetyCertificate->issued_by }}</td>
                                            </tr>
                                            <tr style="border-bottom: 1px dashed var(--border-glow);">
                                                <td style="padding: 8px 0; color: var(--text-muted);">Ngày cấp:</td>
                                                <td style="padding: 8px 0; font-weight: 600; color: var(--text-main);">{{ $eatery->foodSafetyCertificate->issued_at->format('d/m/Y') }}</td>
                                            </tr>
                                            <tr style="border-bottom: 1px dashed var(--border-glow);">
                                                <td style="padding: 8px 0; color: var(--text-muted);">Hạn dùng đến:</td>
                                                <td style="padding: 8px 0; font-weight: 600; color: var(--text-main);">{{ $eatery->foodSafetyCertificate->expired_at->format('d/m/Y') }}</td>
                                            </tr>
                                            <tr style="border-bottom: 1px dashed var(--border-glow);">
                                                <td style="padding: 8px 0; color: var(--text-muted);">Thời hạn giám sát:</td>
                                                <td style="padding: 8px 0; font-weight: 700;">
                                                    @php
                                                        $daysLeft = $eatery->foodSafetyCertificate->days_left;
                                                        $expiryStatus = $eatery->foodSafetyCertificate->expiry_status;
                                                    @endphp
                                                    @if($expiryStatus === 'valid')
                                                        <span style="color: #2ecc71; display: inline-flex; align-items: center; gap: 6px;">
                                                            <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #2ecc71; box-shadow: 0 0 10px #2ecc71; animation: pulse-trust 2s infinite;"></span> Còn {{ $daysLeft }} ngày (An toàn hoạt động)
                                                        </span>
                                                    @elseif($expiryStatus === 'warning')
                                                        <span style="color: #ff9f43; display: inline-flex; align-items: center; gap: 6px; animation: pulse-text 2s infinite;">
                                                            <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #ff9f43; box-shadow: 0 0 10px #ff9f43;"></span> Sắp hết hạn (Còn {{ $daysLeft }} ngày) - Hệ thống đang chuẩn bị hồ sơ gia hạn tự động
                                                        </span>
                                                    @else
                                                        <span style="color: #e74c3c; display: inline-flex; align-items: center; gap: 6px;">
                                                            <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #e74c3c; box-shadow: 0 0 10px #e74c3c;"></span> Đã quá hạn {{ abs($daysLeft) }} ngày - Yêu cầu gia hạn khẩn cấp
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: var(--text-muted);">Trạng thái pháp lý:</td>
                                                <td style="padding: 8px 0;">
                                                    @if($expiryStatus === 'expired')
                                                        <span style="color: #e74c3c; font-weight: 800; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.5px;">🔴 Hết hiệu lực / Tạm đình chỉ</span>
                                                    @else
                                                        <span style="color: #2ecc71; font-weight: 800; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.5px;">🟢 Đang hoạt động / Được bảo hộ</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                        
                                        <!-- Con Dấu Số Thẩm Định & QR Thẩm Định -->
                                        <div style="margin-top: 20px; display: flex; gap: 16px; align-items: center; flex-wrap: wrap; background: linear-gradient(135deg, rgba(39, 174, 96, 0.05) 0%, rgba(46, 204, 113, 0.01) 100%); border: 1px solid rgba(39, 174, 96, 0.2); padding: 16px; border-radius: 16px; box-shadow: inset 0 0 12px rgba(39, 174, 96, 0.02);">
                                            <div style="font-size: 2.2rem; filter: drop-shadow(0 4px 8px rgba(46, 204, 113, 0.3)); flex-shrink: 0; animation: pulse-trust 2s infinite;">🛡️</div>
                                            <div style="flex: 1; min-width: 200px;">
                                                <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap; margin-bottom: 4px;">
                                                    <h5 style="margin: 0; font-size: 0.85rem; font-weight: 800; color: #27ae60; text-transform: uppercase; letter-spacing: 0.5px;">CON DẤU SỐ ĐÔNG ANH</h5>
                                                    <span style="background: rgba(39, 174, 96, 0.15); color: #27ae60; border: 1px solid #2ecc71; font-size: 0.6rem; padding: 1px 6px; border-radius: 4px; font-weight: 800;">ĐÃ ĐỐI CHIẾU CƠ SỞ DỮ LIỆU</span>
                                                </div>
                                                <p style="margin: 0; font-size: 0.8rem; color: var(--text-main); line-height: 1.4;">
                                                    Hệ thống xác thực liên kết trực tiếp với Phòng Y tế Huyện Đông Anh. Chứng thực 100% tài liệu thật, còn hiệu lực và được phê duyệt chính thức bởi UBND Huyện Đông Anh.
                                                </p>
                                            </div>
                                            <div style="flex-shrink: 0; background: #ffffff; padding: 8px; border-radius: 12px; border: 1px solid rgba(39, 174, 96, 0.2); box-shadow: 0 4px 10px rgba(0,0,0,0.08); text-align: center; cursor: pointer;" onclick="openTrustLightbox('https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=https://donganh.hanoi.gov.vn/phong-ban-y-te-xac-minh-id-{{ $eatery->foodSafetyCertificate->id }}', 'Mã QR Xác Thực Công Hành của UBND Huyện Đông Anh')">
                                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=60x60&data=https://donganh.hanoi.gov.vn/phong-ban-y-te-xac-minh-id-{{ $eatery->foodSafetyCertificate->id }}" style="width: 60px; height: 60px; display: block; mix-blend-mode: multiply;">
                                                <span style="display: block; font-size: 0.55rem; color: var(--text-muted); margin-top: 4px; font-weight: 700;">QUÉT THẨM ĐỊNH</span>
                                            </div>
                                        </div>

                                        <!-- Nút Báo Cáo Phản Ánh ATTP Của Khách Hàng -->
                                        <div style="margin-top: 14px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; background: rgba(231, 76, 60, 0.02); border: 1px dashed rgba(231, 76, 60, 0.2); padding: 12px 16px; border-radius: 12px;">
                                            <div style="display: flex; align-items: center; gap: 8px; min-width: 250px; flex: 1;">
                                                <span style="font-size: 1.1rem; animation: pulse-trust 2s infinite;">📢</span>
                                                <span style="font-size: 0.8rem; color: var(--text-muted); line-height: 1.4;">
                                                    Bạn ăn thấy quán không đảm bảo vệ sinh như cam kết? Phản hồi ẩn danh ngay để bảo vệ sức khỏe cộng đồng.
                                                </span>
                                            </div>
                                            <button onclick="openFeedbackModal('{{ $eatery->name }}')" class="btn-secondary" style="font-size: 0.75rem; padding: 6px 12px; border-radius: 8px; font-weight: 700; color: #e74c3c; border-color: rgba(231, 76, 60, 0.2); background: rgba(231, 76, 60, 0.04); display: inline-flex; align-items: center; gap: 4px; transition: all 0.3s; cursor: pointer;" onmouseover="this.style.background='rgba(231, 76, 60, 0.08)'; this.style.color='#c0392b';" onmouseout="this.style.background='rgba(231, 76, 60, 0.04)'; this.style.color='#e74c3c';">
                                                🚨 Gửi Phản Ánh ATTP
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <p style="color: var(--text-muted); font-style: italic; text-align: center; padding: 20px 0;">Chưa cập nhật Giấy chứng nhận ATTP của cơ sở.</p>
                            @endif
                        </div>

                        <!-- Tab 2: Hợp đồng cung ứng thực phẩm sạch -->
                        <div id="trust-contracts" class="trust-tab-content">
                            @if($eatery->foodSupplyContracts->count() > 0)
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
                                    @foreach($eatery->foodSupplyContracts as $contract)
                                        <div class="glass-panel" style="padding: 16px; background: rgba(255,255,255,0.01); display: flex; gap: 16px; align-items: center; border: 1px solid var(--border-glow); border-radius: 12px;">
                                            <div style="position: relative; width: 60px; height: 85px; border-radius: 6px; overflow: hidden; border: 1px solid var(--border-glow); cursor: pointer; flex-shrink: 0;" onclick="openTrustLightbox('{{ $contract->image_path }}', 'Bản quét hợp đồng cung cấp sạch từ {{ $contract->supplier_name }}')">
                                                <img src="{{ $contract->image_path }}" style="width: 100%; height: 100%; object-fit: cover;">
                                                <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.2s;" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0">
                                                    <span style="font-size: 1rem;">🔍</span>
                                                </div>
                                            </div>
                                            <div style="min-width: 0; flex: 1;">
                                                <h5 style="margin: 0 0 4px 0; font-size: 0.95rem; font-weight: 700; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $contract->supplier_name }}</h5>
                                                <p style="margin: 0 0 6px 0; font-size: 0.8rem; color: var(--accent); font-weight: 600; line-height: 1.3;">🌾 {{ $contract->items_supplied }}</p>
                                                <p style="margin: 0; font-size: 0.75rem; color: var(--text-muted);">Hiệu lực: {{ $contract->signed_at->format('d/m/Y') }} - {{ $contract->expired_at->format('d/m/Y') }}</p>
                                                <button class="btn-secondary" style="font-size: 0.7rem; padding: 3px 8px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px;" onclick="openTrustLightbox('{{ $contract->image_path }}', 'Bản quét hợp đồng cung cấp sạch (đã ẩn chi tiết thương mại)')">
                                                    📄 Xem hợp đồng mẫu
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p style="color: var(--text-muted); font-style: italic; text-align: center; padding: 20px 0;">Chưa cập nhật thông tin hợp đồng cung cấp thực phẩm sạch.</p>
                            @endif
                        </div>

                        <!-- Tab 3: Hóa đơn mua bán thực tế -->
                        <div id="trust-invoices" class="trust-tab-content">
                            @if($eatery->purchaseInvoices->count() > 0)
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
                                    @foreach($eatery->purchaseInvoices as $invoice)
                                        @php
                                            $itemLower = mb_strtolower($invoice->items_summary);
                                            $icon = '🧾';
                                            $iconBg = 'rgba(32, 178, 170, 0.1)';
                                            $iconBorder = 'rgba(32, 178, 170, 0.3)';
                                            if (str_contains($itemLower, 'thịt') || str_contains($itemLower, 'heo') || str_contains($itemLower, 'bò') || str_contains($itemLower, 'xương')) {
                                                $icon = '🥩';
                                                $iconBg = 'rgba(231, 76, 60, 0.1)';
                                                $iconBorder = 'rgba(231, 76, 60, 0.3)';
                                            } elseif (str_contains($itemLower, 'gạo') || str_contains($itemLower, 'bột') || str_contains($itemLower, 'nếp')) {
                                                $icon = '🌾';
                                                $iconBg = 'rgba(255, 179, 0, 0.1)';
                                                $iconBorder = 'rgba(255, 179, 0, 0.3)';
                                            } elseif (str_contains($itemLower, 'rau') || str_contains($itemLower, 'quả') || str_contains($itemLower, 'hành') || str_contains($itemLower, 'củ')) {
                                                $icon = '🥬';
                                                $iconBg = 'rgba(46, 204, 113, 0.1)';
                                                $iconBorder = 'rgba(46, 204, 113, 0.3)';
                                            } elseif (str_contains($itemLower, 'cá') || str_contains($itemLower, 'hải sản')) {
                                                $icon = '🐟';
                                                $iconBg = 'rgba(52, 152, 219, 0.1)';
                                                $iconBorder = 'rgba(52, 152, 219, 0.3)';
                                            }
                                        @endphp
                                        <div class="glass-panel" style="padding: 16px; background: rgba(255,255,255,0.01); display: flex; gap: 16px; align-items: center; border: 1px solid var(--border-glow); border-radius: 12px;">
                                            <div style="width: 50px; height: 50px; border-radius: 50%; background: {{ $iconBg }}; border: 1px solid {{ $iconBorder }}; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0;">
                                                {{ $icon }}
                                            </div>
                                            <div style="min-width: 0; flex: 1;">
                                                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; margin-bottom: 2px;">
                                                    <div style="font-size: 0.75rem; color: var(--accent); font-weight: 700; text-transform: uppercase;">
                                                        📅 NHẬP HÀNG: {{ $invoice->invoice_date->format('d/m/Y') }}
                                                    </div>
                                                    <span style="font-size: 0.65rem; background: rgba(32, 178, 170, 0.1); border: 1px solid var(--accent); color: var(--accent); padding: 1px 6px; border-radius: 4px; font-weight: bold; white-space: nowrap;">ĐÃ ĐỐI CHIẾU</span>
                                                </div>
                                                <h5 style="margin: 0 0 4px 0; font-size: 0.92rem; font-weight: 700; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $invoice->supplier_name }}</h5>
                                                <p style="margin: 0 0 6px 0; font-size: 0.8rem; color: var(--text-muted); line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $invoice->items_summary }}</p>
                                                <button class="btn-secondary" style="font-size: 0.7rem; padding: 3px 8px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px;" onclick="openTrustLightbox('{{ $invoice->image_path }}', 'Bản quét hóa đơn nhập hàng sạch (dữ liệu giá trị thương mại đã được che bảo mật)')">
                                                    🔍 Xem hóa đơn
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p style="color: var(--text-muted); font-style: italic; text-align: center; padding: 20px 0;">Chưa cập nhật hóa đơn mua bán gần đây.</p>
                            @endif
                        </div>

                        <!-- Tab 4: Nhật ký hàng ngày -->
                        <div id="trust-logs" class="trust-tab-content">
                            @if($eatery->dailyFoodLogs->count() > 0)
                                <div style="display: flex; flex-direction: column; gap: 14px;">
                                    @foreach($eatery->dailyFoodLogs->take(7) as $log)
                                        @php
                                            $isOfficial = $log->checker_role === 'official';
                                        @endphp
                                        <div class="glass-panel" style="padding: 18px; border-radius: 16px; transition: all 0.3s ease;
                                            @if($isOfficial)
                                                border: 1px solid rgba(231, 76, 60, 0.3); background: rgba(231, 76, 60, 0.02); box-shadow: 0 4px 15px rgba(231, 76, 60, 0.05);
                                            @else
                                                border: 1px solid rgba(46, 204, 113, 0.2); background: rgba(46, 204, 113, 0.01); box-shadow: 0 4px 15px rgba(46, 204, 113, 0.02);
                                            @endif
                                        " onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
                                            
                                            <!-- Header with logo and badge -->
                                            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; border-bottom: 1px dashed var(--border-glow); padding-bottom: 10px; margin-bottom: 12px;">
                                                <div style="display: flex; align-items: center; gap: 6px;">
                                                    @if($isOfficial)
                                                        <span style="font-size: 1.1rem;">🏢</span>
                                                        <span style="font-weight: 700; color: #e74c3c; font-size: 0.92rem;">CƠ QUAN KIỂM TRA CHỨC NĂNG</span>
                                                    @else
                                                        <span style="font-size: 1.1rem;">👨‍🍳</span>
                                                        <span style="font-weight: 700; color: #2ecc71; font-size: 0.92rem;">TỰ KIỂM TRA HÀNG NGÀY</span>
                                                    @endif
                                                </div>
                                                <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">📅 Ngày {{ $log->log_date->format('d/m/Y') }}</span>
                                            </div>

                                            <!-- Checklist columns -->
                                            <div style="font-size: 0.88rem; display: flex; flex-direction: column; gap: 8px; line-height: 1.6;">
                                                <div style="display: flex; gap: 8px; align-items: flex-start;">
                                                    <span style="color: #2ecc71; font-weight: bold;">✓</span>
                                                    <div>
                                                        <strong style="color: var(--primary);">Nguồn gốc nguyên liệu:</strong> 
                                                        <span style="color: var(--text-main);">{{ $log->ingredients_origin }}</span>
                                                    </div>
                                                </div>
                                                <div style="display: flex; gap: 8px; align-items: flex-start;">
                                                    <span style="color: #2ecc71; font-weight: bold;">✓</span>
                                                    <div>
                                                        <strong style="color: var(--primary);">Bảo quản & Điều kiện:</strong> 
                                                        <span style="color: var(--text-main);">{{ $log->storage_condition }}</span>
                                                    </div>
                                                </div>
                                                <div style="display: flex; gap: 8px; align-items: flex-start;">
                                                    <span style="color: #2ecc71; font-weight: bold;">✓</span>
                                                    <div>
                                                        <strong style="color: var(--primary);">Kết quả kiểm nghiệm:</strong> 
                                                        <span style="color: #2ecc71; font-weight: 700;">ĐẠT TIÊU CHUẨN VỆ SINH</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Footer of card with badge inspector logo -->
                                            <div style="margin-top: 12px; border-top: 1px dashed var(--border-glow); padding-top: 10px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; font-size: 0.8rem;">
                                                <span style="color: var(--text-muted);">
                                                    Người phê duyệt: <strong style="color: var(--text-main);">{{ $log->checker_name }}</strong>
                                                </span>
                                                @if($isOfficial)
                                                    <span style="background: rgba(231, 76, 60, 0.1); border: 1px solid #e74c3c; color: #e74c3c; padding: 2px 8px; border-radius: 4px; font-weight: bold; font-size: 0.7rem; display: inline-flex; align-items: center; gap: 4px;">
                                                        🛡️ ĐÃ THẨM ĐỊNH
                                                    </span>
                                                @else
                                                    <span style="color: var(--text-muted); font-size: 0.72rem; font-style: italic; opacity: 0.7;">
                                                        Ghi nhận tự động
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p style="color: var(--text-muted); font-style: italic; text-align: center; padding: 20px 0;">Chưa ghi nhận nhật ký kiểm tra vệ sinh hàng ngày.</p>
                            @endif
                        </div>
                    </div>
                @else
                    <!-- Phân hệ thông tin chưa cập nhật -->
                    <div style="text-align: center; padding: 30px 20px;">
                        <div style="font-size: 3rem; margin-bottom: 16px; filter: drop-shadow(0 0 10px rgba(255,193,7,0.5));">🛡️</div>
                        <h4 style="font-size: 1.1rem; color: var(--text-main); font-weight: 700; margin-bottom: 8px;">HỆ THỐNG TRUY XUẤT CHƯA KÍCH HOẠT</h4>
                        <p style="font-size: 0.9rem; color: var(--text-muted); max-width: 500px; margin: 0 auto 20px auto; line-height: 1.6;">
                            Cơ sở kinh doanh này đang chuẩn bị hồ sơ minh bạch nguồn gốc thực phẩm đầu vào. Vui lòng quay lại sau khi hồ sơ được ban quản lý phê duyệt.
                        </p>
                        <div style="font-size: 0.85rem; color: var(--primary); font-weight: 600; background: var(--bg-btn-secondary); display: inline-block; padding: 8px 20px; border-radius: 30px; border: 1px dashed var(--border-glow);">
                            📞 Hotline Ban Quản Lý ATTP Đông Anh: 024.3883.2241
                        </div>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Right Side: Sidebar Information and Coordinates Map -->
        <aside>
            <!-- Thông tin liên hệ -->
            <div class="sidebar-widget glass-panel">
                <h3 style="font-size: 1.2rem; margin-bottom: 24px; border-bottom: 2px solid rgba(255,126,41,0.3); padding-bottom: 12px; color: var(--text-main); font-weight: 800;">
                    📌 Thông tin chi tiết
                </h3>
                <ul class="widget-info-list">
                    <li class="widget-info-item" style="padding: 10px; border-radius: 12px; transition: all 0.3s ease;" onmouseover="this.style.background='var(--bg-btn-secondary)'; this.style.transform='translateX(4px)';" onmouseout="this.style.background='transparent'; this.style.transform='none';">
                        <span class="widget-info-icon" style="background: rgba(255, 126, 41, 0.1); padding: 8px; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; border: 1px solid rgba(255, 126, 41, 0.2);">📞</span>
                        <div style="margin-left: 4px;">
                            <strong style="display: block; font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Điện thoại liên hệ</strong>
                            <a href="tel:{{ $eatery->phone }}" style="color: var(--text-main); font-weight: 700; font-size: 1.05rem; display: inline-block; margin-top: 2px;">{{ $eatery->phone ?: 'Chưa cập nhật' }}</a>
                        </div>
                    </li>
                    <li class="widget-info-item" style="padding: 10px; border-radius: 12px; transition: all 0.3s ease;" onmouseover="this.style.background='var(--bg-btn-secondary)'; this.style.transform='translateX(4px)';" onmouseout="this.style.background='transparent'; this.style.transform='none';">
                        <span class="widget-info-icon" style="background: rgba(32, 178, 170, 0.1); padding: 8px; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; border: 1px solid rgba(32, 178, 170, 0.2);">🕒</span>
                        <div style="margin-left: 4px;">
                            <strong style="display: block; font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Giờ mở cửa</strong>
                            <span style="color: var(--text-main); font-weight: 500; display: inline-block; margin-top: 2px;">{{ $eatery->opening_hours ?: 'Đang cập nhật' }}</span>
                        </div>
                    </li>
                    <li class="widget-info-item" style="padding: 10px; border-radius: 12px; transition: all 0.3s ease;" onmouseover="this.style.background='var(--bg-btn-secondary)'; this.style.transform='translateX(4px)';" onmouseout="this.style.background='transparent'; this.style.transform='none';">
                        <span class="widget-info-icon" style="background: rgba(255, 179, 0, 0.1); padding: 8px; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; border: 1px solid rgba(255, 179, 0, 0.2);">💰</span>
                        <div style="margin-left: 4px;">
                            <strong style="display: block; font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Mức giá tham khảo</strong>
                            <span style="color: var(--accent); font-weight: 700; font-size: 1.05rem; display: inline-block; margin-top: 2px; white-space: nowrap;">{{ $eatery->price_range ?: 'Đang cập nhật' }}</span>
                        </div>
                    </li>
                    <li class="widget-info-item" style="padding: 10px; border-radius: 12px; transition: all 0.3s ease;" onmouseover="this.style.background='var(--bg-btn-secondary)'; this.style.transform='translateX(4px)';" onmouseout="this.style.background='transparent'; this.style.transform='none';">
                        <span class="widget-info-icon" style="background: rgba(255, 193, 7, 0.1); padding: 8px; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; border: 1px solid rgba(255, 193, 7, 0.2);">⭐</span>
                        <div style="margin-left: 4px;">
                            <strong style="display: block; font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Đánh giá trung bình</strong>
                            <span style="font-weight: 800; color: #ffc107; font-size: 1.1rem; display: inline-block; margin-top: 2px; text-shadow: 0 0 10px rgba(255,193,7,0.3);">★ {{ number_format($eatery->average_rating, 1) }} / 5.0</span>
                        </div>
                    </li>
                </ul>
            </div>
            
            <!-- Mã QR Code Thông Minh -->
            <div class="sidebar-widget glass-panel" style="text-align: center; margin-bottom: 24px; padding-top: 32px;">
                <h3 style="font-size: 1.2rem; margin-bottom: 12px; color: var(--text-main); font-weight: 800;">
                    📲 Mã QR Nhà hàng
                </h3>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 20px; padding: 0 10px;">
                    Lưu mã QR hoặc đưa cho bạn bè quét để mở nhanh trang web thực đơn quán này!
                </p>
                <div style="background: #ffffff; padding: 16px; border-radius: 20px; display: inline-block; box-shadow: 0 8px 24px rgba(0,0,0,0.15), inset 0 0 0 1px rgba(0,0,0,0.05); transition: transform 0.3s ease;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='none'">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode(url()->current()) }}" alt="QR Code {{ $eatery->name }}" style="width: 180px; height: 180px; display: block; mix-blend-mode: multiply;">
                </div>
            </div>
            
            <!-- Định vị vị trí & chỉ đường -->
            <div class="sidebar-widget glass-panel">
                <h3 style="font-size: 1.2rem; margin-bottom: 16px; color: var(--text-main); font-weight: 800; border-bottom: 2px solid rgba(32, 178, 170, 0.3); padding-bottom: 12px;">
                    🗺️ Vị trí & Chỉ đường
                </h3>
                <p style="font-size: 0.8rem; color: var(--text-muted);">
                    📍 Vĩ độ: <strong>{{ $eatery->latitude }}</strong> | Kinh độ: <strong>{{ $eatery->longitude }}</strong>
                </p>
                
                <div class="mini-map-container">
                    <div id="miniMap" style="width: 100%; height: 100%;"></div>
                </div>
                
                <!-- Google Geolocation Distance Widget -->
                <div id="distanceWidget" class="glass-panel" style="padding: 12px; margin-top: 14px; background: rgba(32,178,170,0.05); border-color: rgba(32,178,170,0.1); display: block;">
                    <p style="font-size: 0.85rem; display: flex; align-items: center; gap: 8px;">
                        <span>🚗</span> Khoảng cách đến vị trí của bạn: <strong id="distanceKm" style="color: var(--accent);">Đang tính...</strong>
                    </p>
                </div>
                
                <a href="https://www.google.com/maps/dir/?api=1&destination={{ $eatery->latitude }},{{ $eatery->longitude }}" target="_blank" class="btn-primary" style="width: 100%; justify-content: center; margin-top: 10px; font-size: 0.9rem;">
                    🗺️ Hướng dẫn đường đi (Google Maps)
                </a>
            </div>
        </aside>
        
    </div>
</div>

<!-- Feedback Modal for Food Safety -->
<div id="feedbackModal" style="display: none; position: fixed; inset: 0; z-index: 9999; background: rgba(0, 0, 0, 0.65); backdrop-filter: blur(8px); align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s ease;">
    <div class="lightbox-content" style="background: var(--bg-card); border: 1px solid var(--border-glow); width: 90%; max-width: 500px; border-radius: 20px; box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4); overflow: hidden; transform: scale(0.9); transition: transform 0.3s ease; padding: 24px; position: relative;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed var(--border-glow); padding-bottom: 14px; margin-bottom: 16px;">
            <h4 style="margin: 0; font-size: 1.25rem; font-weight: 800; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
                📢 Báo Cáo Phản Ánh ATTP
            </h4>
            <button onclick="closeFeedbackModal()" style="background: transparent; border: none; font-size: 1.3rem; color: var(--text-muted); cursor: pointer; transition: color 0.2s;" onmouseover="this.style.color='#e74c3c'" onmouseout="this.style.color='var(--text-muted)'">✕</button>
        </div>
        
        <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0; margin-bottom: 16px; line-height: 1.5;">
            Mọi thông tin phản hồi của bạn về <strong id="feedbackEateryName" style="color: var(--accent);"></strong> đều được mã hóa ẩn danh hoàn toàn để đảm bảo an toàn riêng tư, đồng thời gửi trực tiếp tới Ban Quản Lý huyện Đông Anh.
        </p>
        
        <!-- Cảnh báo nếu ở xa quán -->
        <div id="feedbackFarWarning" style="display: none; background: rgba(243, 156, 18, 0.06); border: 1px solid rgba(243, 156, 18, 0.25); padding: 12px; border-radius: 10px; font-size: 0.8rem; color: #d35400; margin-bottom: 16px; line-height: 1.45;">
            ⚠️ <strong>Xác thực từ xa:</strong> Để tránh phản ánh giả mạo dìm hàng từ đối thủ, do bạn đang ở ngoài phạm vi của quán, vui lòng đính kèm <strong>ảnh chụp hóa đơn mua hàng</strong> hoặc <strong>ảnh món ăn tại cơ sở</strong> để làm minh chứng bắt buộc.
        </div>
        
        <form id="feedbackForm" onsubmit="submitFeedback(event)">
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">1. Chọn nội dung phản ánh</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 0.82rem; color: var(--text-main); background: rgba(255,255,255,0.02); border: 1px solid var(--border-glow); padding: 8px 12px; border-radius: 8px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.borderColor='rgba(231,76,60,0.3)';" onmouseout="this.style.borderColor='var(--border-glow)';">
                        <input type="radio" name="feedback_type" value="dirty_utensils" required style="accent-color: #e74c3c; cursor: pointer;">
                        <span>🍽️ Bát đũa bẩn</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 0.82rem; color: var(--text-main); background: rgba(255,255,255,0.02); border: 1px solid var(--border-glow); padding: 8px 12px; border-radius: 8px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.borderColor='rgba(231,76,60,0.3)';" onmouseout="this.style.borderColor='var(--border-glow)';">
                        <input type="radio" name="feedback_type" value="bad_ingredients" style="accent-color: #e74c3c; cursor: pointer;">
                        <span>🥩 Thực phẩm lạ</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 0.82rem; color: var(--text-main); background: rgba(255,255,255,0.02); border: 1px solid var(--border-glow); padding: 8px 12px; border-radius: 8px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.borderColor='rgba(231,76,60,0.3)';" onmouseout="this.style.borderColor='var(--border-glow)';">
                        <input type="radio" name="feedback_type" value="no_gloves" style="accent-color: #e74c3c; cursor: pointer;">
                        <span>👨‍🍳 Không găng tay</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 0.82rem; color: var(--text-main); background: rgba(255,255,255,0.02); border: 1px solid var(--border-glow); padding: 8px 12px; border-radius: 8px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.borderColor='rgba(231,76,60,0.3)';" onmouseout="this.style.borderColor='var(--border-glow)';">
                        <input type="radio" name="feedback_type" value="dirty_space" style="accent-color: #e74c3c; cursor: pointer;">
                        <span>🧹 Rác, mất vệ sinh</span>
                    </label>
                </div>
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">2. Chi tiết phản ánh</label>
                <textarea required name="description" rows="3" class="form-input" placeholder="Mô tả cụ thể sự việc bạn quan sát được để giúp ban quản lý nhanh chóng xác minh..." style="width: 100%; border-radius: 8px; font-size: 0.85rem; padding: 10px; resize: vertical;"></textarea>
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">3. Đính kèm ảnh thực tế (Tùy chọn)</label>
                <input type="file" name="feedback_image" accept="image/*" class="form-input" style="width: 100%; font-size: 0.8rem; padding: 6px;">
            </div>
            
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeFeedbackModal()" class="btn-secondary" style="font-size: 0.85rem; padding: 8px 16px; border-radius: 8px; font-weight: 600; cursor: pointer;">Hủy</button>
                <button type="submit" class="btn-primary" style="font-size: 0.85rem; padding: 8px 16px; border-radius: 8px; font-weight: 700; background: #e74c3c; border-color: #c0392b; cursor: pointer; color: #fff;">Gửi Báo Cáo</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal hiển thị toàn bộ đánh giá & Phân loại sao -->
<div id="allReviewsModal" style="display: none; position: fixed; inset: 0; z-index: 9999; background: rgba(0, 0, 0, 0.75); backdrop-filter: blur(8px); align-items: center; justify-content: center; opacity: 0; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);">
    <div class="lightbox-content" style="background: var(--bg-card); border: 1px solid var(--border-glow); width: 90%; max-width: 780px; height: 85vh; border-radius: 24px; box-shadow: 0 25px 60px rgba(0, 0, 0, 0.45); overflow: hidden; transform: scale(0.9); transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); display: flex; flex-direction: column; position: relative;">
        <!-- Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-glow); padding: 20px 28px; background: rgba(255, 255, 255, 0.015);">
            <div>
                <h4 style="margin: 0; font-size: 1.35rem; font-weight: 800; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                    💬 Toàn bộ Đánh giá & Phản hồi
                </h4>
                <p style="margin: 4px 0 0 0; font-size: 0.8rem; color: var(--text-muted);">
                    {{ $eatery->name }} • {{ $eatery->reviews->count() }} lượt nhận xét
                </p>
            </div>
            <button onclick="closeAllReviewsModal()" style="background: rgba(255,255,255,0.04); border: 1px solid var(--border-glow); width: 36px; height: 36px; border-radius: 50%; font-size: 1.1rem; color: var(--text-muted); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s;" onmouseover="this.style.color='#ff7e29'; this.style.borderColor='rgba(255, 126, 41, 0.3)'; this.style.background='rgba(255, 126, 41, 0.05)'" onmouseout="this.style.color='var(--text-muted)'; this.style.borderColor='var(--border-glow)'; this.style.background='rgba(255,255,255,0.04)'">✕</button>
        </div>
        
        <!-- Star Filter Tabs Segmented Control -->
        <div style="padding: 16px 28px; border-bottom: 1px solid var(--border-glow); background: rgba(255, 255, 255, 0.005); overflow-x: auto; scrollbar-width: none;">
            <div style="display: flex; gap: 8px; align-items: center;">
                <button class="review-modal-tab active" onclick="filterReviewStars('all')" data-star="all" style="white-space: nowrap; font-size: 0.85rem; font-weight: 700; padding: 10px 18px; border-radius: 30px; border: 1.5px solid rgba(255, 126, 41, 0.2); background: rgba(255, 126, 41, 0.06); color: var(--primary); cursor: pointer; transition: all 0.25s ease;">
                    🌟 Tất cả ({{ $eatery->reviews->count() }})
                </button>
                @for($s = 5; $s >= 1; $s--)
                    @php
                        $countForStar = $eatery->reviews->where('rating', $s)->count();
                    @endphp
                    <button class="review-modal-tab" onclick="filterReviewStars({{ $s }})" data-star="{{ $s }}" style="white-space: nowrap; font-size: 0.85rem; font-weight: 600; padding: 8px 16px; border-radius: 30px; border: 1.5px solid var(--border-glow); background: transparent; color: var(--text-muted); cursor: pointer; transition: all 0.25s ease;" onmouseover="if(!this.classList.contains('active')){ this.style.borderColor='rgba(255, 126, 41, 0.2)'; this.style.color='var(--text-main)'; }" onmouseout="if(!this.classList.contains('active')){ this.style.borderColor='var(--border-glow)'; this.style.color='var(--text-muted)'; }">
                        {{ $s }} ★ ({{ $countForStar }})
                    </button>
                @endfor
            </div>
        </div>

        <!-- Scrollable Reviews List Container -->
        <div id="modalReviewListScroll" style="flex: 1; overflow-y: auto; padding: 28px; display: flex; flex-direction: column; gap: 20px; background: rgba(0,0,0,0.02);">
            @foreach($eatery->reviews as $rev)
                <div class="modal-review-card-item" data-rating="{{ $rev->rating }}" style="padding: 24px; border-radius: 20px; border: 1px solid var(--border-glow); background: var(--bg-card); box-shadow: 0 4px 15px rgba(0, 0, 0, 0.01); display: flex; flex-direction: column; gap: 14px; transition: all 0.3s ease;">
                    <!-- Header -->
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
                        <div style="display: flex; align-items: center; gap: 14px;">
                            <!-- Avatar -->
                            <div style="width: 44px; height: 44px; border-radius: 50%; background: linear-gradient(135deg, var(--primary) 0%, #ff8b3d 100%); color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.05rem; box-shadow: 0 4px 12px rgba(255, 126, 41, 0.2); text-transform: uppercase;">
                                {{ substr($rev->user_name, 0, 2) }}
                            </div>
                            <!-- Name & Stars -->
                            <div>
                                <span style="font-weight: 700; color: var(--text-main); font-size: 1rem; display: block;">{{ $rev->user_name }}</span>
                                <div style="color: #ffb03a; font-size: 0.9rem; margin-top: 2px; display: flex; gap: 2px;">
                                    @for($i=1; $i<=5; $i++)
                                        @if($i <= $rev->rating)
                                            <span style="color: #ffb03a; text-shadow: 0 0 6px rgba(255, 176, 58, 0.4);">★</span>
                                        @else
                                            <span style="color: var(--border-glow);">★</span>
                                        @endif
                                    @endfor
                                </div>
                            </div>
                        </div>
                        <span style="font-size: 0.78rem; color: var(--text-muted); background: rgba(255,255,255,0.03); padding: 4px 12px; border-radius: 30px; border: 1px solid var(--border-glow);">
                            📅 {{ $rev->created_at->format('d/m/Y H:i') }}
                        </span>
                    </div>

                    <!-- Text -->
                    <p style="font-size: 0.95rem; color: var(--text-main); line-height: 1.65; margin: 0; white-space: pre-line; font-weight: 450;">{{ $rev->comment }}</p>

                    <!-- Media -->
                    @if($rev->media && $rev->media->count() > 0)
                        <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 2px;">
                            @foreach($rev->media as $mediaItem)
                                @if($mediaItem->file_type === 'image')
                                    <div style="position: relative; width: 90px; height: 90px; border-radius: 10px; overflow: hidden; border: 1px solid var(--border-glow); box-shadow: 0 4px 10px rgba(0,0,0,0.1); cursor: pointer; transition: all 0.25s ease;" onmouseover="this.style.transform='scale(1.05)'; this.style.borderColor='var(--primary)'" onmouseout="this.style.transform='none'; this.style.borderColor='var(--border-glow)'">
                                        <img src="{{ $mediaItem->file_path }}" alt="Review Media" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                @else
                                    <div style="position: relative; width: 130px; height: 90px; border-radius: 10px; overflow: hidden; border: 1px solid var(--border-glow); box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                                        <video src="{{ $mediaItem->file_path }}" style="width: 100%; height: 100%; object-fit: cover;" controls></video>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    <!-- Reply -->
                    @if($rev->seller_reply)
                        <div class="seller-reply-bubble" style="margin-top: 4px; padding: 16px 20px; background: rgba(255, 126, 41, 0.04); border: 1px solid rgba(255, 126, 41, 0.12); border-radius: 16px; font-size: 0.88rem; box-shadow: 0 4px 12px rgba(255, 126, 41, 0.01);">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; flex-wrap: wrap; gap: 8px;">
                                <strong style="color: var(--primary); display: flex; align-items: center; gap: 6px; font-size: 0.9rem; font-weight: 800;">
                                    <span style="font-size: 1.15rem;">🏪</span> Phản hồi từ chủ quán
                                </strong>
                                <span style="font-size: 0.7rem; color: var(--text-muted); font-weight: 600;">Chủ cửa hàng</span>
                            </div>
                            <p style="margin: 0; color: var(--text-main); line-height: 1.6; font-style: italic; font-weight: 500;">
                                "{{ $rev->seller_reply }}"
                            </p>
                        </div>
                    @endif
                </div>
            @endforeach

            <!-- Empty State for filtered stars -->
            <div id="modalReviewsEmptyState" style="display: none; flex-direction: column; align-items: center; justify-content: center; padding: 60px 20px; text-align: center;">
                <div style="font-size: 3.5rem; margin-bottom: 16px; filter: drop-shadow(0 0 10px rgba(255,126,41,0.25));">💬</div>
                <h5 style="margin: 0 0 8px 0; font-size: 1.15rem; color: var(--text-main); font-weight: 700;">Chưa có nhận xét nào!</h5>
                <p style="margin: 0; font-size: 0.88rem; color: var(--text-muted); max-width: 320px; line-height: 1.5;">Không tìm thấy đánh giá nào có mức xếp hạng sao này cho quán ăn.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const eateryLat = {{ $eatery->latitude }};
    const eateryLng = {{ $eatery->longitude }};
    const eateryName = "{{ $eatery->name }}";
    const categoryIcon = "{{ $eatery->category->icon }}";
    let userCurrentDistanceKm = null;

    document.addEventListener("DOMContentLoaded", function() {
        // 1. Khởi tạo mini map
        const miniMap = L.map('miniMap', {
            zoomControl: false,
            scrollWheelZoom: false
        }).setView([eateryLat, eateryLng], 15);

        // Lớp nền màu sáng mặc định (Voyager)
        let activeTileLayer = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap &copy; CARTO'
        }).addTo(miniMap);
        // Custom Marker
        const customIcon = L.divIcon({
            html: `<div style="background-color: var(--primary); width: 28px; height: 28px; border-radius: 50%; border: 2px solid white; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">${categoryIcon}</div>`,
            className: 'custom-leaflet-marker',
            iconSize: [28, 28],
            iconAnchor: [14, 14]
        });

        L.marker([eateryLat, eateryLng], { icon: customIcon })
            .bindPopup(`<strong style="font-family: var(--font-heading);">${eateryName}</strong><br>📍 ${eateryLat}, ${eateryLng}`)
            .addTo(miniMap)
            .openPopup();

        // 2. Logic Chọn Sao Đánh giá (Interactive Stars Picker)
        const stars = document.querySelectorAll("#starsSelector span");
        const ratingInput = document.getElementById("ratingInput");

        stars.forEach(star => {
            star.addEventListener("click", function() {
                const val = this.getAttribute("data-value");
                ratingInput.value = val;
                
                stars.forEach(s => {
                    const sVal = s.getAttribute("data-value");
                    if (parseInt(sVal) <= parseInt(val)) {
                        s.classList.add("selected");
                        s.textContent = "★";
                    } else {
                        s.classList.remove("selected");
                        s.textContent = "☆";
                    }
                });
            });
        });

        // 4. Heritage tabs click logic
        const tabBtns = document.querySelectorAll(".heritage-tab-btn");
        const tabContents = document.querySelectorAll(".heritage-tab-content");

        tabBtns.forEach(btn => {
            btn.addEventListener("click", function() {
                tabBtns.forEach(b => b.classList.remove("active"));
                this.classList.add("active");

                tabContents.forEach(c => c.classList.remove("active-content"));
                
                const targetId = this.getAttribute("data-tab");
                const targetContent = document.getElementById(targetId);
                if (targetContent) {
                    targetContent.classList.add("active-content");
                }
            });
        });

        // 5. AI Speech Synthesis Narrator Play Logic
        const playBtn = document.getElementById("playAudioBtn");
        const eq = document.getElementById("audioEqualizer");
        const statusText = document.getElementById("audioStatusText");
        const playBtnIcon = document.getElementById("playBtnIcon");

        if (playBtn) {
            let synth = window.speechSynthesis;
            let utterance = null;
            let isSpeaking = false;

            const audioText = {!! json_encode(isset($dossier) ? $dossier['audio_narrative'] : '') !!};

            playBtn.addEventListener("click", function() {
                if (!synth) {
                    alert("Trình duyệt của bạn không hỗ trợ công nghệ đọc giọng nói AI.");
                    return;
                }

                if (isSpeaking) {
                    synth.cancel();
                    isSpeaking = false;
                    playBtn.classList.remove("playing");
                    eq.classList.remove("playing-audio");
                    playBtnIcon.textContent = "🔊";
                    statusText.textContent = "Bấm để lắng nghe giọng đọc AI thuyết minh văn hóa món ăn";
                } else {
                    synth.cancel();
                    
                    utterance = new SpeechSynthesisUtterance(audioText);
                    utterance.lang = "vi-VN";
                    utterance.rate = 0.92; // Majestic, calm storytelling pace

                    // Set voice if available
                    const voices = synth.getVoices();
                    const viVoice = voices.find(voice => voice.lang.includes("VI") || voice.lang.includes("vi"));
                    if (viVoice) {
                        utterance.voice = viVoice;
                    }

                    utterance.onend = function() {
                        isSpeaking = false;
                        playBtn.classList.remove("playing");
                        eq.classList.remove("playing-audio");
                        playBtnIcon.textContent = "🔊";
                        statusText.textContent = "Thuyết minh hoàn thành. Bấm để nghe lại!";
                    };

                    utterance.onerror = function(event) {
                        console.error("SpeechSynthesis error:", event);
                        isSpeaking = false;
                        playBtn.classList.remove("playing");
                        eq.classList.remove("playing-audio");
                        playBtnIcon.textContent = "🔊";
                        statusText.textContent = "Đã xảy ra lỗi khi phát âm thanh thuyết minh.";
                    };

                    synth.speak(utterance);
                    isSpeaking = true;
                    playBtn.classList.add("playing");
                    eq.classList.add("playing-audio");
                    playBtnIcon.textContent = "⏸️";
                    statusText.textContent = "Đang thuyết minh về di sản ẩm thực Đông Anh... Lắng nghe văn hóa!";
                }
            });

            // Ensure voice synthesis stops if user leaves or navigates away
            window.addEventListener("beforeunload", function() {
                if (synth) {
                    synth.cancel();
                }
            });
        }

        // 6. Logic chọn Tab của phần Minh bạch thực phẩm (Trust Hub Tabs)
        const trustTabBtns = document.querySelectorAll(".trust-tab-btn");
        const trustTabContents = document.querySelectorAll(".trust-tab-content");

        trustTabBtns.forEach(btn => {
            btn.addEventListener("click", function() {
                trustTabBtns.forEach(b => b.classList.remove("active"));
                this.classList.add("active");

                trustTabContents.forEach(c => c.classList.remove("active-content"));
                
                const targetId = this.getAttribute("data-trust-tab");
                const targetContent = document.getElementById(targetId);
                if (targetContent) {
                    targetContent.classList.add("active-content");
                }
            });
        });
    });

    // Lightbox Popup logic cho ảnh giấy tờ VSATTP, hợp đồng, hóa đơn
    function openTrustLightbox(imgSrc, captionText) {
        const lightbox = document.getElementById("trustLightbox");
        const img = document.getElementById("trustLightboxImg");
        const cap = document.getElementById("trustLightboxCaption");
        
        if (lightbox && img && cap) {
            img.src = imgSrc;
            cap.textContent = captionText;
            lightbox.style.display = "flex";
            setTimeout(() => {
                lightbox.style.opacity = "1";
                lightbox.querySelector(".lightbox-content").style.transform = "scale(1)";
            }, 50);
        }
    }

    function closeTrustLightbox() {
        const lightbox = document.getElementById("trustLightbox");
        if (lightbox) {
            lightbox.style.opacity = "0";
            lightbox.querySelector(".lightbox-content").style.transform = "scale(0.9)";
            setTimeout(() => {
                lightbox.style.display = "none";
            }, 300);
        }
    }

    function scrollToTrustHub(event) {
        event.preventDefault();
        const trustSection = document.getElementById("trust-hub-section");
        if (trustSection) {
            trustSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            // Tạo hiệu ứng phát sáng nhẹ thu hút sự chú ý
            setTimeout(() => {
                trustSection.style.transition = 'all 0.5s ease-in-out';
                trustSection.style.boxShadow = '0 0 25px rgba(32, 178, 170, 0.5)';
                trustSection.style.borderColor = 'rgba(32, 178, 170, 0.8)';
                setTimeout(() => {
                    trustSection.style.boxShadow = 'none';
                    trustSection.style.borderColor = 'var(--border-glow)';
                }, 1500);
            }, 800);
        }
    }
    // 7. Logic Popup Báo Cáo Phản Ánh ATTP Dành Cho Thực Khách
    function openFeedbackModal(eateryName) {
        const modal = document.getElementById("feedbackModal");
        const nameSpan = document.getElementById("feedbackEateryName");
        const farWarning = document.getElementById("feedbackFarWarning");
        const fileInput = document.querySelector("input[name='feedback_image']");

        // Xác định xem người dùng có ở xa quán hay không (hoặc chặn GPS)
        let isFarAway = false;
        if (userCurrentDistanceKm === null || userCurrentDistanceKm === 'denied' || (typeof userCurrentDistanceKm === 'number' && userCurrentDistanceKm > 0.15)) {
            isFarAway = true;
        }

        if (farWarning) {
            if (isFarAway) {
                farWarning.style.display = "block";
                if (fileInput) fileInput.required = true; // Bắt buộc đính kèm ảnh minh chứng
            } else {
                farWarning.style.display = "none";
                if (fileInput) fileInput.required = false;
            }
        }

        if (modal && nameSpan) {
            nameSpan.textContent = eateryName;
            modal.style.display = "flex";
            setTimeout(() => {
                modal.style.opacity = "1";
                modal.querySelector(".lightbox-content").style.transform = "scale(1)";
            }, 50);
        }
    }

    function closeFeedbackModal() {
        const modal = document.getElementById("feedbackModal");
        if (modal) {
            modal.style.opacity = "0";
            modal.querySelector(".lightbox-content").style.transform = "scale(0.9)";
            setTimeout(() => {
                modal.style.display = "none";
                document.getElementById("feedbackForm").reset();
            }, 300);
        }
    }

    function submitFeedback(event) {
        event.preventDefault();
        
        const fileInput = document.querySelector("input[name='feedback_image']");
        let isFarAway = false;
        if (userCurrentDistanceKm === null || userCurrentDistanceKm === 'denied' || (typeof userCurrentDistanceKm === 'number' && userCurrentDistanceKm > 0.15)) {
            isFarAway = true;
        }

        // Kiểm tra bằng chứng bắt buộc nếu ở xa quán
        if (isFarAway && (!fileInput || !fileInput.files || fileInput.files.length === 0)) {
            showPremiumToast("Thiếu Minh Chứng!", "Do bạn đang gửi từ xa, vui lòng đính kèm ảnh hóa đơn hoặc món ăn để xác thực.", "error");
            return;
        }

        showPremiumToast("Báo Cáo Thành Công!", "Phản hồi đã được gửi ẩn danh về Ban Quản Lý Đông Anh để xem xét thực tế.", "success");
        closeFeedbackModal();
    }

    // Hàm hiển thị Toast thông báo đa dạng kiểu dáng cực kỳ cao cấp
    function showPremiumToast(title, message, type = "success") {
        const toast = document.createElement("div");
        toast.style.position = "fixed";
        toast.style.top = "25px";
        toast.style.right = "25px";
        toast.style.zIndex = "999999";
        toast.style.padding = "16px 24px";
        toast.style.borderRadius = "14px";
        toast.style.color = "#ffffff";
        toast.style.display = "flex";
        toast.style.alignItems = "center";
        toast.style.gap = "14px";
        toast.style.opacity = "0";
        toast.style.transform = "translateY(-20px) scale(0.9)";
        toast.style.transition = "all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275)";
        toast.style.border = "1px solid rgba(255,255,255,0.12)";
        toast.style.backdropFilter = "blur(10px)";
        toast.style.maxWidth = "350px";

        let icon = "🔔";
        if (type === "success") {
            toast.style.background = "linear-gradient(135deg, #e74c3c 0%, #c0392b 100%)";
            toast.style.boxShadow = "0 12px 30px rgba(231, 76, 60, 0.4)";
            icon = "🚨";
        } else if (type === "warning") {
            toast.style.background = "linear-gradient(135deg, #ff9f43 0%, #ff9f43 100%)";
            toast.style.boxShadow = "0 12px 30px rgba(255, 159, 67, 0.4)";
            icon = "⚠️";
        } else {
            toast.style.background = "linear-gradient(135deg, #ee5253 0%, #ff2222 100%)";
            toast.style.boxShadow = "0 12px 30px rgba(238, 82, 83, 0.4)";
            icon = "❌";
        }

        toast.innerHTML = `
            <span style="font-size: 1.6rem; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.15));">${icon}</span>
            <div>
                <strong style="display: block; font-size: 0.92rem; margin-bottom: 2px; font-weight: 800;">${title}</strong>
                <span style="font-size: 0.8rem; opacity: 0.95; line-height: 1.4; display: block;">${message}</span>
            </div>
        `;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = "1";
            toast.style.transform = "translateY(0) scale(1)";
        }, 100);

        setTimeout(() => {
            toast.style.opacity = "0";
            toast.style.transform = "translateY(-20px) scale(0.9)";
            setTimeout(() => {
                toast.remove();
            }, 400);
        }, 5000);
    }

    // 3. Tích hợp định vị trình duyệt tính khoảng cách Km (Browser Geolocation API)
    function getUserDistance() {
        if (!navigator.geolocation) {
            document.getElementById("distanceKm").textContent = "Không hỗ trợ GPS";
            userCurrentDistanceKm = 'denied';
            return;
        }

        navigator.geolocation.getCurrentPosition(function(position) {
            const userLat = position.coords.latitude;
            const userLng = position.coords.longitude;
            
            // Công thức Haversine tính khoảng cách đường thẳng giữa 2 tọa độ GPS
            const R = 6371; // Bán kính Trái Đất (km)
            const dLat = (eateryLat - userLat) * Math.PI / 180;
            const dLng = (eateryLng - userLng) * Math.PI / 180;
            const a = 
                Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(userLat * Math.PI / 180) * Math.cos(eateryLat * Math.PI / 180) * 
                Math.sin(dLng/2) * Math.sin(dLng/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            const distance = R * c; // Khoảng cách (km)

            userCurrentDistanceKm = distance;
            document.getElementById("distanceKm").textContent = distance.toFixed(2) + " km";
        }, function(error) {
            document.getElementById("distanceKm").textContent = "Bị từ chối GPS";
            userCurrentDistanceKm = 'denied';
        });
    }

    // 8. Slider Thực đơn & Popup Modal Xem toàn bộ thực đơn
    window.scrollMenuSlider = function(direction) {
        const slider = document.getElementById('menuSliderWrapper');
        if (slider) {
            const card = slider.querySelector('.dish-card');
            if (card) {
                const cardWidth = card.offsetWidth + 20; // card + gap
                slider.scrollBy({
                    left: direction * cardWidth * 1.5,
                    behavior: 'smooth'
                });
            }
        }
    };

    window.openFullMenuModal = function() {
        const modal = document.getElementById('fullMenuModal');
        if (modal) {
            modal.style.display = 'flex';
            setTimeout(() => {
                modal.style.opacity = '1';
                modal.querySelector('.lightbox-content').style.transform = 'scale(1)';
            }, 10);
        }
    };

    window.closeFullMenuModal = function() {
        const modal = document.getElementById('fullMenuModal');
        if (modal) {
            modal.style.opacity = '0';
            modal.querySelector('.lightbox-content').style.transform = 'scale(0.9)';
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        }
    };

    let currentFilterTab = 'all';

    window.filterModalTab = function(tab) {
        currentFilterTab = tab;
        
        // Update active class on tab buttons
        document.querySelectorAll('.modal-tab-btn').forEach(btn => {
            if (btn.getAttribute('data-tab') === tab) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
        
        filterModalDishes();
    };

    window.filterModalDishes = function() {
        const query = document.getElementById('dishSearchInput').value.toLowerCase().trim();
        const cards = document.querySelectorAll('#modalMenuGrid .dish-card');
        let visibleCount = 0;
        
        cards.forEach(card => {
            const name = card.getAttribute('data-name');
            const desc = card.getAttribute('data-desc');
            const isSignature = card.getAttribute('data-signature') === 'true';
            const price = parseFloat(card.getAttribute('data-price') || '0');
            
            let matchesTab = false;
            if (currentFilterTab === 'all') {
                matchesTab = true;
            } else if (currentFilterTab === 'signature') {
                matchesTab = isSignature;
            } else if (currentFilterTab === 'best-price') {
                matchesTab = price <= 200000;
            }
            
            const matchesQuery = name.includes(query) || desc.includes(query);
            
            if (matchesTab && matchesQuery) {
                card.style.display = 'flex';
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 10);
                visibleCount++;
            } else {
                card.style.opacity = '0';
                card.style.transform = 'translateY(10px)';
                setTimeout(() => {
                    if (card.style.opacity === '0') {
                        card.style.display = 'none';
                    }
                }, 250);
            }
        });
        
        const countSpan = document.getElementById('modalVisibleCount');
        if (countSpan) {
            countSpan.textContent = visibleCount + ' món';
        }
    };

    // 9. Điều khiển Modal Xem Toàn Bộ Đánh giá & Phân loại sao
    window.openAllReviewsModal = function() {
        const modal = document.getElementById('allReviewsModal');
        if (modal) {
            modal.style.display = 'flex';
            setTimeout(() => {
                modal.style.opacity = '1';
                modal.querySelector('.lightbox-content').style.transform = 'scale(1)';
            }, 10);
            document.body.style.overflow = 'hidden'; // Khóa cuộn trang nền
        }
    };

    window.closeAllReviewsModal = function() {
        const modal = document.getElementById('allReviewsModal');
        if (modal) {
            modal.style.opacity = '0';
            modal.querySelector('.lightbox-content').style.transform = 'scale(0.9)';
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
            document.body.style.overflow = ''; // Khôi phục cuộn trang nền
        }
    };

    window.filterReviewStars = function(star) {
        // Cập nhật trạng thái Tab hoạt động
        document.querySelectorAll('.review-modal-tab').forEach(tab => {
            const tabStar = tab.getAttribute('data-star');
            if (tabStar === star.toString()) {
                tab.classList.add('active');
                tab.style.border = '1.5px solid rgba(255, 126, 41, 0.2)';
                tab.style.background = 'rgba(255, 126, 41, 0.06)';
                tab.style.color = 'var(--primary)';
                tab.style.fontWeight = '700';
            } else {
                tab.classList.remove('active');
                tab.style.border = '1.5px solid var(--border-glow)';
                tab.style.background = 'transparent';
                tab.style.color = 'var(--text-muted)';
                tab.style.fontWeight = '600';
            }
        });

        // Lọc danh sách thẻ đánh giá trong Modal
        const cards = document.querySelectorAll('#modalReviewListScroll .modal-review-card-item');
        let matchedCount = 0;

        cards.forEach(card => {
            const cardRating = card.getAttribute('data-rating');
            
            if (star === 'all' || cardRating === star.toString()) {
                card.style.display = 'flex';
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 10);
                matchedCount++;
            } else {
                card.style.opacity = '0';
                card.style.transform = 'translateY(10px)';
                card.style.display = 'none';
            }
        });

        // Hiển thị giao diện Trạng thái Rỗng nếu không có nhận xét phù hợp
        const emptyState = document.getElementById('modalReviewsEmptyState');
        if (matchedCount === 0) {
            emptyState.style.display = 'flex';
        } else {
            emptyState.style.display = 'none';
        }
    };

    // Tự động gọi tính khoảng cách ngay khi trang vừa tải xong
    window.addEventListener('load', getUserDistance);
</script>
@endsection
