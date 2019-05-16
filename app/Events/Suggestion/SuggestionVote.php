<?php

namespace App\Events\Suggestion;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

use App\SuggestionsVote;

class SuggestionVote
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $suggestionvote;
    
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(SuggestionsVote $suggestionvote)
    {
        $this->suggestionvote = $suggestionvote;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
