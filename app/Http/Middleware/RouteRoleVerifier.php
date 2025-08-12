<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RouteRoleVerifier
{
    /**
     * Handle an incoming request. ❤️❤️❤️
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!Auth::check()) {
            return response()->json(array_merge(
                json_decode($response->content(), true), [
                'authentication' => [
                    'success' => false,
                    'message' => 'Unauthenticated.',
                ],
            ]), 401);
        }

        $user = Auth::user();
        if ($user->hasRole('hr')) {

            $capture_original = json_decode($response->content(), true);
            return response()->json(array_merge($capture_original, [
                'role' => $user->roles->pluck('name'),
                'routes' => [
                    'path' => '/',
                    'component' => 'src/layouts/HR-pageMain.vue',
                    'children' => [
                        ['path' => '/', 'component' => '/src/pages/dashboard/HR-Dashboard.vue', 'name' => 'dashboard',],
//                        ['path' => '/create-event', 'component' => '/src/pages/trainingEvents/CreateEventPage.vue', 'name' => 'createEvent',],
                        ['path' => '/past-events', 'component' => '/src/pages/trainingEvents/PastEventsPage.vue', 'name' => 'pastEvents',],
                        ['path' => '/events/:id', 'component' => '/src/pages/trainingEvents/EventDetail.vue', 'name' => 'eventDetail',],
                        ['path' => '/bpm-archive', 'component' => '/src/pages/bloodPressure/BPMArchivePage.vue', 'name' => 'bpmArchive',],
                        ['path' => '/pending-recognitions', 'component' => '/src/pages/recognition/PendingRecognitionsPage.vue', 'name' => 'pendingRecognitions',],
                        ['path' => '/recognition-history', 'component' => '/src/pages/recognition/RecognitionHistoryPage.vue', 'name' => 'recognitionHistory',],
                        ['path' => '/profile', 'component' => '/src/pages/client/ProfilePage.vue', 'name' => 'profile',],
                        ['path' => '/attendance', 'component' => '/src/components/AttendancePage.vue', 'name' => 'attendance',],
                    ],
                    'name' => 'hr'
                ]
            ]), 200);
        }

        if ($user->hasRole('admin')) {

            $capture_original = json_decode($response->content(), true);
            return response()->json(array_merge($capture_original, [
                'role' => $user->roles->pluck('name'),
                'routes' => [
                    'path' => '/',
                    'component' => 'src/layouts/Admin-pageMain.vue',
                    'children' => [
                        ['path' => '/', 'component' => '/src/pages/dashboard/Admin-Dashboard.vue', 'name' => 'adminDashboard',],
                        ['path' => '/events', 'component' => '/src/pages/trainingEvents/admin/Admin-ViewEvent.vue', 'name' => 'adminEvents',],
                        ['path' => '/new-bpm', 'component' => '/src/pages/bloodPressure/NewBPMRecordPage.vue', 'name' => 'newBpm',],
                        ['path' => '/employee-bpm', 'component' => '/src/pages/bloodPressure/Admin_BPMArchive.vue', 'name' => 'employeeBpm',],
                        ['path' => '/finished-events', 'component' => '/src/pages/trainingEvents/admin/FinishedEventsPage.vue', 'name' => 'finishedEvents',],
                        ['path' => '/create-recognition', 'component' => '/src/pages/recognition/admin/AdminCreateRecognition.vue', 'name' => 'createRecognition',],
                        ['path' => '/admin-recognition-history', 'component' => '/src/pages/recognition/admin/AdminRecognitionHistory.vue', 'name' => 'adminRecognitionHistory',],
                        ['path' => '/events/:id', 'component' => '/src/pages/trainingEvents/admin/EventDetail.vue', 'name' => 'adminEventDetail',],
                    ],
                    'name' => 'admin'
                ],
            ]), 200);
        }

        return $response;
    }
}
