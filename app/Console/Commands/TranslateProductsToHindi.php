<?php

namespace App\Console\Commands;

use App\Models\products;
use Illuminate\Console\Command;
use Stichoza\GoogleTranslate\GoogleTranslate;

class TranslateProductsToHindi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:translate-products-to-hindi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Translate English product names to Hindi and store in DB';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $batchSize = 200;
        $translator = new GoogleTranslate('hi');
        $translator->setSource('en');
        $translator->setTarget('hi');
        $products = products::whereNull('hindi')
            ->limit($batchSize)
            ->get();

        foreach ($products as $product) {
            try {
                $translated = $translator->translate($product->name);
                $product->hindi = $translated;
                $product->save();

                $this->info("Translated: {$product->name} → {$translated}");
                sleep(3);
            } catch (\Exception $e) {
                $this->error("Error for {$product->id}: " . $e->getMessage());
            }
        }

        $this->info("Batch of {$batchSize} completed.");
    }
}
