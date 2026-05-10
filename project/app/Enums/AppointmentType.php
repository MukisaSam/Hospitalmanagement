<?php

namespace App\Enums;

enum AppointmentType: string
{
    case Opd = 'opd';
    case Ipd = 'ipd';
    case Emergency = 'emergency';
    case FollowUp = 'follow_up';
}
