@extends('layouts.admin')

@section('title', ($eatery ? 'Sửa địa điểm: ' . $eatery->name : 'Thêm địa điểm mới') . ' - Dong Anh Food Map')

@section('content')
<div class="container" style="padding-top: 40px; padding-bottom: 60px;">
    <div class="admin-layout">
        
        <!-- Sidebar Navigation -->
        <aside class="admin-sidebar glass-panel">
            <h3 style="font-size: 1.1rem; color: var(--primary); margin-bottom: 20px; border-bottom: 1px solid var(--border-glow); padding-bottom: 8px;">
                ⚙️ Menu Quản Trị
            </h3>
            <a href="/admin/dashboard" class="admin-menu-item">
                <span>📊</span> Tổng quan số liệu
            </a>
            @if(session('user_role') === 'admin')
            <a href="/admin/eateries/create" class="admin-menu-item {{ !$eatery ? 'active' : '' }}">
                <span>➕</span> Thêm địa điểm mới
            </a>
            @endif
            <a href="/" class="admin-menu-item">
                <span>🗺️</span> Xem Bản đồ tổng
            </a>
        </aside>
        
        <!-- Main Form Area -->
        <div class="admin-content">
            
            <div style="margin-bottom: 30px;">
                <h1 style="font-size: 2rem; font-family: var(--font-heading); background: var(--primary-grad); -webkit-background-clip: text; -webkit-text-fill-color: transparent; display: inline-block; padding: 8px 0; line-height: 1.3;">
                    {{ $eatery ? 'Sửa thông tin địa điểm' : 'Thêm địa điểm ẩm thực' }}
                </h1>
                <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 4px;">
                    {{ $eatery ? 'Thay đổi thông tin chi tiết của: ' . $eatery->name : 'Tạo mới một cửa hàng, nhà hàng, cafe, khách sạn hoặc đặc sản Đông Anh' }}
                </p>
            </div>

            <!-- Error List Banner -->
            @if ($errors->any())
                <div class="glass-panel" style="background: rgba(240, 78, 35, 0.1); border-color: var(--primary-hover); padding: 14px 20px; border-radius: 8px; margin-bottom: 24px; color: var(--primary); font-size: 0.85rem;">
                    <strong style="display: block; margin-bottom: 6px;">⚠️ Vui lòng sửa lại các thông tin lỗi dưới đây:</strong>
                    <ul style="padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Content -->
            <div class="glass-panel" style="padding: 32px;">
                <form action="{{ $eatery ? '/admin/eateries/' . $eatery->id : '/admin/eateries' }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if($eatery)
                        @method('PUT')
                    @endif
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                        
                        <!-- LEFT column (Text inputs) -->
                        <div>
                            <!-- Tên Quán -->
                            <div class="review-form-group">
                                <label class="review-form-label">Tên cơ sở / địa điểm <span style="color: var(--primary);">*</span></label>
                                <input type="text" name="name" class="form-input" required placeholder="Ví dụ: Bún Mạch Tràng Cổ Loa" value="{{ old('name', $eatery ? $eatery->name : '') }}">
                            </div>
                            
                            <!-- Danh mục và Xã -->
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                                <div class="review-form-group">
                                    <label class="review-form-label">Loại hình / Danh mục <span style="color: var(--primary);">*</span></label>
                                    <select name="category_id" class="form-input" required>
                                        <option value="">-- Chọn danh mục --</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}" {{ old('category_id', $eatery ? $eatery->category_id : '') == $cat->id ? 'selected' : '' }}>
                                                {{ $cat->icon }} {{ $cat->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="review-form-group">
                                    <label class="review-form-label">Khu vực Xã / Thị trấn <span style="color: var(--primary);">*</span></label>
                                    <select name="commune_id" class="form-input" required>
                                        <option value="">-- Chọn Xã --</option>
                                        @foreach($communes as $com)
                                            <option value="{{ $com->id }}" {{ old('commune_id', $eatery ? $eatery->commune_id : '') == $com->id ? 'selected' : '' }}>
                                                📍 Xã {{ $com->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Địa chỉ chi tiết -->
                            <div class="review-form-group">
                                <label class="review-form-label">Địa chỉ chi tiết <span style="color: var(--primary);">*</span></label>
                                <input type="text" name="address" class="form-input" required placeholder="Ví dụ: Thôn Mạch Tràng, Xã Cổ Loa, Đông Anh" value="{{ old('address', $eatery ? $eatery->address : '') }}">
                            </div>
                            
                            <!-- Điện thoại & Giờ mở cửa -->
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                                <div class="review-form-group">
                                    <label class="review-form-label">Số điện thoại liên hệ</label>
                                    <input type="text" name="phone" class="form-input" placeholder="Ví dụ: 0987654321" value="{{ old('phone', $eatery ? $eatery->phone : '') }}">
                                </div>
                                <div class="review-form-group">
                                    <label class="review-form-label">Giờ mở cửa</label>
                                    <input type="text" name="opening_hours" class="form-input" placeholder="Ví dụ: 06:00 - 21:00" value="{{ old('opening_hours', $eatery ? $eatery->opening_hours : '') }}">
                                </div>
                            </div>
                            
                            <!-- Mức giá & Khác -->
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                                <div class="review-form-group">
                                    <label class="review-form-label">Mức giá tham khảo</label>
                                    <input type="text" name="price_range" class="form-input" placeholder="Ví dụ: 30.000đ - 70.000đ" value="{{ old('price_range', $eatery ? $eatery->price_range : '') }}">
                                </div>
                                @if(session('user_role') === 'admin')
                                <div class="review-form-group" style="display: flex; align-items: center; padding-top: 35px;">
                                    <label style="display: inline-flex; align-items: center; gap: 10px; cursor: pointer; font-size: 0.9rem;">
                                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $eatery ? $eatery->is_featured : false) ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: var(--primary);">
                                        <strong>⭐ Đánh dấu là Địa điểm nổi bật</strong>
                                    </label>
                                </div>
                                @endif
                            </div>
                            
                            <!-- Upload ảnh -->
                            <div class="review-form-group">
                                <label class="review-form-label">Ảnh đại diện (Upload từ thiết bị)</label>
                                <input type="file" name="image" class="form-input" accept="image/*" style="padding: 8px 16px;">
                                <span style="font-size: 0.75rem; color: var(--text-muted); display: block; margin-top: 4px;">Chấp nhận định dạng file ảnh jpg, png dung lượng &lt; 10MB</span>
                            </div>
                            
                            <div class="review-form-group">
                                <label class="review-form-label">Hoặc Đường dẫn ảnh URL (Nếu không muốn upload)</label>
                                <input type="url" name="image_url" class="form-input" placeholder="https://example.com/photo.jpg" value="{{ old('image_url', $eatery && !Str::startsWith($eatery->image_path, '/uploads') ? $eatery->image_path : '') }}">
                            </div>
                        </div>
                        
                        <!-- RIGHT column (Map coordinates picker & Description) -->
                        <div>
                            <!-- Tự động lấy tọa độ từ Google Maps Link -->
                            <div class="review-form-group">
                                <label class="review-form-label" style="display: flex; justify-content: space-between;">
                                    <span>🔗 Dán đường dẫn Google Maps để lấy tọa độ tự động</span>
                                    <span style="color: var(--accent); font-weight: 500;">(Hỗ trợ cả link chia sẻ maps.app.goo.gl)</span>
                                </label>
                                <div style="display: flex; gap: 12px;">
                                    <input type="url" id="gmapsUrlInput" class="form-input" placeholder="Ví dụ: https://maps.app.goo.gl/6F4vV18TNTa2o77q8 hoặc desktop maps link..." style="flex: 1; min-width: 0;">
                                    <button type="button" id="btnExtractCoords" class="btn-primary" style="padding: 0 20px; font-size: 0.9rem; white-space: nowrap; cursor: pointer;">
                                        ⚡ Trích xuất
                                    </button>
                                </div>
                                <span id="gmapsHelperText" style="font-size: 0.8rem; display: block; margin-top: 6px;"></span>
                            </div>

                            <!-- Kinh độ & Vĩ độ -->
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                                <div class="review-form-group">
                                    <label class="review-form-label">Vĩ độ (Latitude) <span style="color: var(--primary);">*</span></label>
                                    <input type="number" step="any" name="latitude" id="latInput" class="form-input" required placeholder="Vĩ độ GPS..." value="{{ old('latitude', $eatery ? $eatery->latitude : '') }}">
                                </div>
                                <div class="review-form-group">
                                    <label class="review-form-label">Kinh độ (Longitude) <span style="color: var(--primary);">*</span></label>
                                    <input type="number" step="any" name="longitude" id="lngInput" class="form-input" required placeholder="Kinh độ GPS..." value="{{ old('longitude', $eatery ? $eatery->longitude : '') }}">
                                </div>
                            </div>
                            
                            <!-- Map Coordinates Picker Widget -->
                            <div class="review-form-group">
                                <label class="review-form-label" style="display: flex; justify-content: space-between;">
                                    <span>🎯 Bộ chọn vị trí trực quan trên Bản đồ</span>
                                    <span style="color: var(--accent); font-weight: 500;">(Nhấp chuột lên bản đồ để lấy tọa độ tự động)</span>
                                </label>
                                <div class="coordinates-picker-map" id="pickerMap"></div>
                            </div>
                            
                            <!-- Giới thiệu quán -->
                            <div class="review-form-group" style="margin-top: 20px;">
                                <label class="review-form-label">Giới thiệu tóm tắt quán ăn / khách sạn</label>
                                <textarea name="description" class="form-input" rows="5" placeholder="Nhập các đặc điểm nổi bật, lịch sử món ăn hoặc không gian phục vụ..." style="resize: vertical;">{{ old('description', $eatery ? $eatery->description : '') }}</textarea>
                            </div>
                        </div>
                        
                    </div>
                    
                    <!-- Action Submit -->
                    <div style="border-top: 1px solid var(--border-glow); padding-top: 24px; margin-top: 24px; display: flex; justify-content: flex-end; gap: 16px;">
                        <a href="/admin/dashboard" class="btn-secondary">Hủy bỏ</a>
                        <button type="submit" class="btn-primary" style="padding: 12px 32px;">
                            {{ $eatery ? 'Cập nhật địa điểm' : 'Lưu lại & Số hóa' }}
                        </button>
                    </div>
                    
                </form>
            </div>

            <!-- Xuất Mã QR Code Thông Minh cho Cửa hàng -->
            @if($eatery)
                <div class="glass-panel" style="padding: 24px; margin-top: 30px; display: flex; gap: 24px; align-items: center; background: rgba(32, 178, 170, 0.05); border-color: var(--accent);">
                    <div style="background: white; padding: 12px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode(url('/dia-diem/dac-san/' . $eatery->slug)) }}" alt="QR Code" style="width: 140px; height: 140px; display: block; mix-blend-mode: multiply;">
                    </div>
                    <div>
                        <h2 style="font-size: 1.3rem; font-family: var(--font-heading); color: var(--accent); margin-bottom: 8px;">📲 QR Code Cửa Hàng Thông Minh</h2>
                        <p style="color: var(--text-main); font-size: 0.95rem; margin-bottom: 12px;">Mã QR Code này trỏ thẳng đến trang chi tiết của địa điểm này trên hệ thống Bản đồ Ẩm thực.</p>
                        <p style="color: var(--text-muted); font-size: 0.85rem;"><strong>💡 Mẹo kinh doanh:</strong> Lưu hình ảnh mã QR này và in ra đặt tại bàn ăn. Khách hàng quét mã sẽ được dẫn thẳng vào trang để xem thực đơn đặc trưng và để lại Đánh giá 5 Sao!</p>
                    </div>
                </div>

                <!-- Quản lý Thực đơn & Món ăn đặc trưng -->
                <div class="glass-panel" style="padding: 32px; margin-top: 30px;">
                    <h2 style="font-size: 1.5rem; margin-bottom: 20px; font-family: var(--font-heading); color: var(--primary); display: flex; align-items: center; gap: 8px;">
                        <span>📖</span> Quản lý Thực đơn & Món ăn đặc trưng
                    </h2>
                    <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 24px;">
                        Thêm các món ăn tiêu biểu của quán để hiển thị trực quan lên trang chi tiết, giúp người dùng dễ dàng tìm kiếm và xem thực đơn đặc trưng.
                    </p>

                    <div style="display: grid; grid-template-columns: 1fr 350px; gap: 24px;">
                        
                        <!-- Left: Current Dishes List -->
                        <div>
                            <h3 style="font-size: 1.1rem; margin-bottom: 16px; display: flex; align-items: center; gap: 6px;">
                                <span>📋</span> Danh sách món ăn hiện tại ({{ $eatery->dishes->count() }})
                            </h3>

                            @if($eatery->dishes->count() > 0)
                                <div style="display: grid; grid-template-columns: 1fr; gap: 12px;">
                                    @foreach($eatery->dishes as $dish)
                                        <div class="dish-card glass-panel" style="display: flex; gap: 16px; padding: 12px; align-items: center; position: relative;">
                                            <img src="{{ $dish->image_path ?: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=80&q=80' }}" style="width: 70px; height: 70px; border-radius: 8px; object-fit: cover;" alt="{{ $dish->name }}">
                                            <div style="flex: 1;">
                                                <div style="display: flex; align-items: center; gap: 8px;">
                                                    <h4 style="font-size: 1.05rem; font-weight: 600; margin: 0; color: var(--text-main);">{{ $dish->name }}</h4>
                                                    @if($dish->is_signature)
                                                        <span class="tag-badge" style="background: var(--primary); padding: 1px 6px; font-size: 0.65rem; font-weight: 700; color: white; border-radius: 4px;">★ Đặc trưng</span>
                                                    @endif
                                                </div>
                                                <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 4px; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;">{{ $dish->description ?: 'Chưa có mô tả ngắn' }}</p>
                                                <span style="font-weight: 700; color: var(--accent); font-size: 0.95rem; margin-top: 4px; display: inline-block;">{{ number_format($dish->price, 0, ',', '.') }}đ</span>
                                            </div>
                                            <div style="display: flex; gap: 8px;">
                                                <!-- Toggle Signature form -->
                                                <form action="/admin/dishes/{{ $dish->id }}/toggle-signature" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn-secondary" style="padding: 6px 10px; font-size: 0.75rem; border-radius: 6px; border-color: var(--border-glow);" title="Bật/Tắt món đặc trưng">
                                                        {{ $dish->is_signature ? '★ Gỡ đặc trưng' : '☆ Đặt đặc trưng' }}
                                                    </button>
                                                </form>

                                                <!-- Delete form -->
                                                <form action="/admin/dishes/{{ $dish->id }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa món này khỏi thực đơn?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-secondary" style="padding: 6px 10px; font-size: 0.75rem; border-radius: 6px; color: #ff3333; border-color: rgba(255, 51, 51, 0.2);" title="Xóa món ăn">
                                                        🗑️ Xóa
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="glass-panel" style="padding: 40px; text-align: center; color: var(--text-muted); background: rgba(255,255,255,0.01);">
                                    <p style="font-size: 0.9rem;">Thực đơn hiện tại đang trống.</p>
                                    <p style="font-size: 0.8rem; margin-top: 4px;">Hãy sử dụng form bên phải để thêm món ăn đầu tiên của quán!</p>
                                </div>
                            @endif
                        </div>

                        <!-- Right: Add New Dish Form -->
                        <div>
                            <div class="glass-panel" style="padding: 20px; background: rgba(255,255,255,0.015); border-color: var(--border-glow);">
                                <h3 style="font-size: 1.1rem; margin-bottom: 16px; display: flex; align-items: center; gap: 6px; color: var(--accent);">
                                    <span>➕</span> Thêm món mới
                                </h3>
                                
                                <form action="/admin/dishes" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="eatery_id" value="{{ $eatery->id }}">

                                    <div class="review-form-group">
                                        <label class="review-form-label" style="font-size: 0.8rem;">Tên món ăn <span style="color: var(--primary);">*</span></label>
                                        <input type="text" name="dish_name" class="form-input" required placeholder="Ví dụ: Bún chả nguội..." style="font-size: 0.85rem; padding: 8px 12px;">
                                    </div>

                                    <div class="review-form-group">
                                        <label class="review-form-label" style="font-size: 0.8rem;">Giá bán (VNĐ) <span style="color: var(--primary);">*</span></label>
                                        <input type="number" name="dish_price" class="form-input" required placeholder="Ví dụ: 35000" style="font-size: 0.85rem; padding: 8px 12px;">
                                    </div>

                                    <div class="review-form-group">
                                        <label class="review-form-label" style="font-size: 0.8rem;">Mô tả ngắn</label>
                                        <textarea name="dish_description" class="form-input" rows="2" placeholder="Ví dụ: Bún chả truyền thống kèm nước dùng..." style="font-size: 0.85rem; padding: 8px 12px; resize: vertical;"></textarea>
                                    </div>

                                    <div class="review-form-group">
                                        <label class="review-form-label" style="font-size: 0.8rem;">Ảnh món ăn (Upload)</label>
                                        <input type="file" name="dish_image" class="form-input" accept="image/*" style="font-size: 0.8rem; padding: 6px 12px;">
                                    </div>

                                    <div class="review-form-group">
                                        <label class="review-form-label" style="font-size: 0.8rem;">Hoặc Ảnh URL</label>
                                        <input type="url" name="dish_image_url" class="form-input" placeholder="https://example.com/dish.jpg" style="font-size: 0.85rem; padding: 8px 12px;">
                                    </div>

                                    <div class="review-form-group" style="display: flex; align-items: center; margin-top: 15px;">
                                        <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer; font-size: 0.85rem;">
                                            <input type="checkbox" name="is_signature" value="1" style="width: 16px; height: 16px; accent-color: var(--primary);">
                                            <strong>★ Là món đặc trưng nổi bật</strong>
                                        </label>
                                    </div>

                                    <button type="submit" class="btn-primary" style="width: 100%; justify-content: center; margin-top: 15px; font-size: 0.85rem; padding: 10px 0;">
                                        Lưu món ăn
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            @else
                <div class="glass-panel" style="padding: 20px; text-align: center; margin-top: 30px; color: var(--text-muted); background: rgba(255,255,255,0.01);">
                    <p style="font-size: 0.9rem; display: flex; align-items: center; justify-content: center; gap: 6px;">
                        <span>💡</span> <em>Sau khi lưu/tạo mới thông tin địa điểm này thành công, bạn sẽ có thể thêm và quản lý chi tiết thực đơn / món ăn đặc trưng ngay tại trang sửa (Edit).</em>
                    </p>
                </div>
            @endif
            
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Kiểm tra xem là Sửa (Edit) hay Thêm mới
    const hasEatery = {{ $eatery ? 'true' : 'false' }};
    const initLat = {{ $eatery ? $eatery->latitude : 21.1182 }};
    const initLng = {{ $eatery ? $eatery->longitude : 105.8394 }};
    let pickerMap;
    let marker;

    document.addEventListener("DOMContentLoaded", function() {
        // Khởi tạo bản đồ chọn tọa độ
        pickerMap = L.map('pickerMap', {
            zoomControl: true
        }).setView([initLat, initLng], hasEatery ? 15 : 13);

        // Lớp dark mode sang trọng
        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(pickerMap);

        // Biểu tượng Marker
        const customIcon = L.divIcon({
            html: `<div style="background-color: var(--primary); width: 24px; height: 24px; border-radius: 50%; border: 2px solid white; display: flex; align-items: center; justify-content: center; font-size: 1rem;">📍</div>`,
            className: 'custom-leaflet-marker',
            iconSize: [24, 24],
            iconAnchor: [12, 12]
        });

        // Vẽ marker ban đầu nếu có dữ liệu
        if (hasEatery) {
            marker = L.marker([initLat, initLng], { icon: customIcon }).addTo(pickerMap);
        }

        // Sự kiện click bản đồ nhặt tọa độ điền tự động vào Form
        pickerMap.on('click', function(e) {
            const lat = e.latlng.lat.toFixed(6);
            const lng = e.latlng.lng.toFixed(6);
            
            // Cập nhật giá trị 2 ô input
            document.getElementById('latInput').value = lat;
            document.getElementById('lngInput').value = lng;
            
            // Di chuyển hoặc vẽ mới marker định vị
            if (marker) {
                marker.setLatLng(e.latlng);
            } else {
                marker = L.marker(e.latlng, { icon: customIcon }).addTo(pickerMap);
            }
            
            pickerMap.panTo(e.latlng);
        });

        // Hỗ trợ cập nhật marker khi nhập tọa độ thủ công bằng tay
        const latInput = document.getElementById('latInput');
        const lngInput = document.getElementById('lngInput');

        function updateMarkerFromInput() {
            const lat = parseFloat(latInput.value);
            const lng = parseFloat(lngInput.value);
            
            if (!isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
                const newLatLng = new L.LatLng(lat, lng);
                
                if (marker) {
                    marker.setLatLng(newLatLng);
                } else {
                    marker = L.marker(newLatLng, { icon: customIcon }).addTo(pickerMap);
                }
                
                pickerMap.setView(newLatLng, 15);
            }
        }

        latInput.addEventListener('change', updateMarkerFromInput);
        lngInput.addEventListener('change', updateMarkerFromInput);

        // Xử lý trích xuất tọa độ từ Google Maps Link tự động
        const btnExtract = document.getElementById('btnExtractCoords');
        const gmapsInput = document.getElementById('gmapsUrlInput');
        const helperText = document.getElementById('gmapsHelperText');

        btnExtract.addEventListener('click', function() {
            const url = gmapsInput.value.trim();
            if (!url) {
                helperText.innerText = "❌ Vui lòng dán đường dẫn Google Maps trước!";
                helperText.style.color = "var(--primary)";
                return;
            }

            helperText.innerText = "⏳ Đang giải mã đường dẫn và trích xuất tọa độ...";
            helperText.style.color = "var(--accent)";
            btnExtract.disabled = true;

            // Gửi request lên backend endpoint để giải mã link rút gọn và trích xuất tọa độ
            fetch('/admin/parse-google-maps', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ url: url })
            })
            .then(response => response.json())
            .then(data => {
                btnExtract.disabled = false;
                if (data.success) {
                    const lat = parseFloat(data.latitude).toFixed(6);
                    const lng = parseFloat(data.longitude).toFixed(6);

                    // Điền vào form
                    latInput.value = lat;
                    lngInput.value = lng;

                    // Di chuyển marker trên bản đồ
                    const newLatLng = new L.LatLng(lat, lng);
                    if (marker) {
                        marker.setLatLng(newLatLng);
                    } else {
                        marker = L.marker(newLatLng, { icon: customIcon }).addTo(pickerMap);
                    }
                    pickerMap.setView(newLatLng, 15);

                    helperText.innerText = "✅ Trích xuất tọa độ thành công!";
                    helperText.style.color = "#10B981";
                } else {
                    helperText.innerText = "❌ " + data.message;
                    helperText.style.color = "var(--primary)";
                }
            })
            .catch(error => {
                btnExtract.disabled = false;
                console.error("Error:", error);
                helperText.innerText = "❌ Lỗi kết nối hệ thống. Vui lòng kiểm tra lại.";
                helperText.style.color = "var(--primary)";
            });
        });
    });
</script>
@endsection
