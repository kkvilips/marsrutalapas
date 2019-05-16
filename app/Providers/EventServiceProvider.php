<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
//        'App\Events\Suggestion\SuggestionVote' => [
//            'App\Listeners\Suggestion\Vote',
//        ],
        'App\Events\Suggestion\CreatedVoteEvent' => [
            'App\Listeners\Suggestion\VoteCreatedListener',
        ],
        'App\Events\Suggestion\DeletedVoteEvent' => [
            'App\Listeners\Suggestion\VoteDeletedListener',
        ],
        'App\Events\Suggestion\UpdatedVoteEvent' => [
            'App\Listeners\Suggestion\VoteUpdatedListener',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
