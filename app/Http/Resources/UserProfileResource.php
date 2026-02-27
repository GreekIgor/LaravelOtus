<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
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
            'email' => $this->email,
            'role' => $this->role,
            'created_at' => $this->created_at,
            'recipes_count' => $this->whenCounted('recipes', $this->recipes_count ?? 0),
            'recent_recipes' => $this->when(
                $this->relationLoaded('recipes') && $this->recipes->isNotEmpty(),
                function () {
                    return $this->recipes->take(5)->map(function ($recipe) {
                        return [
                            'id' => $recipe->id,
                            'title' => $recipe->title,
                            'created_at' => $recipe->created_at,
                        ];
                    });
                }
            ),
        ];
    }
}
