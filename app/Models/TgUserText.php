<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TgUserText extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'tg_user_texts';
}
