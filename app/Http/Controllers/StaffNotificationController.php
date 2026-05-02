<?php

namespace App\Http\Controllers;

use App\Models\AlinetAppointment;
use App\Models\Feedback;
use App\Models\LiraRequest;

class StaffNotificationController extends Controller
{
    public function pendingCounts()
    {
        $newLiraCount = LiraRequest::where(function ($query) {
            $query->where('status', 'pending')->orWhereNull('status');
        })->count();

        $newAlinetCount = AlinetAppointment::where(function ($query) {
            $query->where('status', 'pending')->orWhereNull('status');
        })->count();

        $newFeedbackCount = Feedback::query()
            ->threads()
            ->where('status', 'open')
            ->count();

        $newFeedbackCommentCount = Feedback::query()
            ->replies()
            ->whereHas('parent', function ($query) {
                $query->threads()->where('status', 'open');
            })
            ->where(function ($query) {
                $query->whereNull('role')->orWhereNotIn('role', ['admin', 'librarian']);
            })
            ->count();

        $totalNewRequests = $newLiraCount + $newAlinetCount + $newFeedbackCount + $newFeedbackCommentCount;

        return response()->json([
            'newLiraCount' => $newLiraCount,
            'newAlinetCount' => $newAlinetCount,
            'newFeedbackCount' => $newFeedbackCount,
            'newFeedbackCommentCount' => $newFeedbackCommentCount,
            'totalNewRequests' => $totalNewRequests,
        ]);
    }
}