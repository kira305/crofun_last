<?php
namespace App;
use App\User;
use App\Observer;
class Logger implements Observer
{
    public function update(User $user)
    {
        $state     = $user->getState();
        $username  = $user->usr_name;
        if ($state == User::LOGIN_SUCCESS) {
          
            echo  "User {$username} vừa online";
        }
    }
}