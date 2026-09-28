<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('user_preferences');
        Schema::dropIfExists('user_activities');
        Schema::dropIfExists('ad_placement_rules');
        Schema::dropIfExists('advertisements');
        Schema::dropIfExists('advertisers');
        Schema::dropIfExists('news_tag');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('news');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('upload_news');
    }

    public function down(): void
    {
    }
};
