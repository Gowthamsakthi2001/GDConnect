<?php

namespace App\Mail;

use Modules\Deliveryman\Entities\Deliveryman;
use App\Models\BusinessSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RecoveryAgentChanged extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Deliveryman $agent;
    public string $action; // 'assigned' or 'removed'
    public string $performedByName;
    public string $roleName;
    public string $footerContent;

    /**
     * Create a new message instance.
     *
     * @param Deliveryman $agent
     * @param string $action  // 'assigned' or 'removed'
     * @param string $performedByName
     * @param string $roleName
     */
    public function __construct(Deliveryman $agent, string $action, string $performedByName, string $roleName)
    {
        $this->agent = $agent->load(['current_city', 'zone']);
        $this->action = $action;
        $this->performedByName = $performedByName;
        $this->roleName = $roleName;

        // Fetch footer from settings; fallback to default
        $footerText = BusinessSetting::where('key_name', 'whatsapp_notify_footer')->value('value');
        $this->footerContent = $footerText ?? "For any assistance, please reach out to Admin Support.\nEmail: support@greendrivemobility.com\nThank you,\nGreenDriveConnect Team";
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subjectAction = $this->action === 'assigned' ? 'Assigned to Recovery Team' : 'Removed from Recovery Team';
        $subject = sprintf('Employee %s - %s', $this->agent->first_name . ' ' . ($this->agent->last_name ?? ''), $subjectAction);
        
        return $this->subject($subject)
                    ->view('email-templates.agent_team_changed')
                    ->text('email-templates.agent_team_changed_plain') // plain text fallback
                    ->with([
                        'agent' => $this->agent,
                        'action' => $this->action,
                        'performedByName' => $this->performedByName,
                        'roleName' => $this->roleName,
                        'footerContent' => $this->footerContent,
                    ]);
    }
}
