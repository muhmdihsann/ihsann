<?php

namespace Tests\Feature;

use App\Models\ImportHistory;
use App\Models\Province;
use App\Models\Regency;
use App\Models\SpmData;
use App\Models\User;
use App\Services\MapAreaNameNormalizer;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PublicMapTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_public_map_page_can_be_opened(): void
    {
        $this->get(route('peta'))
            ->assertOk()
            ->assertSee('Peta Persebaran Kinerja SPM Kebakaran')
            ->assertSee(route('peta.data'), false)
            ->assertSee('Kualitas Udara Real-Time')
            ->assertSee(route('peta.air-quality'), false);
    }

    public function test_homepage_keeps_the_areas_with_the_most_fires_section(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Provinsi yang paling terdampak kebakaran')
            ->assertSee('Data hotspot seluruh provinsi');
    }

    public function test_hotspot_endpoint_returns_cached_firms_geojson(): void
    {
        config([
            'services.nasa.firms.map_key' => 'test-map-key',
            'services.nasa.firms.source' => 'VIIRS_NOAA21_NRT',
        ]);
        Cache::forget('nasa-firms-hotspots:indonesia:VIIRS_NOAA21_NRT:VIIRS_NOAA20_NRT:VIIRS_SNPP_NRT');
        Http::preventStrayRequests();
        Http::fake(['firms.modaps.eosdis.nasa.gov/api/area/csv/*' => Http::response(implode("\n", [
            'latitude,longitude,bright_ti4,scan,track,acq_date,acq_time,satellite,instrument,confidence,version,bright_ti5,frp,daynight',
            '-7.45,109.1,340.2,0.42,0.38,2026-10-20,430,N,VIIRS,n,2.0NRT,300.1,12.5,D',
        ]))]);

        $this->getJson(route('peta.hotspots'))
            ->assertOk()
            ->assertJsonPath('type', 'FeatureCollection')
            ->assertJsonPath('source', 'NASA FIRMS')
            ->assertJsonPath('product', 'VIIRS_NOAA21_NRT + VIIRS_NOAA20_NRT + VIIRS_SNPP_NRT')
            ->assertJsonPath('count', 3)
            ->assertJsonPath('features.0.geometry.coordinates', [109.1, -7.45])
            ->assertJsonPath('features.0.properties.confidence_label', 'Nominal')
            ->assertJsonPath('features.0.properties.frp', 12.5);

        $this->getJson(route('peta.hotspots'))->assertOk();
        Http::assertSentCount(3);
        Http::assertSent(fn ($request): bool => str_contains($request->url(), '/test-map-key/VIIRS_NOAA21_NRT/95,-11,141,6/1'));
    }

    public function test_air_quality_endpoint_returns_iqair_measurements_and_caches_each_city(): void
    {
        config(['services.iqair.api_key' => 'test-iqair-key']);
        foreach (['jakarta', 'bandung', 'semarang', 'yogyakarta', 'surabaya', 'denpasar', 'medan', 'palembang', 'pontianak', 'banjarmasin', 'makassar', 'manado', 'jayapura'] as $city) {
            Cache::forget('iqair:nearest-city:'.$city);
        }
        Http::preventStrayRequests();
        Http::fake(['api.airvisual.com/v2/nearest_city*' => Http::response([
            'status' => 'success',
            'data' => [
                'city' => 'Jakarta',
                'state' => 'Jakarta',
                'country' => 'Indonesia',
                'location' => ['type' => 'Point', 'coordinates' => [106.8456, -6.2088]],
                'current' => [
                    'pollution' => [
                        'ts' => '2026-10-21T01:00:00.000Z',
                        'aqius' => 42,
                        'mainus' => 'p2',
                        'aqicn' => 15,
                        'p2' => ['conc' => 8.9, 'aqius' => 42, 'aqicn' => 15],
                    ],
                    'weather' => ['tp' => 30, 'hu' => 72, 'pr' => 1009, 'ws' => 2.1, 'wd' => 90],
                ],
            ],
        ])]);

        $this->getJson(route('peta.air-quality'))
            ->assertOk()
            ->assertJsonPath('source', 'IQAir AirVisual')
            ->assertJsonPath('count', 13)
            ->assertJsonPath('cities.0.city', 'Jakarta')
            ->assertJsonPath('cities.0.aqi_us', 42)
            ->assertJsonPath('cities.0.main_pollutant', 'p2')
            ->assertJsonPath('cities.0.pollutants.P2.concentration', 8.9)
            ->assertJsonPath('cities.0.weather.temperature_c', 30);

        $this->getJson(route('peta.air-quality'))->assertOk();
        Http::assertSentCount(13);
        Http::assertSent(fn ($request): bool => str_contains($request->url(), 'key=test-iqair-key') && str_contains($request->url(), 'lat=-6.2088'));
    }

    public function test_air_quality_endpoint_requires_a_server_side_iqair_key(): void
    {
        config(['services.iqair.api_key' => null]);
        Http::preventStrayRequests();

        $this->getJson(route('peta.air-quality'))
            ->assertStatus(503)
            ->assertJsonPath('message', 'Kunci API IQAir belum dikonfigurasi pada server.');
    }

    public function test_admin_dashboard_renders_the_detailed_map_panel(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('dashboard-map-detail', false)
            ->assertSee('dashboard-area-metrics', false)
            ->assertSee('Kabupaten/kota terdata');
    }

    public function test_map_data_returns_the_selected_year_with_index_category_and_regencies(): void
    {
        $province = Province::create(['code' => '33000', 'name' => 'Jawa Tengah']);
        $regency = Regency::create(['province_id' => $province->id, 'code' => '33001', 'name' => 'Kab. Cilacap']);
        SpmData::create([
            'import_history_id' => $this->publishedImport(2025)->id,
            'regency_id' => $regency->id,
            'year' => 2025,
            'nilai_akhir' => 82.36,
            'jml_kecamatan' => 12,
            'jml_pos' => 4,
            'total_sdm' => 36,
            'sdm_sertifikat' => 20,
            'jml_desa' => 50,
            'jml_redkar' => 300,
            'dimensi_kelembagaan' => 80,
        ]);
        SpmData::create(['import_history_id' => $this->publishedImport(2026)->id, 'regency_id' => $regency->id, 'year' => 2026, 'nilai_akhir' => 87]);

        $this->getJson(route('peta.data', ['year' => 2025]))
            ->assertOk()
            ->assertJsonPath('year', 2025)
            ->assertJsonPath('provinces.0.geojson_code', '33')
            ->assertJsonPath('provinces.0.province_name', 'Jawa Tengah')
            ->assertJsonPath('provinces.0.index', 82.36)
            ->assertJsonPath('provinces.0.spm', 82.36)
            ->assertJsonPath('provinces.0.category', 'Baik')
            ->assertJsonPath('provinces.0.regency_count', 1)
            ->assertJsonPath('provinces.0.regencies.0.name', 'Kab. Cilacap')
            ->assertJsonPath('provinces.0.regencies.0.regency_name', 'Kab. Cilacap')
            ->assertJsonPath('provinces.0.regencies.0.index', 82.36)
            ->assertJsonPath('provinces.0.regencies.0.category', 'Baik')
            ->assertJsonPath('provinces.0.regencies.0.metrics.kecamatan', 12)
            ->assertJsonPath('provinces.0.regencies.0.metrics.pos_damkar', 4)
            ->assertJsonPath('provinces.0.regencies.0.metrics.sdm', 36)
            ->assertJsonPath('provinces.0.regencies.0.metrics.sdm_bersertifikat', 20)
            ->assertJsonPath('provinces.0.regencies.0.metrics.desa', 50)
            ->assertJsonPath('provinces.0.regencies.0.metrics.redkar', 300)
            ->assertJsonPath('provinces.0.metrics.pos_damkar', 4);

        $this->getJson(route('peta.data', ['year' => 2026]))
            ->assertOk()
            ->assertJsonPath('year', 2026)
            ->assertJsonPath('provinces.0.index', 87)
            ->assertJsonPath('provinces.0.category', 'Sangat Baik');
    }

    public function test_draft_data_is_excluded_and_a_province_without_data_returns_null_metrics(): void
    {
        $province = Province::create(['code' => '32000', 'name' => 'Jawa Barat']);
        $regency = Regency::create(['province_id' => $province->id, 'code' => '32001', 'name' => 'Kab. Bogor']);
        $draftImport = ImportHistory::create([
            'user_id' => User::factory()->create()->id,
            'file_name' => 'draft.xlsx',
            'year' => 2025,
            'status' => ImportHistory::Draft,
        ]);
        SpmData::create(['import_history_id' => $draftImport->id, 'regency_id' => $regency->id, 'year' => 2025, 'nilai_akhir' => 10]);

        $this->getJson(route('peta.data', ['year' => 2025]))
            ->assertOk()
            ->assertJsonPath('year', null)
            ->assertJsonPath('provinces.0.index', null)
            ->assertJsonPath('provinces.0.category', null)
            ->assertJsonPath('provinces.0.regencies', []);
    }

    public function test_map_normalizes_verified_yogyakarta_names_and_derives_all_categories_from_the_index(): void
    {
        $normalizer = app(MapAreaNameNormalizer::class);
        $this->assertSame('JAWATENGAH', $normalizer->provinceKey('PROVINSI JAWA TENGAH'));
        $this->assertSame('JAWABARAT', $normalizer->provinceKey('JAWA BARAT '));
        $this->assertSame('ACEH', $normalizer->provinceKey('ACEH'));
        $this->assertSame('SUMATERAUTARA', $normalizer->provinceKey('SUMATERA UTARA'));
        $this->assertSame('YOGYAKARTA', $normalizer->provinceKey('Daerah Istimewa Yogyakarta'));

        $province = Province::create(['code' => '34000', 'name' => 'DI Yogyakarta']);
        $regency = Regency::create(['province_id' => $province->id, 'code' => '34001', 'name' => 'Kabupaten Sleman']);
        foreach ([2025 => 87, 2026 => 72, 2027 => 60, 2028 => 40] as $year => $index) {
            SpmData::create([
                'import_history_id' => $this->publishedImport($year)->id,
                'regency_id' => $regency->id,
                'year' => $year,
                'nilai_akhir' => $index,
            ]);
        }

        $this->getJson(route('peta.data', ['year' => 2025]))
            ->assertJsonPath('provinces.0.map_key', 'YOGYAKARTA')
            ->assertJsonPath('provinces.0.category', 'Sangat Baik');
        $this->getJson(route('peta.data', ['year' => 2026]))->assertJsonPath('provinces.0.category', 'Baik');
        $this->getJson(route('peta.data', ['year' => 2027]))->assertJsonPath('provinces.0.category', 'Cukup');
        $this->getJson(route('peta.data', ['year' => 2028]))->assertJsonPath('provinces.0.category', 'Kurang');
    }

    public function test_deleted_dataset_data_is_not_returned_by_the_public_map(): void
    {
        $province = Province::create(['code' => '33000', 'name' => 'Jawa Tengah']);
        $regency = Regency::create(['province_id' => $province->id, 'code' => '33001', 'name' => 'Kabupaten Semarang']);
        $import = $this->publishedImport(2025);
        SpmData::create(['import_history_id' => $import->id, 'regency_id' => $regency->id, 'year' => 2025, 'nilai_akhir' => 87]);
        $import->spmData()->delete();
        $import->delete();

        $this->getJson(route('peta.data', ['year' => 2025]))
            ->assertJsonPath('year', null)
            ->assertJsonPath('provinces.0.spm', null)
            ->assertJsonPath('provinces.0.regencies', []);
    }

    private function publishedImport(int $year): ImportHistory
    {
        return ImportHistory::create([
            'user_id' => User::factory()->create()->id,
            'file_name' => "published-{$year}.xlsx",
            'year' => $year,
            'status' => ImportHistory::Published,
        ]);
    }
}
