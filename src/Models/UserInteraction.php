<?php

namespace Koneko\VuexyAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Koneko\VuexyAdmin\Models\User;
use Koneko\VuexyAdmin\Application\Enums\UserInteractions\InteractionSecurityLevel;
use Koneko\VuexyAdmin\Support\Traits\Audit\{HasEscalator,HasFlagler,HasReviewer,HasUser};
use Koneko\VuexyAdmin\Support\Traits\Model\HasVuexyModelMetadata;

/**
 * Modelo de auditoría avanzada de interacciones de usuario.
 *
 * Esta entidad captura acciones de componentes UI o backend, con metadatos de seguridad,
 * flags del usuario, roles activos, y permite seguimiento administrativo estructurado.
 */
class UserInteraction extends Model
{
    use HasVuexyModelMetadata;
    use HasUser,
        HasReviewer,
        HasFlagler,
        HasEscalator;

    // ===================== METADATOS =====================

    public string $sortColumn        = 'id';
    public string $defaultSortOrder  = 'desc';
    public string $singularName      = 'interacción';
    public string $focusColumnOnOpen = 'module';

    // ===================== ATRIBUTOS BASE =====================

    protected $fillable = [
        'module',
        'user_id',
        'livewire_component',
        'action',
        'security_level',
        'ip_address',
        'user_agent',
        'context',
        'user_flags',
        'user_roles',
        'notes',
        'chat_thread',
        'is_reviewed',
        'is_flagged',
        'is_escalated',
        'reviewed_by',
        'flagged_by',
        'escalated_by',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'security_level'  => InteractionSecurityLevel::class,
        'context'         => 'array',
        'user_flags'      => 'array',
        'user_roles'      => 'array',
        'chat_thread'     => 'array',
        'is_reviewed'     => 'boolean',
        'is_flagged'      => 'boolean',
        'is_escalated'    => 'boolean',
    ];

    // ===================== RELATIONS =====================

    public function escalator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'escalated_by');
    }

    // ===================== MÉTODOS DE AUDITORÍA =====================

    /**
     * Agrega un nuevo mensaje al historial de seguimiento.
     * Este historial es inmutable: no se puede borrar ni editar.
     */
    public function addChatMessage(string $message, ?int $userId = null, string $reason = 'note'): void
    {
        if(!$userId){
            $userId = Auth::user()->id;
        }

        $chat = $this->chat_thread ?? [];

        $chat[] = [
            'user_id' => $userId,
            'msg'     => $message,
            'reason'  => $reason,
            'at'      => now()->toDateTimeString(),
        ];

        $this->chat_thread = $chat;
        $this->save();
    }

    /**
     * Marca esta interacción como "flagged" y registra el motivo.
     */
    public function flag(string $reason, ?int $adminId = null): void
    {
        if(!$adminId){
            $adminId = Auth::user()->id;
        }

        $this->is_flagged  = true;
        $this->flagged_by  = $adminId;
        $this->addChatMessage($reason, $adminId, 'flagged');
        $this->save();
    }

    /**
     * Marca esta interacción como "reviewed" y anexa comentario.
     */
    public function review(string $comment, ?int $adminId = null): void
    {
        if(!$adminId){
            $adminId = Auth::user()->id;
        }

        $this->is_reviewed  = true;
        $this->reviewed_by  = $adminId;
        $this->addChatMessage($comment, $adminId, 'reviewed');
        $this->save();
    }

    /**
     * Marca esta interacción como "escalated" para seguimiento mayor.
     */
    public function escalate(string $reason, ?int $adminId = null): void
    {
        if(!$adminId){
            $adminId = Auth::user()->id;
        }

        $this->is_escalated  = true;
        $this->escalated_by  = $adminId;
        $this->addChatMessage($reason, $adminId, 'escalated');
        $this->save();
    }

    // ===================== BOOT PROTEGIDO =====================

    protected static function boot()
    {
        parent::boot();

        // Impedir modificación de campos críticos del evento original
        static::updating(function (self $model) {
            $blocked = ['user_id', 'ip_address', 'actor_type', 'component', 'action', 'security_level'];
            foreach ($blocked as $field) {
                if ($model->isDirty($field)) {
                    throw new \Exception("⚠️ El campo '{$field}' no puede modificarse después del registro.");
                }
            }
        });
    }
}
