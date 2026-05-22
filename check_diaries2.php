<?php
$diaries = App\Models\FoodTourDiary::whereIn('food_tour_id', [8, 9])->get();
foreach ($diaries as $d) {
    echo "Diary ID: {$d->id}, Tour ID: {$d->food_tour_id}, Created: {$d->created_at}\n";
}
