<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::routes(['middleware' => 'auth:sanctum']);

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int)$user->id === (int)$id;
});

Broadcast::channel('CreateEventNotification.{roles}', function (User $user, $roles) {
    return $user->hasRole($roles);
});
