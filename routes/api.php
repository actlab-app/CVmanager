<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/deploy', function (Request $request) {
    $expectedToken = config('services.deploy.token');
    $givenToken = $request->header('X-Deploy-Token');

    if (! $expectedToken || ! hash_equals($expectedToken, (string) $givenToken)) {
        return response()->json([
            'ok' => false,
            'message' => 'Invalid deploy token.',
        ], 403);
    }

    $script = '/home/actlabap/deploy/cvm.sh';
    $log = '/home/actlabap/deploy/cvm.log';
    $lock = '/home/actlabap/deploy/cvm.lock';

    if (file_exists($lock)) {
        return response()->json([
            'ok' => false,
            'message' => 'Deploy zaten çalışıyor.',
        ], 409);
    }

    $command = 'touch ' . escapeshellarg($lock)
        . ' && nohup /bin/bash ' . escapeshellarg($script)
        . ' >> ' . escapeshellarg($log)
        . ' 2>&1; rm -f ' . escapeshellarg($lock)
        . ' > /dev/null 2>&1 & echo $!';

    exec($command, $output, $exitCode);

    if ($exitCode !== 0) {
        @unlink($lock);

        return response()->json([
            'ok' => false,
            'message' => 'Deploy başlatılamadı.',
        ], 500);
    }

    return response()->json([
        'ok' => true,
        'message' => 'Deploy başlatıldı.',
        'pid' => $output[0] ?? null,
    ]);
});