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
        <span class="tag-badge-accent" style="margin-bottom: 12px; display: inline-block; font-size: 0.85rem; color: var(--text-main); background: var(--bg-card); border-color: var(--border-glow); backdrop-filter: blur(8px);">
            {{ $eatery->category->icon }} {{ $eatery->category->name }}
        </span>
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
            
            <!-- Thực đơn món ăn -->
            <div class="detail-section glass-panel" style="padding: 28px;">
                <h2 class="section-title"><span>📖</span> Thực đơn & Món ăn đặc trưng</h2>
                
                @if($eatery->dishes->count() > 0)
                    <div class="menu-grid">
                        @foreach($eatery->dishes as $dish)
                            <div class="dish-card glass-panel" style="background: rgba(255,255,255,0.02);">
                                <img src="{{ $dish->image_path ?: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=150&q=80' }}" class="dish-img" alt="{{ $dish->name }}">
                                <div class="dish-info">
                                    <div>
                                        @if($dish->is_signature)
                                            <span class="tag-badge" style="padding: 1px 6px; font-size: 0.65rem; font-weight: 700; margin-bottom: 4px; display: inline-block;">★ Món đặc trưng</span>
                                        @endif
                                        <h3 class="dish-name">{{ $dish->name }}</h3>
                                        <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 4px;">{{ $dish->description }}</p>
                                    </div>
                                    <span class="dish-price">{{ number_format($dish->price, 0, ',', '.') }}đ</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p style="color: var(--text-muted); font-style: italic; text-align: center;">Chưa cập nhật thực đơn chi tiết cho địa điểm này.</p>
                @endif
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
                    <div class="review-list">
                        @foreach($eatery->reviews as $rev)
                            <div class="review-item">
                                <div class="review-header">
                                    <div>
                                        <span class="review-user">{{ $rev->user_name }}</span>
                                        <div style="color: #ffc107; font-size: 0.8rem; margin-top: 2px;">
                                            @for($i=1; $i<=5; $i++)
                                                {{ $i <= $rev->rating ? '★' : '☆' }}
                                            @endfor
                                        </div>
                                    </div>
                                    <span style="font-size: 0.75rem; color: var(--text-muted);">
                                        {{ $rev->created_at->format('d/m/Y H:i') }}
                                    </span>
                                </div>
                                <p style="font-size: 0.9rem; color: var(--text-main); line-height: 1.6;">{{ $rev->comment }}</p>
                                
                                @if($rev->media && $rev->media->count() > 0)
                                    <div style="display: flex; gap: 8px; margin-top: 10px; flex-wrap: wrap;">
                                        @foreach($rev->media as $mediaItem)
                                            @if($mediaItem->file_type === 'image')
                                                <img src="{{ $mediaItem->file_path }}" alt="Review Image" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border-glow);">
                                            @else
                                                <video src="{{ $mediaItem->file_path }}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border-glow);" controls></video>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p style="color: var(--text-muted); font-style: italic; text-align: center; padding: 20px 0;">Chưa có đánh giá nào. Hãy là người đầu tiên chia sẻ cảm nhận về địa điểm này!</p>
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
@endsection

@section('scripts')
<script>
    const eateryLat = {{ $eatery->latitude }};
    const eateryLng = {{ $eatery->longitude }};
    const eateryName = "{{ $eatery->name }}";
    const categoryIcon = "{{ $eatery->category->icon }}";

    document.addEventListener("DOMContentLoaded", function() {
        // 1. Khởi tạo mini map
        const miniMap = L.map('miniMap', {
            zoomControl: false,
            scrollWheelZoom: false
        }).setView([eateryLat, eateryLng], 15);

        // Lớp nền phù hợp chế độ Sáng/Tối
        let currentTheme = localStorage.getItem('theme') || 'dark';
        let tileUrl = currentTheme === 'light' 
            ? 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png'
            : 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png';
            
        let activeTileLayer = L.tileLayer(tileUrl, {
            attribution: '&copy; OpenStreetMap &copy; CARTO'
        }).addTo(miniMap);

        // Lắng nghe sự kiện đổi chế độ Sáng/Tối để đổi lớp nền bản đồ tức thì
        document.addEventListener('theme-changed', function(e) {
            const nextTheme = e.detail.theme;
            const nextTileUrl = nextTheme === 'light'
                ? 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png'
                : 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png';
            
            miniMap.removeLayer(activeTileLayer);
            activeTileLayer = L.tileLayer(nextTileUrl, {
                attribution: '&copy; OpenStreetMap &copy; CARTO'
            }).addTo(miniMap);
        });

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

            const audioText = `{!! isset($dossier) ? addslashes($dossier['audio_narrative']) : '' !!}`;

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
    });

    // 3. Tích hợp định vị trình duyệt tính khoảng cách Km (Browser Geolocation API)
    function getUserDistance() {
        if (!navigator.geolocation) {
            document.getElementById("distanceKm").textContent = "Không hỗ trợ GPS";
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

            document.getElementById("distanceKm").textContent = distance.toFixed(2) + " km";
        }, function(error) {
            document.getElementById("distanceKm").textContent = "Bị từ chối GPS";
        });
    }

    // Tự động gọi tính khoảng cách ngay khi trang vừa tải xong
    window.addEventListener('load', getUserDistance);
</script>
@endsection
