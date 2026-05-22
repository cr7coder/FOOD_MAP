@extends('layouts.food-tour')

@section('title', 'Hành trình Ẩm thực Đông Anh - Khám phá Cinematic Du lịch Số')

@section('content')
<div style="background: var(--bg-base); min-height: calc(100vh - 70px); padding: 50px 0;">
    <div class="container animate-fade-in">
        
        <!-- Immersive Page Header -->
        <div style="text-align: center; margin-bottom: 36px; max-width: 800px; margin-left: auto; margin-right: auto;">
            <h1 style="font-size: 2.8rem; font-weight: 900; color: var(--text-main); margin-bottom: 16px; line-height: 1.2;">
                Hành trình Trải nghiệm<br>
                <span style="background: var(--primary-grad, var(--primary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Ẩm thực Đông Anh</span>
            </h1>
            <p style="font-size: 1rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 28px;">
                Không chỉ là ăn uống, đây là hành trình văn hóa, lịch sử và khám phá thực tế được thiết kế tinh tế. Chọn lộ trình phù hợp với tâm trạng hoặc sử dụng trí tuệ nhân tạo AI để tự thiết kế riêng cho bạn!
            </p>

            <!-- PRIMARY AI CTA BUTTON -->
            <button id="aiCTABtn" onclick="toggleAIPlanner()" style="display: inline-flex; align-items: center; gap: 12px; background: linear-gradient(135deg, #ff7e29 0%, #ff5200 100%); color: #fff; border: none; padding: 18px 36px; border-radius: 50px; font-size: 1.05rem; font-weight: 900; cursor: pointer; box-shadow: 0 8px 32px rgba(255,126,41,0.45), 0 0 0 0 rgba(255,126,41,0.4); letter-spacing: 0.3px; transition: all 0.3s ease; animation: ctaPulse 2.5s ease-in-out infinite;">
                <span style="font-size: 1.4rem;">🪄</span>
                Tự thiết kế Lộ trình bằng AI ngay!
                <span id="aiCTAArrow" style="font-size: 0.9rem; transition: transform 0.3s;">&#9660;</span>
            </button>

        </div>

        <!-- COLLAPSIBLE AI PLANNER PANEL -->
        <div id="aiPlannerPanel" style="max-height: 0; overflow: hidden; transition: max-height 0.55s cubic-bezier(0.4,0,0.2,1); margin-bottom: 0;">
            <div class="glass-card ai-planner-card" style="border-radius: 24px; padding: 40px; margin-bottom: 40px; position: relative; overflow: hidden; border: 1.5px solid rgba(255,126,41,0.3);">
                <div style="position: absolute; right: -30px; top: -30px; font-size: 9rem; opacity: 0.05; pointer-events: none; transform: rotate(-15deg);">🪄</div>
                <div style="position: absolute; left: -20px; bottom: -20px; font-size: 7rem; opacity: 0.04; pointer-events: none; transform: rotate(20deg);">&#10024;</div>

                <form id="aiPlannerForm" style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px; max-width: 640px; margin: 0 auto;">
                    <div class="ai-field-group">
                        <label class="ai-field-label">💰 Ngân sách dự chi</label>
                        <select id="aiBudget" class="ai-select">
                            <option value="100000">Dưới 100k — Siêu tiết kiệm</option>
                            <option value="200000">100k – 200k — Bình dân</option>
                            <option value="350000" selected>200k – 350k — Phổ thông</option>
                            <option value="600000">350k – 600k — Thoải mái</option>
                            <option value="1000000">Trên 600k — Sang chảnh</option>
                        </select>
                    </div>
                    <div class="ai-field-group">
                        <label class="ai-field-label">🎭 Tâm trạng hôm nay</label>
                        <select id="aiMood" class="ai-select">
                            <option value="chill" selected>☕ Chill nhẹ nhàng</option>
                            <option value="night">🌙 Ăn đêm sôi động</option>
                            <option value="cheap">💰 Ngon rẻ vỉa hè</option>
                            <option value="specialty">🌾 Khám phá đặc sản</option>
                            <option value="romantic">💕 Hẹn hò lãng mạn</option>
                            <option value="family">👨‍👩‍👧 Gia đình sum vầy</option>
                        </select>
                    </div>
                    <div class="ai-field-group">
                        <label class="ai-field-label">👥 Bạn đi cùng ai?</label>
                        <select id="aiGroupSize" class="ai-select">
                            <option value="solo">🧍 Một mình (Solo trip)</option>
                            <option value="couple" selected>👫 2 người (Cặp đôi)</option>
                            <option value="small_group">👥 3 – 4 người (Nhóm nhỏ)</option>
                            <option value="large_group">🎉 5+ người (Nhóm lớn)</option>
                        </select>
                    </div>
                    <div class="ai-field-group">
                        <label class="ai-field-label">⏰ Thời điểm khởi hành</label>
                        <select id="aiTimeOfDay" class="ai-select">
                            <option value="morning">🌅 Buổi sáng (7h – 11h)</option>
                            <option value="noon">☀️ Buổi trưa (11h – 14h)</option>
                            <option value="afternoon" selected>🌤️ Buổi chiều (14h – 18h)</option>
                            <option value="evening">🌆 Tối sớm (18h – 21h)</option>
                            <option value="late_night">🌙 Đêm khuya (21h+)</option>
                        </select>
                    </div>
                    <div style="grid-column: span 2; margin-top: 4px;">
                        <button type="submit" id="aiSubmitBtn" class="btn-primary" style="width: 100%; padding: 16px; border-radius: 16px; font-weight: 800; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 12px; font-size: 1rem; box-shadow: var(--shadow-glow); transition: all 0.3s ease;">
                            <span style="font-size: 1.2rem;">🪄</span>
                            <span>Thiết kế Lộ trình AI ngay cho tôi!</span>
                        </button>
                    </div>
                </form>

                <div id="aiLoader" style="display: none; margin-top: 30px; text-align: center;">
                    <div style="display: inline-flex; flex-direction: column; align-items: center; gap: 16px;">
                        <div style="width: 52px; height: 52px; border: 4px solid rgba(255, 126, 41, 0.15); border-top-color: var(--primary); border-radius: 50%; animation: spin 0.9s linear infinite;"></div>
                        <div>
                            <p style="font-size: 0.9rem; color: var(--primary); font-weight: 800; margin: 0;">Gemini AI đang sáng tác lộ trình riêng cho bạn...</p>
                            <p style="font-size: 0.78rem; color: var(--text-muted); margin: 4px 0 0;">Phân tích dữ liệu · Tối ưu tuyến đường · Viết câu chuyện ẩm thực</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div style="display: grid; grid-template-columns: 1fr; gap: 40px;">
            
            <!-- SECTION 1: Mood Filters and Pre-designed Tours -->
            <div>
                <!-- Mood Selector Pills -->
                <div class="mood-selector-wrapper">
                    <button class="mood-pill {{ !$mood ? 'active' : '' }}" onclick="filterMood('', this)">
                        <span>🌐</span> Tất cả Lộ trình
                    </button>
                    <button class="mood-pill {{ $mood === 'specialty' ? 'active' : '' }}" onclick="filterMood('specialty', this)">
                        <span>🌾</span> Đặc sản xứ Loa
                    </button>
                    <button class="mood-pill {{ $mood === 'chill' ? 'active' : '' }}" onclick="filterMood('chill', this)">
                        <span>☕</span> Chill cuối tuần
                    </button>
                    <button class="mood-pill {{ $mood === 'night' ? 'active' : '' }}" onclick="filterMood('night', this)">
                        <span>🌙</span> Ăn đêm Cao Lỗ
                    </button>
                    <button class="mood-pill {{ $mood === 'cheap' ? 'active' : '' }}" onclick="filterMood('cheap', this)">
                        <span>💰</span> Sinh viên giá rẻ
                    </button>
                </div>

                <!-- Tours Listing Grid -->
                <div id="no-tours-message" class="glass-card" style="display: none; text-align: center; padding: 60px 20px; border-radius: 20px;">
                    <span style="font-size: 3rem;">🔍</span>
                    <h3 style="margin-top: 16px; font-weight: 700; color: var(--text-main);">Chưa tìm thấy lộ trình phù hợp</h3>
                    <p style="color: var(--text-muted); margin-top: 8px;">Bạn hãy thử chuyển đổi bộ lọc tâm trạng khác hoặc tự tạo tour bằng AI phía bên dưới!</p>
                </div>

                <div class="tours-grid" id="main-tours-grid">
                    @foreach($tours as $tour)
                        <div class="tour-card animate-fade-in" data-mood="{{ $tour->mood }}">
                                <div class="tour-popularity-badge">
                                    <span>⭐</span> {{ $tour->popularity }}
                                </div>
                                <div class="tour-thumbnail-wrapper">
                                    <img src="{{ $tour->thumbnail ?: 'https://images.unsplash.com/photo-1591814468924-caf88d1232e1?auto=format&fit=crop&w=800&q=80' }}" class="tour-thumbnail" alt="{{ $tour->name }}">
                                    <div class="tour-card-overlay">
                                        <span class="tour-difficulty-tag">{{ $tour->difficulty }}</span>
                                    </div>
                                </div>
                                <div class="tour-card-body">
                                    <h3 class="tour-card-title">{{ $tour->name }}</h3>
                                    <p class="tour-card-desc">{{ $tour->description }}</p>
                                    
                                    <div class="tour-meta-grid">
                                        <div class="tour-meta-item">
                                            <span class="tour-meta-label">⏱️ Thời gian</span>
                                            <span class="tour-meta-value">{{ $tour->duration }}</span>
                                        </div>
                                        <div class="tour-meta-item">
                                            <span class="tour-meta-label">📏 Khoảng cách</span>
                                            <span class="tour-meta-value">{{ $tour->distance }}</span>
                                        </div>
                                        <div class="tour-meta-item">
                                            <span class="tour-meta-label">🕒 Thời điểm</span>
                                            <span class="tour-meta-value">{{ $tour->best_time }}</span>
                                        </div>
                                    </div>

                                    <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 20px; display: flex; align-items: center; gap: 8px; justify-content: center; background: rgba(255,255,255,0.02); padding: 8px; border-radius: 10px;">
                                        <span>💰</span> Dự chi: <strong style="color: var(--primary);">{{ $tour->budget }}</strong>
                                    </div>
                                    
                                    @if($tour->diaries_count > 0)
                                    <div style="display: flex; gap: 8px;">
                                        <a href="/food-tour/{{ $tour->slug }}" class="btn-primary" style="flex: 1.5; text-align: center; text-decoration: none; padding: 12px; border-radius: 12px; font-weight: 700; display: block; box-shadow: var(--shadow-glow); display: flex; align-items: center; justify-content: center;">
                                            🚀 Trải nghiệm
                                        </a>
                                        <button type="button" onclick="openDiariesModal('{{ $tour->id }}')" class="btn-secondary" style="flex: 1; text-align: center; padding: 12px; border-radius: 12px; font-weight: 700; background: rgba(255,255,255,0.05); color: var(--text-main); border: 1px solid var(--border-glow); display: flex; align-items: center; justify-content: center; gap: 4px; font-size: 0.85rem; cursor: pointer;">
                                            📖 Nhật ký ({{ $tour->diaries_count }})
                                        </button>
                                    </div>
                                    @else
                                    <a href="/food-tour/{{ $tour->slug }}" class="btn-primary" style="text-align: center; text-decoration: none; width: 100%; padding: 12px; border-radius: 12px; font-weight: 700; display: block; box-shadow: var(--shadow-glow);">
                                        🚀 Trải nghiệm hành trình
                                    </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
            </div>

            <!-- SECTION 2: Community AI Tours (Lộ trình AI từ cộng đồng) -->
            @if($communityTours->isNotEmpty())
            <div style="margin-top: 40px;">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
                    <div style="flex: 1; height: 1px; background: linear-gradient(to right, rgba(255,126,41,0.4), transparent);"></div>
                    <span style="background: rgba(255,126,41,0.15); color: var(--primary); padding: 6px 16px; border-radius: 30px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; border: 1px solid rgba(255,126,41,0.25); white-space: nowrap;">
                        🔥 Lộ trình AI từ cộng đồng
                    </span>
                    <div style="flex: 1; height: 1px; background: linear-gradient(to left, rgba(255,126,41,0.4), transparent);"></div>
                </div>

                <div class="tours-grid">
                    @foreach($communityTours as $cTour)
                    @php
                        $hoursLeft = now()->diffInHours($cTour->expires_at, false);
                        $isUrgent = $hoursLeft <= 12;
                    @endphp
                    <div class="tour-card animate-fade-in" data-mood="{{ $cTour->mood }}" style="position: relative; border: 1.5px solid rgba(255,126,41,0.2);">
                        <!-- Community badge + Countdown -->
                        <div style="position: absolute; top: 10px; left: 10px; z-index: 10; display: flex; flex-direction: column; gap: 4px;">
                            <span style="background: rgba(255,126,41,0.9); color: #fff; font-size: 0.62rem; font-weight: 800; padding: 3px 8px; border-radius: 20px; letter-spacing: 0.5px; backdrop-filter: blur(4px);">
                                🤖 AI Cộng đồng
                            </span>
                            <span style="background: {{ $isUrgent ? 'rgba(239,68,68,0.9)' : 'rgba(15,23,42,0.75)' }}; color: #fff; font-size: 0.6rem; font-weight: 700; padding: 2px 8px; border-radius: 20px; backdrop-filter: blur(4px);">
                                ⏳ Còn {{ $hoursLeft }}h
                            </span>
                        </div>

                        <div class="tour-thumbnail-wrapper">
                            <img src="{{ $cTour->thumbnail ?: 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=800&q=80' }}" class="tour-thumbnail" alt="{{ $cTour->name }}">
                            <div class="tour-card-overlay">
                                <span class="tour-difficulty-tag">{{ $cTour->difficulty }}</span>
                            </div>
                        </div>
                        <div class="tour-card-body">
                            <h3 class="tour-card-title">{{ $cTour->name }}</h3>
                            <p class="tour-card-desc">{{ $cTour->description }}</p>

                            <div class="tour-meta-grid">
                                <div class="tour-meta-item">
                                    <span class="tour-meta-label">⏱️ Thời gian</span>
                                    <span class="tour-meta-value">{{ $cTour->duration }}</span>
                                </div>
                                <div class="tour-meta-item">
                                    <span class="tour-meta-label">📍 Điểm dừng</span>
                                    <span class="tour-meta-value">{{ $cTour->stops->count() }} địa điểm</span>
                                </div>
                                <div class="tour-meta-item">
                                    <span class="tour-meta-label">💰 Ngân sách</span>
                                    <span class="tour-meta-value">{{ $cTour->budget }}</span>
                                </div>
                            </div>

                            @if($cTour->diaries_count > 0)
                            <div style="display: flex; gap: 8px;">
                                <a href="/food-tour/{{ $cTour->slug }}" class="btn-primary" style="flex: 1.5; text-align: center; text-decoration: none; padding: 11px; border-radius: 12px; font-weight: 700; display: block; box-shadow: var(--shadow-glow); font-size: 0.88rem; display: flex; align-items: center; justify-content: center;">
                                    🚀 Thử lộ trình này
                                </a>
                                <button type="button" onclick="openDiariesModal('{{ $cTour->id }}')" class="btn-secondary" style="flex: 1; text-align: center; padding: 11px; border-radius: 12px; font-weight: 700; background: rgba(255,255,255,0.05); color: var(--text-main); border: 1px solid var(--border-glow); display: flex; align-items: center; justify-content: center; gap: 4px; font-size: 0.8rem; cursor: pointer;">
                                    📖 Nhật ký ({{ $cTour->diaries_count }})
                                </button>
                            </div>
                            @else
                            <a href="/food-tour/{{ $cTour->slug }}" class="btn-primary" style="text-align: center; text-decoration: none; width: 100%; padding: 11px; border-radius: 12px; font-weight: 700; display: block; box-shadow: var(--shadow-glow); font-size: 0.88rem;">
                                🚀 Thử lộ trình AI này!
                            </a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>

    <!-- 📖 Diaries Modals for all tours -->
    @foreach($tours->merge($communityTours) as $tourData)
        @if($tourData->diaries_count > 0)
        <div id="communityDiariesModal-{{ $tourData->id }}" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); z-index: 10005; align-items: center; justify-content: center; animation: fadeIn 0.3s ease;">
            <div style="width: 90%; max-width: 650px; max-height: 85vh; display: flex; flex-direction: column; padding: 30px; border-radius: 24px; background: rgba(26, 26, 38, 0.85); backdrop-filter: blur(25px); -webkit-backdrop-filter: blur(25px); border: 1.5px solid rgba(255, 255, 255, 0.1); box-shadow: 0 25px 60px rgba(0, 0, 0, 0.4); color: #ffffff; position: relative;">
                
                <button onclick="closeDiariesModal('{{ $tourData->id }}')" style="position: absolute; top: 20px; right: 20px; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.1); width: 36px; height: 36px; border-radius: 50%; font-size: 1.1rem; display: flex; align-items: center; justify-content: center; color: rgba(255,255,255,0.8); cursor: pointer; transition: all 0.2s; z-index: 10;" onmouseover="this.style.background='rgba(255,255,255,0.15)'; this.style.color='#fff';" onmouseout="this.style.background='rgba(255,255,255,0.08)'; this.style.color='rgba(255,255,255,0.8)';">✕</button>
                
                <h3 style="font-weight: 800; color: #ffffff; font-size: 1.4rem; margin: 0 0 20px 0; display: flex; align-items: center; gap: 12px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 16px;">
                    📖 Nhật ký Cộng đồng
                    <span style="font-size: 0.8rem; background: rgba(255,126,41,0.15); border: 1px solid rgba(255,126,41,0.3); color: #ffb03a; padding: 4px 12px; border-radius: 20px; font-weight: 800;">{{ $tourData->diaries_count }} đánh giá</span>
                </h3>
                
                <div style="flex: 1; overflow-y: auto; padding-right: 12px; display: flex; flex-direction: column; gap: 20px;">
                    @foreach($tourData->diaries as $diary)
                        <div style="padding: 20px; border-radius: 16px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); transition: transform 0.2s;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #ff7e29 0%, #ff5e00 100%); color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 800; text-transform: uppercase; box-shadow: 0 2px 8px rgba(255, 126, 41, 0.4);">
                                        {{ substr($diary->user ? $diary->user->name : 'TK', 0, 2) }}
                                    </div>
                                    <div>
                                        <strong style="font-size: 0.85rem; color: #ffffff; display: block;">
                                            {{ $diary->user ? $diary->user->name : 'Thực khách Food Tour' }}
                                        </strong>
                                        <span style="font-size: 0.65rem; color: rgba(255,255,255,0.5); display: block; margin-top: 1px;">
                                            📅 {{ $diary->created_at->format('d/m/Y H:i') }}
                                        </span>
                                    </div>
                                </div>
                                @if($diary->rating)
                                <div style="color: #ffb03a; font-size: 0.7rem; font-weight: 700; display: flex; align-items: center; gap: 3px; background: rgba(255,176,58,0.15); padding: 4px 8px; border-radius: 8px; border: 1px solid rgba(255,176,58,0.2);">
                                    <span>⭐</span><strong>{{ $diary->rating }}</strong>
                                </div>
                                @else
                                <div style="color: #10b981; font-size: 0.7rem; font-weight: 700; display: flex; align-items: center; gap: 3px; background: rgba(16, 185, 129, 0.15); padding: 4px 8px; border-radius: 8px; border: 1px solid rgba(16,185,129,0.2);">
                                    <span>✅</span><strong>Hoàn thành</strong>
                                </div>
                                @endif
                            </div>
                            
                            @if($diary->comment)
                            <p style="margin: 0 0 12px 0; font-size: 0.85rem; color: rgba(255,255,255,0.8); font-style: italic; line-height: 1.5;">
                                "{{ $diary->comment }}"
                            </p>
                            @endif
                            
                            @if($diary->image_path)
                                <div style="position: relative; height: 220px; border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.08); margin-bottom: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.25);">
                                    <img src="{{ $diary->image_path }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    <span style="position: absolute; bottom: 12px; right: 12px; background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); font-size: 0.7rem; color: #ffffff; padding: 6px 12px; border-radius: 20px; font-weight: 800; text-transform: uppercase;">📸 Kỷ niệm Selfie</span>
                                </div>
                            @endif

                            @if(!empty($diary->stop_reviews))
                                <div style="margin-top: 16px; border-top: 1px dashed rgba(255,255,255,0.15); padding-top: 16px;">
                                    <span style="font-size: 0.7rem; color: rgba(255,255,255,0.6); display: block; margin-bottom: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">📍 Check-in tại các chặng dừng:</span>
                                    <div style="display: flex; flex-direction: column; gap: 10px;">
                                        @foreach($diary->stop_reviews as $stopIdx => $stopRev)
                                            @php
                                                $stopEatery = $tourData->stops[$stopIdx]->eatery ?? null;
                                            @endphp
                                            @if($stopEatery)
                                                <div style="display: flex; gap: 12px; background: rgba(255,255,255,0.02); border-radius: 12px; padding: 12px; border: 1px solid rgba(255,255,255,0.05); align-items: flex-start;">
                                                    <div style="flex: 1;">
                                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                                            <span style="font-size: 0.8rem; font-weight: 800; color: #ffffff; display: flex; align-items: center; gap: 6px;">
                                                                <span style="font-size: 1rem;">{{ $stopEatery->category->icon ?: '🍜' }}</span>
                                                                {{ $stopEatery->name }}
                                                            </span>
                                                            <span style="color: #ffb03a; font-size: 0.7rem; font-weight: 700; background: rgba(255,176,58,0.1); padding: 2px 6px; border-radius: 6px; display: flex; align-items: center; gap: 2px;">
                                                                @if(!empty($stopRev['rating']))
                                                                    ⭐ {{ $stopRev['rating'] }}
                                                                @else
                                                                    ✅ Đã đến
                                                                @endif
                                                            </span>
                                                        </div>
                                                        @if(!empty($stopRev['comment']))
                                                        <p style="margin: 0; font-size: 0.8rem; color: rgba(255,255,255,0.7); font-style: italic; line-height: 1.5;">
                                                            "{{ $stopRev['comment'] }}"
                                                        </p>
                                                        @endif
                                                    </div>
                                                    
                                                    @if(!empty($stopRev['image_path']))
                                                        <div style="width: 70px; height: 70px; border-radius: 8px; overflow: hidden; border: 1px solid rgba(255,255,255,0.1); flex-shrink: 0;">
                                                            <img src="{{ $stopRev['image_path'] }}" style="width: 100%; height: 100%; object-fit: cover;">
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    @endforeach
</div>
@endsection

@section('scripts')
<script>
    function openDiariesModal(tourId) {
        const modal = document.getElementById('communityDiariesModal-' + tourId);
        if (modal) modal.style.display = 'flex';
    }

    function closeDiariesModal(tourId) {
        const modal = document.getElementById('communityDiariesModal-' + tourId);
        if (modal) modal.style.display = 'none';
    }

    function filterMood(mood, btnElement) {
        if (btnElement) {
            document.querySelectorAll('.mood-pill').forEach(btn => btn.classList.remove('active'));
            btnElement.classList.add('active');
        }

        let visibleCount = 0;
        document.querySelectorAll('#main-tours-grid .tour-card, .tours-grid .tour-card[data-mood]').forEach(card => {
            if (!mood || card.dataset.mood === mood) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        const noToursMsg = document.getElementById('no-tours-message');
        if (visibleCount === 0) {
            if (noToursMsg) noToursMsg.style.display = 'block';
        } else {
            if (noToursMsg) noToursMsg.style.display = 'none';
        }
        
        // Cập nhật URL mà không reload trang để có thể share link
        const newUrl = mood ? '/food-tours?mood=' + mood : '/food-tours';
        window.history.pushState({path: newUrl}, '', newUrl);
    }
    
    document.addEventListener("DOMContentLoaded", function() {
        const urlParams = new URLSearchParams(window.location.search);
        const initialMood = urlParams.get('mood');
        if(initialMood) {
            const btn = document.querySelector(`.mood-pill[onclick*="filterMood('${initialMood}'"]`);
            if(btn) {
                filterMood(initialMood, btn);
            } else {
                filterMood(initialMood, null);
            }
        }
    });

    let aiPlannerOpen = false;
    function toggleAIPlanner() {
        const panel = document.getElementById('aiPlannerPanel');
        const arrow = document.getElementById('aiCTAArrow');
        const btn = document.getElementById('aiCTABtn');
        aiPlannerOpen = !aiPlannerOpen;
        if (aiPlannerOpen) {
            panel.style.maxHeight = '600px';
            panel.style.marginBottom = '30px';
            arrow.style.transform = 'rotate(180deg)';
            btn.style.animation = 'none';
            // Scroll to panel smoothly
            setTimeout(() => panel.scrollIntoView({ behavior: 'smooth', block: 'start' }), 100);
        } else {
            panel.style.maxHeight = '0';
            panel.style.marginBottom = '0';
            arrow.style.transform = 'rotate(0deg)';
            btn.style.animation = 'ctaPulse 2.5s ease-in-out infinite';
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById('aiPlannerForm');
        const loader = document.getElementById('aiLoader');
        
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const budget = document.getElementById('aiBudget').value;
                const mood = document.getElementById('aiMood').value;
                const groupSize = document.getElementById('aiGroupSize').value;
                const timeOfDay = document.getElementById('aiTimeOfDay').value;
                
                // Show loader and hide form
                form.style.opacity = '0.3';
                form.style.pointerEvents = 'none';
                loader.style.display = 'block';
                
                // Gọi API tạo lộ trình bằng AI
                fetch('/api/food-tours/generate-ai', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ budget, mood, group_size: groupSize, time_of_day: timeOfDay })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Chuyển hướng đến lộ trình mới được tạo
                        window.location.href = `/food-tour/${data.slug}`;
                    } else {
                        alert(data.message || 'Có lỗi xảy ra khi tạo lộ trình AI!');
                        form.style.opacity = '1';
                        form.style.pointerEvents = 'auto';
                        loader.style.display = 'none';
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Lỗi kết nối đến máy chủ AI!');
                    form.style.opacity = '1';
                    form.style.pointerEvents = 'auto';
                    loader.style.display = 'none';
                });
            });
        }
    });
</script>

<style>
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    @keyframes pulse {
        0%, 100% { opacity: 0.6; }
        50% { opacity: 1; }
    }
    @keyframes ctaPulse {
        0%   { box-shadow: 0 8px 32px rgba(255,126,41,0.45), 0 0 0 0 rgba(255,126,41,0.4); transform: scale(1); }
        50%  { box-shadow: 0 8px 32px rgba(255,126,41,0.6), 0 0 0 10px rgba(255,126,41,0); transform: scale(1.02); }
        100% { box-shadow: 0 8px 32px rgba(255,126,41,0.45), 0 0 0 0 rgba(255,126,41,0); transform: scale(1); }
    }

    /* AI Planner Form Styles */
    .ai-field-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .ai-field-label {
        font-size: 0.78rem;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.8px;
    }
    .ai-select {
        width: 100%;
        padding: 13px 14px;
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.04);
        border: 1.5px solid rgba(255, 126, 41, 0.2);
        color: var(--text-main);
        font-weight: 600;
        font-size: 0.88rem;
        outline: none;
        cursor: pointer;
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23ff7e29' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
        padding-right: 38px;
        transition: border-color 0.25s, box-shadow 0.25s;
    }
    .ai-select:hover, .ai-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(255, 126, 41, 0.12);
    }
    .ai-select option {
        background: #1a1a1a;
        color: #fff;
    }
</style>
@endsection

@section('footer')
<footer style="background: #09090b; border-top: 1px solid rgba(255, 126, 41, 0.15); padding: 50px 0 30px 0; color: var(--text-muted); font-size: 0.88rem; font-family: var(--font-body); margin-top: 60px;">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        <div class="footer-grid" style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 40px; margin-bottom: 30px;">
            <div>
                <h3 class="logo" style="margin-bottom: 16px; font-size: 1.3rem; display: flex; align-items: center; gap: 8px; color: var(--text-main); font-family: var(--font-heading);">
                    <span>🗺️</span> <span style="background: var(--primary-grad); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Dong Anh Food Map</span>
                </h3>
                <p style="line-height: 1.6; max-width: 480px;">
                    Bản đồ số Ẩm thực Đông Anh là giải pháp công nghệ số hóa toàn bộ quán ăn, nhà hàng, quán cafe, khách sạn, nhà nghỉ và quảng bá các món ăn đặc sản truyền thống của huyện Đông Anh, Hà Nội. Hỗ trợ chuyển đổi số và nâng tầm văn hóa du lịch địa phương.
                </p>
            </div>
            <div>
                <h4 style="color: var(--text-main); margin-bottom: 16px; font-size: 1rem; font-weight: 700; font-family: var(--font-heading);">Liên kết nhanh</h4>
                <ul style="list-style: none; display: flex; flex-direction: column; gap: 10px; padding: 0; margin: 0;">
                    <li><a href="/" style="transition: color 0.3s; color: var(--text-muted);" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-muted)'">Trang chủ</a></li>
                    <li><a href="/tim-kiem" style="transition: color 0.3s; color: var(--text-muted);" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-muted)'">Bản đồ số</a></li>
                    <li><a href="/?cat=dac-san-dia-phuong" style="transition: color 0.3s; color: var(--text-muted);" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-muted)'">Đặc sản Cổ Loa & Đông Anh</a></li>
                    <li><a href="/auth/login" style="transition: color 0.3s; color: var(--text-muted);" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-muted)'">Đăng nhập quản trị viên</a></li>
                </ul>
            </div>
            <div>
                <h4 style="color: var(--text-main); margin-bottom: 16px; font-size: 1rem; font-weight: 700; font-family: var(--font-heading);">Liên hệ hỗ trợ</h4>
                <p style="line-height: 1.6;">
                    📍 UBND Huyện Đông Anh, Hà Nội<br>
                    📞 Điện thoại: 024.3123.4567<br>
                    ✉️ Email: info@donganh.hanoi.gov.vn<br>
                    🌐 Website: donganh.hanoi.gov.vn
                </p>
            </div>
        </div>
        <div style="border-top: 1px solid rgba(255,255,255,0.05); padding-top: 20px; text-align: center; font-size: 0.8rem; color: rgba(255,255,255,0.3);">
            &copy; 2026 Bản đồ số Ẩm thực Đông Anh (Dong Anh Food Map). Tất cả quyền được bảo lưu. Phát triển bởi Google Deepmind Antigravity.
        </div>
    </div>
</footer>
@endsection

