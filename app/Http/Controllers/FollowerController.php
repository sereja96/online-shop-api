<?php

namespace App\Http\Controllers;

use App\Models\Follower;
use App\Models\User;
use App\Response;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;

class FollowerController extends Controller
{
    public function follow($id)
    {
        $follower = Follower::whereIFollow($id)
            ->first();

        if (!$follower) {
            if ($errorMessage = Auth::user()->follow($id)) {
                return Response::error($errorMessage);
            }
        } elseif ($follower->isDeleted()) {
            $follower->updateDeleted(false);
        }

        return Response::success();
    }

    public function unFollow($id)
    {
        if (User::isMyId($id)) {
            return Response::error('access_deny');
        }

        $follower = Follower::whereIFollow($id)
            ->notDeleted()
            ->first();

        if ($follower) {
            $follower->updateDeleted(true);
        }

        return Response::success();
    }

    public function getMyFollowers()
    {
        $ids = Auth::user()->followersIds();
        $users = $this->getUsersByIds($ids);
        return Response::success($users);
    }

    public function getUserFollowers($id)
    {
        if (!$user = User::find($id)) {
            return Response::error(trans('messages.not_found', ['item' => trans('model.user')]));
        }

        $ids = $user->followersIds();
        $users = $this->getUsersByIds($ids);
        return Response::success($users);
    }

    public function getMyFollowed()
    {
        $ids = Auth::user()->followedIds();
        $users = $this->getUsersByIds($ids);
        return Response::success($users);
    }

    public function getUserFollowed($id)
    {
        if (!$user = User::find($id)) {
            return Response::error(trans('messages.not_found', ['item' => trans('model.user')]));
        }

        $ids = $user->followedIds();
        $users = $this->getUsersByIds($ids);
        return Response::success($users);
    }

    private function getUsersByIds($ids)
    {
        $userController = new UserController();
        return $userController->searchUsers($ids);
    }

}
