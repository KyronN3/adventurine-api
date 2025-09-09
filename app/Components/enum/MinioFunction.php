<?php

namespace App\Components\enum;

enum MinioFunction: string
{
    case FETCH_BY_FILENAME = 'fetchByFileName';
    case DELETE_BY_FILENAME = 'deleteByFileName';
    case DELETE_BATCH = 'deleteBatch';

}
