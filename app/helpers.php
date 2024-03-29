<?php

use App\Contracts\ResponseCodeContract;
use App\Exceptions\ErrorResponse;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

if (!function_exists('error_response')) {
    /** @throws null */
    function error_response(string|ResponseCodeContract $message, string|int $code = 0, ?Throwable $previous = null)
    {
        if ($message instanceof ResponseCodeContract) {
            $code = $message->value ?? $code;
            $message = $message->message();
        }
        if (is_null($previous)) {
            $backfiles = debug_backtrace();
            $previous = new Exception("Error in {$backfiles[1]['file']} on line {$backfiles[1]['line']}" . PHP_EOL . "Error in {$backfiles[0]['file']} on line {$backfiles[0]['line']}");
        }
        throw new ErrorResponse($message, $code, $previous);
    }
}

if (!function_exists('error_unless')) {

    function error_unless(bool|callable $condition, string|ResponseCodeContract $message, string|int $code = 0): void
    {
        $backfiles = debug_backtrace();
        $previous = new Exception('Error in ' . $backfiles[0]['file'] . ' on line ' . $backfiles[0]['line']);
        $condition = is_callable($condition) ? $condition() : $condition;
        if ($condition === false) {
            error_response($message, $code, $previous);
        }
    }
}

if (!function_exists('error')) {
    function error(string|ResponseCodeContract $message): Closure
    {
        return fn() => error_response($message);
    }
}

if (!function_exists('error_if')) {
    function error_if(bool|callable $condition, string|ResponseCodeContract $message, string|int $code = 0): void
    {
        $backfiles = debug_backtrace();
        $previous = new Exception('Error in ' . $backfiles[0]['file'] . ' on line ' . $backfiles[0]['line']);

        $condition = is_callable($condition) ? $condition() : $condition;
        if ($condition === true) {
            error_response($message, $code, $previous);
        }
    }
}

if (!function_exists('get_from_multilingual')) {
    /**
     * @throws null
     */
    function get_from_multilingual(object|string|array|null $multi, string|null $default = null): string
    {
        $default ??= '';
        if ($multi !== null) {
            if (is_object($multi)) $multi = (array)$multi;
            if (!is_array($multi)) $multi = rescue(static fn() => json_decode($multi, true, flags: JSON_THROW_ON_ERROR), $multi);
            if (is_string($multi)) return $multi;
            if (isset($multi[app()->getLocale()])) return $multi[app()->getLocale()];
            foreach (array_keys(config('app.languages', [])) as $lang) {
                if (isset($multi[$lang])) return $multi[$lang];
            }
        }
        return $default;
    }
}

if (!function_exists('minio_url')) {
    function minio_url(string|null $path, int|Carbon|null $expiryMinutes = null, ?callable $not_found_cb = null): string|null
    {
        if (!empty($path)) {
            $disk = Storage::disk(config('filesystems.cloud'));
            if ($expiryMinutes) {
                $expired_at = is_int($expiryMinutes) ? now()->addMinutes($expiryMinutes) : $expiryMinutes;

                try {
                    return $disk->temporaryUrl($path, $expired_at);
                } catch (Exception) {
                }
            } else {
                try {
                    return $disk->url($path);
                } catch (Exception) {
                }
            }
        }

        return is_callable($not_found_cb) ? $not_found_cb() : null;
    }
}

if (!function_exists('user')) {
    function user(): User|Authenticatable|null
    {
        if (app()->runningInConsole()) {
            return User::findOrFail(config('user_id'));
        }
        abort_unless(auth('sanctum')->check(), 401);
        return auth('sanctum')->user();
    }
}

if (!function_exists('db_conn_tpl')) {
    function db_conn_tpl($host, $port, $db, $user, $pwd): array
    {
        return [
            'driver' => 'pgsql',
            'host' => $host,
            'port' => $port,
            'database' => $db,
            'username' => $user,
            'password' => $pwd,
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'sslmode' => 'prefer',
        ];
    }
}

if (!function_exists('date_time')) {
    function date_time(?DateTime $dateTime, string $key = '', string $prefix = '', $dt_only = false): array
    {
        if($dateTime === null) return [];
        if (!empty($prefix)) $prefix = $prefix . '_';
        if (empty($key)) $key = 'date_time';

        return $dt_only ? [
            $key => $dateTime->format('Y-m-d H:i:s'),
        ] : [
            $key => $dateTime->format('Y-m-d H:i:s'),
            $prefix . 'date' => $dateTime->format('d.m.Y'),
            $prefix . 'time' => $dateTime->format('H:i'),
        ];
    }
}

if (!function_exists('formatted')) {
    function formatted(array|int|float|string|null $amount, string $name = '', $tiyin = true, $decimals = 2, $append = '', $prepend = '', $no_zero = false, $value_only = false): array|string
    {
        if ($amount === null) $amount = 0;

        if (is_string($amount)) $amount = (int)preg_replace('/\s+/', '', $amount);

        if (is_array($amount)) {
            $arr = [];
            foreach ($amount as $key => $value) {
                $arr = [...$arr, ...formatted($value, $key, $tiyin, $decimals, $append, $prepend, $no_zero)];
            }
            return $arr;
        } else {
            if (strlen($name) > 0 || $value_only) {
                $fmtd = number_format($tiyin ? $amount / 100 : $amount, $decimals, '.', ' ');
                if ($no_zero) {
                    $fmtd = rtrim($fmtd, '0');
                }
                $fmtd = $prepend . $fmtd . $append;

                return $value_only ? $fmtd : [
                    $name => $amount,
                    $name . '_formatted' => $fmtd,
                ];
            }
        }
        return [];
    }
}

if (!function_exists('phone_mask')) {
    function phone_mask(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        return strlen($phone) == 12 ? '+' . mask($phone, 5, 4) : mask($phone, 4, 3);
    }
}

if (!function_exists('mask')) {
    /**
     * Phone/Card number mask
     */
    function mask(string $number, int $mask_length = -1, int $left_pad = -1, string $mask_char = '*', int $mask_char_length = -1): string
    {
        $length = strlen($number);
        $left_pad = max($left_pad, 0);
        $mask_length = $mask_length < 0 ? $length : min($mask_length, $length - $left_pad);
        $mask_char_length = $mask_char_length < 0 ? max($mask_length, 0) : $mask_char_length;
        $mask = str_repeat($mask_char, $mask_char_length);
        return preg_match('/^(.{' . $left_pad . '})(.{' . $mask_length . '})(.*?)$/', trim($number), $matches) ? $matches[1] . $mask . $matches[3] : $number;
    }
}

if (!function_exists('is_user')) {
    function is_user(): bool
    {
        return auth('api')->check();
    }
}

if (!function_exists('is_guest')) {
    function is_guest(): bool
    {
        return auth('api')->guest();
    }
}

if (!function_exists('set_if')) {
    function set_if(bool|callable|null $condition, &$variable, $value, $elseValue = "\0")
    {
        if ($condition === null) $condition = true;

        if (is_callable($condition)) $condition = $condition();

        if ($condition === true)
            $variable = $value instanceof Closure ? $value() : $value;
        elseif ($elseValue !== chr(0))
            $variable = is_callable($elseValue) ? $elseValue() : $elseValue;

        return $variable;
    }
}

if (!function_exists('ulid')) {
    function ulid($long = false): string
    {
        return $long ? Str::ulid()->toBase32() : Str::ulid()->toBase58();
    }
}

if (!function_exists('uulid')) {
    function uulid(): string
    {
        return Str::ulid()->toRfc4122();
    }
}

if (!function_exists('multrans')) {
    /**
     * @throws null
     */
    function multrans(object|string|array|null $multi, string|null $default = null): string|null
    {
        $default ??= '';
        if ($multi !== null) {
            if (is_object($multi)) $multi = (array)$multi;
            if (!is_array($multi)) $multi = rescue(static fn() => json_decode($multi, true, flags: JSON_THROW_ON_ERROR), $multi);
            if (is_string($multi)) return $multi;
            if (isset($multi[app()->getLocale()])) return $multi[app()->getLocale()];
            foreach (array_keys(config('app.languages', [])) as $lang) {
                if (isset($multi[$lang])) return $multi[$lang];
            }
        }
        return $default;
    }
}

if (!function_exists('input')) {
    /**
     * Get request input value
     */
    function input(string|array|null $key = null, $default = null, string|callable|null $filter = null): mixed
    {
        $val = $key === null ? $default : (is_array($key) ? request()->only($key) : request()->input($key, $default));
        if ($val !== null && $filter !== null) {
            if (is_callable($filter)) {
                $val = $filter($val);
            } elseif (is_string($filter)) {
                foreach (explode('|', $filter) as $func) {
                    if (function_exists($func)) {
                        $val = $func($val);
                    }
                }
            }
        }
        return $val;
    }
}

if (!function_exists('telegram_bot_send')) {
    function telegram_bot_send($message, $chat_id = null, $parse_mode = 'Markdown'): void
    {
        $token = config('services.telegram.token');
        if (empty($token)) return;
        $chat_id = $chat_id ?? config('services.telegram.chat_id');
        if (empty($chat_id)) return;

        $req = Http::baseUrl("https://api.telegram.org")->post("/bot{$token}/sendMessage", [
            'chat_id' => $chat_id,
            'text' => $message,
            'parse_mode' => $parse_mode,
        ]);

        error_unless($req->ok(), 'Telegram bot send error');
    }
}

if (!function_exists('file_to_base64')) {
    function file_to_base64($file): string
    {
        return base64_encode(file_get_contents($file));
    }
}

if (!function_exists('file_upload')) {
    function file_upload($file, $path): false|string|null
    {
        if ($file instanceof UploadedFile) {
            return $file->store($path, 'public');
        }

        return null;
    }
}

if (!function_exists('delete_file')) {
    function delete_file($path, $disk = 'public'): void
    {
        Storage::disk($disk)->delete($path);
    }
}

if(!function_exists('is_match_objects'))
{
    function is_match_objects(object $object_first, object $object_second, array $keys = []): bool
    {
        foreach ($keys as $key) {
            if ($object_first->{$key} != $object_second->{$key}) {
                return false;
            }
        }

        return true;
    }
}

if(!function_exists('is_match_arrays'))
{
    function is_match_arrays(array $array_first, array $array_second, array $keys = []): bool
    {
        foreach ($keys as $key) {
            if($array_first[$key] != $array_second[$key]){
                return false;
            }
        }

        return true;
    }
}

if(!function_exists('user_company'))
{
    function user_company()
    {
        return user()->company;
    }
}
