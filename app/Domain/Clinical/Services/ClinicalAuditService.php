<?php

namespace App\Domain\Clinical\Services;

use App\Models\ClinicalAuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ClinicalAuditService
{
    /**
     * Log a clinical event with sanitization of sensitive data.
     */
    public function log(string $event, Model $auditable, array $metadata = []): ClinicalAuditLog
    {
        $user = Auth::user();
        $role = $user ? ($user->roles->first()?->name ?? 'UNKNOWN') : 'SYSTEM';

        // Sanitize metadata - ensure sensitive clinical notes are not dumped in plain text
        $sanitizedMetadata = $this->sanitizeMetadata($metadata);

        return ClinicalAuditLog::create([
            'user_id' => $user?->id,
            'user_name' => $user?->name ?? 'Sistema Automático',
            'role' => $role,
            'event' => $event,
            'auditable_type' => get_class($auditable),
            'auditable_id' => $auditable->getKey(),
            'ip_address' => Request::ip() ?? '127.0.0.1',
            'user_agent' => substr(Request::userAgent() ?? 'CLI/System', 0, 255),
            'metadata' => $sanitizedMetadata,
            'created_at' => now(),
        ]);
    }

    protected function sanitizeMetadata(array $metadata): array
    {
        $filtered = [];
        foreach ($metadata as $key => $value) {
            if (in_array(strtolower($key), ['password', 'token', 'secret', 'credit_card'], true)) {
                continue;
            }
            if ($key === 'sensitive_fields') {
                $filtered['sensitive_field_codes'] = is_array($value) ? array_keys($value) : 'REDACTED_FOR_PRIVACY';
            } else {
                $filtered[$key] = $value;
            }
        }
        return $filtered;
    }
}
