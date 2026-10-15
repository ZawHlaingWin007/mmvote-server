<?php

namespace App\Services;

use App\Support\QuestionVotingResultReport;
use Illuminate\Support\Facades\File;
use RuntimeException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Throwable;

class QuestionVotingResultPdfGenerator
{
    public static function downloadResponse(int $electionId)
    {
        $report = QuestionVotingResultReport::forElection($electionId);
        $regularFont = self::resolveFontPath('regular');
        $boldFont = self::resolveFontPath('bold');

        $html = view('voting.pdf-result', [
            'report' => $report,
            'fontRegularDataUri' => self::fontDataUri($regularFont),
            'fontBoldDataUri' => self::fontDataUri($boldFont),
        ])->render();

        try {
            $binary = self::renderPdfViaPuppeteer($html);
        } catch (Throwable $exception) {
            $details = trim($exception->getMessage());
            $message = 'PDF generation failed. Ensure Node.js, npm install (puppeteer), and Chrome are available on the server.';
            if (config('app.debug') && $details !== '') {
                $message .= ' Details: ' . $details;
            }

            throw new RuntimeException($message, 0, $exception);
        }

        $filename = 'ques_voting_result.pdf';

        return response($binary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length' => strlen($binary),
        ]);
    }

    private static function renderPdfViaPuppeteer(string $html): string
    {
        $script = base_path('scripts/render-html-to-pdf.cjs');
        if (! File::exists($script)) {
            throw new RuntimeException('Missing PDF script: scripts/render-html-to-pdf.cjs');
        }

        $tmpdir = storage_path('app/pdf-tmp');
        if (! File::isDirectory($tmpdir)) {
            File::makeDirectory($tmpdir, 0755, true);
        }

        $id = str_replace('.', '', uniqid('pdf_', true));
        $htmlPath = $tmpdir . DIRECTORY_SEPARATOR . $id . '.html';
        $pdfPath = $tmpdir . DIRECTORY_SEPARATOR . $id . '.pdf';
        $configPath = $tmpdir . DIRECTORY_SEPARATOR . $id . '.json';

        File::put($htmlPath, $html);
        File::put($configPath, json_encode([
            'htmlPath' => $htmlPath,
            'outputPath' => $pdfPath,
            'executablePath' => self::resolveChromePath(),
            'format' => 'A4',
            'printBackground' => true,
            'noSandbox' => PHP_OS_FAMILY !== 'Windows',
            'timeoutMs' => 120000,
            'margin' => [
                'top' => '12mm',
                'right' => '10mm',
                'bottom' => '14mm',
                'left' => '10mm',
            ],
        ]));

        try {
            $process = new Process(
                [self::resolveNodeBinary(), $script, $configPath],
                base_path(),
                self::nodeProcessEnvironment()
            );
            $process->setTimeout(120);
            $process->run();

            if (! $process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            if (! File::exists($pdfPath)) {
                throw new RuntimeException('Puppeteer did not produce a PDF file.');
            }

            return File::get($pdfPath);
        } finally {
            @unlink($htmlPath);
            @unlink($pdfPath);
            @unlink($configPath);
        }
    }

    private static function nodeProcessEnvironment(): array
    {
        $env = [];
        foreach ($_SERVER as $key => $value) {
            if (is_string($key) && is_string($value)) {
                $env[$key] = $value;
            }
        }

        $env['NODE_PATH'] = base_path('node_modules');
        $env['PUPPETEER_CACHE_DIR'] = self::puppeteerCacheDirectory();

        if (PHP_OS_FAMILY === 'Windows' && empty($env['SystemRoot'])) {
            $env['SystemRoot'] = 'C:\\Windows';
        }

        return $env;
    }

    private static function puppeteerCacheDirectory(): string
    {
        if ($configured = env('PUPPETEER_CACHE_DIR')) {
            return $configured;
        }

        return base_path('.puppeteer-cache');
    }

    private static function resolveNodeBinary(): string
    {
        if ($configured = env('BROWSERSHOT_NODE')) {
            return $configured;
        }

        $candidates = PHP_OS_FAMILY === 'Windows'
            ? [
                'C:\\Program Files\\nodejs\\node.exe',
                'C:\\Program Files (x86)\\nodejs\\node.exe',
            ]
            : [
                '/usr/bin/node',
                '/usr/local/bin/node',
            ];

        foreach ($candidates as $candidate) {
            if (File::exists($candidate)) {
                return $candidate;
            }
        }

        return 'node';
    }

    private static function resolveChromePath(): string
    {
        if ($configured = env('BROWSERSHOT_CHROME_PATH')) {
            if (! File::exists($configured)) {
                throw new RuntimeException('BROWSERSHOT_CHROME_PATH does not exist: ' . $configured);
            }

            return $configured;
        }

        $fromCache = self::findChromeInPuppeteerCache();
        if ($fromCache !== null) {
            return $fromCache;
        }

        $fromPuppeteer = self::puppeteerExecutablePath();
        if ($fromPuppeteer !== null) {
            return $fromPuppeteer;
        }

        $systemChrome = self::findSystemChrome();
        if ($systemChrome !== null) {
            return $systemChrome;
        }

        throw new RuntimeException(
            'Chrome executable not found. Run "npm run puppeteer:install-chrome" in the project root, or set BROWSERSHOT_CHROME_PATH in .env.'
        );
    }

    private static function puppeteerExecutablePath(): ?string
    {
        $node = self::resolveNodeBinary();
        $script = base_path('scripts/puppeteer-chrome-path.cjs');
        if (! File::exists($script)) {
            return null;
        }

        $process = new Process([$node, $script], base_path(), self::nodeProcessEnvironment());
        $process->setTimeout(120);
        $process->run();

        if (! $process->isSuccessful()) {
            return null;
        }

        $path = trim($process->getOutput());
        if ($path === '' || ! File::exists($path)) {
            return null;
        }

        return $path;
    }

    private static function findChromeInPuppeteerCache(): ?string
    {
        return self::findChromeExecutableInDirectory(self::puppeteerCacheDirectory());
    }

    private static function findSystemChrome(): ?string
    {
        if (PHP_OS_FAMILY === 'Windows') {
            $candidates = [
                'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
                'C:\\Program Files (x86)\\Google\\Chrome\\Application\\chrome.exe',
            ];
        } elseif (PHP_OS_FAMILY === 'Darwin') {
            $candidates = [
                '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
            ];
        } else {
            $candidates = [
                '/usr/bin/google-chrome',
                '/usr/bin/google-chrome-stable',
                '/usr/bin/chromium',
                '/usr/bin/chromium-browser',
            ];
        }

        foreach ($candidates as $candidate) {
            if (File::exists($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private static function findChromeExecutableInDirectory(string $directory): ?string
    {
        if (! File::isDirectory($directory)) {
            return null;
        }

        $executableName = PHP_OS_FAMILY === 'Windows' ? 'chrome.exe' : 'chrome';

        try {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if (! $file->isFile()) {
                    continue;
                }

                if (strcasecmp($file->getFilename(), $executableName) === 0) {
                    return $file->getPathname();
                }
            }
        } catch (\Throwable $exception) {
            return null;
        }

        return null;
    }

    private static function resolveFontPath(string $weight): string
    {
        $candidates = $weight === 'bold'
            ? [
                storage_path('fonts/NotoSansMyanmar-Bold-unhinted.ttf'),
                storage_path('fonts/NotoSansMyanmar-Bold.ttf'),
                storage_path('fonts/NotoSansMyanmar-Regular-unhinted.ttf'),
                storage_path('fonts/NotoSansMyanmar-Regular.ttf'),
            ]
            : [
                storage_path('fonts/NotoSansMyanmar-Regular-unhinted.ttf'),
                storage_path('fonts/NotoSansMyanmar-Regular.ttf'),
            ];

        foreach ($candidates as $path) {
            if (File::exists($path)) {
                return $path;
            }
        }

        throw new RuntimeException('Myanmar PDF font files are missing in storage/fonts/.');
    }

    private static function fontDataUri(string $path): string
    {
        return 'data:font/truetype;base64,' . base64_encode(File::get($path));
    }
}
