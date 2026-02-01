<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case VET = 'vet';
    case RECEPCIONISTA = 'recepcionista';
}
