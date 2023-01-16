<?php
    namespace Marinar\Orderable\Database\Seeders;

    use App\Models\Package;
    use Illuminate\Database\Seeder;
    use Symfony\Component\Process\Exception\ProcessFailedException;
    use Symfony\Component\Process\Process;

    class MarinarOrderableInstallSeeder extends Seeder {

        use \Marinar\Marinar\Traits\MarinarSeedersTrait;

        public function run() {
            $this->getRefComponents();
            $this->giveGitPermissions(\Marinar\Orderable\MarinarOrderable::getPackageMainDir());
            $this->refComponents->info("Done!");
        }

    }
