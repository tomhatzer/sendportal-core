<?php

namespace Sendportal\Base\Services\Workspaces;

use Exception;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;
use Sendportal\Base\Models\Invitation;
use Sendportal\Base\Models\User;
use Sendportal\Base\Models\Workspace;

class SendInvitation
{
    /**
     * @throws Exception
     */
    public function handle(Workspace $workspace, string $email): Invitation
    {
        $existingUser = User::where('email', $email)->first();

        $invitation = $this->createInvitation($workspace, $email, Workspace::ROLE_MEMBER, $existingUser);

        $this->emailInvitation($invitation);

        return $invitation;
    }

    protected function emailInvitation(Invitation $invitation): void
    {
        Mail::send(
            $this->getInvitationViewName($invitation),
            compact('invitation'),
            static function (Message $m) use ($invitation) {
                $m->to($invitation->email)->subject(__('New Invitation!'));
            }
        );
    }

    /**
     * @throws Exception
     */
    protected function createInvitation(Workspace $workspace, string $email, string $role, ?User $existingUser = null): Invitation
    {
        $invitationData = [
            'id' => Uuid::uuid4(),
            'user_id' => $existingUser->id ?? null,
            'role' => $role,
            'email' => $email,
            'token' => Str::random(40),
        ];

        return $workspace->invitations()->create($invitationData);
    }

    protected function getInvitationViewName(Invitation $invitation): string
    {
        return $invitation->user_id
            ? 'sendportal::workspaces.emails.invitation-to-existing-user'
            : 'sendportal::workspaces.emails.invitation-to-new-user';
    }
}
