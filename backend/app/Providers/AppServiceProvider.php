<?php

namespace App\Providers;

use App\Models\AiKnowledgeBase;
use App\Observers\AiKnowledgeBaseObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        AiKnowledgeBase::observe(AiKnowledgeBaseObserver::class);
    }
}
