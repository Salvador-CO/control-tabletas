<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StaffController extends Controller
{
    public function index()
    {
        $staff     = Staff::with('location')->orderBy('full_name')->get();
        $locations = Location::orderBy('name')->get();
        return view('staff.index', compact('staff', 'locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name'   => 'required|string|max:200',
            'role'        => 'required|string|max:100',
            'location_id' => 'nullable|exists:locations,id',
            'notes'       => 'nullable|string|max:1000',
        ]);

        Staff::create($request->only('full_name', 'role', 'location_id', 'notes'));

        return redirect()->back()->with('success', 'Personal registrado correctamente.');
    }

    public function update(Request $request, Staff $staff)
    {
        $request->validate([
            'full_name'   => 'required|string|max:200',
            'role'        => 'required|string|max:100',
            'location_id' => 'nullable|exists:locations,id',
            'notes'       => 'nullable|string|max:1000',
        ]);

        $staff->update($request->only('full_name', 'role', 'location_id', 'notes'));

        return redirect()->back()->with('success', 'Personal actualizado correctamente.');
    }

    public function destroy(Staff $staff)
    {
        $staff->delete();
        return redirect()->back()->with('success', 'Personal eliminado.');
    }

    /* ──────────────────────────────────────────────────────────────
     *  IMPORTACIÓN MASIVA — paso 1: pre-validación (sin guardar)
     * ────────────────────────────────────────────────────────────── */
    public function importPreview(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:5120',
        ]);

        $rows = $this->parseFile($request->file('file'));

        if (empty($rows)) {
            return response()->json(['error' => 'El archivo está vacío o no tiene el formato correcto (columnas: Nombre, Sede, Cargo).'], 422);
        }

        $locations       = Location::all()->keyBy(fn($l) => $this->normalize($l->name));
        $existingNames   = Staff::all()->map(fn($s) => $this->normalize($s->full_name))->toArray();

        $toImport  = [];
        $skipped   = [];

        foreach ($rows as $i => $row) {
            $nombre = trim($row['nombre'] ?? $row['Nombre'] ?? $row['NOMBRE'] ?? '');
            $sede   = trim($row['sede']   ?? $row['Sede']   ?? $row['SEDE']   ?? '');
            $cargo  = trim($row['cargo']  ?? $row['Cargo']  ?? $row['CARGO']  ?? '');

            if (empty($nombre)) continue;

            // Detectar duplicado (nombre directo o invertido)
            $normalized         = $this->normalize($nombre);
            $normalizedInverted = $this->invertName($normalized);

            $isDuplicate = in_array($normalized, $existingNames)
                        || in_array($normalizedInverted, $existingNames);

            // Buscar sede
            $locationId   = null;
            $locationWarn = false;
            if (!empty($sede)) {
                $normSede = $this->normalize($sede);
                if (isset($locations[$normSede])) {
                    $locationId = $locations[$normSede]->id;
                } else {
                    $locationWarn = true; // sede no encontrada
                }
            }

            $entry = [
                'nombre'        => $nombre,
                'sede'          => $sede,
                'cargo'         => $cargo,
                'location_id'   => $locationId,
                'location_warn' => $locationWarn,
            ];

            if ($isDuplicate) {
                $entry['status'] = 'duplicate';
                $skipped[] = $entry;
            } else {
                $entry['status'] = $locationWarn ? 'warn' : 'ok';
                $toImport[] = $entry;
            }
        }

        return response()->json([
            'to_import' => $toImport,
            'skipped'   => $skipped,
        ]);
    }

    /* ──────────────────────────────────────────────────────────────
     *  IMPORTACIÓN MASIVA — paso 2: confirmar y guardar
     * ────────────────────────────────────────────────────────────── */
    public function importConfirm(Request $request)
    {
        $request->validate([
            'items'                => 'required|array|min:1',
            'items.*.nombre'       => 'required|string',
            'items.*.cargo'        => 'nullable|string',
            'items.*.location_id'  => 'nullable|integer|exists:locations,id',
        ]);

        $existingNames = Staff::all()->map(fn($s) => $this->normalize($s->full_name))->toArray();
        $inserted = 0;

        foreach ($request->items as $item) {
            $normalized  = $this->normalize($item['nombre']);
            $inverted    = $this->invertName($normalized);

            if (in_array($normalized, $existingNames) || in_array($inverted, $existingNames)) {
                continue; // doble verificación de seguridad
            }

            Staff::create([
                'full_name'   => $item['nombre'],
                'role'        => $item['cargo'] ?? '',
                'location_id' => $item['location_id'] ?? null,
            ]);

            $existingNames[] = $normalized; // evitar duplicados dentro del mismo archivo
            $inserted++;
        }

        return response()->json([
            'success' => true,
            'inserted' => $inserted,
            'message'  => "Se importaron {$inserted} personas correctamente.",
        ]);
    }

    /* ──────────────────────────────────────────────────────────────
     *  Helpers privados
     * ────────────────────────────────────────────────────────────── */

    /** Parsea CSV o XLSX y devuelve array asociativo con las filas */
    private function parseFile(\Illuminate\Http\UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if (in_array($extension, ['xlsx', 'xls'])) {
            return $this->parseExcel($file);
        }

        return $this->parseCsv($file);
    }

    private function parseCsv(\Illuminate\Http\UploadedFile $file): array
    {
        $path = $file->getRealPath();
        $handle = fopen($path, 'r');
        if (!$handle) return [];

        // Detectar delimitador
        $firstLine = fgets($handle);
        rewind($handle);
        $delimiter = str_contains($firstLine, ';') ? ';' : ',';

        $headers = null;
        $rows    = [];

        while (($line = fgetcsv($handle, 0, $delimiter)) !== false) {
            if ($headers === null) {
                $headers = array_map('trim', $line);
                continue;
            }
            if (count($line) < 1 || (count($line) === 1 && empty($line[0]))) continue;

            $row = [];
            foreach ($headers as $i => $h) {
                $row[mb_strtolower(trim($h))] = $line[$i] ?? '';
            }
            $rows[] = $row;
        }
        fclose($handle);
        return $rows;
    }

    private function parseExcel(\Illuminate\Http\UploadedFile $file): array
    {
        if (!class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
            return [];
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $data  = $sheet->toArray(null, true, true, false);

        if (empty($data)) return [];

        $headers = array_map(fn($h) => mb_strtolower(trim((string)$h)), array_shift($data));
        $rows = [];

        foreach ($data as $line) {
            if (empty(array_filter($line))) continue;
            $row = [];
            foreach ($headers as $i => $h) {
                $row[$h] = trim((string)($line[$i] ?? ''));
            }
            $rows[] = $row;
        }

        return $rows;
    }

    /** Normaliza un string: minúsculas, sin acentos, sin espacios dobles */
    private function normalize(string $str): string
    {
        $str = mb_strtolower(trim($str));
        $str = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $str);
        return preg_replace('/\s+/', ' ', $str);
    }

    /** Invierte "apellido nombre" → "nombre apellido" (split por primer espacio) */
    private function invertName(string $normalized): string
    {
        $parts = explode(' ', $normalized, 2);
        if (count($parts) === 2) {
            return $parts[1] . ' ' . $parts[0];
        }
        return $normalized;
    }
}
