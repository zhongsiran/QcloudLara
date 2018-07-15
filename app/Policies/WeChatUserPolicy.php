<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class WeChatUserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function serve($slaic_openid)
    {
        $current_user = User::where('slaic_openid', '=', $slaic_openid)->first();
        return True;
    }
}
