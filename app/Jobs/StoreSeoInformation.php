<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;
use Carbon\Carbon;
use DiDom\Document;

class StoreSeoInformation implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected int $id;
    protected string $name;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $response = Http::get($this->name);
            $status = $response->status();
            $html = $response->body();
            $document = new Document($html);
            if (boolval($document->first('h1'))) {
                $h1 = ($document->first('h1')->innerHtml());
            }
            if (boolval($document->first('meta[name=keywords]'))) {
                $keywords = ($document->first('meta[name=keywords]')->getAttribute('content'));
            }
            if (boolval($document->first('meta[name=description]'))) {
                $description = ($document->first('meta[name=description]')->getAttribute('content'));
            }
        } catch (ConnectionException) {
            $status = 0;
        }

        $now = Carbon::now();
        DB::table('url_checks')->insert(
            [
                'url_id' => $this->id,
                'status_code' => $status,
                'h1' => $h1 ?? '',
                'keywords' => $keywords ?? '',
                'description' => $description ?? '',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
        DB::table('urls')
            ->where('id', $this->id)
            ->update(['updated_at' => $now]);
    }
}
