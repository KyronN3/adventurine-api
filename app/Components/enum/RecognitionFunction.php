<?php

namespace App\Components\enum;

enum RecognitionFunction: string
{
    case CREATION = 'creation';
    case DELETION = 'deletion';
    case DELETE_PENDING = 'deletionPending';
    case APPROVAL = 'approval';
    case REJECTION = 'rejection';
    case SEARCH_ALL = 'searchAll';
    case SEARCH_BY_ID = 'searchById';
    case SEARCH_BY_DEPARTMENT = 'searchByDepartment';
}
