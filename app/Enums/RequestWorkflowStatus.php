<?php

namespace App\Enums;

enum RequestWorkflowStatus: string
{
	case Pending = 'pending';
	case Reviewed = 'reviewed';
	case Reimbursed = 'reimbursed';
	case Rejected = 'rejected';
	case ActionRequired = 'action_required';
}
