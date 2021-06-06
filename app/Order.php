<?php

namespace App;

use App\Ticket;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $guarded = [];

    public function tickets() {
        return $this->hasMany(Ticket::class);
    }

    public function cancel() {
        foreach ($this->tickets as $ticket) {
            $ticket->release();
        }

        $this->delete();
    }
}
