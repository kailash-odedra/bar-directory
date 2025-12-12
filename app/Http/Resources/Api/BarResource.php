<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BarResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'short_description' => $this->short_description,
            'full_description' => $this->when($request->routeIs('api.bars.show'), $this->full_description),
            'cover_image' => $this->cover_image ? asset('storage/' . $this->cover_image) : null,
            'logo' => $this->logo ? asset('storage/' . $this->logo) : null,
            'location' => $this->when($this->relationLoaded('location'), function () {
                return [
                    'city' => $this->location->city ?? null,
                    'city_name' => $this->location->city ?? null,
                    'state_name' => $this->location->state->name ?? null,
                    'country_name' => $this->location->country->name ?? null,
                    'address' => $this->location->address ?? null,
                    'zipcode' => $this->location->zipcode ?? null,
                    'region' => $this->location->region ?? null,
                    'latitude' => $this->location->latitude ?? null,
                    'longitude' => $this->location->longitude ?? null,
                ];
            }),
            'tags' => $this->when($this->relationLoaded('tags'), function () {
                return $this->tags->map(fn($tag) => [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'slug' => $tag->slug,
                ]);
            }),
            'avg_rating' => $this->when(isset($this->avg_rating), fn() => round($this->avg_rating, 1)),
            'approved_reviews_count' => $this->approved_reviews_count ?? 0,
            'is_featured' => (bool) $this->is_featured,
            'status' => $this->status,
            'images' => $this->when($request->routeIs('api.bars.show') && $this->relationLoaded('images'), function () {
                return $this->images->map(fn($image) => [
                    'id' => $image->id,
                    'path' => $image->path ? (filter_var($image->path, FILTER_VALIDATE_URL) ? $image->path : asset('storage/' . $image->path)) : null,
                    'type' => $image->type,
                    'alt' => $image->alt,
                ]);
            }),
            'events' => $this->when($request->routeIs('api.bars.show') && $this->relationLoaded('events'), function () {
                return $this->events->map(fn($event) => [
                    'id' => $event->id,
                    'title' => $event->title,
                    'description' => $event->description,
                    'start_time' => $event->start_time,
                    'end_time' => $event->end_time,
                    'image' => $event->image ? asset('storage/' . $event->image) : null,
                    'ticket_link' => $event->ticket_link,
                    'type' => $event->type,
                ]);
            }),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}

