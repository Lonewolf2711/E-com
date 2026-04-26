<?php
/**
 * Setting Model
 * ─────────────
 * Manages key-value store settings.
 * Table: settings
 */

class Setting extends Model
{
    protected string $table = 'settings';
    private static array $cache = [];

    /**
     * Get a setting value.
     */
    public function getValue(string $key, string $default = ''): string
    {
        if (!isset(self::$cache[$key])) {
            $result = $this->findWhere('setting_key = ?', [$key]);
            self::$cache[$key] = $result ? $result['setting_value'] : $default;
        }
        return self::$cache[$key];
    }

    /**
     * Set a setting value (create or update).
     */
    public function setValue(string $key, string $value): bool
    {
        $existing = $this->findWhere('setting_key = ?', [$key]);
        if ($existing) {
            $result = $this->update($existing['id'], ['setting_value' => $value]);
        } else {
            $result = (bool) $this->create([
                'setting_key'   => $key,
                'setting_value' => $value,
            ]);
        }
        self::$cache[$key] = $value;
        return $result;
    }

    /**
     * Get all settings in a group.
     */
    public function getByGroup(string $group): array
    {
        return $this->where('setting_group = ?', [$group], 'setting_key', 'ASC');
    }

    /**
     * Get all settings grouped.
     */
    public function getAllGrouped(): array
    {
        $all = $this->findAll('setting_group', 'ASC');
        $grouped = [];
        foreach ($all as $setting) {
            $grouped[$setting['setting_group']][] = $setting;
        }
        return $grouped;
    }

    /**
     * Bulk update settings.
     */
    public function bulkUpdate(array $settings): void
    {
        foreach ($settings as $key => $value) {
            $this->setValue($key, $value);
        }
    }

    /**
     * Clear the settings cache.
     */
    public static function clearCache(): void
    {
        self::$cache = [];
    }
}
