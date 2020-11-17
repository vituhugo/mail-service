<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Mail;
use Jenssegers\Blade\Blade;
use LaravelZero\Framework\Commands\Command;
use Mails\EmailPadrao;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class UpCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'app:up';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Inicia a aplicação';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $config = config('queue.connection');
        $connection = new AMQPStreamConnection($config['host'], $config['port'], $config['user'], $config['password']);
        $channel = $connection->channel();

        if (env('APP_ENV') !== 'production') {
            $channel->queue_declare(config('queue.name'), false, false, false, false);
        }

        $callback = function ($req) {
            Mail::send(new EmailPadrao($req));
        };

        $channel->basic_consume(config('queue.name'), '', false, true, false, false, $callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
