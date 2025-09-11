<?php

namespace App\Components\enum;

enum RecognitionFunction: string
{
    case CREATION = 'creation';
    case DELETE_PENDING = 'deletionPending';
    case APPROVES = 'approval';
    case REJECTS = 'rejection';
    case SEARCH_ALL = 'searchAll';
    case SEARCH_BY_ID = 'searchById';
    case SEARCH_BY_DEPARTMENT = 'searchByDepartment';
    case SEARCH_HISTORY = 'searchHistory';
    case SEARCH_MEDIA = 'searchMedia';
}
