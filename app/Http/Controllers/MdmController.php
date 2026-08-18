<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Device;
use App\Models\DeviceTelemetry;
use App\Models\DeviceCommandLog;
use App\Models\UnregisteredDevice;

class MdmController extends Controller
{
    public function index()
    {
        $devices = Device::with(['telemetry', 'commandLogs' => function ($q) {
            $q->latest('sent_at')->limit(3);
        }])->get()->sortByDesc(function ($device) {
            return $device->telemetry ? $device->telemetry->last_sync_at : null;
        });

        $unregistered = UnregisteredDevice::orderBy('last_sync_at', 'desc')->get();

        // KPIs
        $totalDevices  = $devices->count();
        $onlineDevices = $devices->filter(function ($d) {
            return $d->telemetry && $d->telemetry->last_sync_at
                && $d->telemetry->last_sync_at->diffInMinutes(now()) <= 5;
        })->count();
        $avgBattery = $devices->filter(fn($d) => $d->telemetry && $d->telemetry->battery_level !== null)
            ->avg(fn($d) => $d->telemetry->battery_level);
        $pendingCommands = DeviceTelemetry::whereNotNull('pending_command')->count();
        $pendingMessages = DeviceTelemetry::whereNotNull('pending_message')->count();

        // Historial reciente de comandos (global, últimos 20)
        $commandHistory = DeviceCommandLog::with('device')
            ->latest('sent_at')
            ->limit(20)
            ->get();

        return view('mdm.index', compact(
            'devices', 'unregistered',
            'totalDevices', 'onlineDevices', 'avgBattery',
            'pendingCommands', 'pendingMessages', 'commandHistory'
        ));
    }

    public function setGlobalWallpaper(Request $request)
    {
        $request->validate(['wallpaper_url' => 'required|url']);

        DeviceTelemetry::query()->update([
            'target_wallpaper' => $request->input('wallpaper_url')
        ]);

        return back()->with('success', 'Fondo de pantalla global actualizado. Las tablets lo aplicarán en su próxima sincronización.');
    }

    public function sendCommand(Request $request, Device $device)
    {
        $request->validate(['command' => 'required|string']);

        $telemetry = $device->telemetry;
        if ($telemetry) {
            $command = $request->input('command');
            $telemetry->update(['pending_command' => $command]);

            // Registrar en historial
            DeviceCommandLog::create([
                'device_id' => $device->id,
                'command'   => $command,
                'sent_by'   => auth()->user()->name ?? 'Sistema',
                'sent_at'   => now(),
            ]);

            $labels = [
                'lock_device'  => 'Bloquear dispositivo',
                'open_camera'  => 'Abrir cámara',
                'reboot'       => 'Reiniciar',
                'clear_cache'  => 'Limpiar caché',
            ];
            $label = $labels[$command] ?? $command;

            return back()->with('success', "Comando «{$label}» enviado a {$device->model}. Se ejecutará en la próxima sincronización.");
        }

        return back()->with('error', 'El dispositivo no tiene telemetría registrada.');
    }

    /**
     * Enviar un mensaje masivo a todas las tabletas conectadas.
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $message = $request->input('message');

        // Actualizar TODAS las telemetrías activas con el mensaje pendiente
        $count = DeviceTelemetry::count();
        DeviceTelemetry::query()->update(['pending_message' => $message]);

        // Registrar en historial como comando de tipo "message"
        Device::whereHas('telemetry')->each(function ($device) use ($message) {
            DeviceCommandLog::create([
                'device_id' => $device->id,
                'command'   => 'send_message',
                'payload'   => $message,
                'sent_by'   => auth()->user()->name ?? 'Sistema',
                'sent_at'   => now(),
            ]);
        });

        return back()->with('success', "Mensaje enviado a {$count} dispositivo(s). Se mostrará en la próxima sincronización.");
    }

    /**
     * Limpiar mensaje pendiente de todos los dispositivos.
     */
    public function clearMessage()
    {
        DeviceTelemetry::query()->update(['pending_message' => null]);
        return back()->with('success', 'Mensaje masivo cancelado en todos los dispositivos.');
    }

    /**
     * Vincular el serial reportado por el dispositivo con un dispositivo existente.
     */
    public function linkSerial(Request $request, Device $device)
    {
        $request->validate([
            'reported_serial' => 'required|string',
        ]);

        $reportedSerial = $request->input('reported_serial');

        // Evitar duplicados en otros dispositivos
        $conflict = Device::where('device_reported_serial', $reportedSerial)
            ->where('id', '!=', $device->id)
            ->first();

        if ($conflict) {
            return back()->with('error', "El serial «{$reportedSerial}» ya está vinculado al dispositivo {$conflict->model}.");
        }

        $device->update(['device_reported_serial' => $reportedSerial]);

        // Ahora que está vinculado, eliminar el intruso de la lista
        UnregisteredDevice::where('reported_serial', $reportedSerial)->delete();

        return back()->with('success', "Serial del dispositivo vinculado correctamente a {$device->model}.");
    }
}
