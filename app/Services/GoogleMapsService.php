<?php

namespace App\Services;

use Illuminate\Support\Str;

class GoogleMapsService
{
    /**
     * Tự động giải mã đường dẫn Google Maps (kể cả link rút gọn) và rút trích Tọa độ Kinh/Vĩ
     */
    public function parseUrl(string $url): ?array
    {
        // Nếu là link rút gọn maps.app.goo.gl hoặc goo.gl, cần phân giải redirect
        if (Str::contains($url, ['maps.app.goo.gl', 'goo.gl'])) {
            try {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HEADER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_NOBODY, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                curl_exec($ch);
                $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
                curl_close($ch);
                if ($finalUrl) {
                    $url = $finalUrl;
                }
            } catch (\Exception $e) {
                // Tiếp tục xử lý URL gốc nếu xảy ra lỗi cURL
            }
        }

        $lat = null;
        $lng = null;

        // Định dạng 1: Chứa @vĩđộ,kinhđộ (ví dụ: @21.1118671,105.8698539)
        if (preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $url, $matches)) {
            $lat = $matches[1];
            $lng = $matches[2];
        }
        // Định dạng 2: Chứa q=vĩđộ,kinhđộ (ví dụ: q=21.1118671,105.8698539)
        elseif (preg_match('/[?&]q=(-?\d+\.\d+),(-?\d+\.\d+)/', $url, $matches)) {
            $lat = $matches[1];
            $lng = $matches[2];
        }
        // Định dạng 3: Chứa mã nhúng 3d/4d nội bộ (!3d21.1118671!4d105.8698539)
        elseif (preg_match('/!3d(-?\d+\.\d+)!4d(-?\d+\.\d+)/', $url, $matches)) {
            $lat = $matches[1];
            $lng = $matches[2];
        }

        if ($lat && $lng) {
            return [
                'latitude' => (double)$lat,
                'longitude' => (double)$lng,
            ];
        }

        return null;
    }
}
