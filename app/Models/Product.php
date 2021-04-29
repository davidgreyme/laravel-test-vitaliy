<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'price',
        'user_id'
    ];

    public $timestamps = false;

    /**
     * Get the user that owns the phone.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    /**
     * @param $sort
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    static public  function getAndSort($sort){
        $productQuery= self::query();

        switch ($sort) {
            case 'lprice':
                $productQuery->orderBy('price');
                break;
            case 'hprice':
                $productQuery->orderByDesc('price');
                break;
            default:
                abort(422);
        }

       return $productQuery
            ->orderBy('id')//FOR determinate result if has same  price
            ->get();
    }
}
