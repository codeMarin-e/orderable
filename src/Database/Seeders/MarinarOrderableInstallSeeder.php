<?php
    namespace Marinar\Orderable\Database\Seeders;

    use Illuminate\Database\Seeder;
    use Marinar\Orderable\MarinarOrderable;

    class MarinarOrderableInstallSeeder extends Seeder {

        use \Marinar\Marinar\Traits\MarinarSeedersTrait;

        public static function configure() {
            static::$packageName = 'marinar_orderable';
            static::$packageDir = MarinarOrderable::getPackageMainDir();
        }

        public function run() {
            if(!in_array(env('APP_ENV'), ['dev', 'local'])) return;

            $this->autoInstall();

            $this->refComponents->info("Done!");
        }

    }
