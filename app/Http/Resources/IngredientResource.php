<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class IngredientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $imageUrl = null;
        if ($this->img) {
            // Если это уже полный URL, возвращаем как есть
            if (filter_var($this->img, FILTER_VALIDATE_URL)) {
                $imageUrl = $this->img;
            } else {
                // Иначе формируем URL через Storage
                $imageUrl = Storage::url($this->img);
            }
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'image_url' => $imageUrl,
            'unit' => $this->whenLoaded('unit', function () {
                return [
                    'id' => $this->unit->id,
                    'name' => $this->unit->name,
                ];
            }),
        ];
    }
}
