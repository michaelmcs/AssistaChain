<?php
namespace App\Services;
use App\Models\AsistenciaEmpleado;
use App\Models\Configuracion;
use Illuminate\Support\Facades\Log;

class BlockchainService
{
    public function lastHash(): string
    {
        return Configuracion::get('last_block_hash', 'GENESIS');
    }

    public function computeAttendanceHash(array $data, string $prevHash): string
    {
        $timestamp = strtotime($data['fecha']);
        return hash('sha256', 
            $data['id_empleado'] . 
            $data['nombre'] . 
            $data['estado'] . 
            $timestamp . 
            $prevHash .
            uniqid()
        );
    }

    public function updateLastHash(string $hash): void
    {
        Configuracion::set('last_block_hash', $hash);
    }
    public function verifyChainIntegrity(): array
    {
        try {
            Log::info('Iniciando verificación de integridad blockchain');
            
            $blocks = AsistenciaEmpleado::with('empleado')->orderBy('fecha', 'asc')->get();
            
            $results = [
                'total_blocks' => $blocks->count(),
                'valid_blocks' => 0,
                'invalid_blocks' => 0,
                'blocks' => []
            ];
            $previousHash = 'GENESIS';

            foreach ($blocks as $block) {
                $fecha = $block->fecha;
                if ($fecha instanceof \DateTime || $fecha instanceof \Carbon\Carbon) {
                    $fechaString = $fecha->format('Y-m-d H:i:s');
                } else {
                    $fechaString = (string) $fecha;
                }
                $currentHash = $block->hash_blockchain;
                $expectedHash = $this->computeAttendanceHash([
                    'id_empleado' => $block->id_empleado,
                    'nombre' => $block->empleado->nombre ?? 'Unknown',
                    'estado' => $block->estado,
                    'fecha' => $fechaString
                ], $previousHash);

                $isValid = ($currentHash === $expectedHash);
                
                if ($isValid) {
                    $results['valid_blocks']++;
                } else {
                    $results['invalid_blocks']++;
                }

                $results['blocks'][] = [
                    'block_id' => $block->id,
                    'empleado_nombre' => $block->empleado->nombre ?? 'N/A',
                    'current_hash' => $currentHash,
                    'expected_hash' => $expectedHash,
                    'is_valid' => $isValid,
                    'timestamp' => $fechaString
                ];

                $previousHash = $currentHash;
            }

            Log::info('Verificación completada: ' . $results['valid_blocks'] . '/' . $results['total_blocks'] . ' válidos');
            return $results;

        } catch (\Exception $e) {
            Log::error('Error en verifyChainIntegrity: ' . $e->getMessage());
            return [
                'total_blocks' => 0,
                'valid_blocks' => 0,
                'invalid_blocks' => 0,
                'blocks' => [],
                'error' => $e->getMessage()
            ];
        }
    }
}