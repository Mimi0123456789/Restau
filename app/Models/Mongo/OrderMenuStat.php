<?php

namespace App\Models\Mongo;

use MongoDB\Laravel\Eloquent\Model;

class OrderMenuStat extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'order_menu_stats';

    protected $guarded = [];
}