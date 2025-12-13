<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Workflow\Models\WorkflowStepInstance;
use App\Workflow\Support\Notifier;

class SendOverdueWorkflowReminders extends Command
{
    protected $signature = 'workflow:reminders:overdue';
    protected $description = 'Send overdue reminders for workflow steps past their SLA';

    public function handle(): int
    {
        $count = 0;
        $q = WorkflowStepInstance::query()
            ->with(['step.template'])
            ->whereIn('status', ['pending','in_progress','submitted','returned'])
            ->whereHas('step', function($q){ $q->whereNotNull('sla_hours')->where('sla_hours', '>', 0); });

        $overdue = $q->get()->filter(function($si){
            $slaHours = (int) ($si->step->sla_hours ?? 0);
            if ($slaHours <= 0) return false;
            $due = optional($si->created_at)->copy()->addHours($slaHours);
            return $due && $due->isPast();
        });

        foreach ($overdue as $si) {
            Notifier::stepEvent($si, 'overdue');
            $count++;
        }

        $this->info('Overdue reminders sent: '.$count);
        return Command::SUCCESS;
    }
}

