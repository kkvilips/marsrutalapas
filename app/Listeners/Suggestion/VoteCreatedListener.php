<?php

namespace App\Listeners\Suggestion;

use App\Events\Suggestion\CreatedVoteEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;


use App\Suggestion;
use App\SuggestionsVote;

class VoteCreatedListener
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
     * @param  CreatedVoteEvent  $event
     * @return void
     */
    public function handle(CreatedVoteEvent $event)
    {
//        $plussCount = SuggestionsVote::where('vote', 1)->count();
//        $minusCount = SuggestionsVote::where('vote', -1)->count();
//        
//        $suggestion = Suggestion::find($event->suggestionvote->suggestion_id);
//        $suggestion->pluss_count = $plussCount;
//        $suggestion->minus_count = $minusCount;
//        $suggestion->save();
    }
}
