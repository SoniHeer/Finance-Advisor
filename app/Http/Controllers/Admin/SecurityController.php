<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SecurityController extends Controller
{
    /**
     * Security Intelligence Console
     */
    public function index(Request $request): View
    {
        $days = (int) $request->get('days', 7);

        $query = Activity::with('user')
            ->when($days > 0, function ($q) use ($days) {
                $q->where('created_at', '>=', now()->subDays($days));
            });

        $activities = $query->latest()->paginate(20);

        // Cache analytics for performance
        $analysis = Cache::remember(
            "security_analysis_{$days}",
            60, // seconds
            function () use ($query) {
                return $this->analyzeThreat($query->get());
            }
        );

        $severities = $this->mapSeverities($activities);

        return view('admin.activities.index', [
            'activities' => $activities,
            'analysis'   => $analysis,
            'severities' => $severities,
            'days'       => $days,
        ]);
    }

    /**
     * Threat Analyzer Engine (Enterprise Weighted)
     */
    private function analyzeThreat($collection): array
    {
        $weights = [
            'delete'  => 30,
            'blocked' => 40,
            'failed'  => 40,
            'update'  => 15,
        ];

        $score       = 0;
        $deleteCount = 0;
        $updateCount = 0;
        $loginCount  = 0;

        foreach ($collection as $log) {

            $desc = strtolower($log->description ?? '');

            foreach ($weights as $keyword => $value) {
                if (strpos($desc, $keyword) !== false) {
                    $score += $value;

                    if ($keyword === 'delete') {
                        $deleteCount++;
                    }

                    if ($keyword === 'update') {
                        $updateCount++;
                    }
                }
            }

            if (strpos($desc, 'login') !== false) {
                $loginCount++;
            }
        }

        $score = min($score, 100);

        return [
            'score'       => $score,
            'level'       => $this->threatLevel($score),
            'deleteCount' => $deleteCount,
            'updateCount' => $updateCount,
            'loginCount'  => $loginCount,
            'totalLogs'   => $collection->count(),
        ];
    }

    /**
     * Severity Mapping for Table
     */
    private function mapSeverities($activities): array
    {
        $map = [];

        foreach ($activities as $activity) {

            $desc = strtolower($activity->description ?? '');

            if (strpos($desc, 'delete') !== false) {
                $map[$activity->id] = [
                    'label' => 'Critical',
                    'class' => 'bg-rose-100 text-rose-600'
                ];
            } elseif (strpos($desc, 'update') !== false) {
                $map[$activity->id] = [
                    'label' => 'Medium',
                    'class' => 'bg-amber-100 text-amber-600'
                ];
            } elseif (strpos($desc, 'login') !== false) {
                $map[$activity->id] = [
                    'label' => 'Normal',
                    'class' => 'bg-emerald-100 text-emerald-600'
                ];
            } else {
                $map[$activity->id] = [
                    'label' => 'Info',
                    'class' => 'bg-slate-200 text-slate-700'
                ];
            }
        }

        return $map;
    }

    /**
     * Threat Level Classifier
     */
    private function threatLevel(int $score): array
    {
        if ($score >= 70) {
            return [
                'label' => 'CRITICAL',
                'color' => '#dc2626',
                'text'  => 'text-rose-600'
            ];
        }

        if ($score >= 40) {
            return [
                'label' => 'HIGH',
                'color' => '#f59e0b',
                'text'  => 'text-amber-600'
            ];
        }

        if ($score >= 15) {
            return [
                'label' => 'MODERATE',
                'color' => '#eab308',
                'text'  => 'text-yellow-600'
            ];
        }

        return [
            'label' => 'SECURE',
            'color' => '#16a34a',
            'text'  => 'text-emerald-600'
        ];
    }
}
