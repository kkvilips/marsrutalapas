<?php

namespace App\Listeners\Suggestion;

use App\Events\Suggestion\DeletedVoteEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Response;

use \App\SuggestionsVote;
use \App\Suggestion;

class VoteDeletedListener
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
     * @param  DeletedVoteEvent  $event
     * @return void
     */
    public function handle(DeletedVoteEvent $event)
    {
        //Get all CURRENT suggestion votes
        $suggestionsVote = SuggestionsVote::where('suggestion_id', $event->suggestionvote->suggestion_id)->get();
//        dd($suggestionsVote);
        $plussCount = $suggestionsVote
                ->where('vote', 1)
                ->count();
        $minusCount = $suggestionsVote
                ->where('vote', -1)
                ->count();
//        dd($plussCount);
        // Find Suggestion
        // Calculated values of minus and pluss votes put in there columns
        $suggestion = Suggestion::find($event->suggestionvote->suggestion_id);
        
        $suggestion->pluss_count = $plussCount;
        $suggestion->minus_count = $minusCount;
        $suggestion->save();
    }
}
