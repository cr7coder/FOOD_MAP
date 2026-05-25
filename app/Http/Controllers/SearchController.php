<?php

namespace App\Http\Controllers;

use App\DTOs\SearchDTO;
use App\Models\Category;
use App\Models\Commune;
use App\Services\EateryService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(
        protected EateryService $eateryService
    ) {}

    public function search(Request $request)
    {
        $categories = Category::all();
        $communes = Commune::all();
        
        $dto = SearchDTO::fromRequest($request);
        
        // Autocomplete suggestions API
        if ($request->query('ajax') === 'suggest' && $dto->keyword) {
            $suggestions = $this->eateryService->getSuggestions($dto->keyword);
            return response()->json($suggestions);
        }

        $eateries = $this->eateryService->search($dto);
        
        // Return JSON response if requested via AJAX or JSON parameter
        if ($dto->expectsJson) {
            return response()->json($eateries);
        }
        
        $keyword = $dto->keyword;
        $catId = $dto->categoryId;
        $comId = $dto->communeId;
        
        return view('search', compact('categories', 'communes', 'eateries', 'keyword', 'catId', 'comId'));
    }
}
