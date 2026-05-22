<?php
$diaries = App\Models\FoodTourDiary::all();
foreach ($diaries as $d) {
    echo "Diary ID: {$d->id}, Tour ID: {$d->food_tour_id}\n";
}
