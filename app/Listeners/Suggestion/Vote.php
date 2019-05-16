<?php

namespace App\Listeners\Suggestion;

use App\Events\Suggestion\SuggestionVote;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class Vote
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UpdatedVoteEvent  $event
     * @return void
     */
    public function handle(SuggestionVote $event)
    {
        dd('ee');
    }
}
