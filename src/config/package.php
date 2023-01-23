<?php
	return [
		'install' => [
            'php artisan db:seed --class="\Marinar\Orderable\Database\Seeders\MarinarOrderableInstallSeeder"',
		],
        'remove' => [
            'php artisan db:seed --class="\Marinar\Orderable\Database\Seeders\MarinarOrderableRemoveSeeder"',
        ]
	];
