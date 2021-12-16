<?php

namespace App\Listeners\Users;

use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Notification;

use App\Events\Users\UserRegistered;
use App\Events\Users\UserActivated;
use App\Events\Users\UserChangePassword;

use App\Notifications\Users\UserRegisteredNotification;
use App\Notifications\Users\UserActivatedNotification;
use App\Notifications\Users\UserChangePasswordNotification;

class UserEventSubscriber implements ShouldQueue
{
    /**
     * Handle user register events.
     * @param UserRegistered $event
     */
    public function onUserRegister(UserRegistered $event)
    {
        $event->user->notify(new UserRegisteredNotification($event->content));
    }

    /**
     * Handle user activated events.
     * @param UserActivated $event
     */
    public function onUserActivated(UserActivated $event)
    {
        $event->user->notify(new UserActivatedNotification($event->content));
    }

    /**
     * Handle user change password events.
     * @param UserChangePassword $event
     */
    public function onUserChangePassword(UserChangePassword $event)
    {
        $event->user->notify(new UserChangePasswordNotification($event->content));
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen(
            'App\Events\Users\UserRegistered',
            'App\Listeners\Users\UserEventSubscriber@onUserRegister'
        );

        $events->listen(
            'App\Events\Users\UserActivated',
            'App\Listeners\Users\UserEventSubscriber@onUserActivated'
        );

        $events->listen(
            'App\Events\Users\UserChangePassword',
            'App\Listeners\Users\UserEventSubscriber@onUserChangePassword'
        );
    }
}