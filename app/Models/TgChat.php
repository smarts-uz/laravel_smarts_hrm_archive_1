<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TgChat extends Model
{
    use HasFactory;
    protected $table = 'tg_chats';
    protected $guarded = [];

}
