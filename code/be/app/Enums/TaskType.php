<?php
namespace App\Enums;

enum TaskType: string {
    case ONE_TIME = 'ONE TIME';
    case DAILY = 'DAILY';
}