<?php

namespace App\Observers;


use App\Models\Field;
use App\Models\FieldHistory;
use Illuminate\Support\Facades\Auth;

class FieldObserver
{
    /**
     * Handle the Field "created" event.
     */
   public function created(Field $field): void
    {
        $this->logChange($field, 'created', null, 'Field Initialized');
    }

    /**
     * Handle the Field "updated" event.
     */
 public function updated(Field $field): void
    {
        $monitoredFields = ['current_stage', 'notes', 'agent_id', 'name'];
        foreach ($monitoredFields as $attribute) {
            if ($field->isDirty($attribute)) {
                $this->logChange($field, $attribute, $field->getOriginal($attribute), $field->$attribute);
            }
        }
    }
    /**
     * Handle the Field "deleted" event.
     */
   public function deleted(Field $field): void
    {
        // This triggers on soft delete
        $this->logChange($field, 'deleted', 'Active', 'Soft Deleted');
    }

    /**
     * Handle the Field "restored" event.
     */
    public function restored(Field $field): void
    {
        //
    }

    /**
     * Handle the Field "force deleted" event.
     */
    public function forceDeleted(Field $field): void
    {
        //
    }

   private function logChange($field, $fieldChanged, $old, $new)
{
    \App\Models\FieldHistory::create([
        'field_id' => $field->id,
        // If Auth::id() is null, this will now save as null instead of crashing
        'user_id' => \Illuminate\Support\Facades\Auth::id(), 
        'field_changed' => $fieldChanged,
        'old_value' => $old,
        'new_value' => $new,
    ]);
}
}