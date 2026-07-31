<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Device;
use App\Models\Location;
use App\Models\Staff;
use App\Models\Event;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Categorías ──────────────────────────────────────
        $tabletCat  = Category::firstOrCreate(['name' => 'Tableta'],       ['description' => 'Tabletas y iPad']);
        $phoneCat   = Category::firstOrCreate(['name' => 'Celular'],       ['description' => 'Teléfonos celulares']);
        $laptopCat  = Category::firstOrCreate(['name' => 'Laptop'],        ['description' => 'Computadoras portátiles']);

        // ── Sedes / Ubicaciones ───────────────────────────────
        $sedes = [
            ['name' => 'Plantel Tuxtla Gutiérrez',  'state' => 'Tuxtla Gutiérrez'],
            ['name' => 'Plantel San Cristóbal',      'state' => 'San Cristóbal de las Casas'],
            ['name' => 'Plantel Tapachula',          'state' => 'Tapachula'],
            ['name' => 'Plantel Comitán',            'state' => 'Comitán de Domínguez'],
            ['name' => 'Plantel Ocosingo',           'state' => 'Ocosingo'],
        ];
        foreach ($sedes as $sede) {
            Location::firstOrCreate(['name' => $sede['name']], $sede);
        }

        // ── Personal ──────────────────────────────────────────
        $sedeList = Location::all();
        $personalData = [
            ['full_name' => 'MARCELA PEÑA ORDOÑEZ',     'role' => 'Responsable de Entrega',     'location_id' => null],
            ['full_name' => 'JUAN PÉREZ HERNÁNDEZ',      'role' => 'Coordinador Académico',       'location_id' => $sedeList->first()?->id],
            ['full_name' => 'MARÍA LÓPEZ GÓMEZ',         'role' => 'Coordinadora Académica',      'location_id' => $sedeList->get(1)?->id],
            ['full_name' => 'CARLOS RUIZ DOMÍNGUEZ',     'role' => 'Coordinador Académico',       'location_id' => $sedeList->get(2)?->id],
            ['full_name' => 'ANA TORRES MÉNDEZ',         'role' => 'Subdirectora Académica',      'location_id' => $sedeList->get(3)?->id],
            ['full_name' => 'ROBERTO DÍAZ VELÁSQUEZ',    'role' => 'Coordinador Académico',       'location_id' => $sedeList->get(4)?->id],
        ];
        foreach ($personalData as $p) {
            Staff::firstOrCreate(['full_name' => $p['full_name']], $p);
        }

        // ── Tabletas (15 dispositivos de ejemplo) ─────────────
        $tabletas = [
            ['brand' => 'XIAOMI', 'model' => 'Pad 6', 'serial_number' => 'XM2023P6-001', 'charger_details' => 'Cargador punta',    'status' => 'disponible'],
            ['brand' => 'XIAOMI', 'model' => 'Pad 6', 'serial_number' => 'XM2023P6-002', 'charger_details' => 'Cargador punta',    'status' => 'disponible'],
            ['brand' => 'XIAOMI', 'model' => 'Pad 6', 'serial_number' => 'XM2023P6-003', 'charger_details' => 'Cargador maestro', 'status' => 'disponible'],
            ['brand' => 'XIAOMI', 'model' => 'Pad 6', 'serial_number' => 'XM2023P6-004', 'charger_details' => 'Cargador maestro', 'status' => 'disponible'],
            ['brand' => 'XIAOMI', 'model' => 'Pad 6', 'serial_number' => 'XM2023P6-005', 'charger_details' => 'Cargador punta',    'status' => 'disponible'],
            ['brand' => 'XIAOMI', 'model' => 'Pad 6', 'serial_number' => 'XM2023P6-006', 'charger_details' => 'Cargador punta',    'status' => 'disponible'],
            ['brand' => 'XIAOMI', 'model' => 'Pad 6', 'serial_number' => 'XM2023P6-007', 'charger_details' => null,               'status' => 'disponible'],
            ['brand' => 'XIAOMI', 'model' => 'Pad 6', 'serial_number' => 'XM2023P6-008', 'charger_details' => 'Cargador maestro', 'status' => 'disponible'],
            ['brand' => 'XIAOMI', 'model' => 'Pad 6', 'serial_number' => 'XM2023P6-009', 'charger_details' => 'Cargador punta',    'status' => 'disponible'],
            ['brand' => 'XIAOMI', 'model' => 'Pad 6', 'serial_number' => 'XM2023P6-010', 'charger_details' => 'Cargador punta',    'status' => 'disponible'],
            ['brand' => 'XIAOMI', 'model' => 'Pad 6', 'serial_number' => 'XM2023P6-011', 'charger_details' => null,               'status' => 'asignado_fijo'],
            ['brand' => 'XIAOMI', 'model' => 'Pad 6', 'serial_number' => 'XM2023P6-012', 'charger_details' => 'Cargador maestro', 'status' => 'asignado_fijo'],
            ['brand' => 'XIAOMI', 'model' => 'Pad 5', 'serial_number' => 'XM2022P5-001', 'charger_details' => 'Cargador punta',    'status' => 'disponible'],
            ['brand' => 'XIAOMI', 'model' => 'Pad 5', 'serial_number' => 'XM2022P5-002', 'charger_details' => null,               'status' => 'mantenimiento'],
            ['brand' => 'SAMSUNG','model' => 'Tab A8', 'serial_number' => 'SAM2023A8-001','charger_details' => 'Cargador Samsung', 'status' => 'disponible'],
        ];
        foreach ($tabletas as $t) {
            Device::firstOrCreate(
                ['serial_number' => $t['serial_number']],
                array_merge($t, ['category_id' => $tabletCat->id, 'is_charged' => true])
            );
        }

        // ── Evento Exacer de ejemplo ──────────────────────────
        Event::firstOrCreate(
            ['name' => 'Exacer 2025-I'],
            ['start_date' => '2025-03-01', 'end_date' => '2025-03-15']
        );
        Event::firstOrCreate(
            ['name' => 'Exacer 2025-II'],
            ['start_date' => '2025-06-01', 'end_date' => '2025-06-15']
        );
    }
}
