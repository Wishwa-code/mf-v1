<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SlAdminDivisionController extends Controller
{
    // Base URL of the public JSON repo
    private const BASE = 'https://raw.githubusercontent.com/SKIDDOW/SriLankaCitiesDatabase/main';

    /**
     * GET /sl-locations/provinces
     * Returns: { provinces: [ { id, text } ] }
     */
    public function provinces()
    {
        try {
            $json = file_get_contents(self::BASE . '/provinces.json');
            if ($json === false) {
                throw new \Exception('Could not fetch provinces JSON');
            }

            $data = json_decode($json, true);
            if (!is_array($data)) {
                throw new \Exception('Invalid provinces JSON structure');
            }

            // Map to Select2 format
            $provinces = array_map(function ($p) {
                return [
                    'id'   => $p['id'],                  // "1" .. "9"
                    'text' => $p['name_en'],            // Western, Central, ...
                ];
            }, $data);

            return response()->json([
                'provinces' => $provinces,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Failed to load provinces: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /sl-locations/cities?province_id=1&q=Pili
     * Returns: { cities: [ { id, text } ] }
     *
     * Uses:
     *  - provinces.json (for id → province)
     *  - districts.json (province_id)
     *  - cities.json (district_id)
     */
    public function cities(Request $request)
    {
        $provinceId = $request->get('province_id');   // << IMPORTANT: province_id
        $search     = trim((string) $request->get('q', ''));

        if (!$provinceId) {
            // no province → no cities
            return response()->json(['cities' => []]);
        }

        try {
            // 1) Load districts
            $districtsJson = file_get_contents(self::BASE . '/districts.json');
            if ($districtsJson === false) {
                throw new \Exception('Could not fetch districts JSON');
            }
            $districts = json_decode($districtsJson, true);
            if (!is_array($districts)) {
                throw new \Exception('Invalid districts JSON structure');
            }

            // Get district IDs under this province
            $districtIds = [];
            foreach ($districts as $d) {
                if ((string) $d['province_id'] === (string) $provinceId) {
                    $districtIds[] = (string) $d['id'];
                }
            }

            if (empty($districtIds)) {
                return response()->json(['cities' => []]);
            }

            // 2) Load cities
            $citiesJson = file_get_contents(self::BASE . '/cities.json');
            if ($citiesJson === false) {
                throw new \Exception('Could not fetch cities JSON');
            }
            $allCities = json_decode($citiesJson, true);
            if (!is_array($allCities)) {
                throw new \Exception('Invalid cities JSON structure');
            }

            $cities = [];
            foreach ($allCities as $c) {
                if (!in_array((string) $c['district_id'], $districtIds, true)) {
                    continue;
                }

                $label = $c['name_en'];

                if (!empty($c['sub_name_en']) && $c['sub_name_en'] !== 'NULL') {
                    $label .= ' - ' . $c['sub_name_en'];
                }

                if (!empty($c['postcode']) && $c['postcode'] !== 'NULL') {
                    $label .= ' (' . $c['postcode'] . ')';
                }

                // Search filter (optional)
                if ($search !== '') {
                    if (
                        stripos($label, $search) === false &&
                        stripos((string) ($c['postcode'] ?? ''), $search) === false
                    ) {
                        continue;
                    }
                }

                $cities[] = [
                    // you can store by name or id, up to you:
                    'id'   => $c['name_en'], // or $c['id']
                    'text' => $label,
                ];
            }

            return response()->json(['cities' => $cities]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Failed to load cities: ' . $e->getMessage(),
            ], 500);
        }
    }
}
