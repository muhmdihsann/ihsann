<?php

namespace Tests\Feature;

use App\Models\ImportHistory;
use App\Models\Province;
use App\Models\Regency;
use App\Models\SpmData;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AdminAreaDataManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_regency_edit_is_staged_as_draft_then_published_to_public_search(): void
    {
        [$province, $regency, $spmData] = $this->createPublishedRegencyData('KAB. BANYUMAS', 'JAWA TENGAH', 2025, 10);
        $operator = User::factory()->create();

        $this->actingAs($operator)
            ->get(route('regencies.index', ['search' => 'Banyumas', 'year' => 2025]))
            ->assertOk()
            ->assertSee('KAB. BANYUMAS')
            ->assertSee('10');

        $this->actingAs($operator)
            ->get(route('spm.edit', [
                'spmData' => $spmData->id,
                'search' => 'Banyumas',
                'year' => 2025,
                'return_route' => 'regencies.index',
            ]))
            ->assertOk()
            ->assertSee('name="jml_redkar"', false)
            ->assertSee('calculated-index', false)
            ->assertSee('data-index-dimension', false)
            ->assertDontSee('name="nilai_akhir"', false);

        $this->actingAs($operator)
            ->put(route('spm.update', $spmData), [
                ...$this->validEditData(9),
                'search' => 'Banyumas',
                'year' => 2025,
                'return_route' => 'regencies.index',
            ])
            ->assertRedirect(route('regencies.index', ['year' => 2025, 'search' => 'Banyumas']));

        $this->assertDatabaseHas('spm_data', [
            'id' => $spmData->id,
            'jml_redkar' => 9,
            'nilai_akhir' => 60,
            'kategori' => 'Cukup',
            'is_published' => false,
        ]);
        $this->get('/?year=2025')->assertDontSee('KAB. BANYUMAS');

        $this->actingAs($operator)
            ->patch(route('spm.publish', $spmData), [
                'search' => 'Banyumas',
                'year' => 2025,
                'return_route' => 'regencies.index',
            ])
            ->assertRedirect(route('regencies.index', ['year' => 2025, 'search' => 'Banyumas']));

        $this->assertDatabaseHas('spm_data', [
            'id' => $spmData->id,
            'jml_redkar' => 9,
            'nilai_akhir' => 60,
            'kategori' => 'Cukup',
            'is_published' => true,
        ]);
        $this->get('/?year=2025')
            ->assertSee('KAB. BANYUMAS')
            ->assertSee('"redkar":9', false);
    }

    public function test_province_can_be_edited_published_and_deleted_with_related_records(): void
    {
        [$province, $regency, $spmData] = $this->createPublishedRegencyData('KAB. BANYUMAS', 'JAWA TENGAH', 2025, 10, false);
        $operator = User::factory()->create();

        $this->actingAs($operator)
            ->get(route('provinces.index'))
            ->assertOk()
            ->assertSee('JAWA TENGAH')
            ->assertSee('Publish data');

        $this->actingAs($operator)
            ->get(route('provinces.edit', $province))
            ->assertOk()
            ->assertSee('Pos damkar')
            ->assertSee('KAB. BANYUMAS')
            ->assertSee('10');

        $this->actingAs($operator)
            ->put(route('provinces.update', $province), [
                'name' => 'JAWA TENGAH BARAT',
                'code' => $province->code,
            ])
            ->assertRedirect(route('provinces.index'));
        $this->assertDatabaseHas('provinces', ['id' => $province->id, 'name' => 'JAWA TENGAH BARAT']);

        $this->actingAs($operator)
            ->patch(route('provinces.publish', $province))
            ->assertRedirect(route('provinces.index'));
        $this->assertDatabaseHas('spm_data', ['id' => $spmData->id, 'is_published' => true]);

        $this->actingAs($operator)
            ->get(route('provinces.edit', ['province' => $province->id, 'year' => 2025]))
            ->assertOk()
            ->assertSee('Indeks SPM Provinsi')
            ->assertSee('Pos damkar')
            ->assertSee('KAB. BANYUMAS')
            ->assertSee('76,00');

        $this->actingAs($operator)
            ->delete(route('provinces.destroy', $province))
            ->assertRedirect(route('provinces.index'));

        $this->assertDatabaseMissing('provinces', ['id' => $province->id]);
        $this->assertDatabaseMissing('regencies', ['id' => $regency->id]);
        $this->assertDatabaseMissing('spm_data', ['id' => $spmData->id]);
    }

    public function test_data_from_a_draft_import_cannot_be_published_individually(): void
    {
        $province = Province::create(['name' => 'Jawa Tengah', 'code' => '33000']);
        $regency = Regency::create(['province_id' => $province->id, 'name' => 'KAB. BANYUMAS', 'code' => '33002']);
        $import = ImportHistory::create([
            'user_id' => User::factory()->create()->id,
            'file_name' => 'spm-2025.xlsx',
            'year' => 2025,
            'status' => ImportHistory::Draft,
        ]);
        $spmData = SpmData::create([
            'import_history_id' => $import->id,
            'regency_id' => $regency->id,
            'year' => 2025,
            'nilai_akhir' => 70,
            'kategori' => 'Baik',
            'is_published' => false,
        ]);

        $this->actingAs(User::factory()->create())
            ->patch(route('spm.publish', $spmData))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('spm_data', ['id' => $spmData->id, 'is_published' => false]);
    }

    public function test_recalculated_index_never_exceeds_one_hundred(): void
    {
        [, , $spmData] = $this->createPublishedRegencyData('KAB. BANYUMAS', 'JAWA TENGAH', 2025, 10);
        $operator = User::factory()->create();
        $data = $this->validEditData(10);
        $data['dimensi_kelembagaan'] = 20;
        $data['dimensi_perencanaan'] = 20;
        $data['dimensi_capaian'] = 20;
        $data['dimensi_sarpras'] = 10;
        $data['dimensi_sdm_sertifikat'] = 10;
        $data['dimensi_pemberdayaan'] = 20;

        $this->actingAs($operator)
            ->put(route('spm.update', $spmData), $data)
            ->assertRedirect();

        $this->assertDatabaseHas('spm_data', [
            'id' => $spmData->id,
            'nilai_akhir' => 100,
            'kategori' => 'Sangat Baik',
        ]);
    }

    /** @return array{0: Province, 1: Regency, 2: SpmData} */
    private function createPublishedRegencyData(string $regencyName, string $provinceName, int $year, int $redkarCount, bool $isPublished = true): array
    {
        $province = Province::create(['name' => $provinceName, 'code' => '33000']);
        $regency = Regency::create(['province_id' => $province->id, 'name' => $regencyName, 'code' => '33002']);
        $import = ImportHistory::create([
            'user_id' => User::factory()->create()->id,
            'file_name' => "spm-{$year}.xlsx",
            'year' => $year,
            'status' => ImportHistory::Published,
        ]);
        $spmData = SpmData::create([
            'import_history_id' => $import->id,
            'regency_id' => $regency->id,
            'year' => $year,
            'nilai_akhir' => 76,
            'kategori' => 'Baik',
            'jml_kecamatan' => 27,
            'jml_pos' => 4,
            'total_sdm' => 90,
            'sdm_sertifikat' => 24,
            'jml_desa' => 331,
            'jml_redkar' => $redkarCount,
            'pr_redkar' => 20,
            'is_published' => $isPublished,
        ]);

        return [$province, $regency, $spmData];
    }

    /** @return array<string, int|float|string> */
    private function validEditData(int $redkarCount): array
    {
        return [
            'nilai_akhir' => 76,
            'kategori' => 'Baik',
            'jml_kecamatan' => 27,
            'jml_pos' => 4,
            'pr_pos_kecamatan' => 50,
            'total_sdm' => 90,
            'sdm_sertifikat' => 24,
            'pr_sdm_sertifikat' => 25,
            'jml_desa' => 331,
            'jml_redkar' => $redkarCount,
            'pr_redkar' => 20,
            'dimensi_kelembagaan' => 10,
            'dimensi_perencanaan' => 10,
            'dimensi_capaian' => 10,
            'dimensi_sarpras' => 10,
            'dimensi_sdm_sertifikat' => 10,
            'dimensi_pemberdayaan' => 10,
        ];
    }
}
