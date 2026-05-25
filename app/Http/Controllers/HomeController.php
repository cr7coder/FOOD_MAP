<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Commune;
use App\Models\Eatery;
use App\Models\ReviewVideo;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        $communes = Commune::all();
        
        $selectedCatSlug = $request->query('cat');
        $selectedComSlug = $request->query('com');
        
        $query = Eatery::with(['category', 'commune', 'reviewVideos' => function($q) {
            $q->where('status', 'approved');
        }])->active();
        
        if ($selectedCatSlug) {
            $query->whereHas('category', function($q) use ($selectedCatSlug) {
                $q->where('slug', $selectedCatSlug);
            });
        }
        
        if ($selectedComSlug) {
            $query->whereHas('commune', function($q) use ($selectedComSlug) {
                $q->where('slug', $selectedComSlug);
            });
        }
        
        $eateries = $query->get();
        
        // Quán nổi bật
        $featuredEateries = Eatery::with(['category', 'commune', 'reviewVideos' => function($q) {
                $q->where('status', 'approved');
            }])
            ->active()
            ->where('is_featured', true)
            ->get();
            
        // Đặc sản địa phương
        $specialties = Eatery::with(['category', 'commune', 'reviewVideos' => function($q) {
                $q->where('status', 'approved');
            }])
            ->active()
            ->whereHas('category', function($q) {
                $q->where('slug', 'dac-san-dia-phuong');
            })
            ->get();

        if ($request->has('ajax')) {
            return response()->json([
                'eateries' => $eateries,
                'selectedCatSlug' => $selectedCatSlug
            ]);
        }

        return view('home', compact(
            'categories', 
            'communes', 
            'eateries', 
            'featuredEateries', 
            'specialties', 
            'selectedCatSlug', 
            'selectedComSlug'
        ));
    }

    public function sitemap()
    {
        $eateries = Eatery::active()->get();
        $officialTours = \App\Models\FoodTour::public()->get();
        $communityTours = \App\Models\FoodTour::community()->get();
        
        return response()->view('sitemap', compact('eateries', 'officialTours', 'communityTours'))
                         ->header('Content-Type', 'text/xml');
    }

    public function getVideos()
    {
        $videos = ReviewVideo::with(['eatery.category', 'user'])
            ->where('status', 'approved')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function($vid) {
                return [
                    'id' => $vid->id,
                    'title' => $vid->title,
                    'video_url' => $vid->video_url,
                    'video_type' => $vid->video_type,
                    'thumbnail_path' => $vid->thumbnail_path,
                    'likes_count' => $vid->likes_count,
                    'eatery' => [
                        'name' => $vid->eatery->name,
                        'slug' => $vid->eatery->slug,
                        'address' => $vid->eatery->address,
                        'rating' => (float)$vid->eatery->rating,
                        'category' => $vid->eatery->category->name,
                        'latitude' => $vid->eatery->latitude,
                        'longitude' => $vid->eatery->longitude,
                        'image_path' => $vid->eatery->image_path,
                    ]
                ];
            });

        return response()->json($videos);
    }

    public function likeVideo($id)
    {
        $video = ReviewVideo::findOrFail($id);
        $video->increment('likes_count');
        return response()->json([
            'success' => true,
            'likes_count' => $video->likes_count
        ]);
    }
}
