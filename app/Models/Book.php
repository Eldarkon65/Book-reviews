<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    protected static function booted()
    {
        static::updated
        (
            fn(Book $book)=> cache()->forget('book:'. $book->id)
        );
        static::deleted
        (
            fn(Book $book)=> cache()->forget('book:'. $book->id)
        );
    }

    public function scopeTitle(Builder $query, string $title): Builder | \Illuminate\Database\Query\Builder
    {
        return $query->where('title', 'LIKE', '%' . $title . '%');
    }

    public function scopeWithReviewsCount(Builder $query, $from = null, $to=null): Builder | \Illuminate\Database\Query\Builder
    {
        return $query->withCount([
            'reviews'=> fn(Builder $q) => $this->dateRangeFilter($q, $from, $to)
        ]);
    }

    public function scopePopular(Builder $query, $from = null, $to=null): Builder | \Illuminate\Database\Query\Builder
    {
        return $query->withReviewsCount()
            ->orderBy('reviews_count', 'desc');
    }

    public function scopeWithAvgRatings(Builder $query, $from = null, $to = null): Builder | \Illuminate\Database\Query\Builder
    {
        return $query->withAvg([
            'reviews'=> fn(Builder $q) => $this->dateRangeFilter($q, $from, $to)
        ],'rating');
    }

    public function scopeHighestRating(Builder $query, $from = null, $to = null): Builder | \Illuminate\Database\Query\Builder
    {
        return $query->withAvgRatings()
            ->orderBy('reviews_avg_rating', 'desc');
    }

    public function scopeMinReviews(Builder $query, $minReviews):Builder | \Illuminate\Database\Query\Builder
    {
        return $query->having('reviews_count', '>=', $minReviews);
    }
    private function dateRangeFilter(Builder $query, $from = null, $to = null)
    {
        if ($from && !$to){
            $query->where('created_at', '>=', $from);
        } elseif (!$from && $to) {
            $query->where('created_at', '<=', $to);
        } elseif ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        }
    }

    public function scopePopularLastMonth(Builder $query ): Builder | \Illuminate\Database\Query\Builder
    {
        return $query ->popular(now()->subMonth(), now())
            ->highestRating(now()->subMonth(), now())
            ->minReviews(2);
    }

    public function scopePopularLast6Months(Builder $query ): Builder | \Illuminate\Database\Query\Builder
    {
        return $query ->popular(now()->subMonths(6), now())
            ->highestRating(now()->subMonths(6), now())
            ->minReviews(5);
    }

    public function scopeHighestRatingLastMonth(Builder $query): Builder | \Illuminate\Database\Query\Builder
    {
        return $query ->HighestRating(now()->subMonth(), now())
            ->popular(now()->subMonth(), now())
            ->minReviews(2);
    }

    public function scopeHighestRatingLast6Months(Builder $query): Builder | \Illuminate\Database\Query\Builder
    {
        return $query ->highestRating(now()->subMonths(6), now())
            ->popular(now()->subMonths(6), now())
            ->minReviews(5);
    }

}
