<?php

namespace App\Enums;

/**
 * Application roles used by the Pending Requests module (subset of stored user.role).
 */
enum AppUserRole: string
{
	case Admin = 'admin';
	case Employee = 'employee';
}
