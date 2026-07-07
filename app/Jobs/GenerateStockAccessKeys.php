<?php


namespace App\Jobs;

use App\Models\StockAccessKey;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;
class GenerateStockAccessKeys implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $productIdentifier,
        public int $access_key_num,
        public int $bitSize,
        public string $task_level

    ) {
        //

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        $uniqueKeys = [];
        while (count($uniqueKeys) < $this->access_key_num) {
            $key = match ($this->bitSize) {
                16 => strtoupper(Str::random(16)),
                32 => strtoupper(Str::random(32)),
                64 => strtoupper(Str::random(64)),
                default => strtoupper(Str::random(16)),
            };
            $uniqueKeys[$key] = true;
        }
        $insertData = [];
        $now = now();
        $batchSize = 2000;
        foreach (array_keys($uniqueKeys) as $accessKey) {
            $insertData[] = [
                'product_id' => $this->productIdentifier,
                'access_key' => $accessKey,
                'status' => 0,
                'created_at' => $now,
                'updated_at' => $now
            ];
            if (count($insertData) >= $batchSize) {
                StockAccessKey::insertOrIgnore($insertData);
                $insertData = [];
            }
        }
        if (!empty($insertData)) {
            StockAccessKey::insertOrIgnore($insertData);
        }
        info("已成功为产品 [{$this->productIdentifier}] 批量生成并写入 [{$this->access_key_num}] 个互不重复的卡密");
    }
}
