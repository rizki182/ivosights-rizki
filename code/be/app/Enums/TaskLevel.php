<?php
namespace App\Enums;

enum TaskLevel: string {
    case URGENT = 'URGENT';
    case NORMAL = 'NORMAL';
    case LOW = 'LOW';
}