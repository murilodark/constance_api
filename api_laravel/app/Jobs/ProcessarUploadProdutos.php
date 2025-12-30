<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Produto;
use App\Notifications\ProdutosImportadosNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\LazyCollection;

class ProcessarUploadProdutos implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected string $filePath,
        protected int $fornecedorId,
        protected User $user // Quem solicitou o upload
    ) {}

    public function handle(): void
    {
        // Previne timeout e estouro de memória em 2025
        ini_set('memory_limit', '512M');
        set_time_limit(0);

        $path = Storage::path($this->filePath);

        LazyCollection::make(function () use ($path) {
            $handle = fopen($path, 'r');
            $header = fgetcsv($handle, 0, ","); // Lê o cabeçalho real

            while (($line = fgetcsv($handle, 0, ",")) !== false) {
                // Proteção contra linhas com número de colunas diferente das chaves
                if (count($line) === 4) {
                    yield array_combine(['referencia', 'nome', 'cor', 'preco'], $line);
                }
            }
            fclose($handle);
        })->chunk(100)->each(function ($chunk) {
            $toInsert = [];

            foreach ($chunk as $item) {
                $toInsert[] = [
                    'referencia'      => $item['referencia'],
                    'nome'            => $item['nome'],
                    'cor'             => $item['cor'],
                    'preco'           => (float) str_replace(',', '.', $item['preco']),
                    'fornecedores_id' => $this->fornecedorId,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ];
            }

            // Insere em lotes de 100 (Extremamente mais rápido que Produto::create)
            \App\Models\Produto::insert($toInsert);
        });

        Storage::delete($this->filePath);
        $this->user->notify(new \App\Notifications\ProdutosImportadosNotification());
    }
}
