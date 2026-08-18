<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Device;
use App\Models\DeviceTelemetry;
use App\Models\DeviceCommandLog;
use App\Models\UnregisteredDevice;

class TelemetryController extends Controller
{
    public function sync(Request $request)
    {
        $request->validate([
            'serial_number'    => 'required|string',
            'battery'          => 'nullable|integer|min:0|max:100',
            'lat'              => 'nullable|numeric',
            'lng'              => 'nullable|numeric',
            'wallpaper_id'     => 'nullable|string',
            'device_model'     => 'nullable|string',
            // Nuevos campos de telemetría
            'android_version'  => 'nullable|string|max:20',
            'app_version'      => 'nullable|string|max:20',
            'wifi_ssid'        => 'nullable|string|max:100',
            'ip_address'       => 'nullable|string|max:45',
            'is_charging'      => 'nullable|boolean',
            // Si el dispositivo reporta que ejecutó un comando
            'command_executed' => 'nullable|string',
        ]);

        $serial = $request->input('serial_number');

        // Buscar por serial principal O por serial reportado (vinculación de caja vs. dispositivo)
        $device = Device::where('serial_number', $serial)
                        ->orWhere('device_reported_serial', $serial)
                        ->first();

        if ($device) {
            // Si el device_reported_serial estaba vacío, lo asignamos automáticamente
            // (primera vez que se conecta, se auto-vincula el serial del dispositivo)
            if (empty($device->device_reported_serial) && $device->serial_number !== $serial) {
                $device->update(['device_reported_serial' => $serial]);
            }

            // Actualizar telemetría
            $telemetry = DeviceTelemetry::updateOrCreate(
                ['device_id' => $device->id],
                [
                    'battery_level'   => $request->input('battery'),
                    'latitude'        => $request->input('lat'),
                    'longitude'       => $request->input('lng'),
                    'current_wallpaper' => $request->input('wallpaper_id'),
                    'last_sync_at'    => now(),
                    'android_version' => $request->input('android_version'),
                    'app_version'     => $request->input('app_version'),
                    'wifi_ssid'       => $request->input('wifi_ssid'),
                    'ip_address'      => $request->input('ip_address'),
                    'is_charging'     => $request->input('is_charging'),
                ]
            );

            // Registrar ejecución de comando si el dispositivo reporta que lo ejecutó
            if ($request->filled('command_executed')) {
                DeviceCommandLog::where('device_id', $device->id)
                    ->where('command', $request->input('command_executed'))
                    ->whereNull('executed_at')
                    ->latest('sent_at')
                    ->first()
                    ?->update(['executed_at' => now()]);
            }

            // Eliminar de unregistered_devices si estaba allí por error previo
            UnregisteredDevice::where('reported_serial', $serial)->delete();

            $response = [
                'status'           => 'success',
                'message'          => 'Telemetría actualizada',
                'target_wallpaper' => $telemetry->target_wallpaper,
            ];

            // Enviar comando pendiente si existe
            if ($telemetry->pending_command) {
                $response['pending_command'] = $telemetry->pending_command;
                $telemetry->update(['pending_command' => null]);
            }

            // Enviar mensaje masivo pendiente si existe
            if ($telemetry->pending_message) {
                $response['pending_message'] = $telemetry->pending_message;
                $telemetry->update(['pending_message' => null]);
            }

            return response()->json($response);

        } else {
            // Dispositivo no registrado — guardar como intruso
            UnregisteredDevice::updateOrCreate(
                ['reported_serial' => $serial],
                [
                    'device_model' => $request->input('device_model'),
                    'battery_level' => $request->input('battery'),
                    'last_sync_at'  => now(),
                ]
            );

            return response()->json([
                'status'  => 'unregistered',
                'message' => 'Dispositivo no encontrado en el inventario. Registrado como pendiente.',
            ], 404);
        }
    }
}
