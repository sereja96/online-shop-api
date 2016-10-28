<?php

namespace App\Http\Controllers;

use App\Follower;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;

class FollowerController extends Controller
{
    public static function follow($id)
    {
        if ($id == Auth::user()->id) {
            return response()->json([
                'status' => 'error',
                'message' => trans('messages.follow_your_self')
            ], 200);
        }

        $user = User::with('userSettings')->find($id);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => trans(
                    'messages.not_found',
                    ['item' => trans('model.user')]
                )
            ], 200);
        }

        $follower = Follower::where('user_id', $id)
            ->where('follower_user_id', Auth::user()->id)
            ->first();

        if ($follower) {
            $follower->status = $user->userSettings->public_user ? 'submit' : 'request';
        } else {
            $follower = new Follower([
                'user_id' => $user->id,
                'follower_user_id' => Auth::user()->id,
                'status' => $user->userSettings->public_user ? 'submit' : 'request'
            ]);
        }

        if ($follower->saveOrFail()) {
            return response()->json([
                'status' => 'success',
                'data' => $follower->status
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => trans('messages.unknown_error')
        ], 200);
    }

    public static function unFollow($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => trans('messages.not_found', ['item' => trans('model.user')])
            ], 200);
        }

        $follower = Follower::where('user_id', $id)
            ->where('follower_user_id', Auth::user()->id)
            ->first();

        if ($follower) {
            $follower->delete();
        }
        return response()->json(['status' => 'success'], 200);
    }

    private static function setFollowerStatus($id, $status)
    {
        if ($id == Auth::user()->id) {
            return response()->json([
                'status' => 'error',
                'message' => trans('messages.follow_your_self')
            ], 200);
        }

        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => trans('messages.not_found', ['item' => trans('model.user')])
            ], 200);
        }

        $follower = Follower::where('user_id', Auth::user()->id)
            ->where('follower_user_id', $id)
            ->first();

        if (!$follower) {
            return response()->json([
                'status' => 'error',
                'message' => trans('messages.request_not_found')
            ], 200);
        }

        $follower->status = $status;
        if ($follower->saveOrFail()) {
            return response()->json([
                'status' => 'success',
                'data' => $follower->status
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => trans('messages.unknown_error')
        ], 200);
    }

    public static function decline($id)
    {
        return self::setFollowerStatus($id, 'decline');
    }

    public static function confirm($id)
    {
        return self::setFollowerStatus($id, 'submit');
    }

    private static function findFollowers($userId,
                                          $searchField,
                                          $secondField,
                                          $isShowStatus = false)
    {
        $followerIds = Follower::where($searchField, $userId)
            ->select(
                "$secondField AS id",
                "status")
            ->orderBy('status')
            ->get();

        if ($followerIds) {
            $ids = [];
            $statuses = [];
            foreach ($followerIds as $value) {
                array_push($ids, $value->id);
                $statuses[$value->id] = $value->status;
            }

            $followers = User::with(['image', 'countWish'])
                ->whereIn('id', $ids)
                ->where('is_deleted', false)
                ->where('is_enable', true)
                ->get();

            if ($followers) {
                $usersIFollowed = Follower::where('follower_user_id', Auth::user()->id)
                    ->whereIn('user_id', $ids)
                    ->select('user_id AS id', 'status')
                    ->get();

                foreach ($followers as $key => $follower)
                {
                    $follower->my_follow_status = null;
                    foreach ($usersIFollowed as $value)
                    {
                        if ($follower->id == $value->id) {
                            $follower->my_follow_status = $value->status;
                            break;
                        }
                    }

                    if ($isShowStatus) {
                        $follower->status = $statuses[$follower->id];
                        $followers[$key] = $follower;
                    }
                }

            } else {
                $followers = [];
            }

            return response()->json([
                'status' => 'success',
                'data' => $followers,
            ], 200);
        } else {
            return response()->json([
                'status' => 'success',
                'data' => [],
            ], 200);
        }
    }

    public static function getMyFollowers()
    {
        return FollowerController::findFollowers(
            Auth::user()->id,
            'user_id',
            'follower_user_id',
            true
        );
    }

    public static function getUserFollowers($id)
    {
        return FollowerController::findFollowers(
            $id,
            'user_id',
            'follower_user_id'
        );
    }

    public static function getMyFollowed()
    {
        return FollowerController::findFollowers(
            Auth::user()->id,
            'follower_user_id',
            'user_id'
        );
    }

    public static function getUserFollowed($id)
    {
        return FollowerController::findFollowers(
            $id,
            'follower_user_id',
            'user_id'
        );
    }

    public static function getFriendRequestCount()
    {
        $count = Follower::where('user_id', Auth::user()->id)
            ->where('status', 'request')
            ->count();

        return response()->json([
            'status' => 'success',
            'data' => $count
        ], 200);
    }
}
