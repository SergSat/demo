<?php

namespace Database\Factories;

use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Intervention\Image\Decoders\FilePathImageDecoder;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $position = Position::inRandomOrder()->first();

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'position_id' => $position->id,
            'position' => $position->name,
            'photo' => $this->generateResizedPhoto(),
            'phone' => '+38' . fake()->numerify('0#########'), // Number like +380501234567
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Generate and stored a resized photo for the user.
     *
     * @return string
     */
    private function generateResizedPhoto(): string
    {
        $imageManager = new ImageManager(new Driver());

        // Link to random user avatar
        $imageUrl = 'https://randomuser.me/api/portraits/' . fake()->randomElement(['men', 'women']) . '/' . fake()->numberBetween(1, 99) . '.jpg';

        // Получаем изображение через HTTP
        $imageData = Http::get($imageUrl)->body();

        // Создаем временный файл
        $tempFile = tmpfile();
        $tempFilePath = stream_get_meta_data($tempFile)['uri'];

        // Записываем данные изображения в временный файл
        fwrite($tempFile, $imageData);

        // Устанавливаем указатель в начало файла
        fseek($tempFile, 0);

        // Генерируем имя файла для сохранения
        $fileName = Str::random(10) . '.jpg';
        $filePath = storage_path('app/public/users/' . $fileName);

        // Открываем изображение для работы
        $image = $imageManager->read($tempFilePath);

        // Меняем размер изображения
        $image->resize(70, 70);

        // Сохраняем измененное изображение
        $image->save($filePath);

        // Закрываем временный файл
        fclose($tempFile);

        // Возвращаем путь к сохраненному файлу
        return 'storage/users/' . $fileName;
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
