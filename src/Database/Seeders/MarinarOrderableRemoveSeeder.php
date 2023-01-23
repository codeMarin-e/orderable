<?php
    namespace Marinar\Orderable\Database\Seeders;

    use Illuminate\Database\Seeder;
    use Marinar\Orderable\MarinarOrderable;

    class MarinarOrderableRemoveSeeder extends Seeder {

        use \Marinar\Marinar\Traits\MarinarSeedersTrait;

        public function run() {
            if(!in_array(env('APP_ENV'), ['dev', 'local'])) return;
            static::$packageName = 'marinar_orderable';
            static::$packageDir = MarinarOrderable::getPackageMainDir();

            $this->autoRemove();

            $this->refComponents->info("Done!");
        }
    }
