<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PlaceGeocodingTest extends TestCase
{
    public function test_geocoding_returns_search_results_from_the_ign_service(): void
    {
        Http::fake([
            'data.geopf.fr/geocodage/search*' => Http::response([
                'features' => [
                    [
                        'properties' => [
                            'name' => ['Forêt de test'],
                            'postcode' => ['85200'],
                            'depcode' => ['85'],
                            'city' => ['Mervent'],
                        ],
                        'geometry' => ['coordinates' => [-0.73, 46.53]],
                    ],
                ],
            ]),
        ]);

        $response = $this->actingAs(new User(), 'sanctum')
            ->getJson('/api/places/geocode?q=Foret%20de%20test');

        $response->assertOk()
            ->assertJsonPath('data.0.properties.name.0', 'Forêt de test')
            ->assertJsonPath('data.0.geometry.coordinates.0', -0.73);

        Http::assertSent(function ($request) {
            parse_str(parse_url($request->url(), PHP_URL_QUERY) ?? '', $query);

            return str_starts_with($request->url(), 'https://data.geopf.fr/geocodage/search?')
                && $query['q'] === 'Foret de test'
                && $query['index'] === 'address,poi'
                && $query['limit'] === '5';
        });
    }
}
