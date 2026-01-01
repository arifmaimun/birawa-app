<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditObserver
{
    public function created(Model $model)
    {
        $this->log($model, 'created', [], $model->toArray());
    }

    public function updated(Model $model)
    {
        $this->log($model, 'updated', $model->getOriginal(), $model->getChanges());
    }

    public function deleted(Model $model)
    {
        $this->log($model, 'deleted', $model->toArray(), []);
    }

    protected function log(Model $model, string $action, array $old, array $new)
    {
        AuditLog::create([
            'user_id' => Auth::id(), // May be null for system actions
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'old_values' => $old,
            'new_values' => $new,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
