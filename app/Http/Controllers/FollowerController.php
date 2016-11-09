<?php

namespace App\Http\Controllers;

use App\Models\Follower;
use App\Models\User;
use App\Response;

use App\Http\Requests;

class FollowerController extends Controller
{
    public function follow($id)
    {
        if ($errorMessage = User::checkForFollow($id)) {
            return Response::error($errorMessage);
        }

        $follower = Follower::whereIFollow($id)
            ->first();

        if (!$follower) {
            User::follow($id);
        } else {
            $follower->updateDeleted(false);
        }

        return Response::success();
    }

    public function unFollow($id)
    {
        if ($errorMessage = User::checkForFollow($id)) {
            return Response::error($errorMessage);
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
        $ids = Follower::getFollowerIds(User::myId());
        $users = User::searchUsers($ids);
        return Response::success($users);
    }

    public function getUserFollowers($id)
    {
        if (!User::isExists($id)) {
            return Response::error(trans('messages.not_found', ['item' => trans('model.user')]));
        }

        $ids = Follower::getFollowerIds($id);
        $users = User::searchUsers($ids);
        return Response::success($users);
    }

    public function getMyFollowed()
    {
        $ids = Follower::getFollowedIds(User::myId());
        $users = User::searchUsers($ids);
        return Response::success($users);
    }

    public function getUserFollowed($id)
    {
        if (!User::isExists($id)) {
            return Response::error(trans('messages.not_found', ['item' => trans('model.user')]));
        }

        $ids = Follower::getFollowedIds($id);
        $users = User::searchUsers($ids);
        return Response::success($users);
    }

}
