<?php
namespace App\Enums;

enum TaskStatus: string {
    case OPEN = 'OPEN';
    case DONE = 'DONE';
}