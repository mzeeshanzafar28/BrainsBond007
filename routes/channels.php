<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


Broadcast::channel('employee.{id}', function ($user, $id) {
    // Check if the user has permission to listen on this channel
    return (int) $user->id === (int) $id;
});
